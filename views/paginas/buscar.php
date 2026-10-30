<div class="container">
    <h1><?= $filtros['ofertas'] && $filtros['q'] === '' ? 'Ofertas' : 'Buscar productos' ?></h1>
    <hr>

    <form method="GET" action="/buscar" class="row g-2 align-items-end mb-4">
        <div class="col-12 col-md-4">
            <label for="q" class="form-label">Producto</label>
            <input type="search" id="q" name="q" class="form-control" placeholder="Ej. arroz, atún…" value="<?= s($filtros['q']) ?>">
        </div>
        <div class="col-6 col-md-2">
            <label for="departamento" class="form-label">Departamento</label>
            <select id="departamento" name="departamento" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($departamentos as $dep) : ?>
                    <option value="<?= (int) $dep->id ?>" <?= $filtros['departamento'] === (int) $dep->id ? 'selected' : '' ?>>
                        <?= s($dep->nombre_departamento) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-3 col-md-1">
            <label for="min" class="form-label">Desde $</label>
            <input type="number" id="min" name="min" min="0" step="0.01" class="form-control" value="<?= s($filtros['min']) ?>">
        </div>
        <div class="col-3 col-md-1">
            <label for="max" class="form-label">Hasta $</label>
            <input type="number" id="max" name="max" min="0" step="0.01" class="form-control" value="<?= s($filtros['max']) ?>">
        </div>
        <div class="col-8 col-md-2">
            <label for="orden" class="form-label">Ordenar por</label>
            <select id="orden" name="orden" class="form-select">
                <option value="nombre" <?= $filtros['orden'] === 'nombre' ? 'selected' : '' ?>>Nombre</option>
                <option value="precio_asc" <?= $filtros['orden'] === 'precio_asc' ? 'selected' : '' ?>>Precio: menor a mayor</option>
                <option value="precio_desc" <?= $filtros['orden'] === 'precio_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
            </select>
        </div>
        <div class="col-4 col-md-2 d-grid">
            <button type="submit" class="btn btn-success"><i class="bi bi-search"></i> Buscar</button>
        </div>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="ofertas" value="1" id="ofertas" <?= $filtros['ofertas'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="ofertas"><i class="bi bi-tag-fill text-danger"></i> Solo ofertas y promociones</label>
            </div>
        </div>
    </form>

    <p class="text-body-secondary">
        <?= count($resultado) ?> <?= count($resultado) === 1 ? 'producto encontrado' : 'productos encontrados' ?>
        <?php if ($filtros['q'] !== '') : ?> para “<?= s($filtros['q']) ?>”<?php endif ?>
    </p>

    <div class="row">
        <?php foreach ($resultado as $producto) : ?>
            <?php include __DIR__ . '/_tarjeta-producto.php'; ?>
        <?php endforeach ?>
    </div>

    <?php if (empty($resultado)) : ?>
        <div class="alert alert-light text-center">
            No encontramos productos con esos filtros. <a href="/buscar" class="link-success">Ver todo el catálogo</a>
        </div>
    <?php endif ?>
</div>
