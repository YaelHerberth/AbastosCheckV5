<?php use Model\PedidoModel; ?>
<div class="container">
    <a href="/profile" class="btn btn-outline-success mb-3"><i class="bi bi-arrow-left"></i> Volver</a>

    <div class="d-flex justify-content-between align-items-center">
        <h2>Pedidos</h2>
        <a href="/admin/metricas" class="btn btn-outline-dark"><i class="bi bi-graph-up"></i> Métricas</a>
    </div>

    <!-- Filtro por estado -->
    <div class="d-flex flex-wrap gap-2 my-3">
        <a href="<?= s($listado->url(['estado' => '', 'pagina' => 1])) ?>" class="btn btn-sm <?= $estadoActual === null ? 'btn-dark' : 'btn-outline-dark' ?>">
            Todos <span class="badge text-bg-light"><?= array_sum($conteo) ?></span>
        </a>
        <?php foreach ($conteo as $estado => $total) : ?>
            <a href="<?= s($listado->url(['estado' => $estado, 'pagina' => 1])) ?>"
                class="btn btn-sm text-capitalize <?= $estadoActual === $estado ? 'btn-' . PedidoModel::colorEstado($estado) : 'btn-outline-' . PedidoModel::colorEstado($estado) ?>">
                <?= s($estado) ?> <span class="badge text-bg-light"><?= $total ?></span>
            </a>
        <?php endforeach ?>
    </div>

    <?php
    $buscarPlaceholder = 'Buscar por número de pedido, cliente o correo';
    $nombreRegistros = 'pedidos';
    include __DIR__ . '/../admin/_buscador.php';
    ?>
    <div class="table-responsive">
    <table class="table align-middle tabla-admin">
        <thead>
            <tr>
                <?= thOrden($listado, 'id', '#') ?>
                <?= thOrden($listado, 'fecha', 'Fecha') ?>
                <?= thOrden($listado, 'cliente', 'Cliente') ?>
                <th scope="col">Entrega</th>
                <?= thOrden($listado, 'piezas', 'Piezas', 'text-center') ?>
                <?= thOrden($listado, 'total', 'Total', 'text-end') ?>
                <?= thOrden($listado, 'estado', 'Estado') ?>
                <th scope="col"><span class="visually-hidden">Acciones</span></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$pedidos) : ?>
                <?php $columnas = 8; include __DIR__ . '/../admin/_sin-resultados.php'; ?>
            <?php endif ?>
            <?php foreach ($pedidos as $pedido) : ?>
                <tr>
                    <td><?= (int) $pedido['id'] ?></td>
                    <td class="text-nowrap"><?= s(date('d/m/Y H:i', strtotime($pedido['creado_en']))) ?></td>
                    <td>
                        <?= s($pedido['username_usuario']) ?>
                        <small class="d-block text-body-secondary"><?= s($pedido['email_usuario']) ?></small>
                    </td>
                    <td>
                        <i class="bi <?= $pedido['tipo_entrega'] === 'domicilio' ? 'bi-truck' : 'bi-shop' ?>"></i>
                        <?= s(PedidoModel::etiquetaEntrega($pedido['tipo_entrega'])) ?>
                    </td>
                    <td class="text-center"><?= (int) $pedido['piezas'] ?></td>
                    <td class="text-end">$<?= number_format((float) $pedido['total'], 2) ?></td>
                    <td><span class="badge text-bg-<?= PedidoModel::colorEstado($pedido['estado']) ?> text-capitalize"><?= s($pedido['estado']) ?></span></td>
                    <td><a href="/admin/pedidos/ver?id=<?= (int) $pedido['id'] ?>" class="btn btn-sm btn-outline-success">Ver</a></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    </div>
    <?php include __DIR__ . '/../admin/_paginacion.php'; ?>
</div>
