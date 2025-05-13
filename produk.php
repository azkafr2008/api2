<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hayami";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
    SELECT 
        p.id AS produk_id,
        p.name AS produk_name,
        p.hpp,
        p.harga_jual,
        p.hpp_value,
        p.stok,
        p.penjualan,
        p.name,
        p.nominal_stok,
        p.nominal_penjualan,
        p.code AS produk_code,
        p.image AS produk_image,
        g.unassigned,
        g.gudang_utama,
        g.gudang_elektronik,
        g.total AS gudang_total,
        b.id AS barang_id,
        b.jumlah,
        b.harga AS barang_harga,
        i.id AS invoice_id,
        i.name AS invoice_name,
        i.invoice AS invoice_code,
        i.date AS invoice_date,
        i.due AS invoice_due,
        i.amount AS invoice_amount
    FROM produk p
    LEFT JOIN gudang_produk g ON p.id = g.produk_id
    LEFT JOIN barang b ON p.id = b.produk_id
    LEFT JOIN invoices i ON b.invoice_id = i.id
    ORDER BY p.id, i.id
";

$result = $conn->query($sql);

$produk_data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pid = $row['produk_id'];

        if (!isset($produk_data[$pid])) {
            $produk_data[$pid] = [
                'produk_id' => $row['produk_id'],
                'produk_name' => $row['produk_name'],
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
                'invoices' => []
            ];
        }

        if (!empty($row['invoice_id'])) {
            $invoice_key = $row['invoice_id'] . '_' . $row['barang_id'];

            $produk_data[$pid]['invoices'][$invoice_key] = [
                'invoice_id' => $row['invoice_id'],
                'invoice_name' => $row['invoice_name'],
                'invoice_code' => $row['invoice_code'],
                'invoice_date' => $row['invoice_date'],
                'invoice_due' => $row['invoice_due'],
                'invoice_amount' => $row['invoice_amount'],
                'barang' => [
                    'barang_id' => $row['barang_id'],
                    'name' => $row['name'],
                    'jumlah' => $row['jumlah'],
                    'harga' => $row['barang_harga']
                ]
            ];
        }
    }

    foreach ($produk_data as &$produk) {
        $produk['invoices'] = array_values($produk['invoices']);
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