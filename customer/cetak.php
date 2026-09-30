<?php

require_once "../config/database.php";

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {

    $query = mysqli_query(
        $conn,
        "SELECT * FROM customer
         WHERE id_customer = $id"
    );

} else {

    $query = mysqli_query(
        $conn,
        "SELECT * FROM customer
         ORDER BY id_customer ASC"
    );
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Cetak Data Customer</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #222;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 30px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 10px;
        }

        th {
            background: #f1f1f1;
        }

        .print-button {
            margin-bottom: 20px;
            padding: 10px 18px;
            background: #4f46e5;
            color: white;
            border: none;
            cursor: pointer;
        }

        @media print {

            .print-button {
                display: none;
            }

        }

    </style>

</head>

<body>

<button
    class="print-button"
    onclick="window.print()">

    Cetak

</button>

<h2>DATA CUSTOMER</h2>

<div class="subtitle">
    Sistem Faktur Penjualan
</div>

<table>

    <thead>

    <tr>

        <th>No</th>
        <th>Nama Customer</th>
        <th>Perusahaan</th>
        <th>Alamat</th>

    </tr>

    </thead>

    <tbody>

    <?php

    $no = 1;

    while ($row = mysqli_fetch_assoc($query)):

    ?>

    <tr>

        <td><?= $no++; ?></td>

        <td>
            <?= htmlspecialchars($row['nama_customer']); ?>
        </td>

        <td>
            <?= htmlspecialchars($row['perusahaan_cust']); ?>
        </td>

        <td>
            <?= htmlspecialchars($row['alamat']); ?>
        </td>

    </tr>

    <?php endwhile; ?>

    </tbody>

</table>

</body>

</html>