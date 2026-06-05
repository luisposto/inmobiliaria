<?php
require_once __DIR__ . '/../backend/funciones.php';
requerirPermisoAdmin('propiedades');

$operaciones = obtenerOperaciones();
$tipos = obtenerTiposPropiedad();
$estados = obtenerEstadosPropiedad();

$filtros = [
    'ciudad'        => $_GET['ciudad'] ?? '',
    'operacion_id'  => $_GET['operacion_id'] ?? '',
    'tipo_id'       => $_GET['tipo_id'] ?? '',
    'estado_id'     => $_GET['estado_id'] ?? '',
    'precio_min'    => $_GET['precio_min'] ?? '',
    'precio_max'    => $_GET['precio_max'] ?? '',
];
$propiedades = buscarPropiedades($filtros);

$total = count($propiedades);
$venta = count(array_filter($propiedades, fn($p) => ($p['operacion_nombre'] ?? null) === 'Venta'));
$alquiler = count(array_filter($propiedades, fn($p) => ($p['operacion_nombre'] ?? null) === 'Alquiler'));
$destacadas = count(array_filter($propiedades, fn($p) => $p['destacado'] == 1));

$queryString = http_build_query($filtros);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin | Inmobiliaria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/tailwind.css')) ?>?v=<?= publicAssetVersion('css/tailwind.css') ?>" rel="stylesheet">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/theme-overrides.css')) ?>?v=<?= publicAssetVersion('css/theme-overrides.css') ?>" rel="stylesheet">
</head>
<body>
    <header class="app-header">
        <div class="app-header-inner">
            <div class="app-brand">
                <span class="app-brand-mark">IA</span>
                <div class="app-brand-copy">
                    <p class="app-brand-title">Inmobiliaria Argentina</p>
                    <p class="app-brand-subtitle">Panel de administracion</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-xs md:text-sm">
                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-2 text-slate-200">
                    Hola, <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin') ?>
                </span>
                <a href="../backend/logout.php" class="btn-secondary bg-white/10 text-white hover:bg-white/15 hover:text-white">
                    Cerrar sesion
                </a>
            </div>
        </div>
    </header>

    <main class="admin-layout">
        <?= renderAdminSidebar('propiedades') ?>

        <div class="admin-content">
            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="metric-card">
                    <p class="mb-1 text-xs uppercase tracking-[0.18em] text-slate-500">Propiedades totales</p>
                    <p class="text-3xl font-extrabold text-slate-950"><?= $total ?></p>
                </div>
                <div class="metric-card">
                    <p class="mb-1 text-xs uppercase tracking-[0.18em] text-slate-500">En venta</p>
                    <p class="text-3xl font-extrabold text-emerald-600"><?= $venta ?></p>
                </div>
                <div class="metric-card">
                    <p class="mb-1 text-xs uppercase tracking-[0.18em] text-slate-500">En alquiler</p>
                    <p class="text-3xl font-extrabold text-sky-600"><?= $alquiler ?></p>
                </div>
                <div class="metric-card">
                    <p class="mb-1 text-xs uppercase tracking-[0.18em] text-slate-500">Destacadas</p>
                    <p class="text-3xl font-extrabold text-amber-500"><?= $destacadas ?></p>
                </div>
            </section>

            <section class="hero-panel">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <span class="eyebrow">Dashboard</span>
                        <h1 class="section-heading mb-2">Propiedades</h1>
                        <p class="section-copy">Gestiona el inventario, exporta resultados y aplica filtros sin salir del panel.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="../backend/export_propiedades_csv.php?<?= htmlspecialchars($queryString) ?>" class="btn-secondary">
                            Exportar CSV
                        </a>
                        <a href="nueva_propiedad.php" class="btn-accent">+ Nueva propiedad</a>
                    </div>
                </div>
            </section>

            <section class="surface-card p-5 md:p-6">
                <form method="GET" class="grid gap-3 text-xs md:grid-cols-3 md:text-sm xl:grid-cols-6">
                    <input type="text" name="ciudad" value="<?= htmlspecialchars($filtros['ciudad']) ?>" placeholder="Ciudad" class="field-input xl:col-span-2">
                    <select name="operacion_id" class="field-input">
                        <option value="">Operacion</option>
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
                    <select name="estado_id" class="field-input">
                        <option value="">Estado</option>
                        <?php foreach ($estados as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= ($filtros['estado_id'] == $e['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="precio_min" value="<?= htmlspecialchars($filtros['precio_min']) ?>" placeholder="Precio min." class="field-input">
                    <input type="number" name="precio_max" value="<?= htmlspecialchars($filtros['precio_max']) ?>" placeholder="Precio max." class="field-input">
                    <div class="flex justify-end gap-2 pt-1 xl:col-span-6">
                        <a href="dashboard.php" class="btn-secondary">Limpiar</a>
                        <button class="btn-primary">Filtrar</button>
                    </div>
                </form>

                <?php if (isset($_GET['ok'])): ?>
                    <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        Propiedad creada correctamente.
                    </div>
                <?php elseif (isset($_GET['upd'])): ?>
                    <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        Propiedad actualizada correctamente.
                    </div>
                <?php elseif (isset($_GET['del'])): ?>
                    <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        Propiedad eliminada correctamente.
                    </div>
                <?php endif; ?>

                <div class="table-shell mt-5 overflow-x-auto">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Titulo</th>
                                <th>Ciudad</th>
                                <th>Operacion</th>
                                <th>Estado</th>
                                <th>Precio</th>
                                <th>Dest.</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$propiedades): ?>
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-center text-sm text-slate-400">
                                        No hay propiedades cargadas.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($propiedades as $p): ?>
                                    <tr>
                                        <td class="text-xs text-slate-400">#<?= $p['id'] ?></td>
                                        <td>
                                            <div class="font-semibold text-slate-800"><?= htmlspecialchars($p['titulo']) ?></div>
                                            <div class="text-xs text-slate-500"><?= htmlspecialchars($p['direccion'] ?? '') ?></div>
                                        </td>
                                        <td class="text-sm text-slate-600"><?= htmlspecialchars($p['ciudad'] ?? '') ?></td>
                                        <td>
                                            <?php if (!empty($p['operacion_nombre'])): ?>
                                                <span class="pill <?= $p['operacion_nombre'] === 'Venta' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-sky-200 bg-sky-50 text-sky-700' ?>">
                                                    <?= htmlspecialchars($p['operacion_nombre']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($p['estado_nombre'])): ?>
                                                <span class="pill <?= $p['estado_nombre'] === 'Disponible' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : ($p['estado_nombre'] === 'Reservada' ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-slate-200 bg-slate-100 text-slate-700') ?>">
                                                    <?= htmlspecialchars($p['estado_nombre']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="font-semibold text-slate-900">
                                            <?php if (!empty($p['precio_usd'])): ?>
                                                U$S <?= number_format($p['precio_usd'], 0, ',', '.') ?>
                                                <div class="text-xs font-medium text-slate-500">$<?= number_format($p['precio'], 0, ',', '.') ?></div>
                                            <?php else: ?>
                                                $<?= number_format($p['precio'], 0, ',', '.') ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-sm">
                                            <?php if ($p['destacado']): ?>
                                                <span class="text-base text-amber-500" title="Destacada" aria-label="Destacada">&#9733;</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <div class="inline-flex items-center gap-2">
                                                <a href="editar_propiedad.php?id=<?= $p['id'] ?>" class="btn-primary px-3 py-2 text-xs">
                                                    Editar
                                                </a>
                                                <form action="../backend/eliminar_propiedad.php" method="POST" onsubmit="return confirm('¿Eliminar esta propiedad?');">
                                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                    <button class="btn-danger px-3 py-2 text-xs">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
