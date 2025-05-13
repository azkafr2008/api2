<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hayami";


// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM neraca";
$result = $conn->query($sql);

$data = array();
if ($result->num_rows > 0) {
    // Ambil setiap baris hasil
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    $data[] = "No records found";
}

echo json_encode($data);

$conn->close();
?>
