<?php
require_once __DIR__ . '/../backend/funciones.php';

$operaciones = obtenerOperaciones();
$tipos = obtenerTiposPropiedad();

$filtros = [
    'ciudad'        => $_GET['ciudad'] ?? '',
    'operacion_id'  => $_GET['operacion_id'] ?? '',
    'tipo_id'       => $_GET['tipo_id'] ?? '',
    'moneda'        => strtoupper($_GET['moneda'] ?? 'ARS'),
    'precio_min'    => $_GET['precio_min'] ?? '',
    'precio_max'    => $_GET['precio_max'] ?? '',
];
$propiedades = buscarPropiedades($filtros);
$queryString = http_build_query($filtros);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Propiedades | Inmobiliaria Argentina</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/tailwind.css')) ?>?v=<?= publicAssetVersion('css/tailwind.css') ?>" rel="stylesheet">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/theme-overrides.css')) ?>?v=<?= publicAssetVersion('css/theme-overrides.css') ?>" rel="stylesheet">
</head>
<body>
<header class="app-header">
    <div class="app-header-inner">
        <a href="index.php" class="app-brand">
            <span class="app-brand-mark">IA</span>
            <div class="app-brand-copy">
                <p class="app-brand-title">Inmobiliaria Argentina</p>
                <p class="app-brand-subtitle">Propiedades</p>
            </div>
        </a>
        <nav class="app-nav">
            <a href="index.php">Inicio</a>
            <a href="propiedades.php">Propiedades</a>
            <a href="mapa.php">Mapa</a>
            <a href="contacto.php">Contacto</a>
        </nav>
    </div>
</header>

<main class="app-main space-y-6">
    <section class="hero-panel">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="eyebrow">Catálogo</span>
                <h1 class="section-heading mb-2">Propiedades disponibles</h1>
                <p class="section-copy">
                    Encontramos <strong><?= count($propiedades) ?></strong> propiedades según tus filtros.
                </p>
                <div class="mt-4 flex items-center gap-2">
                    <a href="mapa.php<?= $queryString ? '?' . htmlspecialchars($queryString) : '' ?>" class="btn-secondary text-xs md:text-sm">
                        Ver mapa
                    </a>
                </div>
            </div>

            <form method="GET" class="grid w-full gap-3 text-xs md:grid-cols-2 md:text-sm xl:min-w-[720px] xl:grid-cols-3">
                <input type="text" name="ciudad" value="<?= htmlspecialchars($filtros['ciudad']) ?>" placeholder="Ciudad / barrio"
                    class="field-input">
                <select name="operacion_id" class="field-input">
                    <option value="">Operación</option>
                    <?php foreach ($operaciones as $op): ?>
                        <option value="<?= $op['id'] ?>" <?= ($filtros['operacion_id'] == $op['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($op['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="tipo_id" class="field-input">
                    <option value="">Tipo</option>
                    <?php foreach ($tipos as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($filtros['tipo_id'] == $t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="moneda" class="field-input">
                    <option value="ARS" <?= $filtros['moneda'] === 'ARS' ? 'selected' : '' ?>>Pesos</option>
                    <option value="USD" <?= $filtros['moneda'] === 'USD' ? 'selected' : '' ?>>USD</option>
                </select>
                <input type="number" name="precio_min" value="<?= htmlspecialchars($filtros['precio_min']) ?>" placeholder="Precio mín."
                    class="field-input">
                <input type="number" name="precio_max" value="<?= htmlspecialchars($filtros['precio_max']) ?>" placeholder="Precio máx."
                    class="field-input">
                <div class="flex flex-col gap-2 sm:flex-row">
                    <button class="btn-primary flex-1 rounded-2xl">Filtrar</button>
                    <a href="propiedades.php" class="btn-secondary flex-1 rounded-2xl">Limpiar</a>
                </div>
            </form>
        </div>
    </section>

    <section>
        <?php if (!$propiedades): ?>
            <p class="surface-card-soft px-5 py-4 text-sm text-slate-500">
                No encontramos propiedades con estos criterios. Probá ampliando tu búsqueda o limpiando los filtros.
            </p>
        <?php else: ?>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <?php foreach ($propiedades as $p): ?>
                    <article class="listing-card flex flex-col">
                        <?php if (!empty($p['imagen'])): ?>
                            <img src="img/<?= htmlspecialchars($p['imagen']) ?>" alt="" class="listing-image">
                        <?php else: ?>
                            <div class="listing-placeholder">Sin imagen</div>
                        <?php endif; ?>
                        <div class="flex flex-1 flex-col gap-2 p-5">
                            <h2 class="text-sm font-semibold text-slate-900 md:text-base">
                                <?= htmlspecialchars($p['titulo']) ?>
                            </h2>
                            <p class="text-xs text-slate-500">
                                <?= htmlspecialchars($p['ciudad'] ?? '') ?><?= $p['provincia'] ? ', '.htmlspecialchars($p['provincia']) : '' ?>
                            </p>
                            <p class="text-xs text-slate-600 line-clamp-2">
                                <?= nl2br(htmlspecialchars(substr($p['descripcion'] ?? '', 0, 120))) ?>...
                            </p>
                            <div class="mt-auto flex flex-col items-start gap-4 pt-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="space-y-1">
                                    <p class="text-sm font-bold text-emerald-700">
                                        <?php if (!empty($p['precio_usd'])): ?>
                                            U$S <?= number_format($p['precio_usd'], 0, ',', '.') ?>
                                            <span class="block text-[11px] font-medium text-slate-500">
                                                $<?= number_format($p['precio'], 0, ',', '.') ?>
                                            </span>
                                        <?php else: ?>
                                            $<?= number_format($p['precio'], 0, ',', '.') ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        <?= (int)$p['ambientes'] ?> amb · <?= (int)$p['banios'] ?> baños<?= $p['cochera'] ? ' · Cochera' : '' ?>
                                    </p>
                                    <?php if (!empty($p['estado_nombre']) && $p['estado_nombre'] !== 'Disponible'): ?>
                                        <p class="text-[11px]">
                                            <span class="pill <?= $p['estado_nombre'] === 'Reservada' ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-slate-200 bg-slate-100 text-slate-700' ?>">
                                                <?= htmlspecialchars($p['estado_nombre']) ?>
                                            </span>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <a href="propiedad.php?id=<?= $p['id'] ?>" class="btn-secondary px-3 py-1.5 text-xs">
                                    Ver detalle
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<footer class="app-footer">
    <div class="app-footer-inner">
        <p>© <?= date('Y'); ?> Inmobiliaria Argentina. Todos los derechos reservados.</p>
    </div>
</footer>
</body>
</html>
