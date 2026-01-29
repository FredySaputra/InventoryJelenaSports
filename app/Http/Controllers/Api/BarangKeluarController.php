<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Stok;
use Illuminate\Support\Facades\DB;
use App\Models\PerintahProduksi;
use App\Http\Requests\StoreBarangKeluarRequest;
use App\Http\Resources\TransaksiResource;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    public function index()
    { 
        $transaksis = Transaksi::with(['pelanggan', 'details.produk.bahan', 'details.size'])
            ->orderBy('created_at', 'desc')
            ->get();

        return TransaksiResource::collection($transaksis);
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            
            // Validasi: Items wajib dikirim dari frontend (karena mengandung harga yang diinput user)
            $request->validate([
                'tanggal' => 'required|date',
                'idPelanggan' => 'required',
                'items' => 'required|array|min:1', 
                'items.*.harga' => 'required|numeric|min:0', // Harga wajib diisi
            ]);

            // Generate ID TRX (TRX-000001)
            $lastTrx = Transaksi::orderBy('id', 'desc')->first();
            $trxId = $lastTrx ? 'TRX-' . str_pad(((int)substr($lastTrx->id, 4) + 1), 6, '0', STR_PAD_LEFT) : 'TRX-000001';

            $grandTotalHarga = 0;
            $grandTotalItem = 0;

            // Simpan Header
            $transaksi = Transaksi::create([
                'id' => $trxId,
                'tanggalTransaksi' => $request->tanggal,
                'idPelanggan' => $request->idPelanggan,
                // Simpan ID SPK jika ada (dikirim dari frontend)
                'idPerintahProduksi' => $request->idPerintahProduksi ?? null, 
                'totalTransaksi' => 0,
                'totalItem' => 0
            ]);

            // Loop Items (Data FINAL dari frontend)
            foreach ($request->items as $item) {
                $qty = $item['jumlah'];
                $harga = $item['harga']; // Harga ini hasil inputan Admin di tabel
                $subtotal = $qty * $harga;

                // Kurangi Stok
                $stokDb = Stok::where('idProduk', $item['idProduk'])
                    ->where('idSize', $item['idSize'])
                    ->first();

                if (!$stokDb || $stokDb->stok < $qty) {
                    throw new \Exception("Stok tidak cukup untuk produk ID: " . $item['idProduk']);
                }
                
                $stokDb->decrement('stok', $qty);

                DetailTransaksi::create([
                    'idTransaksi' => $trxId,
                    'idProduk' => $item['idProduk'],
                    'idSize' => $item['idSize'],
                    'jumlah' => $qty,
                    'hargaProduk' => $harga
                ]);

                $grandTotalHarga += $subtotal;
                $grandTotalItem += $qty;
            }

            // Update Total Header
            $transaksi->update([
                'totalTransaksi' => $grandTotalHarga,
                'totalItem' => $grandTotalItem
            ]);

            return new TransaksiResource($transaksi->load('details'));
        });
    }

    public function getSpkSiapKirim()
    {
        try {
            $query = PerintahProduksi::where('status', 'Selesai')
                ->where('id', '!=', 'SPK-DIRECT'); // Filter: Jangan ambil SPK Direct

            // Coba ambil data. Jika kolom idPerintahProduksi tidak ada, ini akan error.
            // Kita tangkap errornya di catch bawah.
            $spk = $query->doesntHave('transaksi') 
                ->with('pelanggan')
                ->orderBy('tanggal_target', 'asc')
                ->get();

            return response()->json([
                'data' => $spk->map(function($item) {
                    return [
                        'id' => $item->id,
                        'label' => "{$item->id} - " . ($item->pelanggan->nama ?? 'Umum') . " (" . ($item->tanggal_target ?? '-') . ")",
                        'idPelanggan' => $item->idPelanggan
                    ];
                })
            ]);

        } catch (\Exception $e) {
            // Ini akan menampilkan pesan error asli ke console browser
            return response()->json([
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // 2. Ambil Detail Barang dari SPK (Untuk mengisi tabel Frontend)
    public function getSpkItems($id)
    {
        $spk = PerintahProduksi::with(['details.produk', 'details.size'])->find($id);

        if (!$spk) return response()->json(['message' => 'SPK Tidak ditemukan'], 404);

        $items = [];
        foreach ($spk->details as $det) {
            // Hanya ambil barang yang berhasil diproduksi (jumlah > 0)
            if ($det->jumlah_selesai > 0) {
                
                // Cek Stok Gudang saat ini (Info tambahan untuk Admin)
                $stokGudang = Stok::where('idProduk', $det->idProduk)
                                  ->where('idSize', $det->idSize)
                                  ->value('stok') ?? 0;

                // Format nama produk lengkap
                $namaLengkap = trim(($det->produk->nama ?? '-') . ' ' . ($det->produk->warna ?? ''));

                $items[] = [
                    'idProduk'   => $det->idProduk,
                    'namaProduk' => $namaLengkap,
                    'idSize'     => $det->idSize,
                    'namaSize'   => $det->size->tipe ?? '-',
                    'jumlah'     => $det->jumlah_selesai, // Qty otomatis dari hasil produksi
                    'stokGudang' => $stokGudang,
                    'harga'      => 0 // Default 0, admin akan input ini di frontend
                ];
            }
        }

        return response()->json(['data' => $items]);
    }
}
