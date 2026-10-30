<div class="container">
    <a href="/admin/productos" class="btn btn-outline-success"><i class="bi bi-arrow-left"></i> Volver</a>
    <hr>
    <h1>Crear Producto</h1>
    <hr>
    <form method="POST" enctype="multipart/form-data">
        <?php foreach ($errores as $error) : ?>
            <div class="alert alert-danger text-uppercase text-center" role="alert">
                <?= s($error) ?>
            </div>
        <?php endforeach ?>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <?php if (!$producto->imagen_producto) : ?>
                            <img class="img-thumbnail img-fluid mb-3" src="/build/img/AbastosCheckDefault.png" alt="Product Default">
                        <?php else : ?>
                            <img class="img-thumbnail img-fluid mb-3" src="/build/img/Productos/<?= s($producto->imagen_producto) ?>" alt="<?= s($producto->nombre_producto) ?>">
                        <?php endif ?>

                        <label for="imagen" class="mb-1"><i class="bi bi-image"></i> Imagen (Solo jpg y png)</label>
                        <input type="file" class="form-control" id="imagen" name="producto[imagen_producto]" accept="image/jpeg, image/png" placeholder="Imagen">

                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="username" name="producto[nombre_producto]" value="<?= s($producto->nombre_producto) ?>" placeholder="Nombre de Producto">
                                    <label for="username">Nombre de Producto</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="number" step="any" class="form-control" id="username" name="producto[precio_producto]" value="<?= s($producto->precio_producto) ?>" placeholder="Precio">
                                    <label for="username">Precio</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="username" name="producto[stock]" value="<?= s($producto->stock) ?>" placeholder="Stock">
                                    <label for="username">Stock</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="departamento" name="producto[id_departamento]">
                                        <option disabled selected>Seleccione Departamento</option>
                                        <?php foreach ($departamentos as $departamento) : ?>
                                            <option value="<?= s($departamento->id) ?>" <?php echo $departamento->id === $producto->id_departamento ? 'selected' : '' ?>><?= s($departamento->nombre_departamento) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <label for="departamento">Departamento</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control h-25" name="producto[descripcion_producto]" id="descripcion" placeholder="Descripción"><?= s($producto->descripcion_producto) ?></textarea>
                                    <label for="descripcion">Descripción</label>
                                </div>
                            </div>
<?php include __DIR__ . '/_promocion.php'; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-success">Guardar</button>
    </form>
</div>