<?php
require_once __DIR__ . '/../backend/funciones.php';

$operaciones = obtenerOperaciones();
$tipos       = obtenerTiposPropiedad();

$filtros = [
    'ciudad'        => $_GET['ciudad'] ?? '',
    'operacion_id'  => $_GET['operacion_id'] ?? '',
    'tipo_id'       => $_GET['tipo_id'] ?? '',
    'precio_min'    => $_GET['precio_min'] ?? '',
    'precio_max'    => $_GET['precio_max'] ?? '',
];

// Opciones de paginado
$perPageOptions = [15,25, 50, 100, 150, 200];

// Tamaño de página seleccionado (por defecto 25)
$porPagina = isset($_GET['por_pagina']) ? (int) $_GET['por_pagina'] : 25;
if (!in_array($porPagina, $perPageOptions, true)) {
    $porPagina = 25;
}

// Página actual (1-based)
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
if ($pagina < 1) {
    $pagina = 1;
}

// Traemos todas las propiedades filtradas
$propiedades = buscarPropiedades($filtros);

// Cálculos de paginado sobre el array
$totalPropiedades = count($propiedades);
$totalPaginas     = max(1, (int) ceil($totalPropiedades / $porPagina));
if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}

$offset            = ($pagina - 1) * $porPagina;
$propiedadesPagina = array_slice($propiedades, $offset, $porPagina);

// Armamos el query string sin la página (para reutilizar en los links)
$queryString = http_build_query(array_merge($filtros, [
    'por_pagina' => $porPagina,
]));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Propiedades | Inmobiliaria Argentina</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <span class="badge rounded-circle bg-success me-2 p-2">IA</span>
            <span>
                <div class="fw-semibold">Inmobiliaria Argentina</div>
                <small class="text-light-50">Propiedades en CABA y GBA</small>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="propiedades.php">Propiedades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mapa.php">Mapa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contacto.php">Contacto</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4">

    <!-- Título + botones lista/mapa + export CSV -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1"></h1>
       </div>
        <div class="d-flex flex-wrap gap-2">
            <!-- Switch Lista / Mapa -->
            <div class="btn-group" role="group">
                <a href="propiedades.php?<?= $queryString ?>" class="btn btn-sm btn-primary">
                    📋 Ver lista
                </a>
                <a href="mapa.php?<?= $queryString ?>" class="btn btn-sm btn-outline-primary">
                    🗺 Ver mapa
                </a>
            </div>

            <!-- Export CSV -->
            <a href="../backend/export_propiedades_csv.php?<?= $queryString ?>"
               class="btn btn-sm btn-outline-secondary">
                Exportar CSV
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <label class="form-label mb-1">Ciudad / barrio</label>
                    <input type="text" name="ciudad" class="form-control"
                           value="<?= htmlspecialchars($filtros['ciudad']) ?>"
                           placeholder="Ej: Palermo, San Isidro">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Operación</label>
                    <select name="operacion_id" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach ($operaciones as $op): ?>
                            <option value="<?= $op['id'] ?>"
                                <?= ($filtros['operacion_id'] == $op['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($op['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Tipo</label>
                    <select name="tipo_id" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t['id'] ?>"
                                <?= ($filtros['tipo_id'] == $t['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Precio mín. (ARS)</label>
                    <input type="number" name="precio_min" class="form-control"
                           value="<?= htmlspecialchars($filtros['precio_min']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Precio máx. (ARS)</label>
                    <input type="number" name="precio_max" class="form-control"
                           value="<?= htmlspecialchars($filtros['precio_max']) ?>">
                </div>

                <div class="col-12 d-flex justify-content-end mt-2">
                    <button type="submit" class="btn btn-primary btn-sm me-2">
                        Filtrar
                    </button>
                    <a href="propiedades.php" class="btn btn-outline-secondary btn-sm">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de propiedades -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <div class="small text-muted mb-2 mb-md-0">
            <?php if ($totalPropiedades > 0): ?>
                Mostrando
                <?= $offset + 1 ?>–<?= min($offset + $porPagina, $totalPropiedades) ?>
                de <?= $totalPropiedades ?> propiedades
            <?php else: ?>
                No hay propiedades para los filtros seleccionados.
            <?php endif; ?>
        </div>

        <form method="GET" class="d-flex align-items-center gap-2">
            <?php foreach ($filtros as $nombreFiltro => $valorFiltro): ?>
                <input type="hidden"
                       name="<?= $nombreFiltro ?>"
                       value="<?= htmlspecialchars($valorFiltro) ?>">
            <?php endforeach; ?>
            <label for="por_pagina" class="form-label mb-0 me-1 small">Resultados por página:</label>
            <select name="por_pagina"
                    id="por_pagina"
                    class="form-select form-select-sm"
                    onchange="this.form.submit()">
                <?php foreach ($perPageOptions as $opt): ?>
                    <option value="<?= $opt ?>" <?= $porPagina === $opt ? 'selected' : '' ?>>
                        <?= $opt ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if ($totalPropiedades === 0): ?>
        <div class="alert alert-info">
            No encontramos propiedades con los filtros seleccionados. Probá quitar algún filtro o ampliar el rango de precio.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 g-3">
            <?php foreach ($propiedadesPagina as $p): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <?php
                        // Imagen principal + placeholder fijo para el listado
                        $imagenLista = !empty($p['imagen'])
                            ? $p['imagen']
                            : 'prop_default.jpg'; // imagen fija por defecto en /public/img
                        ?>
                        <img src="img/<?= htmlspecialchars($imagenLista) ?>"
                             class="card-img-top"
                             alt="<?= htmlspecialchars($p['titulo'] ?? 'Propiedad') ?>">
                        <div class="card-body">
                            <h5 class="card-title mb-1">
                                <?= htmlspecialchars($p['titulo'] ?? 'Propiedad') ?>
                            </h5>
                            <p class="card-text small text-muted mb-2">
                                <?= htmlspecialchars($p['tipo_nombre'] ?? '') ?>
                                <?php if (!empty($p['operacion_nombre'])): ?>
                                    · <?= htmlspecialchars($p['operacion_nombre']) ?>
                                <?php endif; ?><br>
                                <?= htmlspecialchars($p['ciudad'] ?? '') ?>
                            </p>
                            <p class="mb-1 fw-semibold">
                                <?php if (!empty($p['precio'])): ?>
                                    $<?= number_format($p['precio'], 0, ',', '.') ?>
                                <?php endif; ?>
                                <?php if (!empty($p['precio_usd'])): ?>
                                    <span class="text-secondary ms-1">
                                        (USD <?= number_format($p['precio_usd'], 0, ',', '.') ?>)
                                    </span>
                                <?php endif; ?>
                            </p>
                            <p class="small mb-0 text-muted">
                                <?php if (!empty($p['ambientes'])): ?>
                                    <?= (int)$p['ambientes'] ?> amb ·
                                <?php endif; ?>
                                <?php if (!empty($p['superficie'])): ?>
                                    <?= (int)$p['superficie'] ?> m²
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="propiedad.php?id=<?= $p['id'] ?>"
                               class="btn btn-sm btn-outline-primary w-100">
                                Ver detalle
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php if ($totalPaginas > 1): ?>
            <nav aria-label="Paginación de propiedades" class="mt-3">
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="propiedades.php?<?= $queryString ?>&pagina=<?= max(1, $pagina - 1) ?>">
                            Anterior
                        </a>
                    </li>

                    <?php
                    $maxLinks = 5;
                    $inicio   = max(1, $pagina - 2);
                    $fin      = min($totalPaginas, $inicio + $maxLinks - 1);
                    if ($fin - $inicio + 1 < $maxLinks) {
                        $inicio = max(1, $fin - $maxLinks + 1);
                    }
                    for ($i = $inicio; $i <= $fin; $i++):
                    ?>
                        <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                            <a class="page-link"
                               href="propiedades.php?<?= $queryString ?>&pagina=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="propiedades.php?<?= $queryString ?>&pagina=<?= min($totalPaginas, $pagina + 1) ?>">
                            Siguiente
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

</main>

<footer class="bg-dark text-light py-3 mt-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-2 small">
        <span>© <?= date('Y'); ?> Inmobiliaria Argentina. Todos los derechos reservados.</span>
        <span>
            Panel admin:
            <a href="../admin/login.php" class="link-light text-decoration-underline">Acceder</a>
        </span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
