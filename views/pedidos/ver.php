<?php
use Model\PedidoModel;

$esFinal = in_array($pedido['estado'], PedidoModel::ESTADOS_FINALES, true);
?>
<div class="container" style="max-width: 900px;">
    <a href="/admin/pedidos" class="btn btn-outline-success mb-3"><i class="bi bi-arrow-left"></i> Pedidos</a>

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h2>Pedido #<?= (int) $pedido['id'] ?></h2>
            <p class="text-body-secondary mb-1"><?= s(date('d/m/Y H:i', strtotime($pedido['creado_en']))) ?></p>
            <p class="mb-0">
                <strong><?= s($pedido['username_usuario']) ?></strong> ·
                <a href="mailto:<?= s($pedido['email_usuario']) ?>" class="link-success"><?= s($pedido['email_usuario']) ?></a>
            </p>
        </div>
        <span class="badge fs-6 text-bg-<?= PedidoModel::colorEstado($pedido['estado']) ?> text-capitalize"><?= s($pedido['estado']) ?></span>
    </div>

    <hr>

    <?php include __DIR__ . '/../paginas/_detalle-pedido.php'; ?>

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">Cambiar estado</h5>
            <?php if ($esFinal) : ?>
                <p class="mb-0 text-body-secondary">Este pedido ya está <?= s($pedido['estado']) ?> y no se puede modificar.</p>
            <?php else : ?>
                <form action="/admin/pedidos/estado" method="POST" class="d-flex flex-wrap gap-2">
                    <input type="hidden" name="id" value="<?= (int) $pedido['id'] ?>">
                    <?php foreach (PedidoModel::ESTADOS as $estado) : ?>
                        <?php if ($estado === $pedido['estado']) continue; ?>
                        <button type="submit" name="estado" value="<?= s($estado) ?>"
                            class="btn btn-outline-<?= PedidoModel::colorEstado($estado) ?> text-capitalize"
                            <?php if ($estado === 'cancelado') : ?>onclick="return confirm('¿Cancelar el pedido? Las piezas regresarán al inventario.')"<?php endif ?>>
                            <?= s($estado) ?>
                        </button>
                    <?php endforeach ?>
                </form>
                <small class="text-body-secondary d-block mt-2">Entregado y cancelado son estados finales.</small>
            <?php endif ?>
        </div>
    </div>
</div>
