@extends('layouts.admin')

@section('title', 'Stok Barang')
@section('header-title', 'Matrix Stok Barang')

@section('content')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h3 style="margin:0; font-weight:700; color:#1e293b;">Input Stok</h3>
            <p style="margin:5px 0 0 0; color:#64748b; font-size:0.9rem;">Kelola jumlah stok per ukuran/varian.</p>
        </div>
        <button class="btn btn-primary" onclick="loadMatrix()">
            🔄 Refresh Data
        </button>
    </div>

    <div id="matrixContainer"></div>

@endsection

@push('scripts')
    <script>
        const token = localStorage.getItem('api_token');

        async function loadMatrix() {
            const container = document.getElementById('matrixContainer');

            container.innerHTML = `
            <div style="text-align:center; padding:50px;">
                <div style="font-size:1.2rem; color:#64748b;">Sedang memuat data stok...</div>
            </div>`;

            try {
                const res = await fetch('/api/stoks', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });

                if(!res.ok) throw new Error("Gagal mengambil data");

                const dataGroups = await res.json();

                container.innerHTML = '';

                if (dataGroups.length === 0) {
                    container.innerHTML = `
                    <div style="text-align:center; padding:40px; background:white; border-radius:8px; border:1px dashed #cbd5e1;">
                        <h4 style="color:#64748b;">Data Kosong</h4>
                        <p>Belum ada produk atau kategori yang diatur.</p>
                    </div>`;
                    return;
                }

                dataGroups.forEach(group => {

                    let headerCols = '';
                    group.sizes.forEach(size => {
                        headerCols += `
                        <th style="padding:10px 5px; text-align:center; min-width:45px; background:#f8fafc; font-size:0.85rem; border-bottom:2px solid #e2e8f0; color:#475569;">
                            ${size.tipe}
                        </th>
                    `;
                    });

                    let bodyRows = '';
                    group.produks.forEach(prod => {

                        let inputCols = '';
                        group.sizes.forEach(size => {
                            const stokData = prod.stoks.find(s => s.idSize == size.id);
                            const val = stokData ? stokData.stok : ''; // Jika 0 atau null, biarkan kosong

                            inputCols += `
                            <td style="padding:0; border:1px solid #f1f5f9; height:40px;">
                                <input type="number"
                                    value="${val}"
                                    placeholder="0"
                                    title="${prod.nama} - Ukuran ${size.tipe}"
                                    style="width:100%; height:100%; border:none; text-align:center; outline:none; font-size:0.9rem; background:transparent;"
                                    onfocus="this.style.background='#eff6ff'; this.parentNode.style.border='1px solid #3b82f6';"
                                    onblur="this.style.background='transparent'; this.parentNode.style.border='1px solid #f1f5f9';"
                                    onchange="updateStok(this, '${prod.id}', '${size.id}')"
                                >
                            </td>
                        `;
                        });

                        const displayName = prod.nama_lengkap || prod.nama;
                        bodyRows += `
                        <tr style="transition:background 0.2s;" onmouseover="this.style.background='#fcfcfc'" onmouseout="this.style.background='white'">
                            <td style="padding:10px 15px; font-weight:500; border-right:2px solid #f1f5f9; color:#334155; white-space:nowrap; background:white; position:sticky; left:0; z-index:10;">
                                ${displayName}
                                <div style="font-size:0.75rem; color:#94a3b8;">${prod.warna || ''}</div>
                            </td>
                            ${inputCols}
                        </tr>
                    `;
                    });

                    const cardHtml = `
                    <div class="card" style="background:white; border-radius:10px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom:40px; overflow:hidden; border:1px solid #e2e8f0;">

                        <div style="background:#fff; padding:15px 20px; border-bottom:1px solid #e2e8f0; border-left:5px solid #3b82f6;">
                            <h4 style="margin:0; font-weight:700; color:#0f172a;">${group.kategori_nama}</h4>
                        </div>

                        <div style="overflow-x:auto;">
                            <table style="width:100%; border-collapse:collapse; min-width:800px;">
                                <thead>
                                    <tr>
                                        <th style="padding:15px; width:280px; text-align:left; background:#f8fafc; border-bottom:2px solid #e2e8f0; color:#475569; position:sticky; left:0; z-index:20;">
                                            Nama Produk
                                        </th>
                                        ${headerCols}
                                    </tr>
                                </thead>
                                <tbody>
                                    ${bodyRows}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                    container.insertAdjacentHTML('beforeend', cardHtml);
                });

            } catch (e) {
                console.error(e);
                container.innerHTML = `
                <div style="padding:20px; background:#fee2e2; color:#991b1b; border-radius:8px; text-align:center;">
                    <strong>Terjadi Kesalahan!</strong><br>Gagal memuat matrix stok.
                </div>`;
            }
        }

        async function updateStok(inputEl, idProduk, idSize) {
            let val = inputEl.value;
            if(val === '') val = 0;

            const originalColor = inputEl.style.color;
            inputEl.style.color = 'orange';

            try {
                const res = await fetch('/api/stoks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        idProduk: idProduk,
                        idSize: idSize,
                        jumlah: parseInt(val)
                    })
                });

                const json = await res.json();

                if(res.ok) {
                    inputEl.style.color = '#16a34a';
                    setTimeout(() => inputEl.style.color = '#334155', 1000);
                } else {
                    console.error("Server Error:", json);
                    alert('Gagal: ' + (json.message || JSON.stringify(json)));
                    inputEl.style.color = 'red';
                }
            } catch(e) {
                console.error(e);
                inputEl.style.color = 'red';
                alert('Terjadi kesalahan koneksi atau server error 500.');
            }
        }
        loadMatrix();
    </script>
@endpush
