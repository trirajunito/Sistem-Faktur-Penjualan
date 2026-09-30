<?php

require_once "../config/database.php";


$no_faktur =
    trim(
        $_GET["id"] ?? ""
    );


if ($no_faktur === "") {

    header(
        "Location: index.php"
    );

    exit;

}


/* =========================================================
   MULAI TRANSACTION
   ========================================================= */

mysqli_begin_transaction($conn);


try {


    /* =====================================================
       CEK FAKTUR
       ===================================================== */

    $stmtCek =
        mysqli_prepare(
            $conn,
            "SELECT no_faktur
             FROM faktur
             WHERE no_faktur = ?
             FOR UPDATE"
        );


    mysqli_stmt_bind_param(
        $stmtCek,
        "s",
        $no_faktur
    );


    mysqli_stmt_execute(
        $stmtCek
    );


    $resultCek =
        mysqli_stmt_get_result(
            $stmtCek
        );


    if (
        mysqli_num_rows(
            $resultCek
        ) === 0
    ) {

        throw new Exception(
            "Data faktur tidak ditemukan."
        );

    }


    /* =====================================================
       AMBIL DETAIL
       ===================================================== */

    $stmtDetail =
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
        $stmtDetail,
        "s",
        $no_faktur
    );


    mysqli_stmt_execute(
        $stmtDetail
    );


    $detailResult =
        mysqli_stmt_get_result(
            $stmtDetail
        );


    /* =====================================================
       KEMBALIKAN STOCK
       ===================================================== */

    while (
        $detail =
        mysqli_fetch_assoc(
            $detailResult
        )
    ) {


        $stmtStock =
            mysqli_prepare(
                $conn,
                "UPDATE produk
                 SET stock = stock + ?
                 WHERE id_produk = ?"
            );


        mysqli_stmt_bind_param(
            $stmtStock,
            "ii",
            $detail["qty"],
            $detail["id_produk"]
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

    }


    /* =====================================================
       HAPUS DETAIL
       ===================================================== */

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


    /* =====================================================
       HAPUS FAKTUR
       ===================================================== */

    $stmtDeleteFaktur =
        mysqli_prepare(
            $conn,
            "DELETE FROM faktur
             WHERE no_faktur = ?"
        );


    mysqli_stmt_bind_param(
        $stmtDeleteFaktur,
        "s",
        $no_faktur
    );


    if (
        !mysqli_stmt_execute(
            $stmtDeleteFaktur
        )
    ) {

        throw new Exception(
            mysqli_stmt_error(
                $stmtDeleteFaktur
            )
        );

    }


    /* =====================================================
       COMMIT
       ===================================================== */

    mysqli_commit($conn);


    header(
        "Location: index.php?deleted=1"
    );

    exit;


} catch (Exception $e) {


    mysqli_rollback($conn);


    die(
        "Gagal menghapus faktur: "
        . htmlspecialchars(
            $e->getMessage()
        )
    );

}