<?php

require_once "../config/database.php";

$id = intval($_GET['id'] ?? 0);

$result = mysqli_query(
    $conn,
    "SELECT * FROM perusahaan WHERE id_perusahaan = $id"
);

$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data perusahaan tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = mysqli_real_escape_string(
        $conn,
        $_POST['nama_perusahaan']
    );

    $alamat = mysqli_real_escape_string(
        $conn,
        $_POST['alamat']
    );

    $no_telp = mysqli_real_escape_string(
        $conn,
        $_POST['no_telp']
    );

    $tax = mysqli_real_escape_string(
        $conn,
        $_POST['tax']
    );

    mysqli_query(
        $conn,
        "UPDATE perusahaan SET
        nama_perusahaan='$nama',
        alamat='$alamat',
        no_telp='$no_telp',
        tax='$tax'
        WHERE id_perusahaan=$id"
    );

    header("Location: index.php");
    exit;
}

$pageTitle = "Ubah Perusahaan";
$activeMenu = "perusahaan";
$basePath = "../";

include "../layouts/header.php";
include "../layouts/navigation.php";
include "../layouts/sidebar.php";

?>

<main class="main-content">

    <div class="content-card">

        <h5 class="mb-4">
            Ubah Data Perusahaan
        </h5>

        <form method="POST">

            <div class="row g-3">

                <div class="col-md-6">

                    <label class="form-label">
                        Nama Perusahaan
                    </label>

                    <input type="text"
                           name="nama_perusahaan"
                           class="form-control"
                           value="<?= htmlspecialchars($data['nama_perusahaan']); ?>"
                           required>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        No. Telepon
                    </label>

                    <input type="text"
                           name="no_telp"
                           class="form-control"
                           value="<?= htmlspecialchars($data['no_telp']); ?>">

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Tax / NPWP
                    </label>

                    <input type="text"
                           name="tax"
                           class="form-control"
                           value="<?= htmlspecialchars($data['tax']); ?>">

                </div>


                <div class="col-12">

                    <label class="form-label">
                        Alamat
                    </label>

                    <textarea name="alamat"
                              class="form-control"
                              rows="4"><?= htmlspecialchars($data['alamat']); ?></textarea>

                </div>


                <div class="col-12 mt-4">

                    <a href="index.php"
                       class="btn btn-light">

                        Kembali

                    </a>

                    <button type="submit"
                            class="btn btn-primary-custom">

                        <i class="bi bi-save me-1"></i>
                        Update

                    </button>

                </div>

            </div>

        </form>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>