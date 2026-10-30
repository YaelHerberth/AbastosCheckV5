<?php
namespace MVC;

class Router
{
    public $rutasGET = [];
    public $rutasPOST = [];

    // Cada ruta se registra también con el prefijo /public que usa el hosting
    public function get($url, $fn)
    {
        foreach (self::variantes($url) as $ruta) {
            $this->rutasGET[$ruta] = $fn;
        }
    }
    public function post($url, $fn)
    {
        foreach (self::variantes($url) as $ruta) {
            $this->rutasPOST[$ruta] = $fn;
        }
    }

    private static function variantes(string $url): array
    {
        return $url === '/' ? ['/', '/public', '/public/'] : [$url, '/public' . $url];
    }

    public function comprobarRutas()
    {
        $auth = $_SESSION['login'] ?? false;
        $esAdmin = ($_SESSION['rol'] ?? '') === 'admin';

        // Solo la ruta, sin "?id=1&..."
        $urlActual = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $metodo = $_SERVER['REQUEST_METHOD'];

        // Ruta sin el prefijo /public que se usa en el hosting
        $ruta = preg_replace('#^/public(?=/|$)#', '', $urlActual);
        $ruta = rtrim($ruta, '/') ?: '/';

        // URLs anteriores: se redirigen (301) para que no fallen enlaces guardados
        if ($metodo === 'GET' && isset(self::URLS_ANTERIORES[$ruta])) {
            $query = $_SERVER['QUERY_STRING'] ?? '';
            header('Location: ' . self::URLS_ANTERIORES[$ruta] . ($query !== '' ? "?{$query}" : ''), true, 301);
            exit;
        }

        // Rutas que requieren sesión iniciada
        $rutasLogin = ['/profile', '/comprar', '/mis-pedidos', '/mis-pedidos/ver', '/mis-pedidos/repetir'];

        // Todo lo que empieza con /admin requiere rol de administrador
        $esRutaAdmin = $ruta === '/admin' || strpos($ruta, '/admin/') === 0;

        if (($esRutaAdmin || in_array($ruta, $rutasLogin)) && !$auth) {
            redirigir($ruta === '/comprar' ? '/login?resultado=3' : '/login?resultado=2');
        }

        if ($esRutaAdmin && !$esAdmin) {
            $this->paginaError(403);
        }

        if ($ruta === '/login' && $auth) {
            redirigir('/profile');
        }

        // Se busca con la ruta normalizada: sin /public y sin "/" al final
        if ($metodo === 'GET') {
            $fn = $this->rutasGET[$ruta] ?? NULL;
        } else {
            $fn = $this->rutasPOST[$ruta] ?? NULL;
        }

        if ($fn) {
            // La URL existe y hay una funcion asociada
            call_user_func($fn, $this);
        } else {
            $this->paginaError(404);
        }
    }

    // Ruta anterior => ruta nueva
    private const URLS_ANTERIORES = [
        '/departamentoss' => '/departamentos',
        '/productoss' => '/departamento',
        '/productos' => '/buscar',
        '/users' => '/admin/usuarios',
        '/pedidos' => '/admin/pedidos',
        '/pedidos/panel' => '/admin/metricas',
    ];

    /** Muestra la página de error (404 o 403) con el diseño del sitio y termina */
    public function paginaError(int $codigo): void
    {
        http_response_code($codigo);
        $this->render('paginas/error', ['codigo' => $codigo]);
        exit;
    }

    // Muestra una vista
    public function render($view, $datos = [])
    {

        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        ob_start(); // Almacenamiento en memoria durante un momento...
        include __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); // Limpiar el buffer
        include __DIR__ . "/views/layout.php";
    }
}
