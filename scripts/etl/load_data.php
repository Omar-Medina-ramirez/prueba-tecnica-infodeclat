<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$pdo = new PDO(
    "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

function loadCSV($file, $table, $pdo) {
    if (!file_exists($file)) {
        echo "❌ No se encontró $file\n";
        return;
    }

    $handle = fopen($file, "r");
    fgetcsv($handle); // skip header
    while (($row = fgetcsv($handle, 1000, ",")) !== false) {
        [$nombre, $fecha, $valor] = $row;

        $stmt = $pdo->prepare("SELECT id FROM vendedores WHERE nombre = ?");
        $stmt->execute([$nombre]);
        $vendedorId = $stmt->fetchColumn();
        if (!$vendedorId) {
            $pdo->prepare("INSERT INTO vendedores (nombre) VALUES (?)")->execute([$nombre]);
            $vendedorId = $pdo->lastInsertId();
        }

        $pdo->prepare("INSERT INTO {$table} (vendedor_id, fecha, valor) VALUES (?, ?, ?)")
            ->execute([$vendedorId, $fecha, $valor]);
    }
    fclose($handle);
    echo "✅ Datos cargados en {$table}\n";
}

loadCSV(__DIR__ . '/../../data/ventas_ejemplo_junio_julio.csv', "ventas", $pdo);
loadCSV(__DIR__ . '/../../data/ventas_con_devoluciones.csv', "devoluciones", $pdo);

echo "🚀 Carga ETL completada!\n";