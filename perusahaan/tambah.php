<?php

require_once "../config/database.php";

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
        "INSERT INTO perusahaan
        (nama_perusahaan, alamat, no_telp, tax)
        VALUES
        ('$nama', '$alamat', '$no_telp', '$tax')"
    );

    header("Location: index.php");
    exit;
}

$pageTitle = "Tambah Perusahaan";
$activeMenu = "perusahaan";
$basePath = "../";

include "../layouts/header.php";
include "../layouts/navigation.php";
include "../layouts/sidebar.php";

?>

<main class="main-content">

    <div class="content-card">

        <div class="card-header-custom">

            <div>
                <h5>Tambah Perusahaan</h5>
                <small class="text-muted">
                    Masukkan data perusahaan baru
                </small>
            </div>

        </div>


        <form method="POST">

            <div class="row g-3">

                <div class="col-md-6">

                    <label class="form-label">
                        Nama Perusahaan
                    </label>

                    <input type="text"
                           name="nama_perusahaan"
                           class="form-control"
                           required>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        No. Telepon
                    </label>

                    <input type="text"
                           name="no_telp"
                           class="form-control">

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Tax / NPWP
                    </label>

                    <input type="text"
                           name="tax"
                           class="form-control">

                </div>


                <div class="col-12">

                    <label class="form-label">
                        Alamat
                    </label>

                    <textarea name="alamat"
                              class="form-control"
                              rows="4"></textarea>

                </div>


                <div class="col-12 mt-4">

                    <a href="index.php"
                       class="btn btn-light">

                        Kembali

                    </a>

                    <button type="submit"
                            class="btn btn-primary-custom">

                        <i class="bi bi-save me-1"></i>
                        Simpan

                    </button>

                </div>

            </div>

        </form>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>