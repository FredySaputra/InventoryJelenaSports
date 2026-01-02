@extends('layouts.admin')

@section('title', 'Dashboard - Jelena Sports')
@section('header-title', 'Dashboard Inventory')

@section('content')
    <div class="card-container">
        <div class="card">
            <h3>Total Item</h3>
            <p style="font-size: 2rem; font-weight: bold; margin: 10px 0;">0</p>
            <small>Jenis Barang Terdaftar</small>
        </div>

        <div class="card">
            <h3>Stok Masuk</h3>
            <p style="font-size: 2rem; font-weight: bold; margin: 10px 0; color: green;">0</p>
            <small>Bulan Ini</small>
        </div>

        <div class="card">
            <h3>Stok Keluar</h3>
            <p style="font-size: 2rem; font-weight: bold; margin: 10px 0; color: orange;">0</p>
            <small>Bulan Ini</small>
        </div>

        <div class="card">
            <h3>Stok Menipis</h3>
            <p style="font-size: 2rem; font-weight: bold; margin: 10px 0; color: red;">0</p>
            <small>Perlu Restock Segera</small>
        </div>
    </div>
@endsection
