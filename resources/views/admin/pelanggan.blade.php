@extends('layouts.admin')

@section('title', 'Data Pelanggan - Jelena Sports')
@section('header-title', 'Manajemen Pelanggan')

@section('content')

    <div style="margin-bottom: 20px;">
        <button class="btn btn-primary" onclick="openModal()">+ Tambah Pelanggan</button>
    </div>

    <div class="table-container">
        <table id="pelangganTable">
            <thead>
            <tr>
                <th style="width: 15%;">Kode (ID)</th>
                <th>Nama Pelanggan</th>
                <th>Kontak</th>
                <th>Alamat</th>
                <th style="width: 150px;">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="5" style="text-align:center;">Sedang memuat data...</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div id="pelangganModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Tambah Pelanggan</h2>

            <form id="pelangganForm">
                <input type="hidden" id="isEditMode" value="false">

                <div class="form-group">
                    <label>Kode Pelanggan / ID <span style="color:red">*</span></label>
                    <input type="text" id="id_pelanggan" required placeholder="Contoh: PLG-001">
                    <small style="color:gray; display:none;" id="idHelp">Kode tidak bisa diubah saat edit.</small>
                </div>

                <div class="form-group">
                    <label>Nama Pelanggan <span style="color:red">*</span></label>
                    <input type="text" id="nama" required placeholder="Nama Toko / PT">
                </div>

                <div class="form-group">
                    <label>Kontak (HP/Telp)</label>
                    <input type="text" id="kontak" placeholder="08xxxx">
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea id="alamat" rows="3" placeholder="Alamat lengkap..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%">Simpan Data</button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const token = localStorage.getItem('api_token');

        const modal = document.getElementById('pelangganModal');
        const form = document.getElementById('pelangganForm');
        const tableBody = document.querySelector('#pelangganTable tbody');
        const idInput = document.getElementById('id_pelanggan');

        async function loadPelanggans() {
            try {
                const res = await fetch('/api/pelanggans', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const json = await res.json();

                tableBody.innerHTML = '';

                if(json.data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Belum ada data pelanggan.</td></tr>';
                    return;
                }

                json.data.forEach(item => {
                    tableBody.innerHTML += `
                    <tr>
                        <td style="font-weight:bold; color:#2563eb;">${item.id}</td>
                        <td>${item.nama}</td>
                        <td>${item.kontak || '-'}</td>
                        <td>${item.alamat || '-'}</td>
                        <td>
                            <button class="btn btn-warning" onclick="editPelanggan('${item.id}')">Edit</button>
                            <button class="btn btn-danger" onclick="deletePelanggan('${item.id}')">Hapus</button>
                        </td>
                    </tr>
                `;
                });
            } catch (err) {
                console.error(err);
                tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:red;">Gagal mengambil data.</td></tr>`;
            }
        }

        loadPelanggans();

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const isEdit = document.getElementById('isEditMode').value === "true";
            const idValue = idInput.value;

            const url = isEdit ? `/api/pelanggans/${idValue}` : '/api/pelanggans';
            const method = isEdit ? 'PUT' : 'POST';

            const payload = {
                id: idValue,
                nama: document.getElementById('nama').value,
                kontak: document.getElementById('kontak').value,
                alamat: document.getElementById('alamat').value,
            };

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

                const responseJson = await res.json();

                if(res.ok) {
                    alert(responseJson.message || 'Berhasil disimpan!');
                    closeModal();
                    loadPelanggans();
                } else {
                    alert('Gagal: ' + (responseJson.message || JSON.stringify(responseJson.errors)));
                }
            } catch (err) {
                alert('Terjadi kesalahan sistem');
            }
        });

        async function editPelanggan(id) {
            try {
                const res = await fetch(`/api/pelanggans/${id}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const json = await res.json();

                if(res.ok) {
                    const data = json.data;

                    document.getElementById('isEditMode').value = "true";

                    idInput.value = data.id;
                    idInput.readOnly = true;
                    idInput.style.backgroundColor = "#e5e7eb";
                    document.getElementById('idHelp').style.display = 'block';

                    document.getElementById('nama').value = data.nama;
                    document.getElementById('kontak').value = data.kontak || '';
                    document.getElementById('alamat').value = data.alamat || '';

                    document.getElementById('modalTitle').innerText = 'Edit Pelanggan';
                    modal.style.display = 'flex';
                }
            } catch (err) {
                alert('Gagal mengambil detail data');
            }
        }

        async function deletePelanggan(id) {
            if(!confirm(`Yakin hapus pelanggan dengan Kode: ${id}?`)) return;

            try {
                const res = await fetch(`/api/pelanggans/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token }
                });

                if(res.ok) {
                    loadPelanggans();
                } else {
                    alert('Gagal menghapus data');
                }
            } catch (err) {
                alert('Terjadi kesalahan saat menghapus');
            }
        }

        function openModal() {
            form.reset();
            document.getElementById('isEditMode').value = "false";

            idInput.readOnly = false;
            idInput.style.backgroundColor = "white";
            document.getElementById('idHelp').style.display = 'none';

            document.getElementById('modalTitle').innerText = 'Tambah Pelanggan';
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) closeModal();
        }
    </script>
@endpush
