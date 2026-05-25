<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=sik', 'root', '');
    $stmt = $pdo->query("SHOW TABLES LIKE '%periksa_lab%'");
    echo "Tables:\n";
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));

    echo "\nSchema periksa_lab:\n";
    $stmt = $pdo->query("SHOW CREATE TABLE periksa_lab");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $row['Create Table'] . "\n";
    
    echo "\nSchema detail_periksa_lab:\n";
    $stmt = $pdo->query("SHOW CREATE TABLE detail_periksa_lab");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $row['Create Table'] . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
