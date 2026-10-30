<div class="container">
    <a href="/admin/usuarios" class="btn btn-outline-success"><i class="bi bi-arrow-left"></i> Volver</a>
    <hr>
    <h1>Crear Usuario</h1>
    <hr>
    <form method="POST" enctype="multipart/form-data">
        <?php foreach ($errores as $error) : ?>
            <div class="alert alert-danger text-uppercase text-center" role="alert">
                <?= s($error) ?>
            </div>
        <?php endforeach ?>

        <div class="row">
            <div class="col-md-12">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="usuario[username_usuario]" value="<?= s($usuario->username_usuario) ?>" placeholder="Nombre de usuario">
                    <label for="username">Nombre de usuario</label>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="usuario[email_usuario]" value="<?= s($usuario->email_usuario) ?>" placeholder="Correo Electronico">
                    <label for="email">Correo Electronico</label>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="usuario[password_usuario]" value="<?= s($usuario->password_usuario) ?>" id="password" placeholder="Contraseña">
                    <label for="password">Contraseña</label>
                </div>
            </div>

            <div class="col-md-12">
                <div class="container row">
                    <div class="col-md-6">
                        <label for="imagen" class="mb-1"><i class="bi bi-image"></i> Imagen (Solo jpg y png)</label>
                        <input type="file" class="form-control" id="imagen" name="usuario[imagen_usuario]" accept="image/jpeg, image/png" placeholder="Contraseña">
                    </div>

                </div>
            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-success">Guardar</button>
    </form>
</div>