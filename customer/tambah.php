<?php

require_once "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = mysqli_real_escape_string(
        $conn,
        $_POST['nama_customer']
    );

    $perusahaan = mysqli_real_escape_string(
        $conn,
        $_POST['perusahaan_cust']
    );

    $alamat = mysqli_real_escape_string(
        $conn,
        $_POST['alamat']
    );

    mysqli_query(
        $conn,
        "INSERT INTO customer
        (nama_customer, perusahaan_cust, alamat)
        VALUES
        ('$nama', '$perusahaan', '$alamat')"
    );

    header("Location: index.php");
    exit;
}

$pageTitle = "Tambah Customer";
$activeMenu = "customer";
$basePath = "../";

include "../layouts/header.php";
include "../layouts/navigation.php";
include "../layouts/sidebar.php";

?>

<main class="main-content">

    <div class="content-card">

        <h5 class="mb-4">
            Tambah Customer
        </h5>

        <form method="POST">

            <div class="row g-3">

                <div class="col-md-6">

                    <label class="form-label">
                        Nama Customer
                    </label>

                    <input type="text"
                           name="nama_customer"
                           class="form-control"
                           required>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Perusahaan Customer
                    </label>

                    <input type="text"
                           name="perusahaan_cust"
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