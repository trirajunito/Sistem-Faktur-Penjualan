<?php

require_once "../config/database.php";

$pageTitle = "Edit Detail Faktur";
$activeMenu = "detail-faktur";
$basePath = "../";


/*
|--------------------------------------------------------------------------
| Ambil Parameter
|--------------------------------------------------------------------------
*/

$no_faktur = isset($_GET['no_faktur'])
    ? $_GET['no_faktur']
    : '';

$id_produk = isset($_GET['id_produk'])
    ? (int) $_GET['id_produk']
    : 0;


/*
|--------------------------------------------------------------------------
| Ambil Data Detail Faktur
|--------------------------------------------------------------------------
*/

$query = mysqli_query($conn, "
    SELECT
        df.no_faktur,
        df.id_produk,
        df.qty,
        df.price,
        p.nama_produk
    FROM detail_faktur df
    JOIN produk p
        ON df.id_produk = p.id_produk
    WHERE df.no_faktur = '$no_faktur'
    AND df.id_produk = '$id_produk'
");


$data = mysqli_fetch_assoc($query);


if (!$data) {

    header("Location: index.php");

    exit;

}


/*
|--------------------------------------------------------------------------
| Ambil Data Produk
|--------------------------------------------------------------------------
*/

$produkQuery = mysqli_query(
    $conn,
    "SELECT * FROM produk ORDER BY nama_produk ASC"
);


/*
|--------------------------------------------------------------------------
| Proses Update
|--------------------------------------------------------------------------
*/

if (isset($_POST['update'])) {

    $qty = (int) $_POST['qty'];

    $price = (float) $_POST['price'];


    if ($qty <= 0) {

        $error = "Qty harus lebih dari 0.";

    } elseif ($price < 0) {

        $error = "Harga tidak boleh kurang dari 0.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE detail_faktur
             SET qty = ?, price = ?
             WHERE no_faktur = ?
             AND id_produk = ?"
        );


        mysqli_stmt_bind_param(
            $stmt,
            "idsi",
            $qty,
            $price,
            $no_faktur,
            $id_produk
        );


        if (mysqli_stmt_execute($stmt)) {

            header("Location: index.php");

            exit;

        } else {

            $error = "Data gagal diperbarui.";

        }

    }

}

?>


<?php include "../layouts/header.php"; ?>

<?php include "../layouts/navigation.php"; ?>

<?php include "../layouts/sidebar.php"; ?>


<main class="main-content">

    <div class="mb-4">

        <h3 class="mb-1">
            Edit Detail Faktur
        </h3>

        <p class="text-muted mb-0">
            Mengubah jumlah dan harga produk pada faktur.
        </p>

    </div>


    <div class="content-card">

        <?php if (isset($error)): ?>

            <div class="alert alert-danger">

                <i class="bi bi-exclamation-circle me-2"></i>

                <?= htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <!-- NO FAKTUR -->

            <div class="mb-3">

                <label class="form-label">
                    No Faktur
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="<?= htmlspecialchars($data['no_faktur']); ?>"
                    readonly
                >

            </div>


            <!-- PRODUK -->

            <div class="mb-3">

                <label class="form-label">
                    Produk
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="<?= htmlspecialchars($data['nama_produk']); ?>"
                    readonly
                >

            </div>


            <!-- QTY -->

            <div class="mb-3">

                <label class="form-label">
                    Quantity
                </label>

                <input
                    type="number"
                    name="qty"
                    class="form-control"
                    min="1"
                    value="<?= htmlspecialchars($data['qty']); ?>"
                    required
                >

            </div>


            <!-- PRICE -->

            <div class="mb-4">

                <label class="form-label">
                    Harga
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