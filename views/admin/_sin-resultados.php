<?php
// Fila de tabla vacía. Espera $listado, $columnas (número de columnas) y $nombreRegistros.
?>
<tr>
    <td colspan="<?= (int) $columnas ?>" class="text-center text-body-secondary py-5">
        <?php if ($listado->q !== '') : ?>
            No hay <?= s($nombreRegistros) ?> que coincidan con “<?= s($listado->q) ?>”.
            <a href="<?= s($listado->url(['q' => '', 'pagina' => 1])) ?>">Ver todos</a>
        <?php else : ?>
            Todavía no hay <?= s($nombreRegistros) ?>.
        <?php endif ?>
    </td>
</tr>
