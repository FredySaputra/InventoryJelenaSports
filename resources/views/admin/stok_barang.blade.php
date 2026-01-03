@extends('layouts.admin')

@section('title', 'Stok Barang - Jelena Sports')
@section('header-title', 'Stok Barang')

@section('content')
    <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">

        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <h3 style="margin: 0;">Matrix Stok</h3>
            <button class="btn btn-primary" onclick="loadMatrix()">🔄 Refresh Data</button>
        </div>

        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; min-width: 800px;">
                <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <tr>
                    <th style="padding: 12px; text-align: left; position: sticky; left: 0; background: #f8fafc; z-index: 10;">Nama Produk</th>
                    <th style="padding: 12px; text-align: left;">Kategori</th>
                    <span id="headerSizesPlaceholder"></span>
                </tr>
                </thead>
                <tbody id="tableBody">
                <tr><td colspan="5" style="text-align: center; padding: 20px;">Memuat Matrix Stok...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const token = localStorage.getItem('api_token');
        let sizes = [];

        async function loadSizes() {
            try {
                const res = await fetch('/api/sizes', { headers: { 'Authorization': 'Bearer ' + token } });
                const json = await res.json();
                sizes = json.data ? json.data : json;

            } catch (e) {
                console.error('Gagal load size', e);
                alert('Gagal memuat data ukuran.');
            }
        }

        async function loadMatrix() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '<tr><td colspan="100%" style="text-align:center; padding:20px;">Memuat data...</td></tr>';

            if (sizes.length === 0) await loadSizes();

            const theadRow = document.querySelector('thead tr');
            while (theadRow.children.length > 2) {
                theadRow.removeChild(theadRow.lastChild);
            }

            sizes.forEach(size => {
                const th = document.createElement('th');
                th.style.padding = '10px';
                th.style.textAlign = 'center';
                th.style.minWidth = '50px';
                th.innerText = size.id;
                theadRow.appendChild(th);
            });

            try {
                const res = await fetch('/api/produks', { headers: { 'Authorization': 'Bearer ' + token } });
                const json = await res.json();
                const produks = json.data ? json.data : json;

                tbody.innerHTML = '';

                produks.forEach(prod => {
                    const tr = document.createElement('tr');
                    tr.style.borderBottom = '1px solid #f1f5f9';

                    tr.innerHTML = `
                    <td style="padding: 10px; font-weight: 500; position: sticky; left: 0; background: white;">
                        ${prod.nama}
                        <div style="font-size: 0.75rem; color: gray;">${prod.bahan_nama || '-'}</div>
                    </td>
                    <td style="padding: 10px; color: #64748b;">${prod.kategori_nama || '-'}</td>
                `;

                    sizes.forEach(size => {
                        const stokItem = prod.stoks ? prod.stoks.find(s => s.idSize == size.id) : null;
                        const jumlah = stokItem ? stokItem.stok : 0;

                        const td = document.createElement('td');
                        td.style.padding = '5px';
                        td.style.textAlign = 'center';

                        const input = document.createElement('input');
                        input.type = 'number';
                        input.value = jumlah;
                        input.style.width = '50px';
                        input.style.padding = '5px';
                        input.style.border = '1px solid #e2e8f0';
                        input.style.borderRadius = '4px';
                        input.style.textAlign = 'center';

                        input.addEventListener('change', (e) => updateStok(e.target, prod.id, size.id));
                        input.addEventListener('focus', (e) => e.target.style.borderColor = '#3b82f6'); // Biru saat fokus
                        input.addEventListener('blur', (e) => e.target.style.borderColor = '#e2e8f0'); // Kembali normal

                        td.appendChild(input);
                        tr.appendChild(td);
                    });

                    tbody.appendChild(tr);
                });

            } catch (e) {
                console.error(e);
                tbody.innerHTML = '<tr><td colspan="100%" style="text-align:center; color:red;">Gagal memuat data stok.</td></tr>';
            }
        }

        async function updateStok(inputElement, idProduk, idSize) {
            const newValue = inputElement.value;

            inputElement.style.backgroundColor = '#fef9c3';

            const payload = {
                idProduk: idProduk,
                idSize: idSize,
                jumlah: parseInt(newValue),
                tipe: 'set'
            };

            try {
                const res = await fetch('/api/stoks/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify(payload)
                });

                if (res.ok) {
                    inputElement.style.backgroundColor = '#dcfce7';
                    setTimeout(() => inputElement.style.backgroundColor = 'white', 1000);
                } else {
                    throw new Error('Gagal');
                }
            } catch (error) {
                inputElement.style.backgroundColor = '#fee2e2';
                alert('Gagal update stok!');
                console.error(error);
            }
        }

        loadMatrix();
    </script>
@endpush
