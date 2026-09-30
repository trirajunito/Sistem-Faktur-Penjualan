<?php

require_once "../classes/Database.php";
require_once "../classes/Produk.php";

$database = new Database();

$conn = $database->getConnection();


$produk = new Produk($conn);


$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


if ($id > 0) {

    $produk->hapus($id);

}

header("Location: index.php");

exit;