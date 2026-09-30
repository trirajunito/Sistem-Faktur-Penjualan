<?php

require_once "../config/database.php";

$pageTitle = "Data Customer";
$activeMenu = "customer";
$basePath = "../";

$query = mysqli_query(
    $conn,
    "SELECT * FROM customer ORDER BY id_customer DESC"
);

include "../layouts/header.php";
include "../layouts/navigation.php";
include "../layouts/sidebar.php";

?>

<main class="main-content">

    <div class="content-card">

        <div class="card-header-custom">

            <div>
                <h5>Data Customer</h5>
                <small class="text-muted">
                    Kelola data pelanggan
                </small>
            </div>

            <div>

                <a href="cetak.php"
                   target="_blank"
                   class="btn btn-outline-success me-2">

                    <i class="bi bi-printer me-1"></i>
                    Cetak

                </a>

                <a href="tambah.php"
                   class="btn btn-primary-custom">

                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Customer

                </a>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table table-modern">

                <thead>

                <tr>

                    <th>No</th>
                    <th>Nama Customer</th>
                    <th>Perusahaan</th>
                    <th>Alamat</th>
                    <th width="130">Aksi</th>

                </tr>

                </thead>

                <tbody>

                <?php
                $no = 1;

                while ($row = mysqli_fetch_assoc($query)):
                ?>

                <tr>

                    <td><?= $no++; ?></td>

                    <td>
                        <strong>
                            <?= htmlspecialchars($row['nama_customer']); ?>
                        </strong>
                    </td>

                    <td>
                        <span class="badge-info">
                            <?= htmlspecialchars($row['perusahaan_cust']); ?>
                        </span>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['alamat']); ?>
                    </td>

                    <td>

                        <a href="cetak.php?id=<?= $row['id_customer']; ?>"
                           target="_blank"
                           class="btn-action btn-print">

                            <i class="bi bi-printer"></i>

                        </a>

                        <a href="edit.php?id=<?= $row['id_customer']; ?>"
                           class="btn-action btn-edit">

                            <i class="bi bi-pencil"></i>

                        </a>

                        <a href="hapus.php?id=<?= $row['id_customer']; ?>"
                           class="btn-action btn-delete"
                           onclick="return confirm('Yakin ingin menghapus customer ini?')">

                            <i class="bi bi-trash"></i>

                        </a>

                    </td>

                </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>