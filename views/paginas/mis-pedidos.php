<?php use Model\PedidoModel; ?>
<div class="container" style="max-width: 900px;">
    <h1>Mis pedidos</h1>
    <hr>

    <?php if (empty($pedidos)) : ?>
        <div class="alert alert-light text-center">
            Aún no has hecho ningún pedido.
        </div>
        <div class="text-center">
            <a href="/buscar" class="btn btn-success">Ver productos</a>
        </div>
    <?php else : ?>
        <div class="list-group">
            <?php foreach ($pedidos as $pedido) : ?>
                <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <a href="/mis-pedidos/ver?id=<?= (int) $pedido['id'] ?>" class="text-reset text-decoration-none flex-grow-1">
                        <div class="fw-bold">Pedido #<?= (int) $pedido['id'] ?></div>
                        <small class="text-body-secondary">
                            <?= s(date('d/m/Y H:i', strtotime($pedido['creado_en']))) ?> ·
                            <?= (int) $pedido['piezas'] ?> <?= (int) $pedido['piezas'] === 1 ? 'pieza' : 'piezas' ?> ·
                            <?= s(PedidoModel::etiquetaEntrega($pedido['tipo_entrega'])) ?>
                        </small>
                    </a>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge text-bg-<?= PedidoModel::colorEstado($pedido['estado']) ?> text-capitalize"><?= s($pedido['estado']) ?></span>
                        <span class="fw-bold">$<?= number_format((float) $pedido['total'], 2) ?></span>
                        <form action="/mis-pedidos/repetir" method="POST">
                            <input type="hidden" name="id" value="<?= (int) $pedido['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-success" title="Volver a pedir"><i class="bi bi-arrow-repeat"></i> Volver a pedir</button>
                        </form>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>
