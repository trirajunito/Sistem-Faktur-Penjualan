<?php

require_once "../config/database.php";

$pageTitle = "Edit Penjualan";
$activeMenu = "penjualan";
$basePath = "../";

$error = "";

$no_faktur = trim($_GET["id"] ?? "");

if ($no_faktur === "") {

    header("Location: index.php");
    exit;

}


/* =========================================================
   AMBIL FAKTUR
   ========================================================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM faktur
     WHERE no_faktur = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $no_faktur
);

mysqli_stmt_execute($stmt);

$fakturResult =
    mysqli_stmt_get_result($stmt);

$faktur =
    mysqli_fetch_assoc($fakturResult);


if (!$faktur) {

    die("Data faktur tidak ditemukan.");

}


/* =========================================================
   CUSTOMER
   ========================================================= */

$customerQuery = mysqli_query(
    $conn,
    "SELECT
        id_customer,
        nama_customer
     FROM customer
     ORDER BY nama_customer"
);


/* =========================================================
   PERUSAHAAN
   ========================================================= */

$perusahaanQuery = mysqli_query(
    $conn,
    "SELECT
        id_perusahaan,
        nama_perusahaan
     FROM perusahaan
     ORDER BY nama_perusahaan"
);


/* =========================================================
   PRODUK
   ========================================================= */

$produkQuery = mysqli_query(
    $conn,
    "SELECT
        id_produk,
        nama_produk,
        price,
        stock
     FROM produk
     ORDER BY nama_produk"
);


/* =========================================================
   DETAIL FAKTUR
   ========================================================= */

$stmtDetail = mysqli_prepare(
    $conn,
    "SELECT
        df.id_produk,
        df.qty,
        df.price,
        p.nama_produk,
        p.stock
     FROM detail_faktur df
     INNER JOIN produk p
        ON df.id_produk = p.id_produk
     WHERE df.no_faktur = ?"
);

mysqli_stmt_bind_param(
    $stmtDetail,
    "s",
    $no_faktur
);

mysqli_stmt_execute($stmtDetail);

$detailResult =
    mysqli_stmt_get_result($stmtDetail);

$details = [];

while (
    $detail =
    mysqli_fetch_assoc($detailResult)
) {

    $details[] = $detail;

}


/* =========================================================
   PROSES UPDATE
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $tgl_faktur =
        $_POST["tgl_faktur"] ?? "";

    $due_date =
        $_POST["due_date"] ?? "";

    $metode_bayar =
        trim(
            $_POST["metode_bayar"] ?? ""
        );

    $ppn =
        (float) (
            $_POST["ppn"] ?? 0
        );

    $username =
        trim(
            $_POST["username"] ?? "admin"
        );

    $id_customer =
        (int) (
            $_POST["id_customer"] ?? 0
        );

    $id_perusahaan =
        (int) (
            $_POST["id_perusahaan"] ?? 0
        );

    $produk =
        $_POST["id_produk"] ?? [];

    $qty =
        $_POST["qty"] ?? [];


    if (
        $tgl_faktur === "" ||
        $due_date === "" ||
        $metode_bayar === "" ||
        $id_customer <= 0 ||
        $id_perusahaan <= 0
    ) {

        $error =
            "Data faktur belum lengkap.";

    } elseif (
        empty($produk)
    ) {

        $error =
            "Minimal harus ada satu produk.";

    } else {


        /* ================================================
           MULAI TRANSACTION
        ================================================= */

        mysqli_begin_transaction($conn);


        try {


            /* ============================================
               KEMBALIKAN STOCK DETAIL LAMA
            ============================================= */

            $stmtOld =
                mysqli_prepare(
                    $conn,
                    "SELECT
                        id_produk,
                        qty
                     FROM detail_faktur
                     WHERE no_faktur = ?
                     FOR UPDATE"
                );


            mysqli_stmt_bind_param(
                $stmtOld,
                "s",
                $no_faktur
            );


            mysqli_stmt_execute(
                $stmtOld
            );


            $oldResult =
                mysqli_stmt_get_result(
                    $stmtOld
                );


            while (
                $old =
                mysqli_fetch_assoc(
                    $oldResult
                )
            ) {

                $stmtRestore =
                    mysqli_prepare(
                        $conn,
                        "UPDATE produk
                         SET stock = stock + ?
                         WHERE id_produk = ?"
                    );


                mysqli_stmt_bind_param(
                    $stmtRestore,
                    "ii",
                    $old["qty"],
                    $old["id_produk"]
                );


                if (
                    !mysqli_stmt_execute(
                        $stmtRestore
                    )
                ) {

                    throw new Exception(
                        mysqli_stmt_error(
                            $stmtRestore
                        )
                    );
                }

            }


            /* ============================================
               HAPUS DETAIL LAMA
            ============================================= */

            $stmtDeleteDetail =
                mysqli_prepare(
                    $conn,
                    "DELETE FROM detail_faktur
                     WHERE no_faktur = ?"
                );


            mysqli_stmt_bind_param(
                $stmtDeleteDetail,
                "s",
                $no_faktur
            );


            if (
                !mysqli_stmt_execute(
                    $stmtDeleteDetail
                )
            ) {

                throw new Exception(
                    mysqli_stmt_error(
                        $stmtDeleteDetail
                    )
                );
            }


            /* ============================================
               HITUNG PRODUK BARU
            ============================================= */

            $subtotalTotal = 0;

            $newDetails = [];


            for (
                $i = 0;
                $i < count($produk);
                $i++
            ) {

                $id_produk =
                    (int) $produk[$i];

                $jumlah =
                    (int) (
                        $qty[$i] ?? 0
                    );


                if (
                    $id_produk <= 0 ||
                    $jumlah <= 0
                ) {

                    continue;

                }


                /* ========================================
                   LOCK PRODUK
                ======================================== */

                $stmtProduk =
                    mysqli_prepare(
                        $conn,
                        "SELECT
                            id_produk,
                            nama_produk,
                            price,
                            stock
                         FROM produk
                         WHERE id_produk = ?
                         FOR UPDATE"
                    );


                mysqli_stmt_bind_param(
                    $stmtProduk,
                    "i",
                    $id_produk
                );


                mysqli_stmt_execute(
                    $stmtProduk
                );


                $resultProduk =
                    mysqli_stmt_get_result(
                        $stmtProduk
                    );


                $dataProduk =
                    mysqli_fetch_assoc(
                        $resultProduk
                    );


                if (!$dataProduk) {

                    throw new Exception(
                        "Produk tidak ditemukan."
                    );

                }


                /* ========================================
                   CEK STOCK
                ======================================== */

                if (
                    $jumlah >
                    (int) $dataProduk["stock"]
                ) {

                    throw new Exception(
                        "Stok produk "
                        . $dataProduk["nama_produk"]
                        . " tidak mencukupi. "
                        . "Stok tersedia: "
                        . $dataProduk["stock"]
                    );

                }


                $harga =
                    (float) $dataProduk["price"];


                $subtotal =
                    $jumlah * $harga;


                $subtotalTotal +=
                    $subtotal;


                $newDetails[] = [
                    "id_produk" =>
                        $id_produk,
                    "qty" =>
                        $jumlah,
                    "price" =>
                        $harga
                ];

            }


            if (
                empty($newDetails)
            ) {

                throw new Exception(
                    "Produk penjualan tidak valid."
                );

            }


            /* ============================================
               GRAND TOTAL
            ============================================= */

            $grandTotal =
                $subtotalTotal + $ppn;


            /* ============================================
               UPDATE FAKTUR
            ============================================= */

            $stmtUpdate =
                mysqli_prepare(
                    $conn,
                    "UPDATE faktur
                     SET
                        tgl_faktur = ?,
                        due_date = ?,
                        metode_bayar = ?,
                        ppn = ?,
                        grand_total = ?,
                        username = ?,
                        id_customer = ?,
                        id_perusahaan = ?
                     WHERE no_faktur = ?"
                );


            mysqli_stmt_bind_param(
                $stmtUpdate,
                "sssddsiis",
                $tgl_faktur,
                $due_date,
                $metode_bayar,
                $ppn,
                $grandTotal,
                $username,
                $id_customer,
                $id_perusahaan,
                $no_faktur
            );


            if (
                !mysqli_stmt_execute(
                    $stmtUpdate
                )
            ) {

                throw new Exception(
                    mysqli_stmt_error(
                        $stmtUpdate
                    )
                );

            }


            /* ============================================
               INSERT DETAIL BARU + KURANGI STOCK
            ============================================= */

            foreach (
                $newDetails as $detail
            ) {

                $id_produk =
                    $detail["id_produk"];

                $jumlah =
                    $detail["qty"];

                $harga =
                    $detail["price"];


                /* INSERT DETAIL */

                $stmtInsert =
                    mysqli_prepare(
                        $conn,
                        "INSERT INTO detail_faktur
                        (
                            no_faktur,
                            id_produk,
                            qty,
                            price
                        )
                        VALUES (?, ?, ?, ?)"
                    );


                mysqli_stmt_bind_param(
                    $stmtInsert,
                    "siid",
                    $no_faktur,
                    $id_produk,
                    $jumlah,
                    $harga
                );


                if (
                    !mysqli_stmt_execute(
                        $stmtInsert
                    )
                ) {

                    throw new Exception(
                        mysqli_stmt_error(
                            $stmtInsert
                        )
                    );

                }


                /* KURANGI STOCK */

                $stmtStock =
                    mysqli_prepare(
                        $conn,
                        "UPDATE produk
                         SET stock = stock - ?
                         WHERE id_produk = ?
                         AND stock >= ?"
                    );


                mysqli_stmt_bind_param(
                    $stmtStock,
                    "iii",
                    $jumlah,
                    $id_produk,
                    $jumlah
                );


                if (
                    !mysqli_stmt_execute(
                        $stmtStock
                    )
                ) {

                    throw new Exception(
                        mysqli_stmt_error(
                            $stmtStock
                        )
                    );

                }


                if (
                    mysqli_stmt_affected_rows(
                        $stmtStock
                    ) !== 1
                ) {

                    throw new Exception(
                        "Stok tidak mencukupi."
                    );

                }

            }


            /* ============================================
               COMMIT
            ============================================= */

            mysqli_commit($conn);


            header(
                "Location: index.php?updated=1"
            );

            exit;


        } catch (Exception $e) {

            mysqli_rollback($conn);

            $error =
                "Gagal mengubah transaksi: "
                . $e->getMessage();
        }

    }

}


/* =========================================================
   LAYOUT
   ========================================================= */

include "../layouts/header.php";
include "../layouts/navigation.php";
include "../layouts/sidebar.php";

?>


<main class="main-content">

    <div class="content-card">

        <div class="card-header-custom">

            <div>

                <h5>
                    Edit Penjualan
                </h5>

                <small class="text-muted">
                    <?= htmlspecialchars(
                        $no_faktur
                    ); ?>
                </small>

            </div>


            <a
                href="index.php"
                class="btn btn-outline-secondary"
            >

                <i class="bi bi-arrow-left"></i>

                Kembali

            </a>

        </div>


        <?php if ($error !== ""): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars(
                    $error
                ); ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <div class="row g-3 mb-4">


                <div class="col-md-4">

                    <label class="form-label">
                        No Faktur
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars(
                            $no_faktur
                        ); ?>"
                        readonly
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Tanggal Faktur
                    </label>

                    <input
                        type="date"
                        name="tgl_faktur"
                        class="form-control"
                        value="<?= htmlspecialchars(
                            $faktur[
                                "tgl_faktur"
                            ]
                        ); ?>"
                        required
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Due Date
                    </label>

                    <input
                        type="date"
                        name="due_date"
                        class="form-control"
                        value="<?= htmlspecialchars(
                            $faktur[
                                "due_date"
                            ]
                        ); ?>"
                        required
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Customer
                    </label>

                    <select
                        name="id_customer"
                        class="form-select"
                        required
                    >

                        <?php while (
                            $customer =
                            mysqli_fetch_assoc(
                                $customerQuery
                            )
                        ): ?>

                            <option
                                value="<?= $customer[
                                    "id_customer"
                                ]; ?>"
                                <?= (
                                    $customer[
                                        "id_customer"
                                    ] ==
                                    $faktur[
                                        "id_customer"
                                    ]
                                )
                                    ? "selected"
                                    : ""
                                ?>
                            >

                                <?= htmlspecialchars(
                                    $customer[
                                        "nama_customer"
                                    ]
                                ); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Perusahaan
                    </label>

                    <select
                        name="id_perusahaan"
                        class="form-select"
                        required
                    >

                        <?php while (
                            $perusahaan =
                            mysqli_fetch_assoc(
                                $perusahaanQuery
                            )
                        ): ?>

                            <option
                                value="<?= $perusahaan[
                                    "id_perusahaan"
                                ]; ?>"
                                <?= (
                                    $perusahaan[
                                        "id_perusahaan"
                                    ] ==
                                    $faktur[
                                        "id_perusahaan"
                                    ]
                                )
                                    ? "selected"
                                    : ""
                                ?>
                            >

                                <?= htmlspecialchars(
                                    $perusahaan[
                                        "nama_perusahaan"
                                    ]
                                ); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Metode Pembayaran
                    </label>

                    <select
                        name="metode_bayar"
                        class="form-select"
                        required
                    >

                        <?php

                        $metode =
                            [
                                "Transfer",
                                "Cash",
                                "Credit"
                            ];

                        foreach (
                            $metode as $item
                        ):

                        ?>

                            <option
                                value="<?= $item; ?>"
                                <?= (
                                    $faktur[
                                        "metode_bayar"
                                    ] === $item
                                )
                                    ? "selected"
                                    : ""
                                ?>
                            >

                                <?= $item; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        PPN
                    </label>

                    <input
                        type="number"
                        name="ppn"
                        id="ppn"
                        class="form-control"
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars(
                            $faktur["ppn"]
                        ); ?>"
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        value="<?= htmlspecialchars(
                            $faktur[
                                "username"
                            ]
                        ); ?>"
                    >

                </div>

            </div>


            <h5 class="mb-3">
                Produk
            </h5>


            <div class="table-responsive">

                <table class="table table-bordered">

                    <thead>

                        <tr>

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

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach (
                        $details as $detail
                    ): ?>

                        <tr>

                            <td>

                                <select
                                    name="id_produk[]"
                                    class="form-select"
                                    required
                                >

                                    <?php

                                    mysqli_data_seek(
                                        $produkQuery,
                                        0
                                    );

                                    while (
                                        $produk =
                                        mysqli_fetch_assoc(
                                            $produkQuery
                                        )
                                    ):

                                    ?>

                                        <option
                                            value="<?= $produk[
                                                "id_produk"
                                            ]; ?>"
                                            <?= (
                                                $produk[
                                                    "id_produk"
                                                ] ==
                                                $detail[
                                                    "id_produk"
                                                ]
                                            )
                                                ? "selected"
                                                : ""
                                            ?>
                                        >

                                            <?= htmlspecialchars(
                                                $produk[
                                                    "nama_produk"
                                                ]
                                            ); ?>

                                            -
                                            Stok:
                                            <?= $produk[
                                                "stock"
                                            ]; ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </td>


                            <td>

                                <input
                                    type="number"
                                    name="qty[]"
                                    class="form-control"
                                    min="1"
                                    value="<?= $detail[
                                        "qty"
                                    ]; ?>"
                                    required
                                >

                            </td>


                            <td>

                                Rp <?= number_format(
                                    $detail[
                                        "price"
                                    ],
                                    0,
                                    ",",
                                    "."
                                ); ?>

                            </td>


                            <td>

                                Rp <?= number_format(
                                    $detail[
                                        "qty"
                                    ] *
                                    $detail[
                                        "price"
                                    ],
                                    0,
                                    ",",
                                    "."
                                ); ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


            <button
                type="submit"
                class="btn btn-primary-custom"
            >

                <i class="bi bi-save me-2"></i>

                Simpan Perubahan

            </button>


            <a
                href="index.php"
                class="btn btn-outline-secondary ms-2"
            >

                Batal

            </a>

        </form>

    </div>

</main>


<?php include "../layouts/footer.php"; ?>