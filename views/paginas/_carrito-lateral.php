<?php
// Contenido del carrito lateral. Espera $lineasLateral y $totalLateral.
// Se usa al cargar la página (layout) y en la respuesta de /carrito/ajax.
// Los formularios envían a /carrito y funcionan aunque no haya JavaScript.
?>
<?php if (empty($lineasLateral)) : ?>
    <div class="text-center py-5">
        <div class="tile-icono mx-auto mb-3"><i class="bi bi-cart"></i></div>
        <p class="fw-bold mb-1">Tu carrito está vacío</p>
        <p class="text-body-secondary small">Agrega productos y aparecerán aquí.</p>
        <a href="/buscar" class="btn btn-success">Ver productos</a>
    </div>
<?php else : ?>
    <ul class="list-unstyled mb-0 carrito-lateral-lista">
        <?php foreach ($lineasLateral as $linea) : ?>
            <?php
            $p = $linea['producto'];
            $pid = (int) $p->id;
            $imagen = $p->imagen_producto ? 'build/img/Productos/' . $p->imagen_producto : 'build/img/AbastosCheckDefault.png';
            ?>
            <li class="d-flex gap-3 py-3 border-bottom">
                <img src="/<?= s($imagen) ?>" alt="" width="64" height="64" class="rounded-3 object-fit-cover flex-shrink-0 tarjeta-imagen"
                    loading="lazy" onerror="this.onerror=null; this.src='/build/img/AbastosCheckDefault.png';">
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex justify-content-between gap-2">
                        <span class="fw-bold text-truncate"><?= s($p->nombre_producto) ?></span>
                        <form action="/carrito" method="POST" class="js-carrito-accion">
                            <input type="hidden" name="id" value="<?= $pid ?>">
                            <input type="hidden" name="accion" value="eliminar">
                            <button type="submit" class="btn btn-sm btn-link text-body-secondary p-0" aria-label="Quitar <?= s($p->nombre_producto) ?>"><i class="bi bi-x-lg"></i></button>
                        </form>
                    </div>
                    <div class="small text-body-secondary">
                        $<?= number_format($linea['unitario'], 2) ?> c/u
                        <?php if ($linea['promo']) : ?><span class="badge text-bg-danger ms-1"><?= s($linea['promo']) ?></span><?php endif ?>
                    </div>
                    <?php if ($linea['sinExistencias']) : ?>
                        <div class="small text-danger">Solo quedan <?= (int) $p->stock ?></div>
                    <?php endif ?>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <div class="d-inline-flex align-items-center gap-2">
                            <form action="/carrito" method="POST" class="js-carrito-accion">
                                <input type="hidden" name="id" value="<?= $pid ?>">
                                <input type="hidden" name="accion" value="restar">
                                <button type="submit" class="btn btn-sm btn-outline-secondary py-0" aria-label="Quitar uno"><i class="bi bi-dash"></i></button>
                            </form>
                            <span class="fw-bold" aria-label="Cantidad"><?= (int) $linea['cantidad'] ?></span>
                            <form action="/carrito" method="POST" class="js-carrito-accion">
                                <input type="hidden" name="id" value="<?= $pid ?>">
                                <input type="hidden" name="accion" value="sumar">
                                <button type="submit" class="btn btn-sm btn-outline-secondary py-0" aria-label="Agregar uno"
                                    <?= $linea['cantidad'] >= (int) $p->stock ? 'disabled' : '' ?>><i class="bi bi-plus"></i></button>
                            </form>
                        </div>
                        <span class="fw-bold">$<?= number_format($linea['subtotal'], 2) ?></span>
                    </div>
                </div>
            </li>
        <?php endforeach ?>
    </ul>

    <div class="pt-3">
        <div class="d-flex justify-content-between fs-5 fw-bold mb-1">
            <span>Total</span>
            <span>$<?= number_format($totalLateral, 2) ?></span>
        </div>
        <?php if ($totalLateral < ENVIO_GRATIS_DESDE) : ?>
            <p class="small text-body-secondary mb-3">Te faltan $<?= number_format(ENVIO_GRATIS_DESDE - $totalLateral, 2) ?> para envío gratis a domicilio.</p>
        <?php else : ?>
            <p class="small text-success fw-bold mb-3"><i class="bi bi-truck"></i> ¡Tienes envío gratis a domicilio!</p>
        <?php endif ?>
        <a href="/carrito" class="btn btn-success btn-lg w-100">Ir a pagar</a>
    </div>
<?php endif ?>
