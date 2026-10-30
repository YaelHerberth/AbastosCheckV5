<section class="h-100 formulario-login">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="card rounded">
                <div class="row g-0">
                    <div class="col-lg-6 d-none d-lg-block align-items-center justify-content-center">
                        <div class="d-flex align-items-center justify-content-center pt-5 pb-5">
                            <img src="/build/img/Login.png" class="img-fluid mx-auto d-block" alt="Persona haciendo sus compras en AbastosCheck">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="col p-5 rounded-end">
                            <div class="text-center mt-4">
                                <img src="/build/img/logoH2.svg" class="img-fluid" alt="Logo" width="80rem">
                                <h2 class="fw-bold py-5">Bienvenido</h2>
                            </div>

                            <?php
                            $resultado = $_GET['resultado'] ?? null;
                            if ($resultado == 2) :
                            ?>
                                <div class="alert alert-warning text-uppercase text-center" role="alert">
                                    ¡No haz iniciado sesion! Inicia sesion para ingresar a propiedades de tu perfil
                                </div>
                            <?php elseif ($resultado == 3) : ?>
                                <div class="alert alert-info text-center" role="alert">
                                    Inicia sesión para completar tu compra. Tu carrito se conserva.
                                </div>
                            <?php endif ?>

                            <?php foreach ($errores as $error) : ?>
                                <div class="alert alert-danger text-uppercase text-center" role="alert">
                                    <?= s($error) ?>
                                </div>
                            <?php endforeach ?>

                            <form method="POST" action="/login" novalidate>
                                <div class="form-outline mb-4">
                                    <label class="form-label" for="">Correo Electronico</label>
                                    <input class="form-control" type="text" name="email_usuario" placeholder="Correo Electronico" required />
                                </div>
                                <div class="form-outline mb-5">
                                    <label class="form-label" for="">Contraseña</label>
                                    <input class="form-control" type="password" name="password_usuario" class="form-control" placeholder="Contraseña" required />
                                </div>
                                <div class="text-center pt-1 mb-5">
                                    <button class="btn btn-danger btn-block fa-lg mb-3 w-100" type="submit" >Iniciar Sesion</button>
                                </div>

                            </form>
                            
                            <div class="text-center pt-1 mb-3 pb-1">
                                ¿Olvidaste tu contraseña? <a class="text-decoration-none" href="#!">¡Haz click aqui!</a>
                            </div>

                            <div class="d-flex align-items-center justify-content-center pb-4">
                                <p class="mb-0 me-2">¿No tienes una cuenta?</p>
                                <a href="/signup" class="btn btn-outline-danger">¡Crea una aqui!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>