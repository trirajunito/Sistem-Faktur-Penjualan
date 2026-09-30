<?php

require_once "../classes/Database.php";
require_once "../classes/Produk.php";

//koneksi db

$database = new Database();

$conn = $database->getConnection();


//objek

$produk = new Produk($conn);


//pengaturan halaman

$pageTitle = "Data Produk";
$activeMenu = "produk";
$basePath = "../";


//ambil semua data

$query = $produk->getAll();

?>

<?php include "../layouts/header.php"; ?>

<?php include "../layouts/navigation.php"; ?>

<?php include "../layouts/sidebar.php"; ?>


<main class="main-content">

    <!-- HEADER HALAMAN -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Data Produk
            </h3>

            <p class="text-muted mb-0">
                Kelola data produk yang tersedia dalam sistem.
            </p>

        </div>


        <a
            href="tambah.php"
            class="btn btn-primary-custom"
        >

            <i class="bi bi-plus-circle me-2"></i>

            Tambah Produk

        </a>

    </div>


    <!-- CARD DATA -->
    <div class="content-card">

        <!-- HEADER CARD -->
        <div class="card-header-custom">

            <div>

                <h5>
                    Daftar Produk
                </h5>

                <small class="text-muted">
                    Data seluruh produk
                </small>

            </div>

        </div>


        <!-- TABLE -->
        <div class="table-responsive">

            <table class="table align-middle">

                <thead>

                    <tr>

                        <th style="width: 60px;">
                            No
                        </th>

                        <th>
                            Nama Produk
                        </th>

                        <th>
                            Jenis
                        </th>

                        <th>
                            Harga
                        </th>

                        <th>
                            Stock
                        </th>

                        <th style="width: 150px;">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php

                    $no = 1;

                    if (
                        $query &&
                        $query->num_rows > 0
                    ):

                        while (
                            $row = $query->fetch_assoc()
                        ):

                    ?>

                    <tr>

                        <!-- NO -->
                        <td>

                            <?= $no++; ?>

                        </td>


                        <!-- NAMA PRODUK -->
                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $row['nama_produk']
                                ); ?>

                            </strong>

                        </td>


                        <!-- JENIS -->
                        <td>

                            <?php if (!empty($row['jenis'])): ?>

                                <span class="badge bg-light text-dark">

                                    <?= htmlspecialchars(
                                        $row['jenis']
                                    ); ?>

                                </span>

                            <?php else: ?>

                                <span class="text-muted">
                                    -
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- HARGA -->
                        <td>

                            Rp
                            <?= number_format(
                                $row['price'],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </td>


                        <!-- STOCK -->
                        <td>

                            <?php if ($row['stock'] > 0): ?>

                                <span class="badge bg-light text-dark">

                                    <?= $row['stock']; ?>

                                </span>

                            <?php else: ?>

                                <span
                                    class="badge"
                                    style="
                                        background:#fee2e2;
                                        color:#dc2626;
                                    "
                                >
                                    Habis
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- AKSI -->
                        <td>

                            <a
                                href="edit.php?id=<?= $row['id_produk']; ?>"
                                class="btn btn-sm btn-outline-primary"
                                title="Edit Produk"
                            >

                                <i class="bi bi-pencil"></i>

                            </a>


                            <a
                                href="hapus.php?id=<?= $row['id_produk']; ?>"
                                class="btn btn-sm btn-outline-danger"
                                title="Hapus Produk"
                                onclick="return confirm('Yakin ingin menghapus produk ini?')"
                            >

                                <i class="bi bi-trash"></i>

                            </a>

                        </td>

                    </tr>


                    <?php

                        endwhile;

                    else:

                    ?>

                    <!-- DATA KOSONG -->
                    <tr>

                        <td
                            colspan="6"
                            class="text-center py-5"
                        >

                            <i
                                class="bi bi-box-seam"
                                style="
                                    font-size:45px;
                                    color:#9ca3af;
                                "
                            ></i>


                            <p class="text-muted mt-3 mb-0">

                                Belum ada data produk.

                            </p>

                            <a
                                href="tambah.php"
                                class="btn btn-primary-custom mt-3"
                            >

                                <i class="bi bi-plus-circle me-2"></i>

                                Tambah Produk

                            </a>

                        </td>

                    </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>


<?php include "../layouts/footer.php"; ?>