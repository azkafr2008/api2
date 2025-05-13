<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');


$host = 'localhost';
$user = 'root';
$password = ''; 
$database = 'hayami2';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql_kontak = "SELECT * FROM kontak";
$result_kontak = $conn->query($sql_kontak);

$data = [];

if ($result_kontak->num_rows > 0) {
    while ($row_kontak = $result_kontak->fetch_assoc()) {
        $kontak_id = $row_kontak['id'];

        $sql_barang = "SELECT * FROM barang_kontak WHERE kontak_id = $kontak_id";
        $result_barang = $conn->query($sql_barang);

        $barang_array = [];
        if ($result_barang->num_rows > 0) {
            while ($row_barang = $result_barang->fetch_assoc()) {
                $barang_array[] = $row_barang;
            }
        }

        $row_kontak['barang_kontak'] = $barang_array;
        $data[] = $row_kontak;
    }
}

header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT);

$conn->close();
?>
