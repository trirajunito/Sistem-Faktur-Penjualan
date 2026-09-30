<?php

require_once "../config/database.php";

$id = intval($_GET['id'] ?? 0);

$result = mysqli_query(
    $conn,
    "SELECT * FROM customer WHERE id_customer = $id"
);

$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data customer tidak ditemukan.");
}

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
        "UPDATE customer SET
        nama_customer='$nama',
        perusahaan_cust='$perusahaan',
        alamat='$alamat'
        WHERE id_customer=$id"
    );

    header("Location: index.php");
    exit;
}

$pageTitle = "Ubah Customer";
$activeMenu = "customer";
$basePath = "../";

include "../layouts/header.php";
include "../layouts/navigation.php";
include "../layouts/sidebar.php";

?>

<main class="main-content">

    <div class="content-card">

        <h5 class="mb-4">
            Ubah Data Customer
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
                           value="<?= htmlspecialchars($data['nama_customer']); ?>"
                           required>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Perusahaan Customer
                    </label>

                    <input type="text"
                           name="perusahaan_cust"
                           class="form-control"
                           value="<?= htmlspecialchars($data['perusahaan_cust']); ?>">

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