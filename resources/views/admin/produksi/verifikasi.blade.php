@extends('layouts.admin')

@section('title', 'Verifikasi Hasil Produksi')

@section('content')
    {{-- 1. BAGIAN UTAMA: TABEL VERIFIKASI --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold m-0 text-primary">Verifikasi Setoran Karyawan</h5>
                <small class="text-muted">Validasi hasil kerja sebelum masuk stok.</small>
            </div>
            <div>
                <button class="btn btn-sm btn-warning me-2 fw-bold text-dark" onclick="openLeaderboard()">
                    <i class="fas fa-trophy me-1"></i> Leaderboard
                </button>
                
                <button class="btn btn-sm btn-outline-primary" onclick="loadVerifikasi()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Waktu Setor</th>
                        <th>Karyawan</th>
                        <th>Info Produk</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                    </thead>
                    <tbody id="tableData">
                        <tr><td colspan="5" class="text-center py-5">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 2. MODAL VERIFIKASI (TERIMA) --}}
    <div class="modal fade" id="modalTerima" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Verifikasi Terima</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Karyawan menyetor <b id="lbl_jml_setor">0</b> Pcs.</p>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Diterima (Lolos QC)</label>
                        <input type="number" id="input_diterima" class="form-control fw-bold text-success" min="0">
                        <small class="text-muted">Jika ada barang reject/rusak, kurangi jumlah ini.</small>
                    </div>
                    <input type="hidden" id="hidden_id_progres">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="submitTerima()">Simpan & Masukkan Stok</button>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. MODAL LEADERBOARD --}}
    <div class="modal fade" id="modalLeaderboard" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning bg-gradient text-dark">
                    <h5 class="modal-title fw-bold"><i class="fas fa-crown me-2"></i>Top Performa Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="bg-white p-3 border-bottom">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold">Dari Tanggal</label>
                                <input type="date" id="filterStartDate" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold">Sampai Tanggal</label>
                                <input type="date" id="filterEndDate" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4 d-flex gap-1">
                                <button class="btn btn-primary btn-sm w-100 fw-bold" onclick="fetchLeaderboard()">
                                    <i class="fas fa-filter"></i> Terapkan
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="resetFilter()" title="Reset ke Bulan Ini">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="px-3 py-2 bg-light border-bottom d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Periode Laporan:</span>
                        <span class="fw-bold text-dark badge bg-warning text-dark" id="lblPeriode">Memuat...</span>
                    </div>

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-striped mb-0">
                            <thead class="small text-muted sticky-top bg-light">
                                <tr>
                                    <th class="ps-4" width="15%">Peringkat</th>
                                    <th>Nama Karyawan</th>
                                    <th class="text-end pe-4">Total Produksi (Pcs)</th>
                                </tr>
                            </thead>
                            <tbody id="leaderboardData">
                                <tr><td colspan="3" class="text-center py-3">Memuat...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <small class="text-muted fst-italic me-auto" style="font-size: 0.75rem;">
                        *Data dihitung berdasarkan jumlah barang yang <b>lolos QC (Diterima)</b>.
                    </small>
                    <button type="button" class="btn btn-sm btn-dark" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const token = localStorage.getItem('api_token');

        document.addEventListener('DOMContentLoaded', () => {
            if (!token) {
                alert("Sesi habis. Silakan login ulang.");
                window.location.href = '/login';
                return;
            }
            
            // Default filter visual saja
            const today = new Date().toISOString().split('T')[0];
            const firstDay = today.substring(0, 8) + '01'; 
            
            const elStart = document.getElementById('filterStartDate');
            const elEnd = document.getElementById('filterEndDate');
            
            if(elStart) elStart.value = firstDay;
            if(elEnd) elEnd.value = today;

            loadVerifikasi();
        });

        // --- FUNGSI LEADERBOARD ---
        function openLeaderboard() {
            const modalEl = document.getElementById('modalLeaderboard');
            if(modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
                resetFilter(); 
            }
        }

        function resetFilter() {
            document.getElementById('filterStartDate').value = '';
            document.getElementById('filterEndDate').value = '';
            fetchLeaderboard();
        }

        async function fetchLeaderboard() {
            const tbody = document.getElementById('leaderboardData');
            const lblPeriode = document.getElementById('lblPeriode');
            
            const startDate = document.getElementById('filterStartDate').value;
            const endDate = document.getElementById('filterEndDate').value;

            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-5"><div class="spinner-border text-warning spinner-border-sm"></div> Memuat data...</td></tr>';

            try {
                let url = '/api/progres-produksi/leaderboard';
                
                // Kirim parameter tanggal hanya jika user memilih
                if(startDate && endDate) {
                    url += `?start_date=${startDate}&end_date=${endDate}`;
                }

                const res = await fetch(url, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                
                const json = await res.json();
                
                if(lblPeriode) lblPeriode.innerText = json.periode || '-';

                if (json.data && json.data.length > 0) {
                    tbody.innerHTML = '';
                    json.data.forEach(item => {
                        let rankDisplay = `<span class="fw-bold text-muted">#${item.rank}</span>`;
                        let rowClass = "";

                        if(item.rank === 1) {
                            rankDisplay = `<i class="fas fa-medal text-warning fa-lg"></i>`;
                            rowClass = "bg-warning bg-opacity-10";
                        } else if(item.rank === 2) {
                            rankDisplay = `<i class="fas fa-medal text-secondary fa-lg"></i>`;
                        } else if(item.rank === 3) {
                            rankDisplay = `<i class="fas fa-medal" style="color: #CD7F32;"></i>`;
                        }

                        tbody.innerHTML += `
                        <tr class="${rowClass}">
                            <td class="ps-4 text-center align-middle">${rankDisplay}</td>
                            <td class="fw-bold align-middle">${item.nama}</td>
                            <td class="text-end pe-4 align-middle">
                                <span class="badge bg-primary rounded-pill fs-6">${item.total}</span>
                            </td>
                        </tr>`;
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="3" class="text-center py-5 text-muted">
                        <i class="fas fa-calendar-times fa-2x mb-2"></i><br>
                        Tidak ada data produksi pada periode ini.
                    </td></tr>`;
                }

            } catch (error) {
                console.error(error);
                tbody.innerHTML = `<tr><td colspan="3" class="text-center text-danger py-3">Gagal memuat leaderboard.</td></tr>`;
            }
        }

        // --- FUNGSI LOAD VERIFIKASI (SAMA) ---
        async function loadVerifikasi() {
            const tbody = document.getElementById('tableData');
            if (!tbody) return;

            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>';

            try {
                const res = await fetch('/api/progres-produksi/pending', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const json = await res.json();

                if (json.data && json.data.length > 0) {
                    tbody.innerHTML = '';
                    json.data.forEach(item => {
                        const dateObj = new Date(item.waktu);
                        const waktuStr = dateObj.toLocaleDateString('id-ID', {
                            day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'
                        }).replace('.', ':');

                        tbody.innerHTML += `
                        <tr>
                            <td class="ps-4 small text-muted">${waktuStr}</td>
                            <td class="fw-bold text-dark">${item.karyawan}</td>
                            <td>
                                <div class="fw-bold">${item.produk}</div>
                                <span class="badge bg-light text-dark border">Size: ${item.size}</span>
                            </td>
                            <td class="text-center fw-bold text-primary" style="font-size: 1.2em;">${item.jumlah_setor}</td>
                            <td class="text-end pe-4">
                                <button class="btn btn-danger btn-sm me-1" onclick="reject('${item.id_progres}')" title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-success btn-sm" onclick="openModalTerima('${item.id_progres}', ${item.jumlah_setor})" title="Terima">
                                    <i class="fas fa-check"></i> Proses
                                </button>
                            </td>
                        </tr>`;
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted">Tidak ada setoran yang menunggu verifikasi.</td></tr>`;
                }
            } catch (error) {
                console.error(error);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Gagal memuat data.</td></tr>`;
            }
        }

        function openModalTerima(id, jumlah) {
            document.getElementById('hidden_id_progres').value = id;
            document.getElementById('lbl_jml_setor').innerText = jumlah;
            document.getElementById('input_diterima').value = jumlah;
            new bootstrap.Modal(document.getElementById('modalTerima')).show();
        }

        async function submitTerima() {
            const id = document.getElementById('hidden_id_progres').value;
            const jumlahDiterima = document.getElementById('input_diterima').value;

            if (jumlahDiterima < 0 || jumlahDiterima === '') {
                alert("Jumlah tidak valid!"); return;
            }

            const btnSimpan = document.querySelector('#modalTerima .btn-success');
            const textAsli = btnSimpan.innerText;
            btnSimpan.innerText = 'Menyimpan...';
            btnSimpan.disabled = true;

            try {
                const res = await fetch(`/api/progres-produksi/${id}/konfirmasi`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({
                        action: 'approve',
                        jumlah_diterima: jumlahDiterima
                    })
                });

                const textResponse = await res.text();
                let json;
                try { json = JSON.parse(textResponse); } catch (err) { throw new Error("Terjadi kesalahan di server."); }

                if (res.ok) {
                    const modalEl = document.getElementById('modalTerima');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                    alert("Berhasil! Stok bertambah.");
                    loadVerifikasi();
                } else {
                    alert("Gagal: " + (json.message || "Error server"));
                    loadVerifikasi();
                }
            } catch (e) {
                alert("Error: " + e.message);
            } finally {
                btnSimpan.innerText = textAsli;
                btnSimpan.disabled = false;
            }
        }

        async function reject(id) {
            if(!confirm('Yakin ingin menolak setoran ini? Stok TIDAK akan bertambah.')) return;

            try {
                const res = await fetch(`/api/progres-produksi/${id}/konfirmasi`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({
                        action: 'reject',
                        jumlah_diterima: 0
                    })
                });
                
                const json = await res.json();
                if (res.ok) {
                    alert("Laporan berhasil ditolak.");
                    loadVerifikasi();
                } else {
                    alert("Gagal: " + json.message);
                    loadVerifikasi();
                }
            } catch (e) {
                alert("Error koneksi");
            }
        }
    </script>
@endpush