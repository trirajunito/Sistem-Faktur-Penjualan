<?php

require_once "../config/database.php";

$pageTitle = "Data Perusahaan";
$activeMenu = "perusahaan";
$basePath = "../";

$query = mysqli_query(
    $conn,
    "SELECT * FROM perusahaan ORDER BY id_perusahaan DESC"
);

include "../layouts/header.php";
include "../layouts/navigation.php";
include "../layouts/sidebar.php";

?>

<main class="main-content">

    <div class="content-card">

        <div class="card-header-custom">

            <div>
                <h5>Data Perusahaan</h5>
                <small class="text-muted">
                    Kelola data perusahaan
                </small>
            </div>

            <a href="tambah.php"
               class="btn btn-primary-custom">

                <i class="bi bi-plus-lg me-1"></i>
                Tambah Perusahaan

            </a>

        </div>


        <div class="table-responsive">

            <table class="table table-modern">

                <thead>

                <tr>

                    <th>No</th>
                    <th>Nama Perusahaan</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th>Tax</th>
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
                            <?= htmlspecialchars($row['nama_perusahaan']); ?>
                        </strong>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['alamat']); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['no_telp']); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['tax']); ?>
                    </td>

                    <td>

                        <a href="edit.php?id=<?= $row['id_perusahaan']; ?>"
                           class="btn-action btn-edit">

                            <i class="bi bi-pencil"></i>

                        </a>

                        <a href="hapus.php?id=<?= $row['id_perusahaan']; ?>"
                           class="btn-action btn-delete"
                           onclick="return confirm('Yakin ingin menghapus data ini?')">

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