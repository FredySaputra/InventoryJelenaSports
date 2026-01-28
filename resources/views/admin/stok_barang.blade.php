@extends('layouts.admin')

@section('title', 'Stok Barang')
@section('header-title', 'Matrix Stok Barang')

@section('content')

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold text-dark m-0">Input Stok</h3>
            <p class="text-muted small">Kelola jumlah stok dan batas minimal stok per ukuran.</p>
        </div>
        <div>
            <button class="btn btn-danger btn-sm me-2" onclick="downloadPdf()">
                <i class="fas fa-file-pdf me-1"></i> Cetak Laporan PDF
            </button>

            <button class="btn btn-primary btn-sm" onclick="loadData()">
                <i class="fas fa-sync-alt me-1"></i> Refresh Data
            </button>
        </div>
    </div>

    <div id="mainContainer">
        <div class="text-center py-5 text-muted">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <p>Memuat matrix stok...</p>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const token = localStorage.getItem('api_token');

        document.addEventListener('DOMContentLoaded', () => {
            loadData();
        });

        async function loadData() {
            const container = document.getElementById('mainContainer');
            if (!container) return;

            // Jangan hapus loading jika ini refresh diam-diam, tapi kalau load awal tampilkan spinner
            if(!container.innerHTML.includes('table')) {
                container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Sedang memuat data...</p></div>';
            }

            try {
                const res = await fetch('/api/stoks', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });

                const json = await res.json();

                if (!res.ok) throw new Error(json.message || 'Gagal mengambil data');

                const groupedData = json.data || [];
                
                // Simpan posisi scroll sebelum wipe content (opsional, biar nyaman)
                const scrollPos = window.scrollY;
                
                container.innerHTML = '';

                // Jika data kosong
                if (groupedData.length === 0) {
                    container.innerHTML = `
                    <div class="alert alert-warning text-center p-5">
                        <h4>Data Kosong</h4>
                        <p>Pastikan Anda sudah membuat Kategori, Size, dan Produk.</p>
                    </div>`;
                    return;
                }

                // Loop per Kategori
                groupedData.forEach(group => {
                    const sizes = group.sizes || [];
                    const produks = group.produks || [];

                    if (sizes.length === 0 && produks.length === 0) return;

                    // A. Header Kolom (Size)
                    let thSizes = '';
                    if (sizes.length > 0) {
                        sizes.forEach(size => {
                            thSizes += `<th class="text-center bg-light" style="width: 90px;">${size.tipe}</th>`;
                        });
                    } else {
                        thSizes = `<th class="text-center bg-light text-danger">Belum ada Size</th>`;
                    }

                    // B. Baris (Produk)
                    let trProduks = '';
                    if (produks.length > 0) {
                        produks.forEach(prod => {
                            let tdInputs = '';

                            if (sizes.length > 0) {
                                sizes.forEach(size => {
                                    const foundStok = (prod.stoks || []).find(s => {
                                        return (s.idSize == size.id) || (s.id_size == size.id);
                                    });

                                    const jumlah = foundStok ? foundStok.stok : 0;
                                    // Ambil Min Stok (Default 50 jika null/undefined)
                                    const minStok = foundStok ? (foundStok.min_stok !== undefined && foundStok.min_stok !== null ? foundStok.min_stok : 50) : 50;

                                    // Logika Warna: Merah jika di bawah minimal
                                    let styleClass = "bg-white text-dark border";
                                    if (jumlah < minStok) {
                                        styleClass = "bg-danger-subtle text-danger border-danger fw-bold";
                                    }

                                    tdInputs += `
                                    <td class="p-1 align-top">
                                        <input type="number" min="0" 
                                            class="form-control form-control-sm text-center input-stok mb-1 ${styleClass}"
                                            style="font-size: 1rem; height: 35px;"
                                            value="${jumlah}"
                                            data-prod="${prod.id}"
                                            data-size="${size.id}"
                                            onchange="updateStok(this, 'qty')"
                                            title="Stok Saat Ini">
                                            
                                        <div class="d-flex align-items-center justify-content-center bg-light rounded border px-1" style="height: 24px;">
                                            <span class="text-muted me-1" style="font-size: 0.65rem;">Min:</span>
                                            <input type="number" min="0" 
                                                class="form-control form-control-sm p-0 text-center text-secondary border-0 bg-transparent fw-bold"
                                                style="font-size: 0.75rem; width: 40px; height: 20px;"
                                                value="${minStok}"
                                                data-prod="${prod.id}"
                                                data-size="${size.id}"
                                                onchange="updateStok(this, 'min')"
                                                title="Batas Minimal Stok">
                                        </div>
                                    </td>`;
                                });
                            } else {
                                tdInputs = `<td class="text-center text-muted">-</td>`;
                            }

                            trProduks += `
                            <tr>
                                <td class="fw-bold ps-3 text-dark align-middle">
                                    ${prod.nama_lengkap}
                                    <div class="text-muted small" style="font-size:0.7rem;">ID: ${prod.id}</div>
                                </td>
                                ${tdInputs}
                            </tr>`;
                        });
                    } else {
                        trProduks = `<tr><td colspan="${sizes.length + 1}" class="text-center py-4 text-muted fst-italic">Belum ada produk di kategori ini.</td></tr>`;
                    }

                    // C. Render Card HTML
                    const cardHtml = `
                <div class="card border-0 shadow-sm mb-5 rounded-3 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="m-0 fw-bold text-primary">${group.kategori_nama}</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-3 bg-white" style="min-width: 250px;">Nama Produk</th>
                                    ${thSizes}
                                </tr>
                            </thead>
                            <tbody>${trProduks}</tbody>
                        </table>
                    </div>
                </div>`;

                    container.insertAdjacentHTML('beforeend', cardHtml);
                });
                
                // Kembalikan posisi scroll (agar tidak lompat ke atas saat update)
                window.scrollTo(0, scrollPos);

            } catch (e) {
                console.error(e);
                container.innerHTML = `
                <div class="alert alert-danger text-center m-4">
                    <h5 class="fw-bold">Terjadi Kesalahan</h5>
                    <p>${e.message}</p>
                </div>`;
            }
        }

        async function updateStok(el, type) {
            const idProduk = el.getAttribute('data-prod');
            const idSize = el.getAttribute('data-size');
            const val = el.value;

            // Visual Loading (Hanya untuk input stok utama biar tidak mengganggu UX)
            if(type === 'qty') el.classList.add('border-warning');

            // Siapkan Payload
            let payload = { idProduk, idSize };
            
            if (type === 'qty') {
                payload.jumlah = val;
            } else if (type === 'min') {
                payload.min_stok = val;
            }

            try {
                const res = await fetch('/api/stoks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if(res.ok) {
                    if(type === 'qty') {
                        el.classList.remove('border-warning');
                        el.classList.add('border-success'); // Sukses hijau
                        setTimeout(() => {
                             el.classList.remove('border-success');
                             // Refresh data agar logika warna Merah/Putih terupdate
                             loadData(); 
                        }, 500);
                    } else {
                        // Jika update min stok, langsung refresh agar kita lihat apakah stok utama jadi merah
                        loadData(); 
                    }
                } else {
                    throw new Error('Gagal simpan');
                }
            } catch(e) {
                if(type === 'qty') {
                    el.classList.remove('border-warning');
                    el.classList.add('border-danger');
                }
                alert('Gagal update. Cek koneksi.');
            }
        }

        async function downloadPdf() {
            const btn = document.querySelector('button[onclick="downloadPdf()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            btn.disabled = true;

            try {
                const res = await fetch('/api/stoks/export-pdf', {
                    method: 'GET',
                    headers: { 'Authorization': 'Bearer ' + token }
                });

                if (!res.ok) throw new Error("Gagal download PDF");

                const blob = await res.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = "Laporan_Stok_Jenela_Sports.pdf";
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

            } catch (error) {
                alert("Gagal mencetak PDF. Silakan coba lagi.");
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    </script>

    <style>
        .input-stok:focus {
            background-color: #f0f9ff;
            border-color: #0d6efd;
            box-shadow: none;
        }
        .input-stok::-webkit-outer-spin-button,
        .input-stok::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endpush