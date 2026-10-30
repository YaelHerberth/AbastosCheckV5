<div class="container">

    <h1>Departamentos</h1>
    <p class="text-body-secondary">Elige un departamento para ver sus productos.</p>
    <hr>
    <div class="row">
        <?php foreach ($resultado as $departamento) : ?>
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <a href="/departamento?id=<?= (int) $departamento->id ?>" class="card tarjeta-producto h-100 text-decoration-none">
                    <?php if (!$departamento->imagen_departamento) : ?>
                        <div class="tarjeta-imagen d-grid" style="place-items: center;">
                            <span class="tile-icono" style="width: 96px; height: 96px; font-size: 2.6rem;">
                                <i class="bi <?= iconoDepartamento($departamento->nombre_departamento) ?>"></i>
                            </span>
                        </div>
                    <?php else : ?>
                        <img src="/build/img/Departamentos/<?= s($departamento->imagen_departamento) ?>" class="card-img-top" loading="lazy" alt="">
                    <?php endif ?>
                    <div class="card-body text-center">
                        <h2 class="card-title h5 mb-1">
                            <i class="bi <?= iconoDepartamento($departamento->nombre_departamento) ?> text-success"></i>
                            <?= s($departamento->nombre_departamento) ?>
                        </h2>
                        <span class="link-success small fw-bold">Ver productos <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>
        <?php endforeach ?>
    </div>

</div>
