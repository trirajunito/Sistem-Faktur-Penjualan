<?php

require_once "../config/database.php";

$id = intval($_GET['id'] ?? 0);

mysqli_query(
    $conn,
    "DELETE FROM customer
     WHERE id_customer = $id"
);

header("Location: index.php");
exit;
?>