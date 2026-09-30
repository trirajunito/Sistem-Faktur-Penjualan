<aside class="sidebar">

    <div class="sidebar-brand">

        <div class="brand-icon">
            <i class="bi bi-receipt-cutoff"></i>
        </div>

        <div class="brand-info">
            <h4>FakturTrii</h4>
            <span>Sales Management</span>
        </div>

    </div>


    <div class="sidebar-menu">

        <!-- MENU UTAMA -->
        <div class="menu-label">
            MENU UTAMA
        </div>

        <a
            href="<?= $basePath ?>index.php"
            class="menu-link <?= $activeMenu == 'dashboard' ? 'active' : ''; ?>"
        >
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Beranda</span>
        </a>


        <!-- MASTER DATA -->
        <div class="menu-label">
            MASTER DATA
        </div>

        <a
            href="<?= $basePath ?>perusahaan/index.php"
            class="menu-link <?= $activeMenu == 'perusahaan' ? 'active' : ''; ?>"
        >
            <i class="bi bi-building"></i>
            <span>Data Perusahaan</span>
        </a>


        <a
            href="<?= $basePath ?>customer/index.php"
            class="menu-link <?= $activeMenu == 'customer' ? 'active' : ''; ?>"
        >
            <i class="bi bi-people"></i>
            <span>Data Customer</span>
        </a>


        <!-- PRODUK -->
        <a
            href="<?= $basePath ?>produk/index.php"
            class="menu-link <?= $activeMenu == 'produk' ? 'active' : ''; ?>"
        >
            <i class="bi bi-box-seam"></i>
            <span>Data Produk</span>
        </a>


        <!-- TRANSAKSI -->
        <div class="menu-label">
            TRANSAKSI
        </div>

        <a
            href="<?= $basePath ?>penjualan/index.php"
            class="menu-link <?= $activeMenu == 'penjualan' ? 'active' : ''; ?>"
        >
            <i class="bi bi-receipt"></i>
            <span>Data Penjualan</span>
        </a>


        <!-- DETAIL FAKTUR -->
        <a
            href="<?= $basePath ?>detail-faktur/index.php"
            class="menu-link <?= $activeMenu == 'detail-faktur' ? 'active' : ''; ?>"
        >
            <i class="bi bi-list-ul"></i>
            <span>Detail Faktur</span>
        </a>


        <!-- LAINNYA
        <div class="menu-label">
            LAINNYA
        </div> -->

        <!-- <a
            href="#"
            class="menu-link"
        >
            <i class="bi bi-gear"></i>
            <span>Pengaturan</span>
        </a> -->

    </div>

</aside>

<div class="main-wrapper">