<?php

namespace Controllers;

use Model\MetricasModel;
use Model\PedidoModel;
use MVC\Router;

// Panel de pedidos del admin. El Router ya exige rol admin para todo /admin.
class PedidosController
{
    public static function panel(Router $router)
    {
        $router->render('pedidos/panel', [
            'ventas' => MetricasModel::ventasDelDia(),
            'porAtender' => MetricasModel::porAtender(),
            'semana' => MetricasModel::ventasSemana(),
            'masVendidos' => MetricasModel::masVendidos(),
            'inventarioBajo' => MetricasModel::inventarioBajo()
        ]);
    }

    public static function index(Router $router)
    {
        $estado = $_GET['estado'] ?? null;
        if (!in_array($estado, PedidoModel::ESTADOS, true)) {
            $estado = null;
        }

        $listado = PedidoModel::listadoAdmin($estado);

        $router->render('pedidos/index', [
            'listado' => $listado,
            'pedidos' => $listado->filas,
            'conteo' => PedidoModel::conteoPorEstado(),
            'estadoActual' => $estado
        ]);
    }

    public static function ver(Router $router)
    {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        $pedido = $id ? PedidoModel::buscarConCliente($id) : null;

        if (!$pedido) {
            flash('danger', 'El pedido no existe');
            redirigir('/admin/pedidos');
        }

        $router->render('pedidos/ver', [
            'pedido' => $pedido
        ]);
    }

    public static function estado(Router $router)
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $nuevo = (string) ($_POST['estado'] ?? '');

        if (!$id) {
            redirigir('/admin/pedidos');
        }

        try {
            PedidoModel::cambiarEstado($id, $nuevo);
            $mensaje = $nuevo === 'cancelado'
                ? "Pedido #{$id} cancelado. Las piezas regresaron al inventario."
                : "Pedido #{$id} ahora está: {$nuevo}";
            flash('success', $mensaje);
        } catch (\DomainException $e) {
            flash('danger', $e->getMessage());
        }

        redirigir("/admin/pedidos/ver?id={$id}");
    }
}
