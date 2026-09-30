<?php

require_once "../config/database.php";

$pageTitle = "Detail Faktur";
$activeMenu = "detail-faktur";
$basePath = "../";


/*
|--------------------------------------------------------------------------
| Ambil Data Detail Faktur
|--------------------------------------------------------------------------
*/

$query = mysqli_query(
    $conn,
    "SELECT
        df.no_faktur,
        df.id_produk,
        df.qty,
        df.price,
        p.nama_produk,
        (df.qty * df.price) AS subtotal
    FROM detail_faktur AS df
    INNER JOIN produk AS p
        ON df.id_produk = p.id_produk
    ORDER BY df.no_faktur DESC"
);


/*
|--------------------------------------------------------------------------
| Cek Query
|--------------------------------------------------------------------------
*/

if (!$query) {

    die(
        "Terjadi kesalahan pada database: "
        . mysqli_error($conn)
    );

}

?>


<?php include "../layouts/header.php"; ?>

<?php include "../layouts/navigation.php"; ?>

<?php include "../layouts/sidebar.php"; ?>


<main class="main-content">


    <!-- =====================================================
         HEADER HALAMAN
    ====================================================== -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Detail Faktur
            </h3>

            <p class="text-muted mb-0">
                Daftar produk pada setiap faktur penjualan.
            </p>

        </div>


        <!-- TOMBOL TAMBAH -->

        <a
            href="tambah.php"
            class="btn btn-primary-custom"
        >

            <i class="bi bi-plus-circle me-2"></i>

            Tambah Detail

        </a>

    </div>



    <!-- =====================================================
         CARD DATA
    ====================================================== -->

    <div class="content-card">


        <!-- HEADER CARD -->

        <div class="card-header-custom">

            <div>

                <h5 class="mb-1">
                    Daftar Detail Faktur
                </h5>

                <small class="text-muted">
                    Rincian produk setiap transaksi
                </small>

            </div>

        </div>



        <!-- =================================================
             TABLE
        ================================================== -->

        <div class="table-responsive">

            <table class="table align-middle">


                <!-- HEADER TABLE -->

                <thead>

                    <tr>

                        <th style="width: 60px;">
                            No
                        </th>

                        <th>
                            No Faktur
                        </th>

                        <th>
                            Produk
                        </th>

                        <th>
                            Qty
                        </th>

                        <th>
                            Harga
                        </th>

                        <th>
                            Subtotal
                        </th>

                        <th style="width: 120px;">
                            Aksi
                        </th>

                    </tr>

                </thead>



                <!-- BODY TABLE -->

                <tbody>


                    <?php

                    $no = 1;


                    if (
                        mysqli_num_rows($query) > 0
                    ):


                        while (
                            $row = mysqli_fetch_assoc($query)
                        ):

                    ?>


                    <tr>


                        <!-- NOMOR -->

                        <td>

                            <?= $no++; ?>

                        </td>



                        <!-- NO FAKTUR -->

                        <td>

                            <span
                                class="badge bg-light text-dark"
                            >

                                <?= htmlspecialchars(
                                    $row['no_faktur']
                                ); ?>

                            </span>

                        </td>



                        <!-- PRODUK -->

                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $row['nama_produk']
                                ); ?>

                            </strong>

                        </td>



                        <!-- QTY -->

                        <td>

                            <?= (int) $row['qty']; ?>

                        </td>



                        <!-- HARGA -->

                        <td>

                            Rp
                            <?= number_format(
                                (float) $row['price'],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </td>



                        <!-- SUBTOTAL -->

                        <td>

                            <strong>

                                Rp
                                <?= number_format(
                                    (float) $row['subtotal'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>

                            </strong>

                        </td>



                        <!-- AKSI -->

                        <td>


                            <!-- EDIT -->

                            <a
                                href="edit.php?no_faktur=<?= urlencode($row['no_faktur']); ?>&id_produk=<?= (int) $row['id_produk']; ?>"
                                class="btn btn-sm btn-outline-primary"
                                title="Edit"
                            >

                                <i class="bi bi-pencil"></i>

                            </a>



                            <!-- HAPUS -->

                            <a
                                href="hapus.php?no_faktur=<?= urlencode($row['no_faktur']); ?>&id_produk=<?= (int) $row['id_produk']; ?>"
                                class="btn btn-sm btn-outline-danger"
                                title="Hapus"
                                onclick="return confirm('Yakin ingin menghapus detail faktur ini?')"
                            >

                                <i class="bi bi-trash"></i>

                            </a>


                        </td>


                    </tr>


                    <?php

                        endwhile;


                    else:

                    ?>


                    <!-- =================================================
                         DATA KOSONG
                    ================================================== -->

                    <tr>

                        <td
                            colspan="7"
                            class="text-center py-5"
                        >


                            <i
                                class="bi bi-receipt"
                                style="
                                    font-size: 40px;
                                    color: #9ca3af;
                                "
                            ></i>


                            <p class="text-muted mt-3 mb-3">

                                Belum ada detail faktur.

                            </p>


                            <a
                                href="tambah.php"
                                class="btn btn-primary-custom"
                            >

                                <i class="bi bi-plus-circle me-2"></i>

                                Tambah Detail

                            </a>


                        </td>

                    </tr>


                    <?php

                    endif;

                    ?>


                </tbody>

            </table>

        </div>

    </div>

</main>


<?php include "../layouts/footer.php"; ?>