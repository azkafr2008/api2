<?php
// Mengatur header untuk mengembalikan data dalam format JSON
header('Content-Type: application/json');

// Koneksi ke database
$servername = "localhost";
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$dbname = "hayami"; // Ganti dengan nama database Anda

$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mendapatkan parameter `product_id` dari query string
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk mendapatkan data gudang berdasarkan `product_id`
$sql = "SELECT 
            g.id, 
            g.unassigned, 
            g.gudang_utama, 
            g.gudang_elektronik, 
            (g.unassigned + g.gudang_utama + g.gudang_elektronik) AS total
        FROM gudang g
        JOIN produk p ON g.id = p.id
        WHERE p.id = $product_id";

$result = $conn->query($sql);

// Memeriksa apakah ada hasil dari query
if ($result->num_rows > 0) {
    // Mengambil data hasil query
    $row = $result->fetch_assoc();
    
    // Mengembalikan data dalam format JSON
    echo json_encode($row);
} else {
    // Jika tidak ada data, mengembalikan pesan error
    echo json_encode(["error" => "Data tidak ditemukan untuk produk ID: $product_id"]);
}

// Menutup koneksi
$conn->close();
?>
