<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "sistem_faktur_penjualan";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>