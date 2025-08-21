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

function loadVentas($file, $pdo) {
    if (!file_exists($file)) {
        echo "❌ No se encontró $file\n";
        return;
    }

    $handle = fopen($file, "r");
    fgetcsv($handle); // skip header
    while (($row = fgetcsv($handle, 1000, ",")) !== false) {
        [$fecha, $nombre,  $producto, $factura, $valor, $cantidad] = $row;

        // Buscar o crear vendedor
        $stmt = $pdo->prepare("SELECT id FROM vendedores WHERE nombre = ?");
        $stmt->execute([$nombre]);
        $vendedorId = $stmt->fetchColumn();
        if (!$vendedorId) {
            $pdo->prepare("INSERT INTO vendedores (nombre) VALUES (?)")->execute([$nombre]);
            $vendedorId = $pdo->lastInsertId();
        }

        // Insertar venta
        $pdo->prepare("
            INSERT INTO ventas (vendedor_id, fecha, factura, producto, cantidad, valor)
            VALUES (?, ?, ?, ?, ?, ?)
            ")->execute([$vendedorId, $fecha, $factura, $producto, $cantidad, $valor]);
    }
    fclose($handle);
    echo "✅ Datos cargados en ventas\n";
}

function loadDevoluciones($file, $pdo) {
    if (!file_exists($file)) {
        echo "❌ No se encontró $file\n";
        return;
    }

    $handle = fopen($file, "r");
    fgetcsv($handle); // skip header
    while (($row = fgetcsv($handle, 1000, ",")) !== false) {
        [$fecha, $nombre, $producto, $referencia, $cantidad, $valor, $motivo] = $row;


        // Buscar venta relacionada
        $stmt = $pdo->prepare("
            SELECT v.id 
            FROM ventas v
            JOIN vendedores ven ON v.vendedor_id = ven.id
            WHERE ven.nombre = ? AND v.producto = ? AND v.fecha = ?
            LIMIT 1
        ");
        $stmt->execute([$nombre, $producto, $fecha]);
        $ventaId = $stmt->fetchColumn();
        var_dump($ventaId);
        var_dump($nombre, $producto, $fecha);
        if ($ventaId) {
            // Obtener el vendedor_id
            $stmt = $pdo->prepare("SELECT vendedor_id FROM ventas WHERE id = ?");
            $stmt->execute([$ventaId]);
            $vendedorId = $stmt->fetchColumn();

            $pdo->prepare("
                INSERT INTO devoluciones (venta_id, vendedor_id, fecha, producto, cantidad, valor, motivo)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ")->execute([$ventaId, $vendedorId, $fecha, $producto, $cantidad, $valor, $motivo]);
        }
    }
    fclose($handle);
    echo "✅ Datos cargados en devoluciones\n";
}

// Ejecutar cargas
loadVentas(__DIR__ . '/../../data/ventas_ejemplo_junio_julio.csv', $pdo);
loadDevoluciones(__DIR__ . '/../../data/ventas_con_devoluciones.csv', $pdo);

echo "🚀 Carga ETL completada!\n";
