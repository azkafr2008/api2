<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "hayami";

// Set header untuk JSON
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $password, $db);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data gabungan produk, gudang_produk, dan transfer
$query = "
    SELECT 
        produk.id AS produk_id, 
        produk.name AS produk_name, 
        produk.hpp, 
        produk.harga_jual, 
        produk.hpp_value, 
        produk.code, 
        produk.stok, 
        produk.penjualan, 
        produk.nominal_stok, 
        produk.nominal_penjualan, 
        produk.image,
        gudang_produk.unassigned, 
        gudang_produk.gudang_utama, 
        gudang_produk.gudang_elektronik, 
        gudang_produk.total AS total_gudang,
        transfer.kode AS transfer_kode,
        transfer.tanggal AS transfer_tanggal
    FROM 
        produk
    LEFT JOIN 
        gudang_produk ON produk.id = gudang_produk.produk_id
    LEFT JOIN 
        transfer ON produk.id = transfer.id_produk;
";

// Menjalankan query
$result = $conn->query($query);

// Mengecek apakah ada hasil
if ($result->num_rows > 0) {
    // Array untuk menampung data
    $data = [];
    
    // Array untuk menampung transfer data berdasarkan produk
    $tempTransfers = [];

    // Fetch data dari query
    while ($row = $result->fetch_assoc()) {
        // Cek apakah produk_id sudah ada dalam data
        if (!isset($data[$row['produk_id']])) {
            // Jika belum ada, inisialisasi data produk
            $data[$row['produk_id']] = [
                'produk_id' => $row['produk_id'],
                'produk_name' => $row['produk_name'],
                'hpp' => $row['hpp'],
                'harga_jual' => $row['harga_jual'],
                'hpp_value' => $row['hpp_value'],
                'code' => $row['code'],
                'stok' => $row['stok'],
                'penjualan' => $row['penjualan'],
                'nominal_stok' => $row['nominal_stok'],
                'nominal_penjualan' => $row['nominal_penjualan'],
                'image' => $row['image'],
                'unassigned' => $row['unassigned'],
                'gudang_utama' => $row['gudang_utama'],
                'gudang_elektronik' => $row['gudang_elektronik'],
                'total_gudang' => $row['total_gudang'],
                'transfers' => [] // Array untuk menyimpan data transfer terkait produk
            ];
        }
        
        // Jika ada transfer untuk produk tersebut, tambahkan ke dalam array 'transfers'
        if ($row['transfer_kode'] && $row['transfer_tanggal']) {
            $data[$row['produk_id']]['transfers'][] = [
                'kode' => $row['transfer_kode'],
                'tanggal' => $row['transfer_tanggal']
            ];
        }
    }

    // Mengembalikan hasil dalam format JSON
    echo json_encode(array_values($data));
} else {
    // Jika tidak ada data ditemukan
    echo json_encode([]);
}

// Menutup koneksi
$conn->close();
?>
