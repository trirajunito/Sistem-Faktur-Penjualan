<?php

require_once "../config/database.php";

$pageTitle = "Data Penjualan";
$activeMenu = "penjualan";
$basePath = "../";


/* =========================================================
   AMBIL DATA PENJUALAN
   ========================================================= */

$query = mysqli_query(
    $conn,
    "SELECT
        f.no_faktur,
        f.tgl_faktur,
        f.due_date,
        f.metode_bayar,
        f.ppn,
        f.grand_total,
        f.username,
        f.id_customer,
        f.id_perusahaan,

        c.nama_customer,
        c.perusahaan_cust,

        p.nama_perusahaan

    FROM faktur AS f

    INNER JOIN customer AS c
        ON f.id_customer = c.id_customer

    INNER JOIN perusahaan AS p
        ON f.id_perusahaan = p.id_perusahaan

    ORDER BY
        f.tgl_faktur DESC,
        f.no_faktur DESC"
);


/* =========================================================
   CEK QUERY
   ========================================================= */

if (!$query) {

    die(
        "Query Data Penjualan Error: "
        . mysqli_error($conn)
    );

}


/* =========================================================
   LAYOUT
   ========================================================= */

include "../layouts/header.php";
include "../layouts/navigation.php";
include "../layouts/sidebar.php";

?>


<main class="main-content">


    <!-- =====================================================
         CARD DATA PENJUALAN
    ====================================================== -->

    <div class="content-card">


        <!-- =================================================
             HEADER
        ================================================== -->

        <div class="card-header-custom">

            <div>

                <h5>
                    Data Penjualan
                </h5>

                <small class="text-muted">
                    Kelola transaksi penjualan dan faktur
                </small>

            </div>


            <a
                href="tambah.php"
                class="btn btn-primary-custom"
            >

                <i class="bi bi-plus-lg me-1"></i>

                Tambah Penjualan

            </a>

        </div>



        <!-- =================================================
             TABLE
        ================================================== -->

        <div class="table-responsive">

            <table class="table table-modern">


                <thead>

                    <tr>

                        <th>
                            NO
                        </th>

                        <th>
                            NO FAKTUR
                        </th>

                        <th>
                            TANGGAL
                        </th>

                        <th>
                            CUSTOMER
                        </th>

                        <th>
                            PERUSAHAAN
                        </th>

                        <th>
                            PEMBAYARAN
                        </th>

                        <th>
                            PPN
                        </th>

                        <th>
                            GRAND TOTAL
                        </th>

                        <th>
                            AKSI
                        </th>

                    </tr>

                </thead>


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


                        <!-- =================================
                             NO
                        ================================== -->

                        <td>

                            <?= $no++; ?>

                        </td>



                        <!-- =================================
                             NO FAKTUR
                        ================================== -->

                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $row['no_faktur']
                                ); ?>

                            </strong>

                        </td>



                        <!-- =================================
                             TANGGAL
                        ================================== -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $row['tgl_faktur']
                                )
                            ) {

                                echo date(
                                    'd/m/Y',
                                    strtotime(
                                        $row['tgl_faktur']
                                    )
                                );

                            } else {

                                echo "-";

                            }

                            ?>

                        </td>



                        <!-- =================================
                             CUSTOMER
                        ================================== -->

                        <td>

                            <?= htmlspecialchars(
                                $row['nama_customer']
                            ); ?>

                        </td>



                        <!-- =================================
                             PERUSAHAAN
                        ================================== -->

                        <td>

                            <?= htmlspecialchars(
                                $row['nama_perusahaan']
                            ); ?>

                        </td>



                        <!-- =================================
                             PEMBAYARAN
                        ================================== -->

                        <td>

                            <span class="badge-info">

                                <?= htmlspecialchars(
                                    $row['metode_bayar']
                                ); ?>

                            </span>

                        </td>



                        <!-- =================================
                             PPN
                        ================================== -->

                        <td>

                            <strong>

                                Rp

                                <?= number_format(
                                    (float) $row['ppn'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>

                            </strong>

                        </td>



                        <!-- =================================
                             GRAND TOTAL
                        ================================== -->

                        <td>

                            <strong>

                                Rp

                                <?= number_format(
                                    (float) $row['grand_total'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>

                            </strong>

                        </td>



                        <!-- =================================
                             AKSI
                        ================================== -->

                        <td>

                            <div
                                style="
                                    display:flex;
                                    gap:8px;
                                    align-items:center;
                                    white-space:nowrap;
                                "
                            >


                                <!-- CETAK -->

                                <a
                                    href="cetak.php?id=<?= urlencode($row['no_faktur']); ?>"
                                    target="_blank"
                                    class="btn-action btn-print"
                                    title="Cetak Faktur"
                                >

                                    <i class="bi bi-printer"></i>

                                </a>



                                <!-- EDIT -->

                                <a
                                    href="edit.php?id=<?= urlencode($row['no_faktur']); ?>"
                                    class="btn-action btn-edit"
                                    title="Edit Penjualan"
                                >

                                    <i class="bi bi-pencil"></i>

                                </a>



                                <!-- HAPUS -->

                                <a
                                    href="hapus.php?id=<?= urlencode($row['no_faktur']); ?>"
                                    class="btn-action btn-delete"
                                    title="Hapus Penjualan"
                                    onclick="return confirm('Yakin ingin menghapus transaksi ini?')"
                                >

                                    <i class="bi bi-trash"></i>

                                </a>


                            </div>

                        </td>


                    </tr>


                <?php

                    endwhile;

                else:

                ?>


                    <!-- =================================
                         DATA KOSONG
                    ================================== -->

                    <tr>

                        <td
                            colspan="9"
                            class="text-center py-5"
                        >

                            <i
                                class="bi bi-receipt"
                                style="
                                    font-size:45px;
                                    color:#9ca3af;
                                "
                            ></i>


                            <p class="text-muted mt-3">

                                Belum ada data penjualan.

                            </p>


                            <a
                                href="tambah.php"
                                class="btn btn-primary-custom"
                            >

                                <i class="bi bi-plus-circle me-2"></i>

                                Tambah Penjualan

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