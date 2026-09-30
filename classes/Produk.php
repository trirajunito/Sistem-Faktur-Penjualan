<?php

class Produk
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        return $this->conn->query(
            "SELECT * FROM produk ORDER BY id_produk DESC"
        );
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM produk WHERE id_produk = ?"
        );

        $stmt->bind_param("i", $id);

        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function tambah($nama_produk, $price, $jenis, $stock)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO produk
            (nama_produk, price, jenis, stock)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sdsi",
            $nama_produk,
            $price,
            $jenis,
            $stock
        );

        return $stmt->execute();
    }

    public function update($id, $nama_produk, $price, $jenis, $stock)
    {
        $stmt = $this->conn->prepare(
            "UPDATE produk SET
                nama_produk = ?,
                price = ?,
                jenis = ?,
                stock = ?
            WHERE id_produk = ?"
        );

        $stmt->bind_param(
            "sdsii",
            $nama_produk,
            $price,
            $jenis,
            $stock,
            $id
        );

        return $stmt->execute();
    }

    public function hapus($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM produk WHERE id_produk = ?"
        );

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}