<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use VentasPlus\Services\CommissionService;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$pdo = new PDO(
    "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Detectar mes/año automáticamente si no se pasan por GET
if (!isset($_GET['mes']) || !isset($_GET['ano'])) {
    $stmt = $pdo->query("SELECT MONTH(MAX(fecha)) as mes, YEAR(MAX(fecha)) as ano FROM ventas");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $mes = $row['mes'];
    $ano = $row['ano'];
} else {
    $mes = (int) $_GET['mes'];
    $ano = (int) $_GET['ano'];
}

$service = new CommissionService($pdo);
$data = $service->calcular($mes, $ano);

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'mes'     => $mes,
    'ano'     => $ano,
    'data'    => $data
]);
