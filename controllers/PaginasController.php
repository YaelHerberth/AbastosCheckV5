<?php
namespace Controllers;

use Model\CarritoModel;
use Model\DepartamentosModel;
use Model\MetricasModel;
use Model\ProductoModel;
use Model\PaginasModel;
use Model\PedidoModel;
use MVC\Router;

class PaginasController{

    public static function index(Router $router){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::agregarAlCarrito('/');
        }

        // Ofertas del día: productos con precio de oferta o promoción, primero los disponibles
        $ofertas = ProductoModel::buscar([
            'q' => '', 'departamento' => null, 'min' => null, 'max' => null,
            'orden' => 'nombre', 'ofertas' => true,
        ]);
        usort($ofertas, function ($a, $b) {
            return ((int) $a->stock <= 0) <=> ((int) $b->stock <= 0);
        });

        // Más vendidos del mes; si todavía no hay ventas, se muestran las novedades
        $idsVendidos = array_column(MetricasModel::masVendidos(30, 8), 'id_producto');
        $masVendidos = array_slice(array_filter(ProductoModel::porIds($idsVendidos), function ($p) {
            return (int) $p->stock > 0;
        }), 0, 4);

        $router->render('paginas/index', [
            'departamentos' => DepartamentosModel::all(),
            'ofertas' => array_slice($ofertas, 0, 4),
            'totalOfertas' => count($ofertas),
            'masVendidos' => $masVendidos ?: ProductoModel::novedades(4),
            'hayVentas' => (bool) $masVendidos,
            'carrito' => CarritoModel::items()
        ]);
    }

    public static function departamentos(Router $router){

        $departamento = new DepartamentosModel();

        $resultado =  $departamento->all();

        $router->render('paginas/departamentos', [
            'resultado' =>$resultado
        ]);
    }

    public static function conocenos(Router $router){

        $router->render('paginas/conocenos', [
            
        ]);
    }
    public static function avisos(Router $router){

        $router->render('paginas/avisos-privacidad', [
            
        ]);
    }

    public static function terminos(Router $router){

        $router->render('paginas/terminos-condiciones', [
            
        ]);
    }

    public static function productos(Router $router){

        $id_departamento = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id_departamento === false) {
            redirigir('/departamentos');
        }

        $producto = new ProductoModel();
        $departamento = $producto->findDepartamento($id_departamento);
        if (!$departamento) {
            redirigir('/departamentos');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::agregarAlCarrito("/departamento?id={$id_departamento}");
        }

        $router->render('paginas/productos', [
            'resultado' => $producto->findProducto($id_departamento),
            'departamento' => $departamento,
            'carrito' => CarritoModel::items()
        ]);
    }

    public static function buscar(Router $router){

        $filtros = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'departamento' => filter_var($_GET['departamento'] ?? null, FILTER_VALIDATE_INT) ?: null,
            'min' => self::precioDe($_GET['min'] ?? null),
            'max' => self::precioDe($_GET['max'] ?? null),
            'orden' => (string) ($_GET['orden'] ?? 'nombre'),
            'ofertas' => !empty($_GET['ofertas']),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Volver a la misma búsqueda después de agregar
            self::agregarAlCarrito('/buscar?' . http_build_query($_GET));
        }

        $router->render('paginas/buscar', [
            'filtros' => $filtros,
            'resultado' => ProductoModel::buscar($filtros),
            'departamentos' => DepartamentosModel::all(),
            'carrito' => CarritoModel::items()
        ]);
    }

    /**
     * Agrega al carrito el producto enviado por POST y redirige.
     * Solo se recibe el ID; el precio se lee de la BD.
     * Redirigir evita que al recargar la página se agregue otra vez.
     */
    private static function agregarAlCarrito(string $volverA): void
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $cantidad = self::cantidadPost();
        $error = $id ? CarritoModel::agregar($id, $cantidad) : 'Producto no válido';

        if ($error) {
            flash('danger', $error);
        } else {
            flash('success', self::mensajeAgregado($id, $cantidad));
        }

        redirigir($volverA);
    }

    /** Cantidad enviada desde el selector de la tarjeta (1 a 99) */
    private static function cantidadPost(): int
    {
        $cantidad = filter_var($_POST['cantidad'] ?? 1, FILTER_VALIDATE_INT);
        return ($cantidad === false || $cantidad < 1) ? 1 : min($cantidad, 99);
    }

    private static function mensajeAgregado(int $id, int $cantidad): string
    {
        $producto = ProductoModel::find($id);
        $nombre = $producto ? $producto->nombre_producto : 'Producto';
        return $cantidad > 1 ? "{$nombre} ×{$cantidad} agregado al carrito" : "{$nombre} agregado al carrito";
    }

    /**
     * Acciones del carrito sin recargar la página (fetch desde app.js).
     * Responde JSON con el mensaje, las unidades y el HTML del carrito lateral.
     * Los mismos formularios funcionan sin JavaScript por las rutas normales.
     */
    public static function carritoAjax(Router $router){

        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $accion = (string) ($_POST['accion'] ?? 'agregar');
        $cantidad = self::cantidadPost();
        $error = null;
        $mensaje = '';

        if (!$id) {
            $error = 'Producto no válido';
        } elseif ($accion === 'agregar') {
            $error = CarritoModel::agregar($id, $cantidad);
            $mensaje = $error ? '' : self::mensajeAgregado($id, $cantidad);
        } elseif ($accion === 'sumar' || $accion === 'restar') {
            $error = CarritoModel::cambiar($id, $accion === 'sumar' ? 1 : -1);
        } elseif ($accion === 'eliminar') {
            CarritoModel::eliminar($id);
            $mensaje = 'Producto eliminado del carrito';
        } else {
            $error = 'Acción no válida';
        }

        // Estado del producto para actualizar su tarjeta en la página
        $estadoProducto = null;
        if ($id && ($producto = ProductoModel::find($id))) {
            $enCarrito = CarritoModel::items()[$id] ?? 0;
            $estadoProducto = [
                'id' => (int) $producto->id,
                'enCarrito' => $enCarrito,
                'disponibles' => max(0, (int) $producto->stock - $enCarrito),
            ];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok' => $error === null,
            'mensaje' => $error ?? $mensaje,
            'unidades' => CarritoModel::totalUnidades(),
            'producto' => $estadoProducto,
            'html' => self::htmlCarritoLateral(),
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private static function htmlCarritoLateral(): string
    {
        $lineasLateral = CarritoModel::detalle();
        $totalLateral = CarritoModel::total($lineasLateral);

        ob_start();
        include __DIR__ . '/../views/paginas/_carrito-lateral.php';
        return ob_get_clean();
    }

    private static function precioDe($valor): ?float
    {
        $precio = filter_var($valor, FILTER_VALIDATE_FLOAT);
        return ($precio === false || $precio < 0) ? null : $precio;
    }

    public static function carrito(Router $router){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            $accion = $_POST['accion'] ?? 'eliminar';

            if ($id !== false && $id !== null) {
                if ($accion === 'sumar' || $accion === 'restar') {
                    $error = CarritoModel::cambiar($id, $accion === 'sumar' ? 1 : -1);
                    if ($error) {
                        flash('danger', $error);
                    }
                } else {
                    CarritoModel::eliminar($id);
                    flash('success', 'Producto eliminado del carrito');
                }
            }
            redirigir('/carrito');
        }

        $lineas = CarritoModel::detalle();

        $router->render('paginas/carrito', [
            'lineas' => $lineas,
            'total' => CarritoModel::total($lineas),
            // Últimos datos de entrega usados (o los que fallaron al validar)
            'checkout' => $_SESSION['checkout'] ?? []
        ]);
    }

    public static function comprar(Router $router){

        // El router ya exige sesión iniciada para esta ruta
        $idUsuario = (int) $_SESSION['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $entrega = self::datosEntrega();

            // Se guardan para volver a llenar el formulario si algo falla
            $_SESSION['checkout'] = $entrega;

            $error = self::validarEntrega($entrega);
            if ($error) {
                flash('danger', $error);
                redirigir('/carrito');
            }

            try {
                $idPedido = PedidoModel::registrar($idUsuario, CarritoModel::items(), $entrega);
            } catch (\DomainException $e) {
                flash('danger', $e->getMessage());
                redirigir('/carrito');
            }

            CarritoModel::vaciar();
            redirigir("/comprar?pedido={$idPedido}");
        }

        // GET: confirmación del pedido
        $idPedido = filter_var($_GET['pedido'] ?? null, FILTER_VALIDATE_INT);
        $pedido = $idPedido ? PedidoModel::buscarDelUsuario($idPedido, $idUsuario) : null;

        if (!$pedido) {
            redirigir('/carrito');
        }

        $router->render('paginas/comprar', [
            'pedido' => $pedido
        ]);
    }

    /** Datos de entrega y pago enviados desde el carrito, ya recortados */
    private static function datosEntrega(): array
    {
        $tipo = (string) ($_POST['tipo_entrega'] ?? 'recoger');
        $esDomicilio = $tipo === 'domicilio';

        return [
            'tipo_entrega' => $tipo,
            'metodo_pago' => (string) ($_POST['metodo_pago'] ?? 'efectivo'),
            // Dirección y teléfono solo se guardan para entrega a domicilio
            'direccion' => $esDomicilio ? self::textoPost('direccion', 255) : null,
            'telefono' => $esDomicilio ? preg_replace('/\D/', '', self::textoPost('telefono', 30)) : null,
            'notas' => self::textoPost('notas', 255) ?: null,
        ];
    }

    private static function textoPost(string $campo, int $max): string
    {
        return mb_substr(trim((string) ($_POST[$campo] ?? '')), 0, $max, 'UTF-8');
    }

    private static function validarEntrega(array $entrega): ?string
    {
        if (!in_array($entrega['tipo_entrega'], PedidoModel::TIPOS_ENTREGA, true)) {
            return 'Elige cómo quieres recibir tu pedido';
        }
        if (!in_array($entrega['metodo_pago'], PedidoModel::METODOS_PAGO, true)) {
            return 'Elige un método de pago';
        }
        if ($entrega['tipo_entrega'] === 'domicilio') {
            if (mb_strlen($entrega['direccion']) < 10) {
                return 'Escribe la dirección completa para la entrega (calle, número y colonia)';
            }
            if (strlen($entrega['telefono']) !== 10) {
                return 'El teléfono debe tener 10 dígitos';
            }
        }
        return null;
    }

    /** Vuelve a poner en el carrito los productos de un pedido anterior */
    public static function repetirPedido(Router $router){

        $idPedido = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $pedido = $idPedido ? PedidoModel::buscarDelUsuario($idPedido, (int) $_SESSION['id']) : null;

        if (!$pedido) {
            redirigir('/mis-pedidos');
        }

        $faltantes = [];
        $agregadas = 0;
        foreach ($pedido['detalle'] as $linea) {
            $cantidad = (int) $linea['cantidad'];
            $puestas = CarritoModel::agregarHasta((int) $linea['id_producto'], $cantidad);
            $agregadas += $puestas;

            if ($puestas < $cantidad) {
                $faltantes[] = $puestas === 0
                    ? "{$linea['nombre_producto']} (agotado)"
                    : "{$linea['nombre_producto']} (solo {$puestas} de {$cantidad})";
            }
        }

        if ($agregadas === 0) {
            flash('warning', 'Ninguno de los productos de ese pedido está disponible ahora.');
            redirigir('/mis-pedidos');
        }

        if ($faltantes) {
            flash('warning', 'Agregamos tu pedido al carrito, excepto: ' . implode(', ', $faltantes) . '. Los precios son los de hoy.');
        } else {
            flash('success', 'Agregamos tu pedido al carrito. Los precios son los de hoy.');
        }

        redirigir('/carrito');
    }

    public static function misPedidos(Router $router){

        $router->render('paginas/mis-pedidos', [
            'pedidos' => PedidoModel::delUsuario((int) $_SESSION['id'])
        ]);
    }

    public static function verPedido(Router $router){

        $idPedido = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        // Solo se encuentra si el pedido es del usuario en sesión
        $pedido = $idPedido ? PedidoModel::buscarDelUsuario($idPedido, (int) $_SESSION['id']) : null;

        if (!$pedido) {
            redirigir('/mis-pedidos');
        }

        $router->render('paginas/pedido', [
            'pedido' => $pedido
        ]);
    }
}