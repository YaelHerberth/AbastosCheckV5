<?php
// Detalle de un pedido. Espera $pedido con 'detalle' y los datos de entrega.
// Usa el nombre y precio guardados al comprar, no los actuales del catálogo.
use Model\PedidoModel;

$esDomicilio = $pedido['tipo_entrega'] === 'domicilio';
$enCurso = !in_array($pedido['estado'], PedidoModel::ESTADOS_FINALES, true);
?>
<div class="row g-3 mb-3">
    <div class="col-sm-6">
        <div class="border rounded p-3 h-100">
            <div class="text-body-secondary small">Entrega</div>
            <div class="fw-bold">
                <i class="bi <?= $esDomicilio ? 'bi-truck' : 'bi-shop' ?>"></i>
                <?= s(PedidoModel::etiquetaEntrega($pedido['tipo_entrega'])) ?>
            </div>
            <?php if ($esDomicilio) : ?>
                <div><?= s($pedido['direccion']) ?></div>
                <div class="small">Tel. <?= s($pedido['telefono']) ?></div>
            <?php endif ?>
            <?php if ($enCurso && $pedido['listo_estimado']) : ?>
                <div class="small text-success mt-1">
                    <?= $esDomicilio ? 'Llega' : 'Listo' ?> aprox. a las <?= s(date('H:i', strtotime($pedido['listo_estimado']))) ?>
                </div>
            <?php endif ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="border rounded p-3 h-100">
            <div class="text-body-secondary small">Pago</div>
            <div class="fw-bold">
                <i class="bi <?= $pedido['metodo_pago'] === 'tarjeta' ? 'bi-credit-card' : 'bi-cash-coin' ?>"></i>
                <?= s(PedidoModel::etiquetaPago($pedido['metodo_pago'], $pedido['tipo_entrega'])) ?>
            </div>
            <?php if ($pedido['notas']) : ?>
                <div class="small mt-1"><span class="text-body-secondary">Notas:</span> <?= s($pedido['notas']) ?></div>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table align-middle">
        <tr>
            <th>Producto</th>
            <th class="text-center">Cantidad</th>
            <th class="text-end">Precio</th>
            <th class="text-end">Subtotal</th>
        </tr>
        <?php foreach ($pedido['detalle'] as $linea) : ?>
            <?php $descuento = (float) $linea['descuento']; ?>
            <tr>
                <td>
                    <?= s($linea['nombre_producto']) ?>
                    <?php if ($linea['promo']) : ?>
                        <span class="badge text-bg-danger ms-1"><?= s($linea['promo']) ?></span>
                    <?php endif ?>
                </td>
                <td class="text-center"><?= (int) $linea['cantidad'] ?></td>
                <td class="text-end">$<?= number_format((float) $linea['precio_unitario'], 2) ?></td>
                <td class="text-end">
                    $<?= number_format($linea['cantidad'] * (float) $linea['precio_unitario'] - $descuento, 2) ?>
                    <?php if ($descuento > 0) : ?>
                        <small class="d-block text-danger">-$<?= number_format($descuento, 2) ?></small>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
        <tr>
            <td colspan="3" class="text-end">Envío</td>
            <td class="text-end"><?= (float) $pedido['costo_envio'] > 0 ? '$' . number_format((float) $pedido['costo_envio'], 2) : 'Gratis' ?></td>
        </tr>
        <tr>
            <td colspan="3" class="text-end"><h4>Total</h4></td>
            <td class="text-end"><h4>$<?= number_format((float) $pedido['total'], 2) ?></h4></td>
        </tr>
    </table>
</div>
