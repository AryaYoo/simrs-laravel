<?php
$pdo = new PDO('mysql:host=192.168.100.2;dbname=sik2', 'rsiabi', '123qwe');
$stmt = $pdo->query("DESCRIBE catatan_observasi_igd");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($columns, JSON_PRETTY_PRINT);
