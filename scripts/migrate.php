<?php
/**
 * Database Migration Script
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

class MigrationRunner {
    private $pdo;
    private $migrationsPath;
    
    public function __construct() {
        $this->migrationsPath = __DIR__ . '/../database/migrations';
        $this->connectDatabase();
    }
    
    private function connectDatabase() {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;charset=utf8mb4',
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'] ?? 3306
        );
        
        try {
            $this->pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if not exists
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `{$_ENV['DB_NAME']}`");
            $this->pdo->exec("USE `{$_ENV['DB_NAME']}`");
            
            echo "✅ Conexión a base de datos establecida\n";
        } catch (PDOException $e) {
            die("❌ Error de conexión: " . $e->getMessage() . "\n");
        }
    }
    
    public function run() {
        echo "🚀 Ejecutando migraciones...\n\n";
        
        // Create migrations table
        $this->createMigrationsTable();
        
        // Get migration files
        $files = glob($this->migrationsPath . '/*.sql');
        sort($files);
        
        foreach ($files as $file) {
            $filename = basename($file);
            
            if ($this->isMigrationExecuted($filename)) {
                echo "⏭️  Saltando {$filename} (ya ejecutada)\n";
                continue;
            }
            
            echo "⚡ Ejecutando {$filename}...\n";
            
            try {
                $sql = file_get_contents($file);
                $this->pdo->exec($sql);
                $this->markMigrationAsExecuted($filename);
                echo "✅ {$filename} ejecutada exitosamente\n";
            } catch (PDOException $e) {
                echo "❌ Error ejecutando {$filename}: " . $e->getMessage() . "\n";
                break;
            }
        }
        
        echo "\n🎉 Migraciones completadas!\n";
    }
    
    private function createMigrationsTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                filename VARCHAR(255) NOT NULL UNIQUE,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $this->pdo->exec($sql);
                echo "🗄️ Tabla de migraciones creada\n";
            }
        
            private function isMigrationExecuted($filename) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM migrations WHERE filename = ?");
                $stmt->execute([$filename]);
                return $stmt->fetchColumn() > 0;
            }
        
            private function markMigrationAsExecuted($filename) {
                $stmt = $this->pdo->prepare("INSERT INTO migrations (filename) VALUES (?)");
                $stmt->execute([$filename]);
            }
        }
        
        $runner = new MigrationRunner();
        $runner->run();