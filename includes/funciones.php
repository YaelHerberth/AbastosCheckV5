<?php
// require 'app.php';

define('TEMPLATES_URL', __DIR__ . '/templates');
define('FUNCIONES_URL', __DIR__ . 'funciones.php');
define('CARPETA_IMAGENES_USUARIOS', $_SERVER['DOCUMENT_ROOT'] . '/build/img/Usuarios/');
define('CARPETA_IMAGENES_DEPARTAMENTOS', $_SERVER['DOCUMENT_ROOT'] . '/build/img/Departamentos/');
define('CARPETA_IMAGENES_PRODUCTOS', $_SERVER['DOCUMENT_ROOT'] . '/build/img/Productos/');

function incluirTemplate(string $nombre, bool $inicio = false)
{
    include TEMPLATES_URL . "/{$nombre}.php";
}

function estaAutenticado()
{
    session_start();

    if (!$_SESSION['login']) {
        header('Location: ../index.php');
        return $auth = false;
    }
    return $auth = true;
}

function debuguear($variable)
{
    var_dump($variable);
    exit;
}

// Escapa / Sanitizar el HTML
function s($html): string
{
    return htmlspecialchars((string) ($html ?? ''), ENT_QUOTES, 'UTF-8');
}

// Redirige y detiene la ejecución (sin exit el resto de la página se sigue procesando)
function redirigir(string $url): void
{
    header("Location: {$url}");
    exit;
}

// Mensaje que se muestra una sola vez en la siguiente página
// $tipo: success, danger, warning, info (clases de alerta de Bootstrap)
function flash(string $tipo, string $texto): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

function tomarFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function esAdmin(): bool
{
    return ($_SESSION['rol'] ?? '') === 'admin';
}

// Validar tipo de contenido
function validarTipoContenido($tipo)
{
    $tipos = ['vendedor', 'propiedad'];

    return in_array($tipo, $tipos);
}

// Muestra los mensajes
function mostrarNotificacion($codigo)
{
    $mensaje = '';

    switch ($codigo) {
        case 1:
            $mensaje = 'Creado correctamente';
            break;
        case 2:
            $mensaje = 'Actualizado correctamente';
            break;
        case 3:
            $mensaje = 'Eliminado correctamente';
            break;
        default:
            $mensaje = false;
            break;
    }
    return $mensaje;
}


function validarORedireccionar(string $url)
{
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

    if ($id === false) {
        redirigir($url);
    }

    return $id;
}

/**
 * Ícono de Bootstrap Icons para un departamento, según palabras de su nombre.
 * Si no coincide ninguna, usa una tiendita.
 */
function iconoDepartamento(?string $nombre): string
{
    $nombre = mb_strtolower((string) $nombre, 'UTF-8');
    $iconos = [
        'despensa' => 'bi-basket2-fill',
        'abarrote' => 'bi-basket2-fill',
        'electr' => 'bi-plug',
        'tecno' => 'bi-tv',
        'mascota' => 'bi-heart-fill',
        'fruta' => 'bi-apple',
        'verdura' => 'bi-apple',
        'bebida' => 'bi-cup-straw',
        'jugo' => 'bi-cup-straw',
        'limpieza' => 'bi-droplet-fill',
        'hogar' => 'bi-house-heart-fill',
        'carne' => 'bi-egg-fried',
        'pescado' => 'bi-egg-fried',
        'lácteo' => 'bi-egg-fried',
        'bebé' => 'bi-balloon-heart',
        'bebe' => 'bi-balloon-heart',
        'farmacia' => 'bi-capsule',
        'salud' => 'bi-bandaid',
        'belleza' => 'bi-flower1',
        'cuidado' => 'bi-flower1',
    ];

    foreach ($iconos as $palabra => $icono) {
        if (strpos($nombre, $palabra) !== false) {
            return $icono;
        }
    }

    return 'bi-shop';
}
