<?php
use Model\Precios;

$hayFaltantes = in_array(true, array_column($lineas, 'sinExistencias'), true);
$ahorro = array_sum(array_column($lineas, 'descuento'));
foreach ($lineas as $l) {
    // Ahorro por precio de oferta frente al precio normal
    $ahorro += $l['cantidad'] * ((float) $l['producto']->precio_producto - $l['unitario']);
}

$tipoEntrega = $checkout['tipo_entrega'] ?? 'recoger';
$metodoPago = $checkout['metodo_pago'] ?? 'efectivo';
$envioDomicilio = Precios::envio('domicilio', $total);
?>
<div class="container">
    <hr>
    <h1>Carrito</h1>
    <hr>
    <div class="col-md-12">
        <?php if (!empty($lineas)) : ?>
            <?php if ($hayFaltantes) : ?>
                <div class="alert alert-warning">
                    Algunos productos ya no tienen suficientes existencias. Ajusta las cantidades marcadas para poder comprar.
                </div>
            <?php endif ?>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-end">Precio</th>
                        <th class="text-end">Subtotal</th>
                        <th></th>
                    </tr>
                    <?php foreach ($lineas as $linea) : ?>
                        <?php
                        $producto = $linea['producto'];
                        $id = (int) $producto->id;
                        $stock = (int) $producto->stock;
                        ?>
                        <tr class="<?= $linea['sinExistencias'] ? 'table-warning' : '' ?>">
                            <td>
                                <?= s($producto->nombre_producto) ?>
                                <?php if ($linea['promo']) : ?>
                                    <span class="badge text-bg-danger ms-1"><?= s($linea['promo']) ?></span>
                                    <?php if ($linea['descuento'] == 0) : ?>
                                        <?php [$llevas] = Precios::PROMOS[$linea['promo']]; ?>
                                        <small class="d-block text-danger">Agrega <?= $llevas - $linea['cantidad'] % $llevas ?> más para aprovechar el <?= s($linea['promo']) ?></small>
                                    <?php endif ?>
                                <?php endif ?>
                                <?php if ($linea['sinExistencias']) : ?>
                                    <small class="d-block text-danger">
                                        <?= $stock > 0 ? "Solo quedan {$stock}" : 'Agotado' ?>
                                    </small>
                                <?php endif ?>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <form method="POST">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <input type="hidden" name="accion" value="restar">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" aria-label="Quitar uno de <?= s($producto->nombre_producto) ?>"><i class="bi bi-dash"></i></button>
                                    </form>
                                    <span class="fw-bold" style="min-width: 2ch;"><?= (int) $linea['cantidad'] ?></span>
                                    <form method="POST">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <input type="hidden" name="accion" value="sumar">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" aria-label="Agregar uno de <?= s($producto->nombre_producto) ?>"
                                            <?= $linea['cantidad'] >= $stock ? 'disabled' : '' ?>><i class="bi bi-plus"></i></button>
                                    </form>
                                </div>
                            </td>
                            <td class="text-end">
                                $<?= number_format($linea['unitario'], 2) ?>
                                <?php if ($producto->tieneOferta()) : ?>
                                    <del class="d-block small text-body-secondary">$<?= number_format((float) $producto->precio_producto, 2) ?></del>
                                <?php endif ?>
                            </td>
                            <td class="text-end">
                                $<?= number_format($linea['subtotal'], 2) ?>
                                <?php if ($linea['descuento'] > 0) : ?>
                                    <small class="d-block text-danger">-$<?= number_format($linea['descuento'], 2) ?> por <?= s($linea['promo']) ?></small>
                                <?php endif ?>
                            </td>
                            <td class="text-end">
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <button type="submit" class="btn btn-sm btn-danger" aria-label="Eliminar <?= s($producto->nombre_producto) ?>"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    <tr>
                        <td colspan="3" class="text-end"><h4 class="mb-0">Productos</h4></td>
                        <td class="text-end"><h4 class="mb-0">$<?= number_format($total, 2) ?></h4></td>
                        <td></td>
                    </tr>
                    <?php if ($ahorro > 0.004) : ?>
                        <tr>
                            <td colspan="3" class="text-end text-danger">Estás ahorrando</td>
                            <td class="text-end text-danger fw-bold">$<?= number_format($ahorro, 2) ?></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                </table>
            </div>

            <!-- Entrega y pago -->
            <form action="/comprar" method="POST" id="form-compra" class="row g-4 mt-1">
                <div class="col-md-7">
                    <h4>¿Cómo quieres recibirlo?</h4>
                    <div class="list-group mb-3">
                        <label class="list-group-item d-flex gap-3">
                            <input class="form-check-input flex-shrink-0" type="radio" name="tipo_entrega" value="recoger" <?= $tipoEntrega !== 'domicilio' ? 'checked' : '' ?>>
                            <span>
                                <strong>Recoger en tienda</strong> · gratis
                                <small class="d-block text-body-secondary">Listo en aprox. <?= MINUTOS_RECOGER ?> minutos</small>
                            </span>
                        </label>
                        <label class="list-group-item d-flex gap-3">
                            <input class="form-check-input flex-shrink-0" type="radio" name="tipo_entrega" value="domicilio" <?= $tipoEntrega === 'domicilio' ? 'checked' : '' ?>>
                            <span>
                                <strong>Entrega a domicilio</strong> ·
                                <?= $envioDomicilio > 0 ? '$' . number_format($envioDomicilio, 2) : 'gratis' ?>
                                <small class="d-block text-body-secondary">
                                    Llega en aprox. <?= MINUTOS_DOMICILIO ?> minutos.
                                    <?php if ($envioDomicilio > 0) : ?>
                                        Envío gratis desde $<?= number_format(ENVIO_GRATIS_DESDE, 2) ?>
                                        (te faltan $<?= number_format(ENVIO_GRATIS_DESDE - $total, 2) ?>).
                                    <?php endif ?>
                                </small>
                            </span>
                        </label>
                    </div>

                    <div id="datos-domicilio" class="<?= $tipoEntrega === 'domicilio' ? '' : 'd-none' ?>">
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección de entrega</label>
                            <input type="text" id="direccion" name="direccion" class="form-control" maxlength="255"
                                placeholder="Calle, número, colonia" value="<?= s($checkout['direccion'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono (10 dígitos)</label>
                            <input type="tel" id="telefono" name="telefono" class="form-control" maxlength="14" inputmode="numeric"
                                placeholder="5512345678" value="<?= s($checkout['telefono'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas (opcional)</label>
                        <input type="text" id="notas" name="notas" class="form-control" maxlength="255"
                            placeholder="Ej. sin bolsa, tocar el timbre…" value="<?= s($checkout['notas'] ?? '') ?>">
                    </div>
                </div>

                <div class="col-md-5">
                    <h4>Pago</h4>
                    <div class="list-group mb-3">
                        <label class="list-group-item d-flex gap-3">
                            <input class="form-check-input flex-shrink-0" type="radio" name="metodo_pago" value="efectivo" <?= $metodoPago !== 'tarjeta' ? 'checked' : '' ?>>
                            <span><i class="bi bi-cash-coin"></i> Efectivo <span class="lugar-pago">en tienda</span></span>
                        </label>
                        <label class="list-group-item d-flex gap-3">
                            <input class="form-check-input flex-shrink-0" type="radio" name="metodo_pago" value="tarjeta" <?= $metodoPago === 'tarjeta' ? 'checked' : '' ?>>
                            <span><i class="bi bi-credit-card"></i> Tarjeta <span class="lugar-pago">en tienda</span></span>
                        </label>
                    </div>
                    <p class="small text-body-secondary">Pagas al recoger o al recibir tu pedido. Todavía no hay pago en línea.</p>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between"><span>Productos</span><span>$<?= number_format($total, 2) ?></span></div>
                            <div class="d-flex justify-content-between"><span>Envío</span><span id="resumen-envio">$0.00</span></div>
                            <hr>
                            <div class="d-flex justify-content-between fs-4 fw-bold"><span>Total</span><span id="resumen-total">$<?= number_format($total, 2) ?></span></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="/buscar" class="btn btn-outline-success">Seguir comprando</a>
                        <button type="submit" class="btn btn-success btn-lg" <?= $hayFaltantes ? 'disabled' : '' ?>><i class="bi bi-bag-check"></i> Confirmar pedido</button>
                    </div>
                </div>
            </form>

            <script>
                // Solo muestra/oculta campos y el resumen; el servidor recalcula todo al comprar
                (function() {
                    const form = document.getElementById('form-compra');
                    const subtotal = <?= json_encode($total) ?>;
                    const envioDomicilio = <?= json_encode($envioDomicilio) ?>;
                    const pesos = n => '$' + n.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    function actualizar() {
                        const domicilio = form.tipo_entrega.value === 'domicilio';
                        const envio = domicilio ? envioDomicilio : 0;
                        document.getElementById('datos-domicilio').classList.toggle('d-none', !domicilio);
                        document.getElementById('direccion').required = domicilio;
                        document.getElementById('telefono').required = domicilio;
                        document.querySelectorAll('.lugar-pago').forEach(el => el.textContent = domicilio ? 'al recibir' : 'en tienda');
                        document.getElementById('resumen-envio').textContent = envio > 0 ? pesos(envio) : 'Gratis';
                        document.getElementById('resumen-total').textContent = pesos(subtotal + envio);
                    }

                    form.addEventListener('change', actualizar);
                    actualizar();
                })();
            </script>
        <?php else : ?>
            <div class="alert alert-success text-uppercase text-center" role="alert">
                Aún no hay nada en el carrito de compras :/
            </div>
            <div class="text-center">
                <a href="/buscar" class="btn btn-success">Ver productos</a>
            </div>
        <?php endif ?>
    </div>

</div>
