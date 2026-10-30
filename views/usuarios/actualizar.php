<div class="container">
    <a href="/admin/usuarios" class="btn btn-outline-success"><i class="bi bi-arrow-left"></i> Volver</a>
    <hr>
    <h1>Actualizar Usuario</h1>
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
                    <?php if (!$usuario->imagen_usuario) : ?>
                        <img class="img-thumbnail img-fluid mb-3" src="/build/img/profile-default.jpg" alt="Foto de perfil predeterminada">
                    <?php else : ?>
                        <img class="img-thumbnail img-fluid mb-3" src="/build/img/Usuarios/<?= s($usuario->imagen_usuario) ?>" alt="<?= s($usuario->username_usuario) ?>">
                    <?php endif ?>
                    <button type="button" class="btn btn-warning mb-3 w-100" data-bs-toggle="modal" data-bs-target="#modalPass"><i class="bi bi-key"></i> Cambiar contraseña</button>
                    <label for="imagen" class="mb-1"><i class="bi bi-image"></i> Imagen (Solo jpg y png)</label>
                    <input type="file" class="form-control" id="imagen" name="usuario[imagen_usuario]" accept="image/jpeg, image/png" placeholder="Contraseña">
                </div>

                <div class="col-md-9">

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="usuario[username_usuario]" value="<?= s($usuario->username_usuario) ?>" placeholder="Nombre de usuario">
                        <label for="username">Nombre de usuario</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="usuario[email_usuario]" value="<?= s($usuario->email_usuario) ?>" placeholder="Correo Electronico">
                        <label for="email">Correo Electronico</label>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-success">Guardar</button>


        <!-- Modal -->
        <div class="modal fade" id="modalPass" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title h5">Cambiar Contraseña</h1>
                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" name="usuario[password_usuario_a]" id="password" placeholder="Contraseña">
                                <label for="password">Contraseña Anterior</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" name="usuario[password_usuario_n]" id="password" placeholder="Contraseña">
                                <label for="password">Nueva Contraseña</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                        <button type="Submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>