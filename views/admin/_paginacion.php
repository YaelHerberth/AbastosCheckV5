<?php
// Paginador de las tablas del admin. Espera $listado (Model\Listado) y $nombreRegistros (ej. "productos").
[$desde, $hasta] = $listado->rango();
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2 mb-4">
    <p class="text-body-secondary small mb-0">
        <?php if ($listado->total === 1) : ?>
            Mostrando 1 de 1
        <?php elseif ($listado->total > 1) : ?>
            Mostrando <?= $desde ?>–<?= $hasta ?> de <?= $listado->total ?> <?= s($nombreRegistros) ?>
        <?php endif ?>
    </p>

    <?php if ($listado->paginas > 1) : ?>
        <nav aria-label="Páginas de <?= s($nombreRegistros) ?>">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $listado->pagina <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= s($listado->url(['pagina' => $listado->pagina - 1])) ?>" aria-label="Página anterior">‹</a>
                </li>
                <?php foreach ($listado->paginasVisibles() as $numero) : ?>
                    <?php if ($numero === null) : ?>
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    <?php elseif ($numero === $listado->pagina) : ?>
                        <li class="page-item active" aria-current="page"><span class="page-link"><?= $numero ?></span></li>
                    <?php else : ?>
                        <li class="page-item"><a class="page-link" href="<?= s($listado->url(['pagina' => $numero])) ?>"><?= $numero ?></a></li>
                    <?php endif ?>
                <?php endforeach ?>
                <li class="page-item <?= $listado->pagina >= $listado->paginas ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= s($listado->url(['pagina' => $listado->pagina + 1])) ?>" aria-label="Página siguiente">›</a>
                </li>
            </ul>
        </nav>
    <?php endif ?>
</div>
