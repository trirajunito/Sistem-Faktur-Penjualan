<?php

require_once "../config/database.php";


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
| Validasi
|--------------------------------------------------------------------------
*/

if ($no_faktur == '' || $id_produk <= 0) {

    header("Location: index.php");

    exit;

}


/*
|--------------------------------------------------------------------------
| Hapus Detail Faktur
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM detail_faktur
     WHERE no_faktur = ?
     AND id_produk = ?"
);


mysqli_stmt_bind_param(
    $stmt,
    "si",
    $no_faktur,
    $id_produk
);


mysqli_stmt_execute($stmt);


/*
|--------------------------------------------------------------------------
| Kembali
|--------------------------------------------------------------------------
*/

header("Location: index.php");

exit;