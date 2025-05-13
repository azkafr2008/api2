<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hayami2";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
    SELECT 
        p.id AS produk_id,
        p.name AS produk_name,
        p.kategori,
        p.hpp,
        p.hpp_value,
        p.harga_jual,
        p.stok,
        p.penjualan,
        p.nominal_stok,
        p.nominal_penjualan,
        p.code AS produk_code,
        p.image AS produk_image,
        g.unassigned,
        g.gudang_utama,
        g.gudang_elektronik,
        g.total AS gudang_total,
        b.id AS barang_id,
        b.nama_barang,
        b.jumlah,
        b.size,
        b.harga AS barang_harga,
        b.total,
        k.id AS kontak_id,
        k.nama AS kontak_name,
        k.invoice AS kontak_code,
        k.date AS kontak_date,
        k.due AS kontak_due,
        k.amount AS kontak_amount,
        t.kode AS transfer_kode,
        t.tanggal AS transfer_tanggal
    FROM produk p
    LEFT JOIN gudang_produk g ON p.id = g.produk_id
    LEFT JOIN barang_kontak b ON p.id = b.produk_id
    LEFT JOIN kontak k ON b.kontak_id = k.id
    LEFT JOIN transfer t ON p.id = t.id_produk
    ORDER BY p.id, k.id, t.tanggal
";

$result = $conn->query($sql);

$produk_data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pid = $row['produk_id'];

        if (!isset($produk_data[$pid])) {
            $produk_data[$pid] = [
                'produk_id' => $row['produk_id'],
                'produk_name' => $row['produk_name'],
                'kategori' => $row['kategori'],
                'hpp' => $row['hpp'],
                'hpp_value' => $row['hpp_value'],
                'harga_jual' => $row['harga_jual'],
                'stok' => $row['stok'],
                'penjualan' => $row['penjualan'],
                'nominal_stok' => $row['nominal_stok'],
                'nominal_penjualan' => $row['nominal_penjualan'],
                'produk_code' => $row['produk_code'],
                'produk_image' => $row['produk_image'],
                'gudang' => [
                    'unassigned' => $row['unassigned'] ?? 0,
                    'gudang_utama' => $row['gudang_utama'] ?? 0,
                    'gudang_elektronik' => $row['gudang_elektronik'] ?? 0,
                    'total' => $row['gudang_total'] ?? 0
                ],
                'kontaks' => [],
                'transfer' => []
            ];
        }

        // Tambahkan kontak & barang_kontak jika ada
        if (!empty($row['kontak_id'])) {
            $kontak_key = $row['kontak_id'] . '_' . $row['barang_id'];

            $produk_data[$pid]['kontaks'][$kontak_key] = [
                'kontak_id' => $row['kontak_id'],
                'kontak_name' => $row['kontak_name'],
                'kontak_code' => $row['kontak_code'],
                'kontak_date' => $row['kontak_date'],
                'kontak_due' => $row['kontak_due'],
                'kontak_amount' => $row['kontak_amount'],
                'barang_kontak' => [
                    'barang_id' => $row['barang_id'],
                    'nama_barang' => $row['produk_name'],
                    'size' => $row['size'],
                    'jumlah' => $row['jumlah'],
                    'harga' => $row['barang_harga'],
                    'total' => $row['total']
                ]
            ];
        }

        if (!empty($row['transfer_kode'])) {
            $transfer_key = $row['transfer_kode'] . '_' . $row['transfer_tanggal'];
            $produk_data[$pid]['transfer'][$transfer_key] = [
                'kode' => $row['transfer_kode'],
                'tanggal' => $row['transfer_tanggal']
            ];
        }
    }

    foreach ($produk_data as &$produk) {
        $produk['kontaks'] = array_values($produk['kontaks']);
        $produk['transfer'] = array_values($produk['transfer']);
    }

    echo json_encode([
        'status' => 'success',
        'data' => array_values($produk_data)
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        'status' => 'success',
        'data' => []
    ], JSON_PRETTY_PRINT);
}

$conn->close();
?>
