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

$mes = $_GET['mes'] ?? date("n");
$ano = $_GET['ano'] ?? date("Y");

$service = new CommissionService($pdo);
$data = $service->calcular($mes, $ano);

header('Content-Type: application/json');
echo json_encode(['success' => true, 'data' => $data]);