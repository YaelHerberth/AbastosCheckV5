<?php
$dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
$maxSemana = max($semana) ?: 1;
$totalSemana = array_sum($semana);
$fechas = array_keys($semana);
$hoy = end($fechas);

$cambio = null;
if ($ventas['total_ayer'] > 0) {
    $cambio = round(($ventas['total_hoy'] - $ventas['total_ayer']) * 100 / $ventas['total_ayer']);
}
$ticket = $ventas['pedidos_hoy'] > 0 ? $ventas['total_hoy'] / $ventas['pedidos_hoy'] : 0;
?>
<style>
    .grafica-semana { display: flex; align-items: flex-end; gap: 12px; height: 180px; border-bottom: 1px solid var(--bs-border-color); }
    .grafica-semana .dia { flex: 1; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; position: relative; }
    .grafica-semana .barra { width: 100%; max-width: 36px; background: var(--bs-success); border-radius: 4px 4px 0 0; opacity: .55; min-height: 2px; transition: opacity .15s; }
    .grafica-semana .dia.hoy .barra { opacity: 1; }
    .grafica-semana .dia:hover .barra { opacity: .9; }
    .grafica-semana .valor { font-size: .75rem; color: var(--bs-secondary-color); margin-bottom: 4px; white-space: nowrap; }
    .grafica-semana .dia:not(.etiquetada) .valor { visibility: hidden; }
    .grafica-semana .dia:hover .valor { visibility: visible; color: var(--bs-body-color); }
    .ejes-semana { display: flex; gap: 12px; }
    .ejes-semana span { flex: 1; text-align: center; font-size: .8rem; color: var(--bs-secondary-color); }
</style>

<div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <a href="/profile" class="btn btn-outline-success mb-2"><i class="bi bi-arrow-left"></i> Volver</a>
            <h2 class="mb-0">Panel de la tienda</h2>
            <small class="text-body-secondary">Los pedidos cancelados no cuentan como venta.</small>
        </div>
        <a href="/admin/pedidos" class="btn btn-success"><i class="bi bi-receipt"></i> Ver pedidos</a>
    </div>

    <!-- Indicadores del día -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-body-secondary small">Ventas de hoy</div>
                <div class="fs-3 fw-bold">$<?= number_format($ventas['total_hoy'], 2) ?></div>
                <?php if ($cambio !== null) : ?>
                    <small class="text-body-secondary">
                        <i class="bi <?= $cambio >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' ?>"></i>
                        <?= $cambio >= 0 ? '+' : '' ?><?= $cambio ?>% vs. ayer
                    </small>
                <?php else : ?>
                    <small class="text-body-secondary">Ayer no hubo ventas</small>
                <?php endif ?>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-body-secondary small">Pedidos de hoy</div>
                <div class="fs-3 fw-bold"><?= $ventas['pedidos_hoy'] ?></div>
                <small class="text-body-secondary">Ayer: <?= $ventas['pedidos_ayer'] ?></small>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-body-secondary small">Ticket promedio hoy</div>
                <div class="fs-3 fw-bold">$<?= number_format($ticket, 2) ?></div>
                <small class="text-body-secondary">por pedido</small>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <a href="/admin/pedidos" class="text-decoration-none">
                <div class="card h-100 <?= $porAtender > 0 ? 'border-warning' : '' ?>"><div class="card-body">
                    <div class="text-body-secondary small">Por atender</div>
                    <div class="fs-3 fw-bold text-body"><?= $porAtender ?></div>
                    <small class="text-body-secondary">
                        <?php if ($porAtender > 0) : ?><i class="bi bi-exclamation-circle-fill text-warning"></i> pendientes, preparando o listos<?php else : ?>Todo al día<?php endif ?>
                    </small>
                </div></div>
            </a>
        </div>
    </div>

    <div class="row g-3">
        <!-- Ventas de la semana -->
        <div class="col-lg-7">
            <div class="card h-100"><div class="card-body">
                <h5 class="card-title mb-0">Ventas de los últimos 7 días</h5>
                <small class="text-body-secondary">Total: $<?= number_format($totalSemana, 2) ?></small>

                <div class="grafica-semana mt-4" aria-hidden="true">
                    <?php foreach ($semana as $fecha => $total) : ?>
                        <?php
                        // Se etiquetan solo hoy y el mejor día; el resto aparece al pasar el mouse
                        $etiquetada = $fecha === $hoy || ($total > 0 && $total == $maxSemana);
                        ?>
                        <div class="dia <?= $fecha === $hoy ? 'hoy' : '' ?> <?= $etiquetada ? 'etiquetada' : '' ?>"
                            title="<?= s(date('d/m/Y', strtotime($fecha))) ?>: $<?= number_format($total, 2) ?>">
                            <span class="valor">$<?= number_format($total, 0) ?></span>
                            <div class="barra" style="height: <?= round($total * 100 / $maxSemana, 1) ?>%"></div>
                        </div>
                    <?php endforeach ?>
                </div>
                <div class="ejes-semana mt-1" aria-hidden="true">
                    <?php foreach ($semana as $fecha => $total) : ?>
                        <span><?= $fecha === $hoy ? 'Hoy' : $dias[(int) date('w', strtotime($fecha))] ?></span>
                    <?php endforeach ?>
                </div>

                <!-- Misma información como tabla para lectores de pantalla -->
                <table class="visually-hidden">
                    <caption>Ventas por día, últimos 7 días</caption>
                    <tr><th>Día</th><th>Ventas</th></tr>
                    <?php foreach ($semana as $fecha => $total) : ?>
                        <tr><td><?= s(date('d/m/Y', strtotime($fecha))) ?></td><td>$<?= number_format($total, 2) ?></td></tr>
                    <?php endforeach ?>
                </table>
            </div></div>
        </div>

        <!-- Inventario bajo -->
        <div class="col-lg-5">
            <div class="card h-100"><div class="card-body">
                <h5 class="card-title">
                    Inventario bajo
                    <small class="text-body-secondary fw-normal fs-6">(<?= STOCK_BAJO ?> piezas o menos)</small>
                </h5>
                <?php if (empty($inventarioBajo)) : ?>
                    <p class="text-body-secondary mb-0"><i class="bi bi-check-circle-fill text-success"></i> Todo el inventario está bien surtido.</p>
                <?php else : ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($inventarioBajo as $p) : ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>
                                    <?= s($p['nombre_producto']) ?>
                                    <small class="d-block text-body-secondary"><?= s($p['nombre_departamento']) ?></small>
                                </span>
                                <span class="d-flex align-items-center gap-2">
                                    <?php if ((int) $p['stock'] <= 0) : ?>
                                        <span class="badge text-bg-danger"><i class="bi bi-x-octagon-fill"></i> Agotado</span>
                                    <?php else : ?>
                                        <span class="badge text-bg-warning"><i class="bi bi-exclamation-triangle-fill"></i> <?= (int) $p['stock'] ?> pzas</span>
                                    <?php endif ?>
                                    <a href="/admin/productos/actualizar?id=<?= (int) $p['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar existencias"><i class="bi bi-pen"></i></a>
                                </span>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>
            </div></div>
        </div>

        <!-- Más vendidos -->
        <div class="col-12">
            <div class="card"><div class="card-body">
                <h5 class="card-title">Más vendidos <small class="text-body-secondary fw-normal fs-6">(últimos 30 días)</small></h5>
                <?php if (empty($masVendidos)) : ?>
                    <p class="text-body-secondary mb-0">Todavía no hay ventas en este periodo.</p>
                <?php else : ?>
                    <?php $maxPiezas = max(array_column($masVendidos, 'piezas')); ?>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th style="width: 40%;">Piezas</th>
                                <th class="text-end">Importe</th>
                            </tr>
                            <?php foreach ($masVendidos as $i => $p) : ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= s($p['nombre_producto']) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="flex-grow-1" style="height: 8px;">
                                                <div class="bg-success rounded-end" style="height: 8px; opacity: .7; width: <?= round($p['piezas'] * 100 / $maxPiezas) ?>%"></div>
                                            </div>
                                            <span style="min-width: 3ch;" class="text-end"><?= (int) $p['piezas'] ?></span>
                                        </div>
                                    </td>
                                    <td class="text-end">$<?= number_format((float) $p['importe'], 2) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </table>
                    </div>
                <?php endif ?>
            </div></div>
        </div>
    </div>
</div>
