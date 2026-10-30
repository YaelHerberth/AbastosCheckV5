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
            Usuario Registrado Correctamente
        </div>
    <?php elseif ($r == 2) : ?>
        <div class="alert alert-success text-uppercase text-center" role="alert">
            Usuario Actualizado Correctamente
        </div>
    <?php elseif ($r == 3) : ?>
        <div class="alert alert-success text-uppercase text-center" role="alert">
            Usuario Dado de Baja Correctamente
        </div>
    <?php elseif ($r == 4) : ?>
        <div class="alert alert-success text-uppercase text-center" role="alert">
            Usuario Eliminado Correctamente
        </div>
    <?php elseif ($r == 5) : ?>
        <div class="alert alert-danger text-uppercase text-center" role="alert">
            Usuario No Existe
        </div>
    <?php endif ?>
    <div class="row">
        <div class="col">
            <h2>Registros de Usuarios</h2>
        </div>
        <div class="col">
            <div class="text-end">
                <a href="/admin/usuarios/crear" class="btn btn-success mb-3"><i class="bi bi-person-plus"></i> Crear</a>
            </div>
        </div>
    </div>
    <br>
    <table class="table" id="tabla">
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultado as $usuarios) : ?>
                <tr>
                    <td><?= s($usuarios->id) ?></td>
                    <td><?= s($usuarios->username_usuario) ?></td>
                    <td><?= s($usuarios->email_usuario) ?></td>
                    <?php if (!$usuarios->imagen_usuario) : ?>
                        <td><img class="img-fluid img-thumbnail" style="width: 75px;" src="/build/img/profile-default.jpg" alt="ProfileImageDefault"></td>
                    <?php else : ?>
                        <td><img class="img-fluid img-thumbnail" style="width: 75px;" src="/build/img/Usuarios/<?= s($usuarios->imagen_usuario) ?>" alt="<?= s($usuarios->username_usuario) ?>"></td>
                    <?php endif ?>
                    <td>
                        <div class="row">
                            <div class="col-3">
                                <a href="/admin/usuarios/actualizar?id=<?= s($usuarios->id) ?>" class="btn btn-warning"><i class="bi bi-pen"></i></a>
                            </div>
                            <div class="col-3">
                                <form action="/admin/usuarios/eliminar" method="POST">
                                    <input type="hidden" name="id" value="<?= s($usuarios->id) ?>">
                                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>

                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
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

<script>
    $(document).ready(function() {
        $('#tabla').DataTable({
            language: {
                searchBuilder: {
                    title: 'Buscar'
                }
            },
            "order": [
                [0, "desc"]
            ],

        });
    });
</script>