<?php
// Buscador de las tablas del admin. Espera $listado (Model\Listado) y $buscarPlaceholder.
// Es un formulario GET normal: la búsqueda queda en la URL y se puede compartir o recargar.
?>
<form method="GET" class="d-flex flex-wrap align-items-center gap-2 mb-3" role="search">
    <?php foreach ($listado->camposOcultos() as $nombre => $valor) : ?>
        <input type="hidden" name="<?= s($nombre) ?>" value="<?= s($valor) ?>">
    <?php endforeach ?>
    <div class="input-group buscador-admin">
        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
        <input type="search" name="q" value="<?= s($listado->q) ?>" class="form-control"
            placeholder="<?= s($buscarPlaceholder) ?>" aria-label="<?= s($buscarPlaceholder) ?>">
        <button type="submit" class="btn btn-success">Buscar</button>
    </div>
    <?php if ($listado->q !== '') : ?>
        <a href="<?= s($listado->url(['q' => '', 'pagina' => 1])) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg" aria-hidden="true"></i> Limpiar
        </a>
        <span class="text-body-secondary small">
            <?= $listado->total ?> <?= $listado->total === 1 ? 'resultado' : 'resultados' ?> para “<?= s($listado->q) ?>”
        </span>
    <?php endif ?>
</form>
