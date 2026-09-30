<?php

require_once "../config/database.php";

$pageTitle = "Tambah Penjualan";
$activeMenu = "penjualan";
$basePath = "../";

$error = "";


/* =========================================================
   AMBIL DATA CUSTOMER
   ========================================================= */

$customerQuery = mysqli_query(
    $conn,
    "SELECT
        id_customer,
        nama_customer,
        perusahaan_cust
     FROM customer
     ORDER BY nama_customer ASC"
);

if (!$customerQuery) {
    die("Error mengambil customer: " . mysqli_error($conn));
}


/* =========================================================
   AMBIL DATA PERUSAHAAN
   ========================================================= */

$perusahaanQuery = mysqli_query(
    $conn,
    "SELECT
        id_perusahaan,
        nama_perusahaan
     FROM perusahaan
     ORDER BY nama_perusahaan ASC"
);

if (!$perusahaanQuery) {
    die("Error mengambil perusahaan: " . mysqli_error($conn));
}


/* =========================================================
   AMBIL DATA PRODUK
   ========================================================= */

$produkQuery = mysqli_query(
    $conn,
    "SELECT
        id_produk,
        nama_produk,
        price,
        jenis,
        stock
     FROM produk
     ORDER BY nama_produk ASC"
);

if (!$produkQuery) {
    die("Error mengambil produk: " . mysqli_error($conn));
}


/* =========================================================
   PROSES SIMPAN
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $no_faktur = trim($_POST["no_faktur"] ?? "");
    $tgl_faktur = $_POST["tgl_faktur"] ?? "";
    $due_date = $_POST["due_date"] ?? "";
    $metode_bayar = trim($_POST["metode_bayar"] ?? "");
    $ppn = (float) ($_POST["ppn"] ?? 0);
    $username = trim($_POST["username"] ?? "admin");

    $id_customer = (int) ($_POST["id_customer"] ?? 0);
    $id_perusahaan = (int) ($_POST["id_perusahaan"] ?? 0);

    $produk = $_POST["id_produk"] ?? [];
    $qty = $_POST["qty"] ?? [];
    $price = $_POST["price"] ?? [];


    /* =====================================================
       VALIDASI HEADER
       ===================================================== */

    if (
        $no_faktur === "" ||
        $tgl_faktur === "" ||
        $due_date === "" ||
        $metode_bayar === "" ||
        $id_customer <= 0 ||
        $id_perusahaan <= 0
    ) {

        $error = "Semua data faktur wajib diisi.";

    } elseif (empty($produk)) {

        $error = "Minimal harus ada satu produk.";

    } else {


        /* =================================================
           CEK NOMOR FAKTUR
           ================================================ */

        $cekFaktur = mysqli_prepare(
            $conn,
            "SELECT no_faktur
             FROM faktur
             WHERE no_faktur = ?"
        );

        mysqli_stmt_bind_param(
            $cekFaktur,
            "s",
            $no_faktur
        );

        mysqli_stmt_execute($cekFaktur);

        $hasilCek = mysqli_stmt_get_result($cekFaktur);

        if (mysqli_num_rows($hasilCek) > 0) {

            $error = "Nomor faktur sudah digunakan.";

        } else {


            /* =============================================
               HITUNG TOTAL DETAIL
               ============================================= */

            $subtotalTotal = 0;

            $detailData = [];

            for ($i = 0; $i < count($produk); $i++) {

                $id_produk = (int) $produk[$i];
                $jumlah = (int) ($qty[$i] ?? 0);

                if ($id_produk <= 0 || $jumlah <= 0) {
                    continue;
                }


                /* =========================================
                   AMBIL HARGA DARI DATABASE
                   ========================================= */

                $stmtProduk = mysqli_prepare(
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

                mysqli_stmt_execute($stmtProduk);

                $resultProduk = mysqli_stmt_get_result(
                    $stmtProduk
                );

                $dataProduk = mysqli_fetch_assoc(
                    $resultProduk
                );


                if (!$dataProduk) {

                    $error =
                        "Produk dengan ID "
                        . $id_produk
                        . " tidak ditemukan.";

                    break;
                }


                /* =========================================
                   CEK STOCK
                   ========================================= */

                if ($jumlah > (int) $dataProduk["stock"]) {

                    $error =
                        "Stok produk "
                        . $dataProduk["nama_produk"]
                        . " tidak cukup. "
                        . "Stok tersedia: "
                        . $dataProduk["stock"]
                        . ", jumlah dibeli: "
                        . $jumlah;

                    break;
                }


                /* =========================================
                   GUNAKAN HARGA DATABASE
                   ========================================= */

                $harga = (float) $dataProduk["price"];

                $subtotal = $harga * $jumlah;

                $subtotalTotal += $subtotal;


                $detailData[] = [
                    "id_produk" => $id_produk,
                    "qty" => $jumlah,
                    "price" => $harga
                ];
            }


            /* =================================================
               SIMPAN DATA
               ================================================= */

            if ($error === "") {

                $grandTotal = $subtotalTotal + $ppn;


                mysqli_begin_transaction($conn);


                try {


                    /* =========================================
                       INSERT FAKTUR
                       ========================================= */

                    $stmtFaktur = mysqli_prepare(
                        $conn,
                        "INSERT INTO faktur
                        (
                            no_faktur,
                            tgl_faktur,
                            due_date,
                            metode_bayar,
                            ppn,
                            grand_total,
                            username,
                            id_customer,
                            id_perusahaan
                        )
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                    );


                    mysqli_stmt_bind_param(
                        $stmtFaktur,
                        "ssssddsii",
                        $no_faktur,
                        $tgl_faktur,
                        $due_date,
                        $metode_bayar,
                        $ppn,
                        $grandTotal,
                        $username,
                        $id_customer,
                        $id_perusahaan
                    );


                    if (
                        !mysqli_stmt_execute(
                            $stmtFaktur
                        )
                    ) {

                        throw new Exception(
                            mysqli_stmt_error(
                                $stmtFaktur
                            )
                        );
                    }


                    /* =========================================
                       INSERT DETAIL + KURANGI STOCK
                       ========================================= */

                    foreach (
                        $detailData as $detail
                    ) {

                        $id_produk =
                            $detail["id_produk"];

                        $jumlah =
                            $detail["qty"];

                        $harga =
                            $detail["price"];


                        /* INSERT DETAIL */

                        $stmtDetail = mysqli_prepare(
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
                            $stmtDetail,
                            "siid",
                            $no_faktur,
                            $id_produk,
                            $jumlah,
                            $harga
                        );


                        if (
                            !mysqli_stmt_execute(
                                $stmtDetail
                            )
                        ) {

                            throw new Exception(
                                mysqli_stmt_error(
                                    $stmtDetail
                                )
                            );
                        }


                        /* =====================================
                           KURANGI STOCK
                           ===================================== */

                        $stmtStock = mysqli_prepare(
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
                                "Stok produk tidak mencukupi."
                            );
                        }
                    }


                    /* =========================================
                       COMMIT
                       ========================================= */

                    mysqli_commit($conn);


                    header(
                        "Location: index.php?success=1"
                    );

                    exit;


                } catch (Exception $e) {

                    mysqli_rollback($conn);

                    $error =
                        "Gagal menyimpan transaksi: "
                        . $e->getMessage();
                }
            }
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
                    Tambah Penjualan
                </h5>

                <small class="text-muted">
                    Buat transaksi faktur penjualan baru
                </small>

            </div>

            <a
                href="index.php"
                class="btn btn-outline-secondary"
            >

                <i class="bi bi-arrow-left me-1"></i>

                Kembali

            </a>

        </div>


        <?php if ($error !== ""): ?>

            <div class="alert alert-danger">

                <i class="bi bi-exclamation-triangle me-2"></i>

                <?= htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <!-- =================================================
                 DATA FAKTUR
            ================================================== -->

            <div class="row g-3 mb-4">

                <div class="col-md-4">

                    <label class="form-label">
                        No Faktur
                    </label>

                    <input
                        type="text"
                        name="no_faktur"
                        class="form-control"
                        value="<?= htmlspecialchars(
                            $_POST["no_faktur"]
                            ?? "INV-"
                            . date("YmdHis")
                        ); ?>"
                        required
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
                            $_POST["tgl_faktur"]
                            ?? date("Y-m-d")
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
                            $_POST["due_date"]
                            ?? date(
                                "Y-m-d",
                                strtotime("+7 days")
                            )
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

                        <option value="">
                            -- Pilih Customer --
                        </option>

                        <?php while (
                            $customer =
                            mysqli_fetch_assoc(
                                $customerQuery
                            )
                        ): ?>

                            <option
                                value="<?= $customer["id_customer"]; ?>"
                                <?= (
                                    ($_POST["id_customer"]
                                    ?? "") ==
                                    $customer["id_customer"]
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

                        <option value="">
                            -- Pilih Perusahaan --
                        </option>

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
                                    ($_POST[
                                        "id_perusahaan"
                                    ] ?? "") ==
                                    $perusahaan[
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

                        <option value="">
                            -- Pilih Pembayaran --
                        </option>

                        <option
                            value="Transfer"
                            <?= (
                                ($_POST[
                                    "metode_bayar"
                                ] ?? "") === "Transfer"
                            )
                                ? "selected"
                                : ""
                            ?>
                        >
                            Transfer
                        </option>

                        <option
                            value="Cash"
                            <?= (
                                ($_POST[
                                    "metode_bayar"
                                ] ?? "") === "Cash"
                            )
                                ? "selected"
                                : ""
                            ?>
                        >
                            Cash
                        </option>

                        <option
                            value="Credit"
                            <?= (
                                ($_POST[
                                    "metode_bayar"
                                ] ?? "") === "Credit"
                            )
                                ? "selected"
                                : ""
                            ?>
                        >
                            Credit
                        </option>

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
                            $_POST["ppn"] ?? "0"
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
                            $_POST["username"]
                            ?? "admin"
                        ); ?>"
                    >

                </div>

            </div>


            <!-- =================================================
                 PRODUK
            ================================================== -->

            <h5 class="mb-3">
                <i class="bi bi-box-seam me-2"></i>
                Produk Penjualan
            </h5>


            <div class="table-responsive">

                <table class="table table-bordered">

                    <thead>

                        <tr>

                            <th width="35%">
                                Produk
                            </th>

                            <th width="15%">
                                Stok
                            </th>

                            <th width="15%">
                                Qty
                            </th>

                            <th width="20%">
                                Harga
                            </th>

                            <th width="20%">
                                Subtotal
                            </th>

                            <th width="80">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody id="produk-container">

                        <tr class="produk-row">

                            <td>

                                <select
                                    name="id_produk[]"
                                    class="form-select produk-select"
                                    required
                                >

                                    <option value="">
                                        -- Pilih Produk --
                                    </option>

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
                                            data-price="<?= $produk[
                                                "price"
                                            ]; ?>"
                                            data-stock="<?= $produk[
                                                "stock"
                                            ]; ?>"
                                        >

                                            <?= htmlspecialchars(
                                                $produk[
                                                    "nama_produk"
                                                ]
                                            ); ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </td>


                            <td>

                                <span class="stock-info">
                                    -
                                </span>

                            </td>


                            <td>

                                <input
                                    type="number"
                                    name="qty[]"
                                    class="form-control qty-input"
                                    min="1"
                                    value="1"
                                    required
                                >

                            </td>


                            <td>

                                <input
                                    type="text"
                                    class="form-control price-display"
                                    value="Rp 0"
                                    readonly
                                >

                                <input
                                    type="hidden"
                                    name="price[]"
                                    class="price-input"
                                    value="0"
                                >

                            </td>


                            <td>

                                <strong
                                    class="subtotal-display"
                                >
                                    Rp 0
                                </strong>

                            </td>


                            <td>

                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-remove"
                                    onclick="hapusBaris(this)"
                                >

                                    <i class="bi bi-trash"></i>

                                </button>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <button
                type="button"
                class="btn btn-outline-primary mb-4"
                onclick="tambahBaris()"
            >

                <i class="bi bi-plus-circle me-1"></i>

                Tambah Produk

            </button>


            <!-- =================================================
                 TOTAL
            ================================================== -->

            <div class="row justify-content-end">

                <div class="col-md-5">

                    <div class="content-card">

                        <div
                            class="d-flex justify-content-between mb-2"
                        >

                            <span>
                                Subtotal
                            </span>

                            <strong id="subtotal-total">
                                Rp 0
                            </strong>

                        </div>


                        <div
                            class="d-flex justify-content-between mb-2"
                        >

                            <span>
                                PPN
                            </span>

                            <strong id="ppn-display">
                                Rp 0
                            </strong>

                        </div>


                        <hr>


                        <div
                            class="d-flex justify-content-between"
                        >

                            <strong>
                                Grand Total
                            </strong>

                            <strong
                                id="grand-total"
                                style="
                                    font-size:20px;
                                    color:#4f46e5;
                                "
                            >
                                Rp 0
                            </strong>

                        </div>

                    </div>

                </div>

            </div>


            <div class="mt-4">

                <button
                    type="submit"
                    class="btn btn-primary-custom"
                >

                    <i class="bi bi-save me-2"></i>

                    Simpan Penjualan

                </button>


                <a
                    href="index.php"
                    class="btn btn-outline-secondary ms-2"
                >

                    Batal

                </a>

            </div>


        </form>

    </div>

</main>


<script>

function formatRupiah(angka) {

    return "Rp " + Number(angka).toLocaleString(
        "id-ID"
    );

}


function hitungTotal() {

    let subtotalTotal = 0;


    document
        .querySelectorAll(".produk-row")
        .forEach(function(row) {

            const qtyInput =
                row.querySelector(".qty-input");

            const priceInput =
                row.querySelector(".price-input");

            const subtotalDisplay =
                row.querySelector(
                    ".subtotal-display"
                );


            const qty =
                Number(qtyInput.value) || 0;

            const price =
                Number(priceInput.value) || 0;


            const subtotal =
                qty * price;


            subtotalTotal += subtotal;


            subtotalDisplay.innerText =
                formatRupiah(subtotal);

        });


    const ppn =
        Number(
            document.getElementById("ppn").value
        ) || 0;


    const grandTotal =
        subtotalTotal + ppn;


    document.getElementById(
        "subtotal-total"
    ).innerText =
        formatRupiah(subtotalTotal);


    document.getElementById(
        "ppn-display"
    ).innerText =
        formatRupiah(ppn);


    document.getElementById(
        "grand-total"
    ).innerText =
        formatRupiah(grandTotal);

}


function updateProduk(row) {

    const select =
        row.querySelector(
            ".produk-select"
        );


    const option =
        select.options[
            select.selectedIndex
        ];


    if (!option || !option.value) {

        row.querySelector(
            ".stock-info"
        ).innerText = "-";

        row.querySelector(
            ".price-display"
        ).value = "Rp 0";

        row.querySelector(
            ".price-input"
        ).value = 0;

        hitungTotal();

        return;
    }


    const price =
        Number(
            option.dataset.price
        ) || 0;


    const stock =
        Number(
            option.dataset.stock
        ) || 0;


    row.querySelector(
        ".stock-info"
    ).innerText = stock;


    row.querySelector(
        ".price-display"
    ).value =
        formatRupiah(price);


    row.querySelector(
        ".price-input"
    ).value = price;


    const qty =
        row.querySelector(
            ".qty-input"
        );


    qty.max = stock;


    if (
        Number(qty.value) > stock
    ) {

        qty.value =
            stock > 0
                ? stock
                : 1;

    }


    hitungTotal();

}


function tambahBaris() {

    const container =
        document.getElementById(
            "produk-container"
        );


    const pertama =
        document.querySelector(
            ".produk-row"
        );


    const row =
        pertama.cloneNode(true);


    row.querySelector(
        ".produk-select"
    ).selectedIndex = 0;


    row.querySelector(
        ".stock-info"
    ).innerText = "-";


    row.querySelector(
        ".qty-input"
    ).value = 1;


    row.querySelector(
        ".price-display"
    ).value = "Rp 0";


    row.querySelector(
        ".price-input"
    ).value = 0;


    row.querySelector(
        ".subtotal-display"
    ).innerText = "Rp 0";


    container.appendChild(row);


    hitungTotal();

}


function hapusBaris(button) {

    const rows =
        document.querySelectorAll(
            ".produk-row"
        );


    if (rows.length <= 1) {

        alert(
            "Minimal harus ada satu produk."
        );

        return;
    }


    button
        .closest(".produk-row")
        .remove();


    hitungTotal();

}


document.addEventListener(
    "change",
    function(event) {

        if (
            event.target.classList.contains(
                "produk-select"
            )
        ) {

            updateProduk(
                event.target.closest(
                    ".produk-row"
                )
            );

        }

    }
);


document.addEventListener(
    "input",
    function(event) {

        if (
            event.target.classList.contains(
                "qty-input"
            )
        ) {

            const row =
                event.target.closest(
                    ".produk-row"
                );


            const stock =
                Number(
                    row.querySelector(
                        ".stock-info"
                    ).innerText
                ) || 0;


            if (
                Number(
                    event.target.value
                ) > stock
            ) {

                event.target.value =
                    stock;

                alert(
                    "Qty tidak boleh melebihi stok."
                );

            }


            hitungTotal();

        }


        if (
            event.target.id === "ppn"
        ) {

            hitungTotal();

        }

    }
);


document.addEventListener(
    "DOMContentLoaded",
    function() {

        hitungTotal();

    }
);

</script>


<?php include "../layouts/footer.php"; ?>