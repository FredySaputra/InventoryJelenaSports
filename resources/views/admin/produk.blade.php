@extends('layouts.admin')

@section('title', 'Data Produk')
@section('header-title', 'Manajemen Produk')

@section('content')

    <div id="mainContainer">
        <div style="text-align:center; padding:50px; color:gray;">
            <p>Memuat data...</p>
        </div>
    </div>

    <div id="modalProduk" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 id="modalTitle">Tambah Produk</h3>
            <p style="color:gray; margin-top:-10px; font-size:0.9rem;">
                Kategori: <strong id="labelKategori" style="color:#2563eb;">-</strong>
            </p>

            <form id="formProduk">
                <input type="hidden" id="prod_kategori">

                <div class="form-group">
                    <label>Kode Produk (ID) <span style="color:red">*</span></label>
                    <input type="text" id="prod_id" required maxlength="100" placeholder="Contoh: KAOS-01">
                </div>

                <div class="form-group">
                    <label>Nama Produk <span style="color:red">*</span></label>
                    <input type="text" id="prod_nama" required placeholder="Contoh: Baju Karate">
                </div>

                <div class="form-group">
                    <label>Warna <small>(Opsional)</small></label>
                    <input type="text" id="prod_warna" placeholder="Contoh: Merah Hitam">
                </div>

                <div class="form-group">
                    <label>Bahan <span style="color:red">*</span></label>
                    <select id="prod_bahan" required style="width: 100%; padding: 8px; margin-top: 5px; background-color: #f8fafc;">
                        <option value="">-- Pilih Bahan --</option>
                    </select>
                    <small style="color:gray; font-size:0.8rem;">
                        *Pilihan bahan muncul sesuai kategori yang dipilih.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; margin-top: 15px;">Simpan</button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const token = localStorage.getItem('api_token');

        async function loadData() {
            const container = document.getElementById('mainContainer');

            try {
                const res = await fetch('/api/produks', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });

                if (!res.ok) throw new Error(`Server Error: ${res.status}`);

                const json = await res.json();
                const data = json.data ? json.data : json;

                container.innerHTML = '';

                if (!data || data.length === 0) {
                    container.innerHTML = `<div style="text-align:center; padding:40px; color:gray;">Belum ada data.</div>`;
                    return;
                }

                // LOOP KATEGORI
                data.forEach(cat => {
                    let tableContent = '';

                    if (cat.produks && cat.produks.length > 0) {
                        // Header Tabel (Tanpa Warna)
                        tableContent += `
                        <div style="overflow-x:auto;">
                        <table class="table table-hover" style="width:100%; margin-bottom:0; font-size:0.95rem;">
                            <thead style="background-color:#f8fafc; border-bottom:2px solid #e2e8f0; color:#64748b; font-size:0.85rem;">
                                <tr>
                                    <th style="padding:12px 20px; width:25%;">Kode (ID)</th>
                                    <th style="padding:12px 20px;">Nama Produk</th>
                                    <th style="padding:12px 20px; width:15%; text-align:right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                        // Isi Tabel
                        cat.produks.forEach(prod => {
                            const displayName = prod.nama_lengkap || prod.nama;

                            tableContent += `
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:12px 20px; vertical-align:middle;">
                                    <span style="background:#eff6ff; color:#2563eb; font-weight:600; font-size:0.8rem; padding:4px 8px; border-radius:4px; border:1px solid #dbeafe; white-space:nowrap;">
                                        ${prod.id}
                                    </span>
                                </td>
                                <td style="padding:12px 20px; vertical-align:middle; font-weight:500; color:#334155;">
                                    ${displayName}
                                </td>
                                <td style="padding:12px 20px; vertical-align:middle; text-align:right;">
                                    <button class="btn btn-danger btn-sm" onclick="deleteProduk('${prod.id}')" style="padding:4px 10px; font-size:0.8rem;">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        `;
                        });

                        tableContent += `</tbody></table></div>`;
                    } else {
                        tableContent = `<div style="padding:20px; text-align:center; color:#94a3b8; font-style:italic;">Belum ada produk.</div>`;
                    }

                    // Render Card Kategori
                    const sectionHtml = `
                    <div class="card" style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 30px; overflow:hidden; border:1px solid #e2e8f0;">
                        <div style="background: #f1f5f9; padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                            <h3 style="margin:0; font-size:1.1rem; color:#0f172a; font-weight:700;">${cat.nama}</h3>
                            <button class="btn btn-primary btn-sm" onclick="openAddModal('${cat.id}', '${cat.nama}')">
                                + Tambah Produk
                            </button>
                        </div>
                        <div style="background:white;">
                            ${tableContent}
                        </div>
                    </div>
                `;

                    container.insertAdjacentHTML('beforeend', sectionHtml);
                });

            } catch (e) {
                console.error(e);
                container.innerHTML = `<div style="text-align:center; color:red; padding:20px;">Gagal memuat data.</div>`;
            }
        }

        // --- 2. BUKA MODAL & LOAD BAHAN ---
        async function openAddModal(kategoriId, kategoriNama) {
            document.getElementById('formProduk').reset();

            // Set Judul & Input Hidden
            document.getElementById('labelKategori').innerText = kategoriNama;
            document.getElementById('prod_kategori').value = kategoriId;
            document.getElementById('modalProduk').style.display = 'flex';

            // Reset Dropdown Bahan
            const selectBahan = document.getElementById('prod_bahan');
            selectBahan.innerHTML = '<option value="">Sedang memuat bahan...</option>';
            selectBahan.disabled = true;

            try {
                // Panggil API Bahan Filtered
                // Pastikan Route API ini ada di routes/api.php!
                const res = await fetch(`/api/bahans/kategori/${kategoriId}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });

                if(!res.ok) throw new Error('Gagal load bahan');

                const json = await res.json();
                const data = json.data ? json.data : json;

                selectBahan.innerHTML = '<option value="">-- Pilih Bahan --</option>';

                if(data.length > 0) {
                    data.forEach(bhn => {
                        selectBahan.innerHTML += `<option value="${bhn.id}">${bhn.nama}</option>`;
                    });
                    selectBahan.disabled = false;
                } else {
                    selectBahan.innerHTML = '<option value="">Tidak ada data bahan untuk kategori ini</option>';
                }
            } catch (e) {
                console.error(e);
                selectBahan.innerHTML = '<option value="">Gagal memuat bahan (Cek API)</option>';
            }
        }

        // --- 3. SIMPAN PRODUK KE SERVER ---
        document.getElementById('formProduk').addEventListener('submit', async (e) => {
            e.preventDefault();

            const payload = {
                id: document.getElementById('prod_id').value,
                nama: document.getElementById('prod_nama').value,
                warna: document.getElementById('prod_warna').value,
                idKategori: document.getElementById('prod_kategori').value, // Dari hidden input
                idBahan: document.getElementById('prod_bahan').value
            };

            const btn = e.target.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            btn.innerText = 'Menyimpan...';
            btn.disabled = true;

            try {
                const res = await fetch('/api/produks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const json = await res.json();

                if(res.ok) {
                    closeModal();
                    loadData(); // Refresh tampilan otomatis
                    alert("Berhasil menyimpan produk!");
                } else {
                    // Tampilkan pesan error dari backend jika ada
                    alert('Gagal: ' + (json.message || JSON.stringify(json)));
                }
            } catch (e) {
                console.error(e);
                alert("Terjadi kesalahan koneksi.");
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        });

        // --- 4. HAPUS PRODUK ---
        async function deleteProduk(id) {
            if(!confirm('Yakin ingin menghapus produk ini?')) return;

            try {
                const res = await fetch(`/api/produks/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token }
                });

                if(res.ok) {
                    loadData();
                } else {
                    alert('Gagal menghapus produk.');
                }
            } catch(e) {
                alert('Terjadi kesalahan sistem.');
            }
        }

        // Utils Modal
        function closeModal() { document.getElementById('modalProduk').style.display = 'none'; }
        window.onclick = function(event) { if (event.target == document.getElementById('modalProduk')) closeModal(); }

        // Jalankan saat halaman dibuka
        loadData();
    </script>
@endpush
