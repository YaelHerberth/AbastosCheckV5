<?php
if (!isset($_SESSION)) {
    session_start();
}

$auth = $_SESSION['login'] ?? false;

use Model\CarritoModel;
use Model\DepartamentosModel;

$flash = tomarFlash();

$departamento = new DepartamentosModel();
$resultado =  $departamento->all();

$unidadesCarrito = CarritoModel::totalUnidades();

// Carrito lateral (offcanvas)
$lineasLateral = CarritoModel::detalle();
$totalLateral = CarritoModel::total($lineasLateral);

// Ruta actual sin /public, para marcar la pestaña activa
$rutaActual = preg_replace('#^/public(?=/|$)#', '', parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$rutaActual = rtrim($rutaActual, '/') ?: '/';
$activa = function (string $ruta) use ($rutaActual): string {
    return ($ruta === '/' ? $rutaActual === '/' : strpos($rutaActual, $ruta) === 0) ? 'activa' : '';
};
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#14753D">

    <title>AbastosCheck · Tu tienda de la esquina, en línea</title>

    <!-- Aplica el tema guardado antes de pintar, para que no parpadee al recargar -->
    <script>
        (function() {
            var tema = null;
            try { tema = localStorage.getItem('tema'); } catch (e) {}
            if (tema !== 'light' && tema !== 'dark') {
                tema = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-bs-theme', tema);
        })();
    </script>

    <link rel="shortcut icon" href="/build/img/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/build/css/app.css">
    <script src="/build/js/app.js"></script>
    <script src="/build/js/bootstrap/bootstrap.bundle.min.js"></script>
</head>

<body class="con-barra-inferior">
    <a href="#contenido" class="visually-hidden-focusable btn btn-success position-absolute m-2" style="z-index: 2000;">Saltar al contenido</a>
    <div class="container-fluid">
        <header class="site-header d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
            <div class="col-12 col-md-auto mb-2 mb-md-0 text-center text-md-start">
                <a href="/" class="marca d-inline-flex align-items-center text-decoration-none">
                    <img src="/build/img/LogoH2.svg" alt="" class="img-fluid ms-2 me-3" style="width: 3rem;">
                    <span class="marca-nombre">Abastos<span>Check</span></span>
                </a>
            </div>

            <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
                <li><a href="/" class="nav-link link-success px-2">Inicio</a></li>
                <li><a href="/buscar?ofertas=1" class="nav-link link-danger px-2"><i class="bi bi-tag-fill"></i> Ofertas</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle link-success" href="/departamentos" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Departamentos
                    </a>
                    <ul class="dropdown-menu">
                        <?php foreach ($resultado as $departamento) : ?>
                            <li>
                                <a class="dropdown-item" href="/departamento?id=<?= s($departamento->id) ?>">
                                    <i class="bi <?= iconoDepartamento($departamento->nombre_departamento) ?> me-1"></i>
                                    <?= s($departamento->nombre_departamento) ?>
                                </a>
                            </li>
                        <?php endforeach ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/departamentos">Ver todos</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle link-success" href="/conocenos" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Sobre nosotros
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/conocenos">Conócenos</a></li>
                        <li><a class="dropdown-item" href="/avisos-privacidad">Aviso de privacidad</a></li>
                        <li><a class="dropdown-item" href="/terminos-condiciones">Términos y condiciones</a></li>
                    </ul>
                </li>
            </ul>

            <form action="/buscar" method="GET" class="col-12 col-md-auto mb-2 mb-md-0 mx-md-2 buscador" role="search">
                <div class="input-group">
                    <input type="search" name="q" class="form-control" placeholder="Buscar productos…" aria-label="Buscar productos"
                        value="<?= $rutaActual === '/buscar' ? s($_GET['q'] ?? '') : '' ?>">
                    <button class="btn btn-success" type="submit" aria-label="Buscar"><i class="bi bi-search"></i></button>
                </div>
            </form>

            <div class="col-12 col-md-auto text-center text-md-end text-nowrap d-flex align-items-center justify-content-center gap-2">
                <!-- Abre el carrito lateral; sin JavaScript lleva a /carrito -->
                <a href="/carrito" class="btn btn-outline-success position-relative me-2 d-none d-md-inline-block"
                    data-bs-toggle="offcanvas" data-bs-target="#carritoLateral" aria-controls="carritoLateral" aria-label="Abrir carrito">
                    <i class="bi bi-cart-fill"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger js-contador <?= $unidadesCarrito > 0 ? '' : 'd-none' ?>"><?= $unidadesCarrito ?></span>
                </a>
                <?php if (!$auth) : ?>
                    <a href="/login" class="btn btn-outline-success"><i class="bi bi-person-circle"></i> Entrar</a>
                    <a href="/signup" class="btn btn-success">Registrarse</a>
                <?php else :  ?>
                    <div class="dropdown">
                        <a role="button" href="/profile" class="link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menú de <?= s($_SESSION['username']) ?>">
                            <?php if (!$_SESSION['imagen']) : ?>
                                <img src="/build/img/profile-default.jpg" alt="" width="32" height="32" class="rounded-circle">
                            <?php else : ?>
                                <img src="/build/img/Usuarios/<?= s($_SESSION['imagen']) ?>" alt="" width="32" height="32" class="rounded-circle object-fit-cover">
                            <?php endif ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end text-small">
                            <li>
                                <p class="dropdown-item-text fw-bold text-success text-uppercase mb-0"><?= s($_SESSION['username']) ?></p>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/profile"><i class="bi bi-person-circle"></i> Perfil</a></li>
                            <li><a class="dropdown-item" href="/mis-pedidos"><i class="bi bi-bag"></i> Mis pedidos</a></li>
                            <?php if (esAdmin()) : ?>
                                <li><a class="dropdown-item" href="/admin/pedidos"><i class="bi bi-receipt"></i> Pedidos (admin)</a></li>
                            <?php endif ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
                        </ul>
                    </div>
                <?php endif ?>
                <button type="button" id="button-darkmode" class="btn btn-tema dark-mode" aria-label="Cambiar tema claro u oscuro">
                    <i id="darkmode" class="bi bi-moon-fill"></i>
                </button>
            </div>
        </header>
    </div>

    <?php if ($flash) : ?>
        <div class="container">
            <div class="alert alert-<?= s($flash['tipo']) ?> alert-dismissible fade show text-center" role="alert">
                <?= s($flash['texto']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        </div>
    <?php endif ?>

    <main id="contenido">
        <?php echo $contenido; ?>
    </main>

    <!-- Carrito lateral -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="carritoLateral" aria-labelledby="carritoLateralTitulo">
        <div class="offcanvas-header border-bottom">
            <h2 class="offcanvas-title h5 mb-0" id="carritoLateralTitulo"><i class="bi bi-cart-fill text-success"></i> Tu carrito</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body" id="carritoLateralCuerpo">
            <?php include __DIR__ . '/paginas/_carrito-lateral.php'; ?>
        </div>
    </div>

    <!-- Avisos breves (agregar al carrito, errores) -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3 contenedor-avisos" aria-live="polite" aria-atomic="true"></div>

    <!-- Barra inferior en celular -->
    <nav class="barra-inferior d-md-none" aria-label="Navegación principal">
        <a href="/" class="<?= $activa('/') ?>"><i class="bi bi-house-door-fill"></i><span>Inicio</span></a>
        <a href="/buscar" class="<?= $activa('/buscar') ?>"><i class="bi bi-search"></i><span>Buscar</span></a>
        <a href="/carrito" class="position-relative <?= $activa('/carrito') ?>" data-bs-toggle="offcanvas" data-bs-target="#carritoLateral" aria-controls="carritoLateral">
            <span class="position-relative d-inline-block">
                <i class="bi bi-cart-fill"></i>
                <span class="badge rounded-pill bg-danger js-contador contador-barra <?= $unidadesCarrito > 0 ? '' : 'd-none' ?>"><?= $unidadesCarrito ?></span>
            </span>
            <span>Carrito</span>
        </a>
        <?php if ($auth) : ?>
            <a href="/mis-pedidos" class="<?= $activa('/mis-pedidos') ?>"><i class="bi bi-bag-fill"></i><span>Pedidos</span></a>
            <a href="/profile" class="<?= $activa('/profile') ?>"><i class="bi bi-person-circle"></i><span>Cuenta</span></a>
        <?php else : ?>
            <a href="/login" class="<?= $activa('/login') ?>"><i class="bi bi-person-circle"></i><span>Entrar</span></a>
        <?php endif ?>
    </nav>

    <footer class="site-footer mt-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-md-5">
                    <a href="/" class="marca d-inline-flex align-items-center text-decoration-none mb-2">
                        <img src="/build/img/LogoH2.svg" alt="" class="img-fluid me-2" style="width: 2.5rem;">
                        <span class="marca-nombre fs-5">Abastos<span>Check</span></span>
                    </a>
                    <p class="text-body-secondary mb-0">Tu tienda de la esquina, en línea. Pide en minutos y recoge en tienda o recíbelo en casa.</p>
                </div>
                <div class="col-6 col-md-3">
                    <h6 class="fw-bold">Tienda</h6>
                    <ul class="list-unstyled mb-0">
                        <li><a href="/departamentos" class="link-secondary">Departamentos</a></li>
                        <li><a href="/buscar?ofertas=1" class="link-secondary">Ofertas</a></li>
                        <li><a href="/mis-pedidos" class="link-secondary">Mis pedidos</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4">
                    <h6 class="fw-bold">AbastosCheck</h6>
                    <ul class="list-unstyled mb-0">
                        <li><a href="/conocenos" class="link-secondary">Conócenos</a></li>
                        <li><a href="/avisos-privacidad" class="link-secondary">Aviso de privacidad</a></li>
                        <li><a href="/terminos-condiciones" class="link-secondary">Términos y condiciones</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="d-flex flex-wrap justify-content-between gap-2 small text-body-secondary">
                <span>&copy; <?= date('Y') ?> AbastosCheck</span>
                <span>Desarrollado por: Yael Sánchez Herberth</span>
            </div>
        </div>
    </footer>
</body>

</html>
