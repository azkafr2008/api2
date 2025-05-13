<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hayami";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT section, awal, debit, kredit, akhir FROM perubahan";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    // Ambil data dari hasil query
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo "0 results";
}

// Kirim data dalam format JSON
header('Content-Type: application/json');
echo json_encode($data);

// Tutup koneksi
$conn->close();
?>
