<?php
// Campos de promoción del producto (se usan en crear y actualizar)
use Model\Precios;
?>
<div class="col-12">
    <fieldset class="border rounded p-3 mb-3">
        <legend class="fs-6 fw-bold w-auto px-2 mb-0"><i class="bi bi-tag-fill text-danger"></i> Promoción (opcional)</legend>
        <div class="row">
            <div class="col-md-6">
                <div class="form-floating mb-2">
                    <input type="number" step="0.01" min="0" class="form-control" id="precio_oferta" name="producto[precio_oferta]"
                        value="<?= s($producto->precio_oferta) ?>" placeholder="Precio de oferta">
                    <label for="precio_oferta">Precio de oferta</label>
                </div>
                <small class="text-body-secondary">Debe ser menor al precio normal, que se mostrará tachado. Déjalo vacío para quitar la oferta.</small>
            </div>
            <div class="col-md-6">
                <div class="form-floating mb-2">
                    <select class="form-select" id="promo_tipo" name="producto[promo_tipo]">
                        <option value="">Sin promoción</option>
                        <?php foreach (Precios::PROMOS as $promo => [$llevas, $pagas]) : ?>
                            <option value="<?= s($promo) ?>" <?= $producto->promo_tipo === $promo ? 'selected' : '' ?>>
                                <?= s($promo) ?> (llevas <?= $llevas ?>, pagas <?= $pagas ?>)
                            </option>
                        <?php endforeach ?>
                    </select>
                    <label for="promo_tipo">Promoción por cantidad</label>
                </div>
                <small class="text-body-secondary">Se aplica sobre el precio vigente (el de oferta si lo hay).</small>
            </div>
        </div>
    </fieldset>
</div>
