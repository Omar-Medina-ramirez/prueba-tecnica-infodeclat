<?php
namespace VentasPlus\Services;

use PDO;

class CommissionService {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function calcular($mes, $ano) {
        $sql = "
            SELECT v.id AS vendedor_id, v.nombre,
                   IFNULL(SUM(ven.valor),0) AS total_ventas,
                   IFNULL(SUM(dev.valor),0) AS total_devoluciones
            FROM vendedores v
            LEFT JOIN ventas ven ON v.id = ven.vendedor_id AND MONTH(ven.fecha)=:mes AND YEAR(ven.fecha)=:ano
            LEFT JOIN devoluciones dev ON v.id = dev.vendedor_id AND MONTH(dev.fecha)=:mes AND YEAR(dev.fecha)=:ano
            GROUP BY v.id, v.nombre
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['mes'=>$mes,'ano'=>$ano]);
        $result = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ventas = $row['total_ventas'];
            $devoluciones = $row['total_devoluciones'];
            $tasaDevolucion = $ventas > 0 ? ($devoluciones / $ventas) * 100 : 0;

            $comisionBase = $ventas * 0.05;
            $bono = ($ventas > 50000000) ? $ventas * 0.02 : 0;
            $penalizacion = ($tasaDevolucion > 5) ? $ventas * 0.01 : 0;
            $final = $comisionBase + $bono - $penalizacion;

            $result[] = [
                'vendedor' => $row['nombre'],
                'ventas' => $ventas,
                'devoluciones' => $devoluciones,
                'comision_base' => $comisionBase,
                'bono' => $bono,
                'penalizacion' => $penalizacion,
                'comision_final' => $final,
            ];
        }

        return $result;
    }
}