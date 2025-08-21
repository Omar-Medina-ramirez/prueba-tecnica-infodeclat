<?php
namespace VentasPlus\Services;

use PDO;

class CommissionService {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function calcular($mes, $ano) {
        // Si mes es null → acumulado del año completo
        $filtroMesVentas = "";
        $filtroMesDev = "";
        $params = ['ano' => $ano];

        if (!empty($mes)) {
            $filtroMesVentas = "AND MONTH(ven.fecha) = :mes";
            $filtroMesDev = "AND MONTH(dev.fecha) = :mes";
            $params['mes'] = $mes;
        }

        $sql = "
            SELECT v.id AS vendedor_id, v.nombre,
                   IFNULL(SUM(ven.valor),0) AS total_ventas,
                   IFNULL(SUM(dev.valor),0) AS total_devoluciones
            FROM vendedores v
            LEFT JOIN ventas ven 
                ON v.id = ven.vendedor_id 
               AND YEAR(ven.fecha) = :ano $filtroMesVentas
            LEFT JOIN devoluciones dev 
                ON v.id = dev.vendedor_id 
               AND YEAR(dev.fecha) = :ano $filtroMesDev
            GROUP BY v.id, v.nombre
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ventas = (float) $row['total_ventas'];
            $devoluciones = (float) $row['total_devoluciones'];

            // % de devoluciones respecto a ventas
            $tasaDevolucion = $ventas > 0 ? ($devoluciones / $ventas) * 100 : 0;

            // Comisión base
            $comisionBase = $ventas * 0.05;

            // Bono adicional (para pruebas 5M, en producción 50M)
            $bono = ($ventas > 5000000) ? $ventas * 0.02 : 0;

            // Penalización si devoluciones > 5%
            $penalizacion = ($tasaDevolucion > 5) ? $ventas * 0.01 : 0;

            // Comisión final
            $final = $comisionBase + $bono - $penalizacion;

            $result[] = [
                'vendedor'       => $row['nombre'],
                'ventas'         => $ventas,
                'devoluciones'   => $devoluciones,
                'tasa_devolucion'=> round($tasaDevolucion, 2) . '%',
                'comision_base'  => $comisionBase,
                'bono'           => $bono,
                'penalizacion'   => $penalizacion,
                'comision_final' => $final,
            ];
        }

        return $result;
    }
}
