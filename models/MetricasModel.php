<?php

namespace Model;

/**
 * Consultas del panel de métricas del admin.
 * Los pedidos cancelados no cuentan como venta.
 */
class MetricasModel extends ActiveRecord
{
    /** Ventas de hoy y de ayer: número de pedidos e importe */
    public static function ventasDelDia(): array
    {
        $fila = self::$db->query(
            "SELECT
                SUM(DATE(creado_en) = CURDATE()) AS pedidos_hoy,
                COALESCE(SUM(CASE WHEN DATE(creado_en) = CURDATE() THEN total END), 0) AS total_hoy,
                SUM(DATE(creado_en) = CURDATE() - INTERVAL 1 DAY) AS pedidos_ayer,
                COALESCE(SUM(CASE WHEN DATE(creado_en) = CURDATE() - INTERVAL 1 DAY THEN total END), 0) AS total_ayer
             FROM pedidos
             WHERE estado <> 'cancelado' AND creado_en >= CURDATE() - INTERVAL 1 DAY"
        )->fetch_assoc();

        return [
            'pedidos_hoy' => (int) $fila['pedidos_hoy'],
            'total_hoy' => (float) $fila['total_hoy'],
            'pedidos_ayer' => (int) $fila['pedidos_ayer'],
            'total_ayer' => (float) $fila['total_ayer'],
        ];
    }

    /** Pedidos que el personal todavía tiene que atender */
    public static function porAtender(): int
    {
        $fila = self::$db->query(
            "SELECT COUNT(*) AS n FROM pedidos WHERE estado IN ('pendiente', 'preparando', 'listo')"
        )->fetch_assoc();

        return (int) $fila['n'];
    }

    /** Ventas de los últimos 7 días (incluye hoy), un registro por día aunque no haya ventas */
    public static function ventasSemana(): array
    {
        // "Hoy" según MySQL, para no mezclar con la zona horaria de PHP
        $hoy = self::$db->query('SELECT CURDATE() AS hoy')->fetch_assoc()['hoy'];

        $dias = [];
        for ($i = 6; $i >= 0; $i--) {
            $dias[date('Y-m-d', strtotime("{$hoy} -{$i} days"))] = 0.0;
        }

        $resultado = self::$db->query(
            "SELECT DATE(creado_en) AS dia, SUM(total) AS total
             FROM pedidos
             WHERE estado <> 'cancelado' AND creado_en >= CURDATE() - INTERVAL 6 DAY
             GROUP BY DATE(creado_en)"
        );
        while ($fila = $resultado->fetch_assoc()) {
            if (isset($dias[$fila['dia']])) {
                $dias[$fila['dia']] = (float) $fila['total'];
            }
        }

        return $dias;
    }

    /** Productos más vendidos en los últimos $dias días */
    public static function masVendidos(int $dias = 30, int $limite = 5): array
    {
        $consulta = self::$db->prepare(
            "SELECT d.id_producto, d.nombre_producto,
                    SUM(d.cantidad) AS piezas,
                    SUM(d.cantidad * d.precio_unitario - d.descuento) AS importe
             FROM pedido_detalle AS d
             JOIN pedidos AS p ON p.id = d.id_pedido
             WHERE p.estado <> 'cancelado' AND p.creado_en >= CURDATE() - INTERVAL ? DAY
             GROUP BY d.id_producto, d.nombre_producto
             ORDER BY piezas DESC, importe DESC
             LIMIT ?"
        );
        $consulta->bind_param('ii', $dias, $limite);
        $consulta->execute();

        return $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Productos con existencias en STOCK_BAJO o menos, los más urgentes primero */
    public static function inventarioBajo(): array
    {
        $consulta = self::$db->prepare(
            'SELECT p.id, p.nombre_producto, p.stock, d.nombre_departamento
             FROM productos AS p LEFT JOIN departamentos AS d ON d.id = p.id_departamento
             WHERE p.stock <= ?
             ORDER BY p.stock ASC, p.nombre_producto'
        );
        $limite = STOCK_BAJO;
        $consulta->bind_param('i', $limite);
        $consulta->execute();

        return $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
