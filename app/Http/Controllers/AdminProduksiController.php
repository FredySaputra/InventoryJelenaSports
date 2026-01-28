<?php

namespace App\Http\Controllers;

use App\Models\ProgresProduksi;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // <--- WAJIB DITAMBAHKAN AGAR TIDAK ERROR 500

class AdminProduksiController extends Controller
{
    // ... method konfirmasiPekerjaan tetap sama ...
    public function konfirmasiPekerjaan(Request $request, $idProgres)
    {
        // ... (kode sama seperti yang Anda kirim, tidak perlu diubah) ...
        $progres = ProgresProduksi::with('detail')->find($idProgres);

        if (!$progres) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        if (trim(strtolower($progres->status)) !== 'menunggu') {
            return response()->json([
                'message' => 'Gagal: Data ini statusnya sudah bukan Menunggu (Status: ' . $progres->status . ')'
            ], 400);
        }

        return DB::transaction(function () use ($request, $progres) {
            if ($request->action === 'reject') {
                $progres->update([
                    'status' => 'Ditolak',
                    'waktu_konfirmasi' => now()
                ]);
                return response()->json(['message' => 'Laporan berhasil ditolak.']);
            }

            $request->validate([
                'jumlah_diterima' => 'required|integer|min:0'
            ]);

            $progres->update([
                'status' => 'Disetujui',
                'jumlah_diterima' => $request->jumlah_diterima,
                'waktu_konfirmasi' => now()
            ]);

            $stok = Stok::firstOrCreate(
                [
                    'idProduk' => $progres->detail->idProduk,
                    'idSize'   => $progres->detail->idSize
                ],
                ['stok' => 0]
            );

            $stok->increment('stok', $request->jumlah_diterima);
            $progres->detail->increment('jumlah_selesai', $request->jumlah_diterima);

            $detailCurrent = $progres->detail;
            $spkInduk = $detailCurrent->perintahProduksi;

            if ($spkInduk) {
                $allDetails = $spkInduk->details()->get();
                $isAllDone = true;
                foreach($allDetails as $det) {
                    if ($det->jumlah_selesai < $det->jumlah_target) {
                        $isAllDone = false;
                        break;
                    }
                }

                if ($isAllDone) {
                    $spkInduk->update(['status' => 'Selesai']);
                } else {
                    if ($spkInduk->status === 'Pending') {
                        $spkInduk->update(['status' => 'Proses']);
                    }
                }
            }

            return response()->json(['message' => 'Sukses! Stok update & Status SPK diperbarui.']);
        });
    }

    public function getPending()
    {
        $data = ProgresProduksi::with([
            'karyawan',
            'detail.produk.bahan',
            'detail.size'
        ])
            ->where('status', 'Menunggu')
            ->orderBy('waktu_setor', 'asc')
            ->get();

        $formatted = $data->map(function($item) {
            $produk = $item->detail->produk ?? null;
            $namaLengkap = '-';
            if ($produk) {
                $namaBahan = $produk->bahan ? $produk->bahan->nama : '';
                $warna = $produk->warna ? $produk->warna : '';
                $namaLengkap = trim($produk->nama . ' ' . $warna . ' ' . $namaBahan);
            }

            return [
                'id_progres'   => $item->id,
                'waktu'        => $item->waktu_setor,
                'karyawan'     => $item->karyawan->nama ?? 'Unknown',
                'no_spk'       => $item->detail->perintahProduksi->id ?? '-',
                'produk'       => $namaLengkap,
                'size'         => $item->detail->size->tipe ?? '-',
                'jumlah_setor' => $item->jumlah_disetor,
                'id_detail'    => $item->idDetailProduksi
            ];
        });

        return response()->json(['data' => $formatted]);
    }

    // --- PERBAIKAN METHOD LEADERBOARD ---
    public function getLeaderboard(Request $request)
    {
        // Default: Bulan Ini
        $startDate = Carbon::now()->startOfMonth();
        $endDate   = Carbon::now()->endOfMonth();
        $periodeLabel = Carbon::now()->translatedFormat('F Y'); 

        // Jika ada filter tanggal
        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
            $periodeLabel = $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y');
        }

        // Query
        $leaderboard = DB::table('progres_produksis')
            ->join('users', 'progres_produksis.idKaryawan', '=', 'users.id')
            ->where('progres_produksis.status', 'Disetujui')
            ->whereBetween('progres_produksis.waktu_setor', [$startDate, $endDate])
            ->select(
                'users.nama as nama_karyawan',
                DB::raw('SUM(progres_produksis.jumlah_diterima) as total_produksi')
            )
            ->groupBy('progres_produksis.idKaryawan', 'users.nama')
            ->orderByDesc('total_produksi')
            ->limit(10)
            ->get();

        // Formatting
        $data = $leaderboard->map(function ($item, $index) {
            return [
                'rank' => $index + 1,
                'nama' => $item->nama_karyawan,
                'total' => (int) $item->total_produksi
            ];
        });

        return response()->json([
            'periode' => $periodeLabel,
            'data' => $data
        ]);
    }
}