<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$host = "localhost";
$user = "root";
$pass = "";
$db   = "hayami";


$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$name = $_POST['name'] ?? '';
$company = $_POST['company'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    echo "Nama, Email, dan Password wajib diisi.";
    exit();
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "Email sudah digunakan.";
    exit();
}
$check->close();

$stmt = $conn->prepare("INSERT INTO users (name, company, phone, email, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $company, $phone, $email, $hashed_password);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Gagal menyimpan data.";
}

$stmt->close();
$conn->close();
?>
