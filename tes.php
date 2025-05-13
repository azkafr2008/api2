<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Koneksi ke database
$servername = "localhost";
$username = "root"; // ganti dengan username database Anda
$password = ""; // ganti dengan password database Anda
$dbname = "hayami"; // ganti dengan nama database Anda

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query untuk mengambil data dari tabel laba_rugi
$sql = "SELECT * FROM laba_rugi";
$result = $conn->query($sql);

// Cek apakah ada data pada tabel laba_rugi
if ($result->num_rows > 0) {
    $labaRugiData = [];

    // Loop untuk mengambil setiap baris data dari tabel laba_rugi
    while ($row = $result->fetch_assoc()) {
        $id_laba = $row['id']; // Ambil id_laba yang akan digunakan untuk query di tabel detail

        // Query untuk mengambil data terkait di tabel detail berdasarkan id_laba
        $detailSql = "SELECT * FROM detail WHERE id_laba = $id_laba";
        $detailResult = $conn->query($detailSql);

        $details = [];
        
        // Cek apakah ada data pada tabel detail yang terkait dengan id_laba
        if ($detailResult->num_rows > 0) {
            while ($detailRow = $detailResult->fetch_assoc()) {
                // Menyimpan data detail (termasuk kolom tanggal) ke array details
                $details[] = $detailRow;
            }
        }

        // Menambahkan data detail ke dalam baris laba_rugi
        $row['detail'] = $details;
        
        // Menambahkan data laba_rugi yang sudah digabung dengan detail ke dalam array $labaRugiData
        $labaRugiData[] = $row;
    }

    // Mengubah data menjadi format JSON dan menampilkannya
    echo json_encode($labaRugiData, JSON_PRETTY_PRINT);
} else {
    echo "0 results";
}

// Menutup koneksi
$conn->close();
?>
