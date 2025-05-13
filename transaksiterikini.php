<?php

header("Access-Control-Allow-Origin: *");  // Atau ganti '*' dengan domain yang lebih spesifik, misal 'http://localhost:55537'
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');


// Konfigurasi database
$servername = "localhost"; // Ganti dengan host database Anda
$username = "root";    // Ganti dengan username database Anda
$password = "";    // Ganti dengan password database Anda
$dbname = "hayami";        // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Koneksi gagal: ' . $conn->connect_error
    ]));
}

// Query untuk mengambil data dari tabel invoices dan barang
$sql = "
    SELECT
        i.invoice,
        i.name,
        i.date AS invoice_date,  -- Mengambil tanggal dari tabel invoices
        b.total
    FROM
        invoices i
    JOIN
        barang b ON i.id = b.invoice_id
    WHERE
        i.date IS NOT NULL  -- Mengambil data yang memiliki tanggal
";

$result = $conn->query($sql);

// Menyiapkan array untuk menyimpan data
$transaksiList = [];

if ($result && $result->num_rows > 0) {
    // Mengambil hasil query dan menambahkan ke array
    while ($row = $result->fetch_assoc()) {
        $transaksiList[] = [
            'invoice' => $row['invoice'],
            'nama' => $row['name'],
            'date' => $row['invoice_date'] ? $row['invoice_date'] : 'Tanggal Tidak Tersedia',  // Menangani NULL
            'total' => $row['total']
        ];
    }

    // Mengirimkan data dalam format JSON
    echo json_encode([
        'status' => 'success',
        'data' => $transaksiList
    ]);
} else {
    // Jika tidak ada data atau terjadi error
    echo json_encode([
        'status' => 'error',
        'message' => 'Tidak ada data transaksi atau query gagal'
    ]);
}

// Menutup koneksi
$conn->close();
?>
