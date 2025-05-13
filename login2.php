<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/x-www-form-urlencoded");

// Konfigurasi koneksi
$host = "localhost";
$user = "root";
$pass = "";
$db   = "hayami";

// Koneksi ke database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Koneksi ke database gagal."]));
}

// Ambil data dari request
$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validasi input
if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Email dan password wajib diisi."]);
    exit;
}

// Cari user berdasarkan email
$sql  = "SELECT * FROM users WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verifikasi password
    if (password_verify($password, $user['password'])) {
        echo json_encode([
            "status" => "success",
            "message" => "Login berhasil",
            "user" => [
                "id"      => $user["id"],
                "name"    => $user["name"],
                "email"   => $user["email"],
                "company" => $user["company"],
                "phone"   => $user["phone"]
            ]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Password salah."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Email tidak ditemukan."]);
}

$conn->close();
?>
