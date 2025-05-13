<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');


$host = "localhost";
$user = "root";
$password = "";
$dbname = "hayami";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "SELECT jenis, saldo_awal, uang_diterima, uang_dibelanjakan, saldo_akhir FROM ringkasan_bank";
$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[$row['jenis']] = [
        'saldoAwal' => $row['saldo_awal'],
        'uangDiterima' => $row['uang_diterima'],
        'uangDibelanjakan' => $row['uang_dibelanjakan'],
        'saldoAkhir' => $row['saldo_akhir'],
    ];
}

echo json_encode($data);

$conn->close();
