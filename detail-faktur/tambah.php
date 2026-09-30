<?php

require_once "../config/database.php";

$pageTitle = "Tambah Detail Faktur";
$activeMenu = "detail-faktur";
$basePath = "../";

$error = "";


/*
|--------------------------------------------------------------------------
| Ambil Data Faktur
|--------------------------------------------------------------------------
*/

$fakturQuery = mysqli_query(
    $conn,
    "SELECT no_faktur
     FROM faktur
     ORDER BY tgl_faktur DESC"
);


/*
|--------------------------------------------------------------------------
| Ambil Data Produk
|--------------------------------------------------------------------------
*/

$produkQuery = mysqli_query(
    $conn,
    "SELECT id_produk, nama_produk, price, stock
     FROM produk
     ORDER BY nama_produk ASC"
);


/*
|--------------------------------------------------------------------------
| Proses Simpan
|--------------------------------------------------------------------------
*/

if (isset($_POST['simpan'])) {

    $no_faktur = $_POST['no_faktur'];
    $id_produk = (int) $_POST['id_produk'];
    $qty = (int) $_POST['qty'];
    $price = (float) $_POST['price'];


    /*
    |--------------------------------------------------------------------------
    | Validasi
    |--------------------------------------------------------------------------
    */

    if ($no_faktur == '') {

        $error = "Faktur wajib dipilih.";

    } elseif ($id_produk <= 0) {

        $error = "Produk wajib dipilih.";

    } elseif ($qty <= 0) {

        $error = "Quantity harus lebih dari 0.";

    } elseif ($price < 0) {

        $error = "Harga tidak boleh kurang dari 0.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | Cek Apakah Produk Sudah Ada Dalam Faktur
        |--------------------------------------------------------------------------
        */

        $cek = mysqli_prepare(
            $conn,
            "SELECT no_faktur
             FROM detail_faktur
             WHERE no_faktur = ?
             AND id_produk = ?"
        );


        mysqli_stmt_bind_param(
            $cek,
            "si",
            $no_faktur,
            $id_produk
        );


        mysqli_stmt_execute($cek);

        $hasilCek = mysqli_stmt_get_result($cek);


        if (mysqli_num_rows($hasilCek) > 0) {

            $error = "Produk tersebut sudah ada di faktur.";

        } else {


            /*
            |--------------------------------------------------------------------------
            | Simpan Detail Faktur
            |--------------------------------------------------------------------------
            */

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO detail_faktur
                (no_faktur, id_produk, qty, price)
                VALUES (?, ?, ?, ?)"
            );


            mysqli_stmt_bind_param(
                $stmt,
                "siid",
                $no_faktur,
                $id_produk,
                $qty,
                $price
            );


            if (mysqli_stmt_execute($stmt)) {

                header("Location: index.php");

                exit;

            } else {

                $error = "Data detail faktur gagal disimpan.";

            }

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
            Tambah Detail Faktur
        </h3>

        <p class="text-muted mb-0">
            Tambahkan produk ke dalam faktur penjualan.
        </p>

    </div>


    <!-- FORM -->

    <div class="content-card">


        <?php if ($error != ""): ?>

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

                    <span class="text-danger">
                        *
                    </span>

                </label>


                <select
                    name="no_faktur"
                    class="form-control"
                    required
                >

                    <option value="">
                        -- Pilih Faktur --
                    </option>


                    <?php while ($faktur = mysqli_fetch_assoc($fakturQuery)): ?>

                        <option
                            value="<?= htmlspecialchars($faktur['no_faktur']); ?>"
                            <?= (
                                isset($_POST['no_faktur']) &&
                                $_POST['no_faktur'] == $faktur['no_faktur']
                            )
                                ? 'selected'
                                : ''
                            ?>
                        >

                            <?= htmlspecialchars($faktur['no_faktur']); ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <!-- PRODUK -->

            <div class="mb-3">

                <label class="form-label">

                    Produk

                    <span class="text-danger">
                        *
                    </span>

                </label>


                <select
                    name="id_produk"
                    id="id_produk"
                    class="form-control"
                    required
                >

                    <option value="">
                        -- Pilih Produk --
                    </option>


                    <?php while ($produk = mysqli_fetch_assoc($produkQuery)): ?>

                        <option
                            value="<?= $produk['id_produk']; ?>"
                            data-price="<?= $produk['price']; ?>"
                            <?= (
                                isset($_POST['id_produk']) &&
                                $_POST['id_produk'] == $produk['id_produk']
                            )
                                ? 'selected'
                                : ''
                            ?>
                        >

                            <?= htmlspecialchars($produk['nama_produk']); ?>

                            -
                            Rp <?= number_format(
                                $produk['price'],
                                0,
                                ',',
                                '.'
                            ); ?>

                            (Stock: <?= $produk['stock']; ?>)

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <!-- QTY -->

            <div class="mb-3">

                <label class="form-label">

                    Quantity

                    <span class="text-danger">
                        *
                    </span>

                </label>


                <input
                    type="number"
                    name="qty"
                    class="form-control"
                    min="1"
                    value="<?= isset($_POST['qty']) ? htmlspecialchars($_POST['qty']) : '1'; ?>"
                    required
                >

            </div>


            <!-- HARGA -->

            <div class="mb-4">

                <label class="form-label">

                    Harga

                    <span class="text-danger">
                        *
                    </span>

                </label>


                <input
                    type="number"
                    name="price"
                    id="price"
                    class="form-control"
                    min="0"
                    step="0.01"
                    value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>"
                    required
                >

                <small class="text-muted">

                    Harga akan otomatis mengikuti harga produk.

                </small>

            </div>


            <!-- BUTTON -->

            <div class="d-flex gap-2">


                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-primary-custom"
                >

                    <i class="bi bi-save me-2"></i>

                    Simpan Detail Faktur

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


<script>

document
    .getElementById("id_produk")
    .addEventListener("change", function () {

        const selectedOption =
            this.options[this.selectedIndex];

        const price =
            selectedOption.getAttribute("data-price");


        if (price) {

            document.getElementById("price").value = price;

        } else {

            document.getElementById("price").value = "";

        }

    });

</script>


<?php include "../layouts/footer.php"; ?>