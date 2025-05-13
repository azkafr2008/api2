<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "hayami";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi gagal: " . $conn->connect_error]));
}

$sql = "
    SELECT 
        c.id AS category_id,
        c.name AS category,
        a.id AS account_id,
        a.name AS account,
        IFNULL(b.debit, 0) AS debit,
        IFNULL(b.credit, 0) AS credit,
        IFNULL(a.saldo_awal_debit, 0) AS saldo_awal_debit,
        IFNULL(a.saldo_awal_kredit, 0) AS saldo_awal_kredit
    FROM categories c
    JOIN accounts a ON a.category_id = c.id
    LEFT JOIN balances b ON b.account_id = a.id
    ORDER BY c.id, a.id
";

$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $catId = $row['category_id'];
        $catName = $row['category'];

        if (!isset($data[$catId])) {
            $data[$catId] = [
                "category" => $catName,
                "accounts" => []
            ];
        }

        $data[$catId]["accounts"][] = [
            "name" => $row['account'],
            "saldo_awal_debit" => (float)$row['saldo_awal_debit'],
            "saldo_awal_kredit" => (float)$row['saldo_awal_kredit'],
            "debit" => (float)$row['debit'],
            "credit" => (float)$row['credit']
        ];
    }
}

$response = array_values($data);

echo json_encode($response, JSON_PRETTY_PRINT);
$conn->close();
?>
