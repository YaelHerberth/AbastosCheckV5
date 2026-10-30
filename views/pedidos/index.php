<?php use Model\PedidoModel; ?>
<div class="container">
    <a href="/profile" class="btn btn-outline-success mb-3"><i class="bi bi-arrow-left"></i> Volver</a>

    <div class="d-flex justify-content-between align-items-center">
        <h2>Pedidos</h2>
        <a href="/admin/metricas" class="btn btn-outline-dark"><i class="bi bi-graph-up"></i> Métricas</a>
    </div>

    <!-- Filtro por estado -->
    <div class="d-flex flex-wrap gap-2 my-3">
        <a href="/admin/pedidos" class="btn btn-sm <?= $estadoActual === null ? 'btn-dark' : 'btn-outline-dark' ?>">
            Todos <span class="badge text-bg-light"><?= array_sum($conteo) ?></span>
        </a>
        <?php foreach ($conteo as $estado => $total) : ?>
            <a href="/admin/pedidos?estado=<?= s($estado) ?>"
                class="btn btn-sm text-capitalize <?= $estadoActual === $estado ? 'btn-' . PedidoModel::colorEstado($estado) : 'btn-outline-' . PedidoModel::colorEstado($estado) ?>">
                <?= s($estado) ?> <span class="badge text-bg-light"><?= $total ?></span>
            </a>
        <?php endforeach ?>
    </div>

    <table class="table align-middle" id="tabla">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Entrega</th>
                <th class="text-center">Piezas</th>
                <th class="text-end">Total</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $pedido) : ?>
                <tr>
                    <td><?= (int) $pedido['id'] ?></td>
                    <td data-order="<?= s($pedido['creado_en']) ?>"><?= s(date('d/m/Y H:i', strtotime($pedido['creado_en']))) ?></td>
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

<script>
    $(document).ready(function() {
        $('#tabla').DataTable({
            order: [[0, 'desc']],
            language: { emptyTable: 'No hay pedidos', search: 'Buscar:' }
        });
    });
</script>
