<div class="container">

    <!-- Portada -->
    <section class="hero mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h1>Tu tienda de la esquina, <span class="resaltado">en línea</span>.</h1>
                <p class="lead mb-4">
                    Despensa, bebidas y lo que te haga falta. Pide en un par de minutos y
                    recógelo en tienda o recíbelo en tu casa.
                </p>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <a href="/buscar" class="btn btn-dorado btn-lg"><i class="bi bi-basket2-fill"></i> Empezar a comprar</a>
                    <a href="/buscar?ofertas=1" class="btn btn-outline-light btn-lg"><i class="bi bi-tag-fill"></i> Ver ofertas</a>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="hero-beneficio"><i class="bi bi-lightning-charge-fill"></i> Listo en <?= MINUTOS_RECOGER ?> min</span>
                    <span class="hero-beneficio"><i class="bi bi-truck"></i> Envío gratis desde $<?= number_format(ENVIO_GRATIS_DESDE, 0) ?></span>
                    <span class="hero-beneficio"><i class="bi bi-cash-coin"></i> Paga al recibir</span>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <img src="/build/img/LogoH2.svg" alt="" class="hero-logo">
            </div>
        </div>
    </section>

    <!-- Departamentos -->
    <?php if (!empty($departamentos)) : ?>
        <section class="mb-5" aria-labelledby="titulo-departamentos">
            <div class="seccion-titulo">
                <h2 id="titulo-departamentos">Departamentos</h2>
                <a href="/departamentos" class="link-success">Ver todos <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row row-cols-3 row-cols-sm-4 row-cols-lg-6 g-3">
                <?php foreach ($departamentos as $dep) : ?>
                    <div class="col">
                        <a href="/departamento?id=<?= (int) $dep->id ?>" class="tile-departamento">
                            <span class="tile-icono"><i class="bi <?= iconoDepartamento($dep->nombre_departamento) ?>"></i></span>
                            <span><?= s($dep->nombre_departamento) ?></span>
                        </a>
                    </div>
                <?php endforeach ?>
            </div>
        </section>
    <?php endif ?>

    <!-- Ofertas del día -->
    <?php if (!empty($ofertas)) : ?>
        <section class="mb-5" aria-labelledby="titulo-ofertas">
            <div class="seccion-titulo">
                <h2 id="titulo-ofertas"><i class="bi bi-tag-fill text-danger"></i> Ofertas del día</h2>
                <?php if ($totalOfertas > count($ofertas)) : ?>
                    <a href="/buscar?ofertas=1" class="link-success">Ver las <?= $totalOfertas ?> ofertas <i class="bi bi-arrow-right"></i></a>
                <?php endif ?>
            </div>
            <div class="row">
                <?php foreach ($ofertas as $producto) : ?>
                    <?php include __DIR__ . '/_tarjeta-producto.php'; ?>
                <?php endforeach ?>
            </div>
        </section>
    <?php endif ?>

    <!-- Más vendidos (o novedades si aún no hay ventas) -->
    <?php if (!empty($masVendidos)) : ?>
        <section class="mb-5" aria-labelledby="titulo-vendidos">
            <div class="seccion-titulo">
                <h2 id="titulo-vendidos">
                    <?php if ($hayVentas) : ?>
                        <i class="bi bi-stars text-warning"></i> Lo más vendido
                    <?php else : ?>
                        <i class="bi bi-stars text-warning"></i> Novedades
                    <?php endif ?>
                </h2>
                <a href="/buscar" class="link-success">Ver catálogo <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row">
                <?php foreach ($masVendidos as $producto) : ?>
                    <?php include __DIR__ . '/_tarjeta-producto.php'; ?>
                <?php endforeach ?>
            </div>
        </section>
    <?php endif ?>

    <!-- Cómo funciona -->
    <section class="mb-4" aria-labelledby="titulo-como">
        <div class="seccion-titulo">
            <h2 id="titulo-como">Así de fácil</h2>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="paso">
                    <div class="paso-numero">1</div>
                    <h3 class="h5">Arma tu carrito</h3>
                    <p class="mb-0 text-body-secondary">Busca lo que necesitas o entra a un departamento. Las ofertas y el 2x1 se aplican solos.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="paso">
                    <div class="paso-numero">2</div>
                    <h3 class="h5">Elige cómo recibirlo</h3>
                    <p class="mb-0 text-body-secondary">
                        Recoge en tienda en unos <?= MINUTOS_RECOGER ?> minutos o pide a domicilio
                        (gratis desde $<?= number_format(ENVIO_GRATIS_DESDE, 0) ?>).
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="paso">
                    <div class="paso-numero">3</div>
                    <h3 class="h5">Paga al recibir</h3>
                    <p class="mb-0 text-body-secondary">En efectivo o con tarjeta, al recoger o cuando llegue tu pedido. Sigue su estado en "Mis pedidos".</p>
                </div>
            </div>
        </div>
    </section>
</div>
