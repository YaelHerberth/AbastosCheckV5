<div class="container">
    <a href="/admin/departamentos" class="btn btn-outline-success"><i class="bi bi-arrow-left"></i> Volver</a>
    <hr>
    <h1>Crear Departamento</h1>
    <hr>
    <form method="POST" enctype="multipart/form-data">
        <?php foreach ($errores as $error) : ?>
            <div class="alert alert-danger text-uppercase text-center" role="alert">
                <?= s($error) ?>
            </div>
        <?php endforeach ?>

        <div class="row">
            <div class="col-md-12 row">

                <div class="col-md-3">
                    <?php if (!$departamento->imagen_departamento) : ?>
                        <img class="img-thumbnail img-fluid mb-3" src="/build/img/AbastosCheckDefault.png" alt="Sin imagen">
                    <?php else : ?>
                        <img class="img-thumbnail img-fluid mb-3" src="/build/img/Departamentos/<?= s($departamento->imagen_departamento) ?>" alt="<?= s($departamento->nombre_departamento) ?>">
                    <?php endif ?>
                    <label for="imagen" class="mb-1"><i class="bi bi-image"></i> Imagen (Solo jpg y png)</label>
                    <input type="file" class="form-control" id="imagen" name="departamento[imagen_departamento]" accept="image/jpeg, image/png" placeholder="Imagen">
                </div>
                <div class="col-md-9">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="departamento[nombre_departamento]" value="<?= s($departamento->nombre_departamento) ?>" placeholder="Nombre de Departamento">
                        <label for="username">Nombre de Departamento</label>
                    </div>
                </div>


            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-success">Guardar</button>
    </form>
</div>