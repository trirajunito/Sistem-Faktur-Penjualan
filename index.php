<?php

require_once "config/database.php";

$pageTitle = "Beranda";
$activeMenu = "dashboard";
$basePath = "";

$jumlahPerusahaan = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM perusahaan")
)['total'];

$jumlahCustomer = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM customer")
)['total'];

$jumlahProduk = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk")
)['total'];

$jumlahPenjualan = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM faktur")
)['total'];

$totalPenjualan = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COALESCE(SUM(grand_total),0) AS total FROM faktur")
)['total'];

include "layouts/header.php";
include "layouts/navigation.php";
include "layouts/sidebar.php";

?>

<main class="main-content">

    <div class="welcome-card">

        <h3>Selamat Datang di FakturTrii 👋</h3>

        <p>
            Kelola perusahaan, customer, produk, dan transaksi
            penjualan dengan mudah.
        </p>

    </div>


    <div class="row g-4 mb-4">

        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon">
                    <i class="bi bi-building"></i>
                </div>

                <h3><?= $jumlahPerusahaan; ?></h3>

                <p>Total Perusahaan</p>

            </div>

        </div>


        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>

                <h3><?= $jumlahCustomer; ?></h3>

                <p>Total Customer</p>

            </div>

        </div>


        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>

                <h3><?= $jumlahProduk; ?></h3>

                <p>Total Produk</p>

            </div>

        </div>


        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon">
                    <i class="bi bi-receipt"></i>
                </div>

                <h3><?= $jumlahPenjualan; ?></h3>

                <p>Total Penjualan</p>

            </div>

        </div>

    </div>


    <div class="row g-4">

        <div class="col-lg-8">

            <div class="content-card">

                <div class="card-header-custom">

                    <div>
                        <h5>Ringkasan Penjualan</h5>
                        <small class="text-muted">
                            Total nilai transaksi
                        </small>
                    </div>

                </div>

                <div class="text-center py-5">

                    <i class="bi bi-cash-stack"
                       style="font-size:45px;color:#4f46e5"></i>

                    <h2 class="mt-3">
                        Rp <?= number_format($totalPenjualan, 0, ',', '.'); ?>
                    </h2>

                    <p class="text-muted">
                        Total seluruh transaksi penjualan
                    </p>

                </div>

            </div>

        </div>


        <div class="col-lg-4">

            <div class="content-card">

                <div class="card-header-custom">

                    <h5>Menu Cepat</h5>

                </div>

                <div class="d-grid gap-2">

                    <a href="perusahaan/tambah.php"
                       class="btn btn-primary-custom">

                        <i class="bi bi-plus-circle me-2"></i>
                        Tambah Perusahaan

                    </a>

                    <a href="customer/tambah.php"
                       class="btn btn-outline-primary">

                        <i class="bi bi-person-plus me-2"></i>
                        Tambah Customer

                    </a>

                    <a href="penjualan/tambah.php"
                       class="btn btn-outline-success">

                        <i class="bi bi-receipt me-2"></i>
                        Buat Penjualan

                    </a>

                </div>

            </div>

        </div>

    </div>

</main>

<?php include "layouts/footer.php"; ?>