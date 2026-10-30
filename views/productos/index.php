<div class="container">
    <div class="row">
        <div class="col-md-2">
            <a href="/profile" class="btn btn-outline-success mb-3"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>
        <div class="col-md-10">
        </div>
    </div>
    <?php $r = $_GET['resultado'] ?? null ?>

    <?php if ($r == 1) : ?>
        <div class="alert alert-success text-uppercase text-center" role="alert">
            Producto Registrado Correctamente
        </div>
    <?php elseif ($r == 2) : ?>
        <div class="alert alert-success text-uppercase text-center" role="alert">
            Producto Actualizado Correctamente
        </div>
    <?php elseif ($r == 3) : ?>
        <div class="alert alert-success text-uppercase text-center" role="alert">
            Producto Dado de Baja Correctamente
        </div>
    <?php elseif ($r == 4) : ?>
        <div class="alert alert-success text-uppercase text-center" role="alert">
            Producto Eliminado Correctamente
        </div>
    <?php elseif ($r == 5) : ?>
        <div class="alert alert-danger text-uppercase text-center" role="alert">
            Producto No Existe
        </div>
    <?php endif ?>
    <div class="row">
        <div class="col">
            <h2>Registros de Productos</h2>
        </div>
        <div class="col">
            <div class="text-end">
                <a href="/admin/productos/crear" class="btn btn-success mb-3"><i class="bi bi-person-plus"></i> Crear</a>
            </div>
        </div>
    </div>
    <?php
    $buscarPlaceholder = 'Buscar producto, departamento o id';
    $nombreRegistros = 'productos';
    include __DIR__ . '/../admin/_buscador.php';
    ?>
    <div class="table-responsive">
    <table class="table align-middle tabla-admin">
        <thead>
            <tr>
                <?= thOrden($listado, 'id', 'Id') ?>
                <?= thOrden($listado, 'nombre', 'Nombre') ?>
                <?= thOrden($listado, 'precio', 'Precio') ?>
                <th scope="col">Descripción</th>
                <th scope="col">Imagen</th>
                <?= thOrden($listado, 'stock', 'Stock') ?>
                <?= thOrden($listado, 'departamento', 'Departamento') ?>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$resultado) : ?>
                <?php $columnas = 8; include __DIR__ . '/../admin/_sin-resultados.php'; ?>
            <?php endif ?>
            <?php foreach ($resultado as $producto) : ?>
                <tr>
                    <td><?= s($producto->id) ?></td>
                    <td><?= s($producto->nombre_producto) ?></td>
                    <td class="text-nowrap">
                        <?php if ($producto->tieneOferta()) : ?>
                            <del class="text-body-secondary">$<?= number_format((float) $producto->precio_producto, 2) ?></del>
                            <span class="text-danger fw-bold">$<?= number_format((float) $producto->precio_oferta, 2) ?></span>
                        <?php else : ?>
                            $<?= number_format((float) $producto->precio_producto, 2) ?>
                        <?php endif ?>
                        <?php if ($producto->promo_tipo) : ?>
                            <span class="badge text-bg-danger"><?= s($producto->promo_tipo) ?></span>
                        <?php endif ?>
                    </td>
                    <td><?= s($producto->descripcion_producto) ?></td>
                    
                    <?php if (!$producto->imagen_producto) : ?>
                        <td><img class="img-fluid img-thumbnail" style="width: 75px;" src="/build/img/AbastosCheckDefault.png" alt="Sin imagen"></td>
                    <?php else : ?>
                        <td><img class="img-fluid img-thumbnail" style="width: 75px;" src="/build/img/Productos/<?= s($producto->imagen_producto) ?>" alt="<?= s($producto->nombre_producto) ?>"></td>
                    <?php endif ?>
                    <td>
                        <?php if ((int) $producto->stock <= STOCK_BAJO) : ?>
                            <span class="badge text-bg-warning" title="Inventario bajo"><?= (int) $producto->stock ?></span>
                        <?php else : ?>
                            <?= (int) $producto->stock ?>
                        <?php endif ?>
                    </td>
                    <td><?= s($producto->nombre_departamento) ?></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="/admin/productos/actualizar?id=<?= s($producto->id) ?>" class="btn btn-warning" title="Editar" aria-label="Editar"><i class="bi bi-pen"></i></a>
                            <form action="/admin/productos/eliminar" method="POST"> <input type="hidden" name="id" value="<?= s($producto->id) ?>"> <button type="submit" class="btn btn-danger" title="Eliminar" aria-label="Eliminar"><i class="bi bi-trash"></i></button> </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    </div>
    <?php include __DIR__ . '/../admin/_paginacion.php'; ?>
</div>

<div class="modal fade" id="crear" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h1 class="modal-title fs-5">Crear Usuario</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">

                    <div class="modal-footer">

                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success">Guardar</button>
                    </div>
            </form>

        </div>
    </div>
</div>