<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Stok;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreBarangKeluarRequest;
use App\Http\Resources\TransaksiResource;

class BarangKeluarController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with(['pelanggan', 'details.produk'])
            ->orderBy('created_at', 'desc')
            ->get();

        return TransaksiResource::collection($transaksis);
    }

    public function store(StoreBarangKeluarRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {


            $lastTrx = Transaksi::orderBy('id', 'desc')->first();

            if (!$lastTrx) {
                $trxId = 'TRX-000001';
            } else {
                $lastNumber = (int) substr($lastTrx->id, 4);

                $nextNumber = $lastNumber + 1;

                $trxId = 'TRX-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            }

            $grandTotalHarga = 0;
            $grandTotalItem = 0;

            foreach ($data['items'] as $item) {
                $subtotal = $item['jumlah'] * $item['harga'];
                $grandTotalHarga += $subtotal;
                $grandTotalItem += $item['jumlah'];
            }

            $transaksi = Transaksi::create([
                'id' => $trxId,
                'tanggalTransaksi' => $data['tanggal'],
                'idPelanggan' => $data['idPelanggan'],
                'totalTransaksi' => $grandTotalHarga,
                'totalItem' => $grandTotalItem
            ]);

            foreach ($data['items'] as $item) {
                $stokDb = Stok::where('idProduk', $item['idProduk'])
                    ->where('idSize', $item['idSize'])
                    ->first();

                if (!$stokDb || $stokDb->stok < $item['jumlah']) {
                    throw new \Exception("Stok kurang!");
                }

                $stokDb->decrement('stok', $item['jumlah']);

                DetailTransaksi::create([
                    'idTransaksi' => $trxId,
                    'idProduk' => $item['idProduk'],
                    'idSize' => $item['idSize'],
                    'jumlah' => $item['jumlah'],
                    'hargaProduk' => $item['harga']
                ]);
            }

            return new TransaksiResource($transaksi->load('details.produk', 'details.size'));
        });
    }
}
