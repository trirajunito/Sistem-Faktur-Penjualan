<?php

require_once "../config/database.php";

$no_faktur = $_GET['id'] ?? '';

$no_faktur = mysqli_real_escape_string(
    $conn,
    $no_faktur
);

$query = mysqli_query(
    $conn,
    "SELECT
        f.*,

        c.nama_customer,
        c.perusahaan_cust,
        c.alamat AS alamat_customer,

        p.nama_perusahaan,
        p.alamat AS alamat_perusahaan,
        p.no_telp,
        p.tax

     FROM faktur f

     INNER JOIN customer c
        ON f.id_customer = c.id_customer

     INNER JOIN perusahaan p
        ON f.id_perusahaan = p.id_perusahaan

     WHERE f.no_faktur='$no_faktur'"
);

$faktur = mysqli_fetch_assoc($query);

if (!$faktur) {
    die("Faktur tidak ditemukan.");
}


$detail = mysqli_query(
    $conn,
    "SELECT
        df.*,
        p.nama_produk,
        p.jenis

     FROM detail_faktur df

     INNER JOIN produk p
        ON df.id_produk = p.id_produk

     WHERE df.no_faktur='$no_faktur'"
);

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<title>
Faktur <?= htmlspecialchars($faktur['no_faktur']); ?>
</title>


<style>

* {
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: #eee;
    color: #222;
}

.invoice {
    width: 210mm;
    min-height: 297mm;
    margin: 20px auto;
    padding: 20mm;
    background: white;
}

.header {
    display: flex;
    justify-content: space-between;
    border-bottom: 2px solid #222;
    padding-bottom: 20px;
}

.company h2 {
    margin: 0 0 5px;
}

.company p {
    margin: 3px 0;
    color: #555;
    font-size: 13px;
}

.invoice-title {
    text-align: right;
}

.invoice-title h1 {
    margin: 0;
    font-size: 28px;
}

.invoice-title p {
    margin: 5px 0;
}

.info {
    display: flex;
    justify-content: space-between;
    margin: 30px 0;
}

.info-box {
    width: 48%;
}

.info-box h4 {
    margin-bottom: 8px;
}

.info-box p {
    margin: 4px 0;
    font-size: 13px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    border-bottom: 1px solid #ddd;
    padding: 10px;
    font-size: 13px;
}

th {
    background: #f5f5f5;
    text-align: left;
}

.text-right {
    text-align: right;
}

.total {
    width: 40%;
    margin-left: auto;
    margin-top: 20px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 7px 0;
}

.grand-total {
    font-size: 17px;
    font-weight: bold;
    border-top: 2px solid #222;
    padding-top: 10px;
}

.footer {
    margin-top: 50px;
    font-size: 12px;
}

.print-button {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 20px;
    background: #4f46e5;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

@media print {

    body {
        background: white;
    }

    .invoice {
        margin: 0;
        width: 100%;
        min-height: auto;
    }

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

    🖨 Cetak / Simpan PDF

</button>


<div class="invoice">


<div class="header">


<div class="company">

<h2>
<?= htmlspecialchars(
    $faktur['nama_perusahaan']
); ?>
</h2>

<p>
<?= htmlspecialchars(
    $faktur['alamat_perusahaan']
); ?>
</p>

<p>
Telp:
<?= htmlspecialchars(
    $faktur['no_telp']
); ?>
</p>

<p>
Tax:
<?= htmlspecialchars(
    $faktur['tax']
); ?>
</p>

</div>


<div class="invoice-title">

<h1>FAKTUR</h1>

<p>
<strong>
<?= htmlspecialchars(
    $faktur['no_faktur']
); ?>
</strong>
</p>

<p>
Tanggal:
<?= date(
    'd/m/Y',
    strtotime($faktur['tgl_faktur'])
); ?>
</p>

<p>
Jatuh Tempo:
<?= date(
    'd/m/Y',
    strtotime($faktur['due_date'])
); ?>
</p>

</div>


</div>


<div class="info">


<div class="info-box">

<h4>Ditagihkan Kepada:</h4>

<p>
<strong>
<?= htmlspecialchars(
    $faktur['nama_customer']
); ?>
</strong>
</p>

<p>
<?= htmlspecialchars(
    $faktur['perusahaan_cust']
); ?>
</p>

<p>
<?= htmlspecialchars(
    $faktur['alamat_customer']
); ?>
</p>

</div>


<div class="info-box">

<h4>Informasi Pembayaran:</h4>

<p>
Metode:
<strong>
<?= htmlspecialchars(
    $faktur['metode_bayar']
); ?>
</strong>
</p>

<p>
User:
<?= htmlspecialchars(
    $faktur['username']
); ?>
</p>

</div>


</div>


<table>

<thead>

<tr>

<th>No</th>
<th>Produk</th>
<th>Jenis</th>
<th>Qty</th>
<th>Harga</th>
<th class="text-right">
Subtotal
</th>

</tr>

</thead>


<tbody>

<?php

$no = 1;
$subtotal = 0;

while (
    $row = mysqli_fetch_assoc(
        $detail
    )
):

$itemSubtotal =
    $row['qty'] *
    $row['price'];

$subtotal += $itemSubtotal;

?>

<tr>

<td><?= $no++; ?></td>

<td>
<?= htmlspecialchars(
    $row['nama_produk']
); ?>
</td>

<td>
<?= htmlspecialchars(
    $row['jenis']
); ?>
</td>

<td>
<?= $row['qty']; ?>
</td>

<td>
Rp <?= number_format(
    $row['price'],
    0,
    ',',
    '.'
); ?>
</td>

<td class="text-right">

Rp <?= number_format(
    $itemSubtotal,
    0,
    ',',
    '.'
); ?>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>


<div class="total">

<div class="total-row">

<span>Subtotal</span>

<strong>
Rp <?= number_format(
    $subtotal,
    0,
    ',',
    '.'
); ?>
</strong>

</div>


<div class="total-row">

<span>PPN</span>

<strong>

Rp <?= number_format(
    $faktur['ppn'],
    0,
    ',',
    '.'
); ?>

</strong>

</div>


<div class="total-row grand-total">

<span>GRAND TOTAL</span>

<strong>

Rp <?= number_format(
    $faktur['grand_total'],
    0,
    ',',
    '.'
); ?>

</strong>

</div>

</div>


<div class="footer">

<p>
Terima kasih telah melakukan transaksi dengan kami.
</p>

<p>
Dokumen ini merupakan bukti transaksi penjualan yang sah.
</p>

</div>


</div>

</body>

</html>