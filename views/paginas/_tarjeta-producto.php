<?php
// Tarjeta de producto del catálogo. Espera $producto y $carrito ([id => cantidad]).
$stock = (int) $producto->stock;
$enCarrito = $carrito[(int) $producto->id] ?? 0;
$disponibles = $stock - $enCarrito;
?>
<div class="col-6 col-md-4 col-lg-3 mb-4">
    <div class="card tarjeta-producto h-100 position-relative">
        <?php if ($producto->promo_tipo) : ?>
            <span class="position-absolute top-0 start-0 m-2 badge fs-6 text-bg-danger etiqueta-promo">¡<?= s($producto->promo_tipo) ?>!</span>
        <?php elseif ($producto->tieneOferta()) : ?>
            <span class="position-absolute top-0 start-0 m-2 badge fs-6 text-bg-danger etiqueta-promo">
                -<?= round(100 - $producto->precioVigente() * 100 / (float) $producto->precio_producto) ?>%
            </span>
        <?php endif ?>

        <?php if (!$producto->imagen_producto) : ?>
            <img src="/build/img/AbastosCheckDefault.png" class="card-img-top" loading="lazy" alt="<?= s($producto->nombre_producto) ?>" onerror="this.onerror=null; this.src='/build/img/AbastosCheckDefault.png';">
        <?php else : ?>
            <img src="/build/img/Productos/<?= s($producto->imagen_producto) ?>" class="card-img-top" loading="lazy" alt="<?= s($producto->nombre_producto) ?>" onerror="this.onerror=null; this.src='/build/img/AbastosCheckDefault.png';">
        <?php endif ?>

        <div class="card-body d-flex flex-column">
            <?php if (!empty($producto->nombre_departamento)) : ?>
                <small class="text-body-secondary"><?= s($producto->nombre_departamento) ?></small>
            <?php endif ?>
            <h3 class="card-title mb-1"><?= s($producto->nombre_producto) ?></h3>

            <p class="mb-1">
                <span class="precio <?= $producto->tieneOferta() ? 'text-danger' : 'text-success' ?>">$<?= number_format($producto->precioVigente(), 2) ?></span>
                <?php if ($producto->tieneOferta()) : ?>
                    <del class="text-body-secondary ms-1">$<?= number_format((float) $producto->precio_producto, 2) ?></del>
                <?php endif ?>
            </p>
            <?php if ($producto->promo_tipo) : ?>
                <?php [$llevas, $pagas] = \Model\Precios::PROMOS[$producto->promo_tipo]; ?>
                <p class="small fw-bold text-danger mb-1">Llévate <?= $llevas ?> y paga <?= $pagas ?></p>
            <?php endif ?>

            <p class="mb-2">
                <?php if ($stock <= 0) : ?>
                    <span class="badge text-bg-secondary">Agotado</span>
                <?php elseif ($stock <= STOCK_BAJO) : ?>
                    <span class="badge text-bg-warning">¡Quedan <?= $stock ?>!</span>
                <?php else : ?>
                    <span class="badge rounded-pill text-success border border-success-subtle bg-success-subtle">Disponible</span>
                <?php endif ?>
            </p>

            <p class="card-text text-body-secondary descripcion mb-3"><?= s($producto->descripcion_producto) ?></p>

            <!-- Sin JavaScript se envía a la página actual; con JavaScript, app.js lo manda sin recargar -->
            <form action="" method="POST" class="mt-auto js-agregar" data-producto="<?= (int) $producto->id ?>">
                <input type="hidden" name="id" value="<?= (int) $producto->id ?>">
                <?php if ($stock <= 0) : ?>
                    <button type="button" class="btn btn-secondary w-100" disabled>Agotado</button>
                <?php else : ?>
                    <div class="d-flex flex-wrap gap-2 js-zona-agregar <?= $disponibles <= 0 ? 'd-none' : '' ?>">
                        <div class="input-group input-group-sm selector-cantidad">
                            <button type="button" class="btn btn-outline-secondary js-menos" aria-label="Menos">−</button>
                            <input type="number" name="cantidad" value="1" min="1" max="<?= max(1, $disponibles) ?>"
                                class="form-control text-center" aria-label="Cantidad de <?= s($producto->nombre_producto) ?>">
                            <button type="button" class="btn btn-outline-secondary js-mas" aria-label="Más">+</button>
                        </div>
                        <button type="submit" class="btn btn-success flex-grow-1"><i class="bi bi-cart-plus"></i> Agregar</button>
                    </div>
                    <button type="button" class="btn btn-outline-secondary w-100 js-sin-disponibles <?= $disponibles > 0 ? 'd-none' : '' ?>" disabled>Ya tienes todas</button>
                <?php endif ?>
                <small class="d-block text-center text-body-secondary mt-1 js-en-carrito"><?= $enCarrito > 0 ? "{$enCarrito} en tu carrito" : '' ?></small>
            </form>
        </div>
    </div>
</div>
