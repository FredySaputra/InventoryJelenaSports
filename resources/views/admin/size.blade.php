@extends('layouts.admin')

@section('title', 'Manajemen Size')
@section('header-title', 'Master Data Size')

@section('content')

    <div style="margin-bottom: 30px;">
        <h3 style="margin: 0; font-weight: 700; color: #1e293b;">Daftar Ukuran (Size)</h3>
        <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.9rem;">Data dikelompokkan berdasarkan kategori produk.</p>
    </div>

    <div id="mainContainer">
        <div style="text-align:center; padding:50px; color:gray;">Memuat data...</div>
    </div>

    <div id="modalSize" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; padding: 25px; border: 1px solid #888; width: 100%; max-width: 500px; border-radius: 8px;">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 id="modalTitle" style="margin: 0;">Tambah Size</h3>
                <span onclick="closeModal()" style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            </div>

            <form id="formSize">
                <div id="errorAlert" style="display:none; background:#fee2e2; color:#991b1b; padding:10px; border-radius:5px; margin-bottom:15px; font-size:0.9rem;"></div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">ID Size <span style="color: red">*</span></label>
                    <input type="text" id="id_size" class="form-control" placeholder="Contoh: BJU-001" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px;">
                    <small style="color:gray; font-size:0.8rem;">Gunakan prefix kategori, misal: <b>BJU-</b>xxx</small>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Tipe (Label) <span style="color: red">*</span></label>
                    <input type="text" id="tipe_size" class="form-control" placeholder="Contoh: XL, PT" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px;">
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Panjang (cm)</label>
                        <input type="number" step="0.01" id="panjang_size" class="form-control" placeholder="0" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Lebar (cm)</label>
                        <input type="number" step="0.01" id="lebar_size" class="form-control" placeholder="0" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px;">
                    </div>
                </div>

                <div style="text-align: right;">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary" style="margin-right: 10px;">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const token = localStorage.getItem('api_token');
        let isEditMode = false;
        let currentId = null;

        async function loadData() {
            const container = document.getElementById('mainContainer');

            try {
                const res = await fetch('/api/sizes', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });

                if (!res.ok) throw new Error('Gagal memuat data');

                const json = await res.json();
                const groups = json.data;

                container.innerHTML = '';

                if (groups.length === 0) {
                    container.innerHTML = `<div style="text-align:center; padding:40px; background:white; border-radius:8px;">Belum ada data.</div>`;
                    return;
                }

                groups.forEach(group => {
                    let rows = '';

                    if(group.sizes.length > 0) {
                        group.sizes.forEach(item => {
                            const p = item.panjang ? item.panjang : '-';
                            const l = item.lebar ? item.lebar : '-';

                            rows += `
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 15px;">
                                    <span style="background:#eff6ff; color:#2563eb; padding:4px 8px; border-radius:4px; font-weight:600; font-size:0.9rem;">
                                        ${item.id}
                                    </span>
                                </td>
                                <td style="padding: 12px 15px; font-weight:500;">${item.tipe}</td>
                                <td style="padding: 12px 15px; text-align:center;">${p}</td>
                                <td style="padding: 12px 15px; text-align:center;">${l}</td>
                                <td style="padding: 12px 15px; text-align: right;">
                                    <button class="btn btn-warning btn-sm" onclick="openModal('edit', '${item.id}', '${item.tipe}', '${item.panjang||''}', '${item.lebar||''}')">Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteData('${item.id}')" style="margin-left:5px;">Hapus</button>
                                </td>
                            </tr>
                        `;
                        });
                    } else {
                        rows = `<tr><td colspan="5" style="text-align:center; padding:20px; font-style:italic; color:#94a3b8;">Belum ada size di kategori ini.</td></tr>`;
                    }

                    const cardHtml = `
                    <div class="card" style="background: white; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; overflow:hidden; border:1px solid #e2e8f0;">
                        <div style="background: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                            <h4 style="margin:0; color:#0f172a; font-weight:700;">${group.kategori_nama}</h4>
                            <button class="btn btn-primary btn-sm" onclick="openModal('add', '', '', '', '', '${group.prefix}')">
                                + Tambah Size
                            </button>
                        </div>

                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead style="background-color: #fff; border-bottom: 2px solid #f1f5f9;">
                                    <tr>
                                        <th style="padding: 12px 15px; text-align: left; width: 20%; color: #64748b; font-size:0.85rem;">ID Size</th>
                                        <th style="padding: 12px 15px; text-align: left; color: #64748b; font-size:0.85rem;">Tipe</th>
                                        <th style="padding: 12px 15px; text-align: center; width: 15%; color: #64748b; font-size:0.85rem;">Panjang</th>
                                        <th style="padding: 12px 15px; text-align: center; width: 15%; color: #64748b; font-size:0.85rem;">Lebar</th>
                                        <th style="padding: 12px 15px; text-align: right; width: 15%; color: #64748b; font-size:0.85rem;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                    container.insertAdjacentHTML('beforeend', cardHtml);
                });

            } catch (e) {
                console.error(e);
                container.innerHTML = `<div style="text-align:center; color:red; padding:20px;">Gagal memuat data: ${e.message}</div>`;
            }
        }

        function openModal(mode, id = '', tipe = '', panjang = '', lebar = '', prefix = '') {
            const modal = document.getElementById('modalSize');
            const title = document.getElementById('modalTitle');
            const inputId = document.getElementById('id_size');
            const inputTipe = document.getElementById('tipe_size');
            const inputPanjang = document.getElementById('panjang_size');
            const inputLebar = document.getElementById('lebar_size');
            const errorAlert = document.getElementById('errorAlert');

            errorAlert.style.display = 'none';
            modal.style.display = 'block';

            if (mode === 'edit') {
                isEditMode = true;
                currentId = id;
                title.innerText = 'Edit Size';

                inputId.value = id;
                inputId.disabled = true;
                inputId.style.backgroundColor = '#f1f5f9';

                inputTipe.value = tipe;
                inputPanjang.value = panjang;
                inputLebar.value = lebar;
            } else {
                isEditMode = false;
                currentId = null;
                title.innerText = 'Tambah Size Baru';

                inputId.value = prefix ? prefix + '-' : '';
                inputId.disabled = false;
                inputId.style.backgroundColor = 'white';
                inputId.focus();

                inputTipe.value = '';
                inputPanjang.value = '';
                inputLebar.value = '';
            }
        }

        function closeModal() {
            document.getElementById('modalSize').style.display = 'none';
        }

        document.getElementById('formSize').addEventListener('submit', async (e) => {
            e.preventDefault();

            const idVal = document.getElementById('id_size').value;
            const tipeVal = document.getElementById('tipe_size').value;
            const panjangVal = document.getElementById('panjang_size').value;
            const lebarVal = document.getElementById('lebar_size').value;

            const btn = e.target.querySelector('button[type="submit"]');
            const errorAlert = document.getElementById('errorAlert');

            let url = '/api/sizes';
            let method = 'POST';

            let payload = {
                tipe: tipeVal,
                panjang: panjangVal === '' ? null : panjangVal,
                lebar: lebarVal === '' ? null : lebarVal
            };

            if (isEditMode) {
                url += '/' + currentId;
                method = 'PUT';
            } else {
                payload.id = idVal;
            }

            btn.innerText = 'Menyimpan...';
            btn.disabled = true;

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const json = await res.json();

                if (!res.ok) {
                    if (res.status === 400 || res.status === 422) {
                        let errorMsg = 'Validasi Gagal:<br>';
                        if (json.errors) {
                            if (Array.isArray(json.errors)) {
                                json.errors.forEach(errObj => {
                                    for (const [key, msgs] of Object.entries(errObj)) {
                                        errorMsg += `- ${msgs}<br>`;
                                    }
                                });
                            } else {
                                for (const [key, msgs] of Object.entries(json.errors)) {
                                    errorMsg += `- ${msgs}<br>`;
                                }
                            }
                        } else {
                            errorMsg += json.message;
                        }
                        errorAlert.innerHTML = errorMsg;
                        errorAlert.style.display = 'block';
                    } else {
                        alert('Error: ' + (json.message || res.status));
                    }
                } else {
                    closeModal();
                    loadData();
                }
            } catch (e) {
                alert('Gagal terhubung ke server.');
            } finally {
                btn.innerText = 'Simpan';
                btn.disabled = false;
            }
        });

        async function deleteData(id) {
            if (!confirm('Hapus Size ' + id + '?')) return;
            try {
                const res = await fetch('/api/sizes/' + id, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                if (res.ok) loadData();
                else alert('Gagal hapus.');
            } catch (e) {
                alert('Kesalahan koneksi.');
            }
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('modalSize')) closeModal();
        }

        loadData();
    </script>
@endpush
