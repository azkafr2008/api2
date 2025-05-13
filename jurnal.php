<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "hayami2");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$kontakQuery = "SELECT * FROM kontak";
$kontakResult = mysqli_query($conn, $kontakQuery);

$data = [];

while ($row = mysqli_fetch_assoc($kontakResult)) {
    $kontak_id = $row['id'];

    $barangQuery = "SELECT * FROM barang_kontak WHERE kontak_id = '$kontak_id'";
    $barangResult = mysqli_query($conn, $barangQuery);

    $barang_kontak = [];
    while ($barang = mysqli_fetch_assoc($barangResult)) {
        $produk_id = $barang['produk_id'];

        // Ambil data produk berdasarkan produk_id
        $produkQuery = "SELECT name, code FROM produk WHERE id = '$produk_id'";
        $produkResult = mysqli_query($conn, $produkQuery);
        $produkData = mysqli_fetch_assoc($produkResult);

        $barang_kontak[] = [
            'id' => $barang['id'],
            'nama_barang' => $barang['nama_barang'],
            'jumlah' => $barang['jumlah'],
            'harga' => $barang['harga'],
            'total' => $barang['total'],
            'size' => $barang['size'],
            'produk_id' => $produk_id,
            'produk' => $produkData,
        ];
    }

    $row['barang_kontak'] = $barang_kontak;

    $data[] = $row;
}

echo json_encode($data, JSON_PRETTY_PRINT);

mysqli_close($conn);
?>
