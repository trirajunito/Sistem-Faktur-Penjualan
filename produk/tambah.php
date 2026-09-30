<?php

require_once "../classes/Database.php";
require_once "../classes/Produk.php";


//db

$database = new Database();

$conn = $database->getConnection();


//objek

$produk = new Produk($conn);


//atur halaman

$pageTitle = "Tambah Produk";
$activeMenu = "produk";
$basePath = "../";


$error = "";


//proses simpan

if (isset($_POST['simpan'])) {

    $nama_produk = trim($_POST['nama_produk']);

    $price = (float) $_POST['price'];

    $jenis = trim($_POST['jenis']);

    $stock = (int) $_POST['stock'];


 //validasi

    if ($nama_produk == "") {

        $error = "Nama produk wajib diisi.";

    } elseif ($price < 0) {

        $error = "Harga tidak boleh kurang dari 0.";

    } elseif ($stock < 0) {

        $error = "Stock tidak boleh kurang dari 0.";

    } else {

        $simpan = $produk->tambah(
            $nama_produk,
            $price,
            $jenis,
            $stock
        );


        if ($simpan) {

            header("Location: index.php");

            exit;

        } else {

            $error = "Data produk gagal disimpan.";

        }

    }

}

?>

<?php include "../layouts/header.php"; ?>

<?php include "../layouts/navigation.php"; ?>

<?php include "../layouts/sidebar.php"; ?>


<main class="main-content">


    <!-- HEADER -->
    <div class="mb-4">

        <h3 class="mb-1">
            Tambah Produk
        </h3>

        <p class="text-muted mb-0">
            Tambahkan produk baru ke dalam sistem.
        </p>

    </div>


    <!-- FORM CARD -->
    <div class="content-card">

        <?php if ($error != ""): ?>

            <div class="alert alert-danger">

                <i class="bi bi-exclamation-circle me-2"></i>

                <?= htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action=""
        >


            <!-- NAMA PRODUK -->
            <div class="mb-3">

                <label class="form-label">

                    Nama Produk

                    <span class="text-danger">
                        *
                    </span>

                </label>


                <input
                    type="text"
                    name="nama_produk"
                    class="form-control"
                    placeholder="Masukkan nama produk"
                    value="<?= isset($_POST['nama_produk']) ? htmlspecialchars($_POST['nama_produk']) : ''; ?>"
                    required
                >

            </div>


            <!-- JENIS -->
            <div class="mb-3">

                <label class="form-label">
                    Jenis Produk
                </label>


                <input
                    type="text"
                    name="jenis"
                    class="form-control"
                    placeholder="Contoh: Elektronik, Aksesoris"
                    value="<?= isset($_POST['jenis']) ? htmlspecialchars($_POST['jenis']) : ''; ?>"
                >

            </div>


            <!-- HARGA -->
            <div class="mb-3">

                <label class="form-label">

                    Harga

                    <span class="text-danger">
                        *
                    </span>

                </label>


                <input
                    type="number"
                    name="price"
                    class="form-control"
                    placeholder="Masukkan harga"
                    min="0"
                    step="0.01"
                    value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>"
                    required
                >

            </div>


            <!-- STOCK -->
            <div class="mb-4">

                <label class="form-label">

                    Stock

                    <span class="text-danger">
                        *
                    </span>

                </label>


                <input
                    type="number"
                    name="stock"
                    class="form-control"
                    placeholder="Masukkan jumlah stock"
                    min="0"
                    value="<?= isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : ''; ?>"
                    required
                >

            </div>


            <!-- BUTTON -->
            <div class="d-flex gap-2">

                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-primary-custom"
                >

                    <i class="bi bi-save me-2"></i>

                    Simpan Produk

                </button>


                <a
                    href="index.php"
                    class="btn btn-outline-secondary"
                >

                    <i class="bi bi-arrow-left me-2"></i>

                    Kembali

                </a>

            </div>


        </form>

    </div>

</main>


<?php include "../layouts/footer.php"; ?>