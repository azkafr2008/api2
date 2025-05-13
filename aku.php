<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$conn = mysqli_connect('localhost', 'toor', '', 'hayami2');

if (!$conn) {
    die(json_encode(['error' => 'Koneksi gagal: ' . mysqli_connect_error()]));
}

$sql = "
    SELECT 
        barang_kontak.id,
        barang_kontak.kontak_id,
        barang_kontak.nama_barang AS nama_produk,
        barang_kontak.jumlah,
        barang_kontak.size,
        barang_kontak.harga,
        barang_kontak.total,
        produk.id AS produk_id,
        produk.name AS nama_barang,
        produk.hpp,
        produk.harga_jual,
        produk.code,
        produk.stok,
        produk.penjualan
    FROM barang_kontak
    INNER JOIN produk ON barang_kontak.produk_id = produk.id
";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die(json_encode(['error' => 'Query gagal: ' . mysqli_error($conn)]));
}

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

echo json_encode($data, JSON_PRETTY_PRINT);

?>
