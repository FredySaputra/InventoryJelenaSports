@extends('layouts.admin')

@section('title', 'Barang Keluar')
@section('header-title', 'Transaksi Penjualan')

@push('styles')
    <style>
        .form-select-modern {
            border: 1px solid #cbd5e1; border-radius: 10px; padding: 10px 15px;
            background-color: #f8fafc; font-size: 0.95rem; transition: all 0.3s ease;
            cursor: pointer; appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23334155' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat; background-position: right 1rem center; background-size: 16px 12px;
        }
        .form-select-modern:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); background-color: #ffffff; outline: none; }
        .form-select-modern:disabled { background-color: #e2e8f0; cursor: not-allowed; opacity: 0.7; }

        .btn-modern-add {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; border: none;
            border-radius: 10px; padding: 12px; font-weight: 600; letter-spacing: 0.5px;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); transition: transform 0.2s, box-shadow 0.2s;
            width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px;
        }
        .btn-modern-add:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); color: white; }

        .btn-modern-back {
            border: 2px solid #64748b; color: #64748b; background: transparent;
            border-radius: 50px; padding: 6px 20px; font-weight: 700; font-size: 0.85rem;
            transition: all 0.3s; display: flex; align-items: center; gap: 5px;
        }
        .btn-modern-back:hover { background-color: #64748b; color: white; border-color: #64748b; }

        .btn-modern-save {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none;
            border-radius: 8px; padding: 12px 24px; font-weight: bold; font-size: 1rem;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2); transition: all 0.3s;
        }
        .btn-modern-save:hover { filter: brightness(110%); transform: scale(1.02); }

        .input-modern { border-radius: 10px; padding: 10px; border: 1px solid #cbd5e1; }
        .input-modern:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

        .btn-modern-detail {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white; border: none; border-radius: 50px;
            padding: 5px 18px; font-size: 0.85rem; font-weight: 600;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
            transition: all 0.2s;
        }
        .btn-modern-detail:hover {
            transform: translateY(-1px); box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4); color: white;
        }

        .input-harga-tabel {
            border: 1px solid #e2e8f0; border-radius: 6px; padding: 5px 10px; width: 100%; min-width: 100px; text-align: right; font-weight: bold; color: #0f172a;
        }
        .input-harga-tabel:focus { outline: 2px solid #3b82f6; border-color: transparent; }

        /* Custom Modal Style */
        .custom-modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: none; justify-content: center; align-items: center;
            z-index: 9999; opacity: 0; transition: opacity 0.3s ease;
        }
        .custom-modal-box {
            background: white; width: 90%; max-width: 700px;
            border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
        }
        .custom-modal-header {
            background: #f1f5f9; padding: 15px 25px;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .custom-modal-body { padding: 25px; max-height: 70vh; overflow-y: auto; }
        .custom-modal-overlay.show { display: flex; opacity: 1; }
        .custom-modal-overlay.show .custom-modal-box { transform: scale(1); }
    </style>
@endpush

@section('content')

    <div id="viewList">
        <div class="card" style="padding: 20px; border-radius: 12px; border:none; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h4 style="margin:0; font-weight:800; color:#334155;">Riwayat Transaksi</h4>
                <button class="btn btn-primary btn-lg shadow-sm" style="border-radius:10px; font-weight:600;" onclick="switchView('form')">
                    <i class="fas fa-plus"></i> Transaksi Baru
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th class="py-3 ps-3">ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th class="text-center">Total Item</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody id="tableRiwayat">
                    <tr><td colspan="6" class="text-center py-4">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="viewForm" style="display: none;">
        <div class="card mb-4" style="padding: 25px; border-radius: 12px; border:none; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); border-left: 5px solid #3b82f6;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h5 style="margin:0; font-weight:700; color:#1e293b;">Form Transaksi Baru</h5>
                <button class="btn-modern-back" onclick="switchView('list')">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
            </div>
            <hr class="my-3 text-muted opacity-25">
            
            <div class="row align-items-end mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-primary small">SUMBER DATA TRANSAKSI</label>
                    <select id="sumber_data" class="form-select-modern bg-white border-primary" onchange="toggleSumberData()">
                        <option value="manual">Input Barang Manual (Stok Ready)</option>
                        <option value="spk">Dari SPK (Pesanan Selesai Produksi)</option>
                    </select>
                </div>
                <div class="col-md-4" id="div_pilih_spk" style="display: none;">
                    <label class="form-label fw-bold text-muted small">PILIH SPK SELESAI</label>
                    <select id="pilih_spk" class="form-select-modern" onchange="loadItemFromSpk()">
                        <option value="">-- Pilih SPK --</option>
                    </select>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted small">TANGGAL TRANSAKSI</label>
                    <input type="date" id="tgl_trx" class="form-control input-modern" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold text-muted small">PILIH PELANGGAN <span class="text-danger">*</span></label>
                    <select id="pilih_pelanggan" class="form-select-modern" onchange="enableInputBarang()">
                        <option value="">-- Pilih Pelanggan Dahulu --</option>
                    </select>
                    <small id="hint_pelanggan" class="text-primary fst-italic mt-1 d-block">* Pilih pelanggan untuk membuka menu input barang.</small>
                </div>
            </div>
        </div>

        <div id="sectionInputBarang" class="row" style="display: none; opacity: 0; transition: opacity 0.5s;">
            
            <div class="col-md-4" id="boxInputManual">
                <div class="card h-100" style="padding: 25px; border-radius: 12px; border:none; box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                    <h5 style="margin-bottom: 20px; font-weight:700; color:#334155;">Langkah 2: Input Barang</h5>

                    <div class="form-group mb-3">
                        <label class="small text-muted fw-bold mb-1">PRODUK</label>
                        <select id="pilih_produk" class="form-select-modern" onchange="loadSizes(this.value)">
                            <option value="">-- Pilih Produk --</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small text-muted fw-bold mb-1">UKURAN (SIZE)</label>
                        <select id="pilih_size" class="form-select-modern" disabled onchange="cekHargaTerdaftar()">
                            <option value="">-- Pilih Produk Dulu --</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="small text-muted fw-bold mb-1">HARGA (Rp)</label>
                                <input type="number" id="harga_satuan" class="form-control input-modern" placeholder="0">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="small text-muted fw-bold mb-1">QTY</label>
                                <input type="number" id="jumlah_keluar" class="form-control input-modern" value="1" min="1">
                            </div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <button type="button" class="btn-modern-add" onclick="tambahItemKeKeranjang()">
                            <i class="fas fa-plus-circle"></i> Tambah Barang
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-8" id="boxKeranjang">
                <div class="card h-100" style="padding: 25px; border-radius: 12px; border:none; box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-bottom:2px solid #f1f5f9; padding-bottom:15px;">
                        <h5 style="margin:0; font-weight:700; color:#334155;">List Barang Keluar</h5>
                        <h4 style="margin:0; color:#2563eb; font-weight:800;" id="labelTotalContainer">Total: Rp <span id="labelGrandTotal">0</span></h4>
                    </div>

                    <div id="msgSpkMode" class="alert alert-info py-2" style="display: none;">
                        <i class="fas fa-info-circle me-1"></i> Data barang diambil otomatis dari SPK. Silakan sesuaikan <b>Harga Jual</b> jika diperlukan.
                    </div>

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;" id="containerTabelKeranjang">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light sticky-top">
                            <tr>
                                <th>Produk</th>
                                <th>Size</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end" style="width: 150px;">Harga Satuan (Rp)</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                            </thead>
                            <tbody id="keranjangBody">
                            <tr><td colspan="6" class="text-center text-muted py-5">Keranjang kosong.</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-auto text-end pt-3">
                        <button class="btn-modern-save w-100" onclick="simpanTransaksi()">
                            <i class="fas fa-save me-2"></i> SIMPAN SEMUA DATA
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalDetailOverlay" class="custom-modal-overlay">
        <div class="custom-modal-box">
            <div class="custom-modal-header">
                <h5 style="margin:0; font-weight:700; color:#334155;">Detail Transaksi: <span id="modalIdTrx" class="text-primary"></span></h5>
                <button onclick="tutupModalDetail()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
            </div>
            <div class="custom-modal-body">
                <div class="row mb-4">
                    <div class="col-6">
                        <small class="text-muted fw-bold">PELANGGAN</small><br>
                        <span id="modalPelanggan" class="fs-5 fw-bold text-dark">-</span>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted fw-bold">TANGGAL</small><br>
                        <span id="modalTanggal" class="fs-5 text-dark">-</span>
                    </div>
                </div>

                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th>Size</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                    </thead>
                    <tbody id="modalTableBody">
                    </tbody>
                    <tfoot class="table-light">
                    <tr>
                        <td colspan="4" class="text-end fw-bold">GRAND TOTAL</td>
                        <td class="text-end fw-bold text-primary" id="modalGrandTotal">-</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const token = localStorage.getItem('api_token');

        let keranjang = [];
        let produkList = [];
        let riwayatDataGlobal = [];
        let spkList = [];

        const formatRupiah = (num) => new Intl.NumberFormat('id-ID').format(num);

        document.addEventListener('DOMContentLoaded', () => {
            loadRiwayatTransaksi(); 
            loadPelanggan();
            loadMasterProduk();
            loadSpkList(); 
        });

        function switchView(viewName) {
            const listDiv = document.getElementById('viewList');
            const formDiv = document.getElementById('viewForm');

            if(viewName === 'form') {
                listDiv.style.display = 'none';
                formDiv.style.display = 'block';
                loadPelanggan();
                loadMasterProduk();
                loadSpkList();
            } else {
                listDiv.style.display = 'block';
                formDiv.style.display = 'none';
                loadRiwayatTransaksi();
                resetForm();
            }
        }

        // --- FITUR BARU: TOGGLE SUMBER DATA ---
        function toggleSumberData() {
            const sumber = document.getElementById('sumber_data').value;
            const divSpk = document.getElementById('div_pilih_spk');
            const boxManual = document.getElementById('boxInputManual');
            const boxKeranjang = document.getElementById('boxKeranjang');
            const msgSpk = document.getElementById('msgSpkMode');
            const selPelanggan = document.getElementById('pilih_pelanggan');

            // Reset state
            keranjang = [];
            renderKeranjang();
            document.getElementById('pilih_spk').value = "";
            selPelanggan.value = "";
            selPelanggan.disabled = false; 

            if (sumber === 'spk') {
                // Mode SPK
                divSpk.style.display = 'block';
                boxManual.style.display = 'none'; 
                boxKeranjang.className = 'col-md-12';
                msgSpk.style.display = 'block'; 
                selPelanggan.disabled = true;
            } else {
                // Mode Manual
                divSpk.style.display = 'none';
                boxManual.style.display = 'block';
                boxKeranjang.className = 'col-md-8';
                msgSpk.style.display = 'none';
            }
            enableInputBarang(); 
        }

        async function loadSpkList() {
            try {
                const res = await fetch('/api/transaksi/spk-siap', { 
                    headers: { 'Authorization': 'Bearer ' + token } 
                });
                const json = await res.json();
                spkList = json.data || [];

                const select = document.getElementById('pilih_spk');
                select.innerHTML = '<option value="">-- Pilih SPK --</option>';
                spkList.forEach(spk => {
                    select.innerHTML += `<option value="${spk.id}" data-pelanggan="${spk.idPelanggan}">${spk.label}</option>`;
                });
            } catch (e) { console.error("Gagal load SPK", e); }
        }

        async function loadItemFromSpk() {
            const spkId = document.getElementById('pilih_spk').value;
            const selectSpk = document.getElementById('pilih_spk');
            
            const idPel = selectSpk.options[selectSpk.selectedIndex].getAttribute('data-pelanggan');
            const selPelanggan = document.getElementById('pilih_pelanggan');

            if (idPel) {
                selPelanggan.value = idPel;
                enableInputBarang(); 
            } else {
                selPelanggan.value = "";
                document.getElementById('sectionInputBarang').style.display = 'none';
                return;
            }

            if(!spkId) return;

            try {
                // SweetAlert Loading
                Swal.fire({
                    title: 'Memuat Data...',
                    text: 'Mengambil item dari SPK',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() }
                });
                
                const res = await fetch(`/api/transaksi/spk-detail/${spkId}`, { headers: { 'Authorization': 'Bearer ' + token } });
                const json = await res.json();
                
                Swal.close(); // Tutup loading

                keranjang = json.data || []; 
                renderKeranjang(); 

            } catch(e) { 
                Swal.fire('Error', 'Gagal mengambil detail SPK', 'error');
                keranjang = []; renderKeranjang(); 
            }
        }

        function renderKeranjang() {
            const tbody = document.getElementById('keranjangBody');
            const lblTotal = document.getElementById('labelGrandTotal');
            tbody.innerHTML = '';

            if(keranjang.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-5">Keranjang kosong.</td></tr>';
                lblTotal.innerText = '0';
                return;
            }

            let grandTotal = 0;
            keranjang.forEach((item, index) => {
                const subtotal = item.jumlah * item.harga;
                grandTotal += subtotal;

                let stokInfo = "";
                if(item.stokGudang !== undefined) {
                    const color = item.stokGudang < item.jumlah ? 'text-danger' : 'text-muted';
                    stokInfo = `<br><small class="${color}">Stok Gudang: ${item.stokGudang}</small>`;
                }

                tbody.innerHTML += `
                <tr>
                    <td>
                        <div class="fw-bold">${item.namaProduk}</div>
                        ${stokInfo}
                    </td>
                    <td><span class="badge bg-light text-dark border">${item.namaSize}</span></td>
                    <td class="text-center fs-6">${item.jumlah}</td>
                    <td class="text-end">
                        <input type="number" class="input-harga-tabel" 
                            value="${item.harga == 0 ? '' : item.harga}" 
                            placeholder="0"
                            onchange="updateHarga(${index}, this.value)" 
                            onkeyup="updateHarga(${index}, this.value)">
                    </td>
                    <td class="text-end fw-bold text-primary" id="subtotal-${index}">
                        Rp ${formatRupiah(subtotal)}
                    </td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm" onclick="hapusItem(${index})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            });
            lblTotal.innerText = formatRupiah(grandTotal);
        }

        function updateHarga(index, newPrice) {
            keranjang[index].harga = parseInt(newPrice) || 0;
            
            const newSubtotal = keranjang[index].jumlah * keranjang[index].harga;
            document.getElementById(`subtotal-${index}`).innerText = 'Rp ' + formatRupiah(newSubtotal);

            let total = keranjang.reduce((sum, item) => sum + (item.jumlah * item.harga), 0);
            document.getElementById('labelGrandTotal').innerText = formatRupiah(total);
        }

        function enableInputBarang() {
            const idPel = document.getElementById('pilih_pelanggan').value;
            const section = document.getElementById('sectionInputBarang');
            const sumber = document.getElementById('sumber_data').value;

            const isSpkSelected = sumber === 'spk' && document.getElementById('pilih_spk').value !== "";
            const isManualReady = sumber === 'manual' && idPel !== "";

            if(isManualReady || isSpkSelected) {
                section.style.display = 'flex';
                setTimeout(() => { section.style.opacity = 1; }, 50);
            } else {
                section.style.opacity = 0;
                setTimeout(() => { section.style.display = 'none'; }, 500);
            }
        }

        async function loadPelanggan() {
            const select = document.getElementById('pilih_pelanggan');
            if(select.options.length > 1) return;

            try {
                const res = await fetch('/api/pelanggans', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                const json = await res.json();
                const dataPelanggan = json.data;

                select.innerHTML = '<option value="">-- Pilih Pelanggan --</option>';
                if(dataPelanggan && dataPelanggan.length > 0) {
                    dataPelanggan.forEach(p => select.innerHTML += `<option value="${p.id}">${p.nama}</option>`);
                }
            } catch(e) { console.error(e); }
        }

        async function loadMasterProduk() {
            if(produkList.length > 0) return;
            try {
                const res = await fetch('/api/produks', { headers: { 'Authorization': 'Bearer ' + token } });
                const json = await res.json();
                produkList = json.data;

                const select = document.getElementById('pilih_produk');
                select.innerHTML = '<option value="">-- Pilih Produk --</option>';
                produkList.forEach(cat => {
                    if(cat.produks && cat.produks.length > 0) {
                        const optgroup = document.createElement('optgroup');
                        optgroup.label = cat.nama;
                        cat.produks.forEach(prod => {
                            const option = document.createElement('option');
                            option.value = prod.id;
                            option.text = prod.nama_lengkap || prod.nama;
                            optgroup.appendChild(option);
                        });
                        select.appendChild(optgroup);
                    }
                });
            } catch(e) { console.error(e); }
        }

        async function loadSizes(idProduk) {
            const selectSize = document.getElementById('pilih_size');
            const inputHarga = document.getElementById('harga_satuan');

            inputHarga.value = '';
            inputHarga.readOnly = false;
            inputHarga.style.backgroundColor = 'white';

            if(!idProduk) {
                selectSize.innerHTML = '<option value="">-- Pilih Produk Dulu --</option>';
                selectSize.disabled = true;
                return;
            }

            try {
                const res = await fetch('/api/stoks', { headers: { 'Authorization': 'Bearer ' + token } });
                const dataMatrix = await res.json();
                const items = Array.isArray(dataMatrix) ? dataMatrix : dataMatrix.data;
                let foundSizes = [];

                items.forEach(cat => {
                    const foundProd = cat.produks.find(p => p.id == idProduk);
                    if(foundProd) {
                        cat.sizes.forEach(s => {
                            const stokItem = foundProd.stoks.find(st => st.idSize == s.id);
                            if(stokItem && stokItem.stok > 0) {
                                const itemInCart = keranjang.find(k => k.idProduk == idProduk && k.idSize == s.id);
                                const qtyInCart = itemInCart ? itemInCart.jumlah : 0;
                                const sisaRealtime = stokItem.stok - qtyInCart;

                                foundSizes.push({
                                    id: s.id, tipe: s.tipe, stokAsli: stokItem.stok, sisaTampil: sisaRealtime
                                });
                            }
                        });
                    }
                });

                selectSize.innerHTML = '<option value="">-- Pilih Size --</option>';

                if(foundSizes.length === 0) {
                    selectSize.innerHTML = '<option value="">Stok Kosong</option>';
                    selectSize.disabled = true;
                } else {
                    foundSizes.forEach(s => {
                        const isDisabled = s.sisaTampil <= 0 ? 'disabled' : '';
                        const labelSisa = s.sisaTampil <= 0 ? 'Habis di Keranjang' : `Sisa: ${s.sisaTampil}`;
                        selectSize.innerHTML += `<option value="${s.id}" data-stok="${s.stokAsli}" ${isDisabled}>${s.tipe} (${labelSisa})</option>`;
                    });
                    selectSize.disabled = false;
                }

            } catch(e) { console.error(e); }
        }

        function cekHargaTerdaftar() {
            const idProd = document.getElementById('pilih_produk').value;
            const idSize = document.getElementById('pilih_size').value;
            const inputHarga = document.getElementById('harga_satuan');

            if(!idProd || !idSize) return;

            const itemExist = keranjang.find(k => k.idProduk == idProd && k.idSize == idSize);

            if (itemExist) {
                inputHarga.value = itemExist.harga;
                inputHarga.readOnly = true;
                inputHarga.style.backgroundColor = '#e2e8f0';
            } else {
                if(inputHarga.readOnly) inputHarga.value = '';
                inputHarga.readOnly = false;
                inputHarga.style.backgroundColor = 'white';
            }
        }

        function tambahItemKeKeranjang() {
            const idProd = document.getElementById('pilih_produk').value;
            const idSize = document.getElementById('pilih_size').value;
            const harga = parseInt(document.getElementById('harga_satuan').value);
            const jmlInput = parseInt(document.getElementById('jumlah_keluar').value);

            const selectSizeElem = document.getElementById('pilih_size');
            const selectedOption = selectSizeElem.options[selectSizeElem.selectedIndex];

            if(selectedOption.disabled) { 
                Swal.fire('Stok Kosong', 'Stok barang ini sudah habis di keranjang!', 'warning');
                return; 
            }

            const stokTersedia = parseInt(selectedOption.getAttribute('data-stok'));
            const selProd = document.getElementById('pilih_produk');
            const txtProd = selProd.options[selProd.selectedIndex].text;
            let txtSize = selectedOption.text.split('(')[0].trim();

            if(!idProd || !idSize || isNaN(harga) || isNaN(jmlInput) || jmlInput < 1) {
                Swal.fire('Data Kurang', 'Mohon lengkapi produk, size, harga, dan jumlah.', 'info');
                return;
            }

            const existingIndex = keranjang.findIndex(item => item.idProduk === idProd && item.idSize === idSize);
            let jumlahDiKeranjang = existingIndex !== -1 ? keranjang[existingIndex].jumlah : 0;

            if (jmlInput + jumlahDiKeranjang > stokTersedia) {
                Swal.fire({
                    icon: 'error',
                    title: 'Stok Tidak Cukup!',
                    text: `Total Stok: ${stokTersedia} | Sudah di keranjang: ${jumlahDiKeranjang}`
                });
                return;
            }

            if (existingIndex !== -1) {
                keranjang[existingIndex].jumlah += jmlInput;
                keranjang[existingIndex].subtotal = keranjang[existingIndex].jumlah * keranjang[existingIndex].harga;
            } else {
                keranjang.push({
                    idProduk: idProd, namaProduk: txtProd,
                    idSize: idSize, namaSize: txtSize,
                    harga: harga, jumlah: jmlInput, 
                    stokGudang: stokTersedia
                });
            }

            renderKeranjang();

            document.getElementById('pilih_size').value = "";
            document.getElementById('harga_satuan').value = "";
            document.getElementById('harga_satuan').readOnly = false;
            document.getElementById('harga_satuan').style.backgroundColor = 'white';
            document.getElementById('jumlah_keluar').value = 1;

            loadSizes(idProd);
        }

        // --- GANTI CONFIRM DENGAN SWEETALERT ---
        function hapusItem(index) {
            Swal.fire({
                title: 'Hapus Item?',
                text: "Item ini akan dihapus dari daftar keranjang.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const idProdYangDihapus = keranjang[index].idProduk;
                    keranjang.splice(index, 1);
                    renderKeranjang();
                    
                    const currentSelectedProd = document.getElementById('pilih_produk').value;
                    if(currentSelectedProd && currentSelectedProd == idProdYangDihapus) { loadSizes(currentSelectedProd); }
                    
                    Swal.fire('Terhapus!', 'Item berhasil dihapus.', 'success');
                }
            });
        }

        // --- GANTI ALERT SIMPAN DENGAN SWEETALERT ---
        async function simpanTransaksi() {
            const idPelanggan = document.getElementById('pilih_pelanggan').value;
            const sumber = document.getElementById('sumber_data').value;
            const spkId = document.getElementById('pilih_spk').value;
            
            if(!idPelanggan) { 
                Swal.fire('Pilih Pelanggan', 'Silakan pilih pelanggan terlebih dahulu.', 'warning'); 
                return; 
            }
            if(keranjang.length === 0) { 
                Swal.fire('Keranjang Kosong', 'Belum ada barang yang akan ditransaksikan.', 'warning'); 
                return; 
            }

            // Validasi Harga Nol
            const adaHargaNol = keranjang.some(i => i.harga <= 0);
            if(adaHargaNol) {
                const confirmHarga = await Swal.fire({
                    title: 'Harga Masih Kosong?',
                    text: "Ada item dengan harga Rp 0. Apakah Anda yakin ingin melanjutkan?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Periksa Lagi'
                });
                if(!confirmHarga.isConfirmed) return;
            }

            // Konfirmasi Akhir Sebelum Simpan
            const confirmSimpan = await Swal.fire({
                title: 'Simpan Transaksi?',
                text: "Pastikan semua data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Ya, Simpan Transaksi!'
            });

            if(!confirmSimpan.isConfirmed) return;

            // Loading saat proses simpan
            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading() }
            });

            const payload = {
                tanggal: document.getElementById('tgl_trx').value,
                idPelanggan: idPelanggan,
                idPerintahProduksi: (sumber === 'spk') ? spkId : null,
                items: keranjang.map(item => ({
                    idProduk: item.idProduk, idSize: item.idSize, harga: item.harga, jumlah: item.jumlah
                }))
            };

            try {
                const res = await fetch('/api/barang-keluar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                
                if(res.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Transaksi berhasil disimpan.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        switchView('list');
                    });
                } else {
                    Swal.fire('Gagal!', (json.message || 'Terjadi kesalahan saat menyimpan.'), 'error');
                }
            } catch(e) { 
                console.error(e);
                Swal.fire('Error Sistem', 'Tidak dapat terhubung ke server.', 'error'); 
            }
        }

        function resetForm() {
            document.getElementById('sumber_data').value = "manual";
            toggleSumberData(); // Reset ke manual
            keranjang = [];
            renderKeranjang();
            document.getElementById('pilih_pelanggan').value = "";
            document.getElementById('tgl_trx').value = new Date().toISOString().split('T')[0];
        }

        async function loadRiwayatTransaksi() {
            const tbody = document.getElementById('tableRiwayat');
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Memuat data...</td></tr>';

            try {
                const res = await fetch('/api/barang-keluar', { headers: { 'Authorization': 'Bearer ' + token } });
                const json = await res.json();
                riwayatDataGlobal = json.data;

                tbody.innerHTML = '';
                if(!riwayatDataGlobal || riwayatDataGlobal.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">Belum ada riwayat transaksi.</td></tr>';
                    return;
                }

                riwayatDataGlobal.forEach(trx => {
                    const namaPelanggan = trx.pelanggan ? trx.pelanggan.nama : '<span class="text-muted">-</span>';
                    const totItem = trx.total_item ?? 0;
                    const totHarga = trx.total_harga ?? 0;

                    tbody.innerHTML += `
                    <tr>
                        <td class="ps-3"><span class="badge bg-secondary fw-normal">${trx.id}</span></td>
                        <td>${trx.tanggal}</td>
                        <td class="fw-bold text-dark">${namaPelanggan}</td>
                        <td class="text-center">${totItem}</td>
                        <td class="fw-bold text-primary">Rp ${formatRupiah(totHarga)}</td>
                        <td>
                            <button class="btn-modern-detail" onclick="bukaModalDetail('${trx.id}')">
                                <i class="fas fa-eye me-1"></i> Detail
                            </button>
                        </td>
                    </tr>
                `;
                });
            } catch(e) { console.error(e); }
        }

        function bukaModalDetail(idTrx) {
            const data = riwayatDataGlobal.find(d => d.id === idTrx);
            if(!data) { 
                Swal.fire('Oops...', 'Data transaksi tidak ditemukan!', 'error');
                return; 
            }

            document.getElementById('modalIdTrx').innerText = data.id;
            document.getElementById('modalPelanggan').innerText = data.pelanggan ? data.pelanggan.nama : '-';
            document.getElementById('modalTanggal').innerText = data.tanggal;
            document.getElementById('modalGrandTotal').innerText = 'Rp ' + formatRupiah(data.total_harga);

            const tbody = document.getElementById('modalTableBody');
            tbody.innerHTML = '';

            if(data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    tbody.innerHTML += `
                    <tr>
                        <td>${item.produk}</td>
                        <td><span class="badge bg-light text-dark border">${item.size}</span></td>
                        <td class="text-center">${item.jumlah}</td>
                        <td class="text-end text-muted">Rp ${formatRupiah(item.harga_satuan)}</td>
                        <td class="text-end fw-bold">Rp ${formatRupiah(item.subtotal)}</td>
                    </tr>
                `;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Tidak ada detail item.</td></tr>';
            }

            const overlay = document.getElementById('modalDetailOverlay');
            overlay.classList.add('show');
        }

        function tutupModalDetail() {
            document.getElementById('modalDetailOverlay').classList.remove('show');
        }

        document.getElementById('modalDetailOverlay').addEventListener('click', function(e) {
            if (e.target === this) tutupModalDetail();
        });
    </script>
@endpush