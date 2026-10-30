<div class="container">
    <nav aria-label="Ruta de navegación">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="link-success">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/departamentos" class="link-success">Departamentos</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= s($departamento->nombre_departamento) ?></li>
        </ol>
    </nav>

    <div class="d-flex align-items-center gap-3 mb-3">
        <span class="tile-icono"><i class="bi <?= iconoDepartamento($departamento->nombre_departamento) ?>"></i></span>
        <div>
            <h1 class="mb-0"><?= s($departamento->nombre_departamento) ?></h1>
            <small class="text-body-secondary"><?= count($resultado) ?> <?= count($resultado) === 1 ? 'producto' : 'productos' ?></small>
        </div>
    </div>
    <hr>
    <div class="row">
        <?php foreach ($resultado as $producto) : ?>
            <?php include __DIR__ . '/_tarjeta-producto.php'; ?>
        <?php endforeach ?>

        <?php if (empty($resultado)) : ?>
            <div class="alert alert-light text-center">Este departamento aún no tiene productos.</div>
        <?php endif ?>
    </div>
</div>
