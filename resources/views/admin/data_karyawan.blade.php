@extends('layouts.admin')

@section('title', 'Data Karyawan')
@section('header-title', 'Kelola Data Karyawan')

@push('styles')
    <style>
        /* Styling Konsisten dengan Barang Keluar */
        .input-modern { border-radius: 10px; padding: 10px; border: 1px solid #cbd5e1; }
        .input-modern:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

        .btn-modern-add {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; border: none;
            border-radius: 10px; padding: 10px 20px; font-weight: 600; letter-spacing: 0.5px;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-modern-add:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); color: white; }

        .btn-action-edit {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); /* Gradasi Orange */
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;  /* Padding lebih besar */
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
            display: inline-flex; align-items: center; gap: 5px; /* Agar ikon & teks rapi */
        }
        .btn-action-edit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(245, 158, 11, 0.4);
            color: white;
        }

        .btn-action-delete {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); /* Gradasi Merah */
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px; /* Padding lebih besar */
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
            display: inline-flex; align-items: center; gap: 5px;
        }
        .btn-action-delete:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(239, 68, 68, 0.4);
            color: white;
        }
        /* Modal Custom Style */
        .custom-modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
            display: none; justify-content: center; align-items: center;
            z-index: 9999; opacity: 0; transition: opacity 0.3s ease;
        }
        .custom-modal-box {
            background: white; width: 90%; max-width: 500px;
            border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
        }
        .custom-modal-header {
            background: #f1f5f9; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .custom-modal-body { padding: 25px; }

        .custom-modal-overlay.show { display: flex; opacity: 1; }
        .custom-modal-overlay.show .custom-modal-box { transform: scale(1); }
    </style>
@endpush

@section('content')

    <div class="card" style="padding: 20px; border-radius: 12px; border:none; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h4 style="margin:0; font-weight:800; color:#334155;">Daftar Karyawan</h4>
            <button class="btn-modern-add" onclick="bukaModalTambah()">
                <i class="fas fa-plus me-2"></i> Tambah Karyawan
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th class="ps-3">ID Karyawan</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>No. Telepon</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody id="tableKaryawanBody">
                <tr><td colspan="5" class="text-center py-4">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalKaryawanOverlay" class="custom-modal-overlay">
        <div class="custom-modal-box">
            <div class="custom-modal-header">
                <h5 id="modalTitle" style="margin:0; font-weight:700; color:#334155;">Tambah Karyawan</h5>
                <button onclick="tutupModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
            </div>
            <div class="custom-modal-body">
                <form id="formKaryawan" onsubmit="simpanData(event)">
                    <input type="hidden" id="karyawanId"> <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NAMA LENGKAP</label>
                        <input type="text" id="inputNama" class="form-control input-modern" required placeholder="Contoh: Budi Santoso">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NO. TELEPON</label>
                        <input type="text" id="inputTelp" class="form-control input-modern" required placeholder="0812...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">USERNAME</label>
                        <input type="text" id="inputUsername" class="form-control input-modern" required placeholder="Username untuk login">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">PASSWORD</label>
                        <input type="password" id="inputPassword" class="form-control input-modern" placeholder="Minimal 6 karakter">
                        <small id="passwordHelp" class="text-muted d-none" style="font-size: 0.8rem">* Kosongkan jika tidak ingin mengubah password</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius:10px;">SIMPAN DATA</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const token = localStorage.getItem('api_token');

        async function loadKaryawan() {
            const tbody = document.getElementById('tableKaryawanBody');
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">Sedang memuat data...</td></tr>';

            try {
                const res = await fetch('/api/karyawan', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if (res.status === 401) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Sesi habis. Silakan logout dan login ulang.</td></tr>';
                    return;
                }
                if (!res.ok) {
                    throw new Error(`Server Error: ${res.status} ${res.statusText}`);
                }

                const json = await res.json();

                const karyawanData = json.data ? json.data : json;

                tbody.innerHTML = '';

                if(!Array.isArray(karyawanData) || karyawanData.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Belum ada data karyawan.</td></tr>';
                    return;
                }

                karyawanData.forEach(user => {
                    tbody.innerHTML += `
                    <tr>
                        <td class="ps-3"><span class="badge bg-secondary">${user.id}</span></td>
                        <td class="fw-bold text-dark">${user.nama}</td>
                        <td>${user.username}</td>
                        <td>${user.noTelp}</td>
                        <td class="text-center">
                            <button class="btn-action-edit me-2" onclick="bukaModalEdit('${user.id}', '${user.nama}', '${user.username}', '${user.noTelp}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn-action-delete" onclick="hapusKaryawan('${user.id}')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                `;
                });

            } catch(error) {
                console.error("Error Detail:", error);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Gagal: ${error.message}</td></tr>`;
            }
        }

        const modalOverlay = document.getElementById('modalKaryawanOverlay');

        function bukaModalTambah() {
            document.getElementById('modalTitle').innerText = 'Tambah Karyawan';
            document.getElementById('formKaryawan').reset();
            document.getElementById('karyawanId').value = '';

            document.getElementById('inputPassword').required = true;
            document.getElementById('inputPassword').placeholder = "Minimal 6 karakter";
            document.getElementById('passwordHelp').classList.add('d-none');

            modalOverlay.classList.add('show');
        }

        function bukaModalEdit(id, nama, username, telp) {
            document.getElementById('modalTitle').innerText = 'Edit Karyawan';
            document.getElementById('karyawanId').value = id;
            document.getElementById('inputNama').value = nama;
            document.getElementById('inputUsername').value = username;
            document.getElementById('inputTelp').value = telp;

            document.getElementById('inputPassword').value = '';
            document.getElementById('inputPassword').required = false;
            document.getElementById('inputPassword').placeholder = "(Biarkan kosong jika tetap)";
            document.getElementById('passwordHelp').classList.remove('d-none');

            modalOverlay.classList.add('show');
        }

        function tutupModal() {
            modalOverlay.classList.remove('show');
        }

        modalOverlay.addEventListener('click', function(e) {
            if (e.target === this) tutupModal();
        });

        async function simpanData(e) {
            e.preventDefault();

            const id = document.getElementById('karyawanId').value;
            const isEdit = id ? true : false;

            const payload = {
                nama: document.getElementById('inputNama').value,
                noTelp: document.getElementById('inputTelp').value,
                username: document.getElementById('inputUsername').value,
                password: document.getElementById('inputPassword').value
            };

            const url = isEdit ? `/api/karyawan/${id}` : '/api/karyawan';
            const method = isEdit ? 'PUT' : 'POST';

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

                if (res.ok) {
                    alert(json.message);
                    tutupModal();
                    loadKaryawan();
                } else {
                    let msg = 'Gagal menyimpan.';
                    if(json.errors) {
                        msg += '\n' + JSON.stringify(json.errors);
                    } else if (json.message) {
                        msg = json.message;
                    }
                    alert(msg);
                }
            } catch (error) {
                alert('Terjadi kesalahan sistem.');
                console.error(error);
            }
        }

        async function hapusKaryawan(id) {
            if(!confirm('Apakah Anda yakin ingin menghapus karyawan ini? Data yang dihapus tidak bisa dikembalikan.')) return;

            try {
                const res = await fetch(`/api/karyawan/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if(res.ok) {
                    alert('Karyawan berhasil dihapus.');
                    loadKaryawan();
                } else {
                    alert('Gagal menghapus data.');
                }
            } catch (error) {
                console.error(error);
            }
        }

        loadKaryawan();
    </script>
@endpush
