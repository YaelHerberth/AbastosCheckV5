<?php use Model\PedidoModel; ?>
<div class="container" style="max-width: 720px;">
    <div class="text-center mb-4">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
        <h1 class="mt-2">¡Gracias por su compra!</h1>
        <p class="text-body-secondary">
            Pedido <strong>#<?= (int) $pedido['id'] ?></strong> ·
            <?= s(date('d/m/Y H:i', strtotime($pedido['creado_en']))) ?> ·
            Estado: <span class="badge text-bg-<?= PedidoModel::colorEstado($pedido['estado']) ?> text-capitalize"><?= s($pedido['estado']) ?></span>
        </p>
    </div>

    <?php include __DIR__ . '/_detalle-pedido.php'; ?>

    <div class="text-center mt-4">
        <a href="/mis-pedidos" class="btn btn-outline-success me-2">Ver mis pedidos</a>
        <a href="/buscar" class="btn btn-success">Seguir comprando</a>
    </div>
</div>
