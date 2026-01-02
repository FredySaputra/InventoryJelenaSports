<nav class="sidebar">
    <div class="sidebar-header">Jelena Sports</div>
    <ul class="nav-links">
        <li>
            <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
        </li>

        <li>
            <a href="#" class="{{ request()->is('barang') ? 'active' : '' }}">
                Data Barang (Master)
            </a>
        </li>

        <li>
            <a href="#" class="{{ request()->is('barang-masuk') ? 'active' : '' }}">
                Barang Masuk
            </a>
        </li>

        <li>
            <a href="#" class="{{ request()->is('barang-keluar') ? 'active' : '' }}">
                Barang Keluar
            </a>
        </li>

        <li>
            <a href="#" class="{{ request()->is('laporan*') ? 'active' : '' }}">
                Laporan Stok
            </a>
        </li>

        <li>
            <a href="/pelanggans" class="{{ request()->is('pelanggans*') ? 'active' : '' }}">
                Pelanggan
            </a>
        </li>
    </ul>
</nav>
