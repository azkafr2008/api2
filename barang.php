<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$conn = mysqli_connect('localhost', 'toor', '', 'hayami');
$query = mysqli_query($conn, 'SELECT * FROM barang');
while ($row = mysqli_fetch_array($query)) {
	$data[] = $row;
}
echo json_encode($data);
?>