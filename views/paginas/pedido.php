<?php use Model\PedidoModel; ?>
<div class="container" style="max-width: 720px;">
    <a href="/mis-pedidos" class="btn btn-outline-success mb-3"><i class="bi bi-arrow-left"></i> Mis pedidos</a>

    <h1>Pedido #<?= (int) $pedido['id'] ?></h1>
    <p class="text-body-secondary">
        <?= s(date('d/m/Y H:i', strtotime($pedido['creado_en']))) ?> ·
        Estado: <span class="badge text-bg-<?= PedidoModel::colorEstado($pedido['estado']) ?> text-capitalize"><?= s($pedido['estado']) ?></span>
    </p>

    <?php include __DIR__ . '/_detalle-pedido.php'; ?>

    <form action="/mis-pedidos/repetir" method="POST" class="text-end">
        <input type="hidden" name="id" value="<?= (int) $pedido['id'] ?>">
        <button type="submit" class="btn btn-success"><i class="bi bi-arrow-repeat"></i> Volver a pedir</button>
    </form>
</div>
