<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");


// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "hayami");

if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi gagal: " . $conn->connect_error]));
}

$sql = "
    SELECT 
        a.id AS aset_id,
        a.name,
        a.code,
        a.date,
        a.amount,
        a.status,
        a.akun,
        p.akun_akumulasi,
        p.akun_penyusutan,
        p.metode,
        p.masa_manfaat,
        p.tanggal_pelepasan,
        p.batas_biaya
    FROM aset_tetap a
    LEFT JOIN penyusutan p ON a.id = p.aset_id
";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>
