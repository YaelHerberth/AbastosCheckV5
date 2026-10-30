<?php
$es403 = $codigo === 403;
?>
<div class="container text-center py-5" style="max-width: 640px;">
    <div class="tile-icono mx-auto mb-4" style="width: 96px; height: 96px; font-size: 2.8rem;">
        <i class="bi <?= $es403 ? 'bi-lock-fill' : 'bi-signpost-split' ?>"></i>
    </div>
    <p class="text-body-secondary fw-bold mb-1">Error <?= (int) $codigo ?></p>

    <?php if ($es403) : ?>
        <h1 class="mb-3">Esta sección es solo para administradores</h1>
        <p class="lead text-body-secondary mb-4">Tu cuenta no tiene permiso para entrar aquí. Si crees que es un error, contacta al encargado de la tienda.</p>
    <?php else : ?>
        <h1 class="mb-3">No encontramos esta página</h1>
        <p class="lead text-body-secondary mb-4">Puede que el enlace esté mal escrito o que la página ya no exista. Prueba buscando lo que necesitas:</p>
        <form action="/buscar" method="GET" class="mb-4 mx-auto" role="search" style="max-width: 420px;">
            <div class="input-group input-group-lg">
                <input type="search" name="q" class="form-control" placeholder="Ej. arroz, atún…" aria-label="Buscar productos">
                <button class="btn btn-success" type="submit" aria-label="Buscar"><i class="bi bi-search"></i></button>
            </div>
        </form>
    <?php endif ?>

    <div class="d-flex flex-wrap justify-content-center gap-2">
        <a href="/" class="btn btn-success"><i class="bi bi-house-door"></i> Ir al inicio</a>
        <a href="/departamentos" class="btn btn-outline-success">Ver departamentos</a>
    </div>
</div>
