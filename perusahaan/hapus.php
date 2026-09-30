<?php

require_once "../config/database.php";

$id = intval($_GET['id'] ?? 0);

mysqli_query(
    $conn,
    "DELETE FROM perusahaan
     WHERE id_perusahaan = $id"
);

header("Location: index.php");
exit;
?>