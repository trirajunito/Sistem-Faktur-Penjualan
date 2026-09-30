<?php

require_once "../classes/Database.php";
require_once "../classes/Produk.php";


//databse

$database = new Database();

$conn = $database->getConnection();


//objek produk

$produk = new Produk($conn);


//pengaturan halaman

$pageTitle = "Edit Produk";
$activeMenu = "produk";
$basePath = "../";


$error = "";


//ambil id

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


//ambil data produk

$data = $produk->getById($id);


if (!$data) {

    header("Location: index.php");

    exit;

}


//proses update

if (isset($_POST['update'])) {

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

        $update = $produk->update(
            $id,
            $nama_produk,
            $price,
            $jenis,
            $stock
        );


        if ($update) {

            header("Location: index.php");

            exit;

        } else {

            $error = "Data produk gagal diperbarui.";

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
            Edit Produk
        </h3>

        <p class="text-muted mb-0">
            Perbarui informasi produk.
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
                    value="<?= htmlspecialchars($data['nama_produk']); ?>"
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
                    value="<?= htmlspecialchars($data['jenis']); ?>"
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
                    min="0"
                    step="0.01"
                    value="<?= htmlspecialchars($data['price']); ?>"
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
                    min="0"
                    value="<?= htmlspecialchars($data['stock']); ?>"
                    required
                >

            </div>


            <!-- BUTTON -->
            <div class="d-flex gap-2">

                <button
                    type="submit"
                    name="update"
                    class="btn btn-primary-custom"
                >

                    <i class="bi bi-save me-2"></i>

                    Simpan Perubahan

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