<?php

namespace Model;

class PedidoModel extends ActiveRecord
{
    protected static $tabla = 'pedidos';

    /**
     * Crea el pedido y descuenta el inventario en una sola transacción.
     * Lanza \DomainException con un mensaje para el cliente si algo no alcanza.
     *
     * @param array $items [id_producto => cantidad]
     * @param array $entrega ya validado: tipo_entrega, direccion, telefono, notas, metodo_pago
     * @return int ID del pedido creado
     */
    public static function registrar(int $idUsuario, array $items, array $entrega): int
    {
        if (empty($items)) {
            throw new \DomainException('Tu carrito está vacío');
        }

        $db = self::$db;
        $db->begin_transaction();

        try {
            $lineas = [];
            $total = 0;

            // FOR UPDATE bloquea los productos hasta terminar, así dos compras
            // simultáneas no pueden vender la misma existencia
            $consulta = $db->prepare('SELECT id, nombre_producto, precio_producto, precio_oferta, promo_tipo, stock FROM productos WHERE id = ? FOR UPDATE');

            foreach ($items as $idProducto => $cantidad) {
                $consulta->bind_param('i', $idProducto);
                $consulta->execute();
                $producto = $consulta->get_result()->fetch_assoc();

                if (!$producto) {
                    throw new \DomainException('Un producto de tu carrito ya no está disponible');
                }
                if ((int) $producto['stock'] < $cantidad) {
                    throw new \DomainException(
                        "Solo quedan {$producto['stock']} piezas de {$producto['nombre_producto']}"
                    );
                }

                $precio = Precios::linea($producto['precio_producto'], $producto['precio_oferta'], $producto['promo_tipo'], $cantidad);
                $lineas[] = [$producto, $cantidad, $precio];
                $total += $precio['subtotal'];
            }

            $costoEnvio = Precios::envio($entrega['tipo_entrega'], $total);
            $total = round($total + $costoEnvio, 2);

            $minutos = $entrega['tipo_entrega'] === 'domicilio' ? MINUTOS_DOMICILIO : MINUTOS_RECOGER;

            // La hora se calcula en MySQL, igual que creado_en, para usar el mismo reloj
            $insertarPedido = $db->prepare(
                'INSERT INTO pedidos (id_usuario, total, tipo_entrega, direccion, telefono, notas, metodo_pago, costo_envio, listo_estimado)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW() + INTERVAL ? MINUTE)'
            );
            $insertarPedido->bind_param(
                'idsssssdi',
                $idUsuario,
                $total,
                $entrega['tipo_entrega'],
                $entrega['direccion'],
                $entrega['telefono'],
                $entrega['notas'],
                $entrega['metodo_pago'],
                $costoEnvio,
                $minutos
            );
            $insertarPedido->execute();
            $idPedido = $db->insert_id;

            $insertarDetalle = $db->prepare(
                'INSERT INTO pedido_detalle (id_pedido, id_producto, nombre_producto, precio_unitario, cantidad, promo, descuento)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $descontarStock = $db->prepare('UPDATE productos SET stock = stock - ? WHERE id = ?');

            foreach ($lineas as [$producto, $cantidad, $precio]) {
                $idProducto = (int) $producto['id'];

                $insertarDetalle->bind_param(
                    'iisdisd',
                    $idPedido,
                    $idProducto,
                    $producto['nombre_producto'],
                    $precio['unitario'],
                    $cantidad,
                    $precio['promo'],
                    $precio['descuento']
                );
                $insertarDetalle->execute();

                $descontarStock->bind_param('ii', $cantidad, $idProducto);
                $descontarStock->execute();
            }

            $db->commit();
            return $idPedido;
        } catch (\Throwable $e) {
            $db->rollback();
            throw $e;
        }
    }

    public const ESTADOS = ['pendiente', 'preparando', 'listo', 'entregado', 'cancelado'];

    // Un pedido entregado o cancelado ya no cambia de estado
    public const ESTADOS_FINALES = ['entregado', 'cancelado'];

    /** Color de Bootstrap para la etiqueta de cada estado */
    public static function colorEstado(string $estado): string
    {
        return [
            'pendiente' => 'warning',
            'preparando' => 'info',
            'listo' => 'primary',
            'entregado' => 'success',
            'cancelado' => 'secondary',
        ][$estado] ?? 'light';
    }

    public const TIPOS_ENTREGA = ['recoger', 'domicilio'];
    public const METODOS_PAGO = ['efectivo', 'tarjeta'];

    public static function etiquetaEntrega(string $tipo): string
    {
        return $tipo === 'domicilio' ? 'Entrega a domicilio' : 'Recoger en tienda';
    }

    /** Ej. "Efectivo al recibir" o "Tarjeta en tienda" */
    public static function etiquetaPago(string $metodo, string $tipoEntrega): string
    {
        $donde = $tipoEntrega === 'domicilio' ? 'al recibir' : 'en tienda';
        return ($metodo === 'tarjeta' ? 'Tarjeta' : 'Efectivo') . " {$donde}";
    }

    /** Pedidos de un cliente, del más reciente al más antiguo, con número de piezas */
    public static function delUsuario(int $idUsuario): array
    {
        $consulta = self::$db->prepare(
            'SELECT p.*, COALESCE(SUM(d.cantidad), 0) AS piezas
             FROM pedidos AS p LEFT JOIN pedido_detalle AS d ON d.id_pedido = p.id
             WHERE p.id_usuario = ?
             GROUP BY p.id ORDER BY p.id DESC'
        );
        $consulta->bind_param('i', $idUsuario);
        $consulta->execute();

        return $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Pedidos para el admin con búsqueda, orden y paginación en el servidor (ver Listado).
     * Busca por número de pedido (12 o #12), nombre o correo del cliente.
     */
    public static function listadoAdmin(?string $estado = null): Listado
    {
        $listado = new Listado([
            'id' => 'p.id',
            'fecha' => 'p.creado_en',
            'cliente' => 'u.username_usuario',
            'piezas' => 'piezas',
            'total' => 'p.total',
            'estado' => 'p.estado',
        ], 'id', 'desc', ['estado' => $estado]);

        $condiciones = [];
        $tipos = '';
        $valores = [];
        if ($estado !== null) {
            $condiciones[] = 'p.estado = ?';
            $tipos = 's';
            $valores[] = $estado;
        }

        return $listado->cargar(
            self::$db,
            'SELECT p.*, u.username_usuario, u.email_usuario, COALESCE(SUM(d.cantidad), 0) AS piezas',
            'FROM pedidos AS p
             JOIN usuarios AS u ON u.id = p.id_usuario
             LEFT JOIN pedido_detalle AS d ON d.id_pedido = p.id',
            ['u.username_usuario', 'u.email_usuario'],
            'p.id',
            $condiciones,
            $tipos,
            $valores,
            'GROUP BY p.id'
        );
    }

    /** Cuántos pedidos hay en cada estado, para los filtros del admin */
    public static function conteoPorEstado(): array
    {
        $conteo = array_fill_keys(self::ESTADOS, 0);
        $resultado = self::$db->query('SELECT estado, COUNT(*) AS total FROM pedidos GROUP BY estado');
        while ($fila = $resultado->fetch_assoc()) {
            $conteo[$fila['estado']] = (int) $fila['total'];
        }

        return $conteo;
    }

    /** Pedido con su detalle y los datos del cliente (vista del admin) */
    public static function buscarConCliente(int $idPedido): ?array
    {
        $consulta = self::$db->prepare(
            'SELECT p.*, u.username_usuario, u.email_usuario
             FROM pedidos AS p JOIN usuarios AS u ON u.id = p.id_usuario
             WHERE p.id = ?'
        );
        $consulta->bind_param('i', $idPedido);
        $consulta->execute();
        $pedido = $consulta->get_result()->fetch_assoc();

        if (!$pedido) {
            return null;
        }

        $pedido['detalle'] = self::detalleDe($idPedido);
        return $pedido;
    }

    /**
     * Cambia el estado de un pedido. Al cancelar, devuelve las piezas al inventario.
     * Lanza \DomainException con un mensaje para el admin si el cambio no es válido.
     */
    public static function cambiarEstado(int $idPedido, string $nuevo): void
    {
        if (!in_array($nuevo, self::ESTADOS, true)) {
            throw new \DomainException('Estado no válido');
        }

        $db = self::$db;
        $db->begin_transaction();

        try {
            $consulta = $db->prepare('SELECT estado FROM pedidos WHERE id = ? FOR UPDATE');
            $consulta->bind_param('i', $idPedido);
            $consulta->execute();
            $pedido = $consulta->get_result()->fetch_assoc();

            if (!$pedido) {
                throw new \DomainException('El pedido no existe');
            }
            if (in_array($pedido['estado'], self::ESTADOS_FINALES, true)) {
                throw new \DomainException("El pedido ya está {$pedido['estado']} y no se puede cambiar");
            }

            $actualizar = $db->prepare('UPDATE pedidos SET estado = ? WHERE id = ?');
            $actualizar->bind_param('si', $nuevo, $idPedido);
            $actualizar->execute();

            if ($nuevo === 'cancelado') {
                // Si el producto ya fue borrado del catálogo, el UPDATE simplemente no afecta filas
                $devolver = $db->prepare(
                    'UPDATE productos AS pr JOIN pedido_detalle AS d ON d.id_producto = pr.id
                     SET pr.stock = pr.stock + d.cantidad
                     WHERE d.id_pedido = ?'
                );
                $devolver->bind_param('i', $idPedido);
                $devolver->execute();
            }

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollback();
            throw $e;
        }
    }

    private static function detalleDe(int $idPedido): array
    {
        $consulta = self::$db->prepare('SELECT * FROM pedido_detalle WHERE id_pedido = ? ORDER BY id');
        $consulta->bind_param('i', $idPedido);
        $consulta->execute();

        return $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Devuelve el pedido con su detalle solo si pertenece al usuario.
     */
    public static function buscarDelUsuario(int $idPedido, int $idUsuario): ?array
    {
        $db = self::$db;

        $consulta = $db->prepare('SELECT * FROM pedidos WHERE id = ? AND id_usuario = ?');
        $consulta->bind_param('ii', $idPedido, $idUsuario);
        $consulta->execute();
        $pedido = $consulta->get_result()->fetch_assoc();

        if (!$pedido) {
            return null;
        }

        $pedido['detalle'] = self::detalleDe($idPedido);

        return $pedido;
    }
}
