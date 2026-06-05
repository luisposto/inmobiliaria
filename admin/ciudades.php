<?php
require_once __DIR__ . '/../backend/funciones.php';
requerirPermisoAdmin('ubicaciones');

$tablaUbicacionesDisponible = existeTablaProvincias() && existeTablaCiudades();
$provincias = obtenerProvincias();
$ciudades = obtenerCiudades();
$ciudadEditando = ($tablaUbicacionesDisponible && isset($_GET['id'])) ? obtenerCiudadPorId((int) $_GET['id']) : null;
$mostrarFormulario = $ciudadEditando !== null || isset($_GET['new']);
$errores = [
    'missing_name' => 'El nombre de la ciudad es obligatorio.',
    'missing_table' => 'Faltan crear las tablas de provincias y ciudades en la base de datos.',
    'missing_province' => 'Debes seleccionar una provincia.',
    'invalid_province' => 'La provincia elegida no existe.',
    'duplicate_city' => 'Ya existe una ciudad con ese nombre en la provincia seleccionada.',
    'not_found' => 'La ciudad solicitada no existe.',
];
$error = isset($_GET['err']) ? ($errores[$_GET['err']] ?? 'No se pudo completar la operacion.') : null;

$ciudadesPorProvincia = [];
foreach ($ciudades as $ciudad) {
    $provinciaId = (int) ($ciudad['provincia_id'] ?? 0);
    $ciudadesPorProvincia[$provinciaId] = ($ciudadesPorProvincia[$provinciaId] ?? 0) + 1;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ciudades y provincias | Inmobiliaria</title>
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
                    <p class="app-brand-subtitle">ABM de ciudades y provincias</p>
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
        <?= renderAdminSidebar('ubicaciones') ?>

        <div class="admin-content">
            <section class="hero-panel">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <span class="eyebrow">Administracion</span>
                        <h1 class="section-heading mb-2">Ciudades y provincias</h1>
                        <p class="section-copy">Gestiona el catalogo de provincias argentinas y da de alta ciudades vinculandolas desde un combo.</p>
                    </div>
                    <a href="ciudades.php?new=1" class="btn-secondary">Nueva ciudad</a>
                </div>
            </section>

            <?php if (isset($_GET['ok'])): ?>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Ciudad guardada correctamente.
                </div>
            <?php elseif (isset($_GET['del'])): ?>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Ciudad eliminada correctamente.
                </div>
            <?php elseif ($error): ?>
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!$tablaUbicacionesDisponible): ?>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    Ejecuta el script <code>sql/2026_06_05_provincias_ciudades.sql</code> para habilitar esta seccion.
                </div>
            <?php endif; ?>

            <section class="admin-split">
                <?php if ($mostrarFormulario): ?>
                    <form action="../backend/<?= $ciudadEditando ? 'editar_ciudad.php' : 'crear_ciudad.php' ?>" method="POST" class="admin-form admin-split-form max-w-xl space-y-4 text-sm">
                        <?php if ($ciudadEditando): ?>
                            <input type="hidden" name="id" value="<?= (int) $ciudadEditando['id'] ?>">
                        <?php endif; ?>

                        <div>
                            <span class="eyebrow"><?= $ciudadEditando ? 'Edicion' : 'Alta' ?></span>
                            <h2 class="section-heading mb-2 text-2xl"><?= $ciudadEditando ? 'Editar ciudad' : 'Nueva ciudad' ?></h2>
                            <p class="section-copy">Las provincias se cargan desde el catalogo inicial de Argentina y la ciudad queda asociada a una sola provincia.</p>
                        </div>

                        <div>
                            <label class="field-label">Provincia</label>
                            <select name="provincia_id" class="field-input" required>
                                <option value="">Seleccionar provincia</option>
                                <?php foreach ($provincias as $provincia): ?>
                                    <option value="<?= (int) $provincia['id'] ?>" <?= ((string) ($ciudadEditando['provincia_id'] ?? '') === (string) $provincia['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($provincia['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="field-label">Ciudad</label>
                            <input type="text" name="nombre" required value="<?= htmlspecialchars($ciudadEditando['nombre'] ?? '') ?>" class="field-input" placeholder="Ej. Rosario">
                        </div>

                        <label class="surface-card-soft flex items-center gap-3 p-4">
                            <input type="checkbox" name="activo" value="1" class="h-5 w-5 shrink-0" <?= !isset($ciudadEditando['activo']) || !empty($ciudadEditando['activo']) ? 'checked' : '' ?>>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Ciudad activa</p>
                                <p class="text-xs text-slate-500">Si la desactivas, se mantiene en la base pero no deberia usarse en nuevas cargas.</p>
                            </div>
                        </label>

                        <div class="flex justify-end gap-3 pt-2">
                            <a href="ciudades.php" class="btn-secondary">Cancelar</a>
                            <button type="submit" class="btn-accent" <?= ($tablaUbicacionesDisponible && $provincias !== []) ? '' : 'disabled' ?>>
                                <?= $ciudadEditando ? 'Guardar cambios' : 'Crear ciudad' ?>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <section class="surface-card admin-split-table p-5 md:p-6">
                    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Ciudades cargadas</h2>
                            <p class="text-sm text-slate-500">Cada ciudad queda relacionada con una provincia de Argentina.</p>
                        </div>
                        <p class="shrink-0 text-sm text-slate-500"><?= count($ciudades) ?> ciudad(es)</p>
                    </div>

                    <div class="table-shell w-full overflow-hidden">
                        <table class="table-compact">
                            <thead>
                                <tr>
                                    <th>Ciudad</th>
                                    <th>Provincia</th>
                                    <th>Estado</th>
                                    <th class="text-right">Acc.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$ciudades): ?>
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-400">Todavia no hay ciudades cargadas.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($ciudades as $ciudad): ?>
                                        <tr>
                                            <td class="font-semibold text-slate-800"><?= htmlspecialchars($ciudad['nombre']) ?></td>
                                            <td class="text-sm text-slate-500"><?= htmlspecialchars($ciudad['provincia_nombre']) ?></td>
                                            <td>
                                                <span class="pill <?= !empty($ciudad['activo']) ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-100 text-slate-700' ?>">
                                                    <?= !empty($ciudad['activo']) ? 'Activa' : 'Inactiva' ?>
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                <?php if ($tablaUbicacionesDisponible): ?>
                                                    <div class="inline-flex items-center gap-2">
                                                        <a href="ciudades.php?id=<?= (int) $ciudad['id'] ?>" class="btn-primary px-3 py-2 text-xs">Editar</a>
                                                        <form action="../backend/eliminar_ciudad.php" method="POST" onsubmit="return confirm('Eliminar esta ciudad?');">
                                                            <input type="hidden" name="id" value="<?= (int) $ciudad['id'] ?>">
                                                            <button class="btn-danger px-3 py-2 text-xs">Eliminar</button>
                                                        </form>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-xs text-slate-400">Sin acciones</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="surface-card admin-split-table p-5 md:p-6">
                    <div class="mb-5 flex flex-col gap-2">
                        <h2 class="text-lg font-semibold text-slate-900">Provincias base</h2>
                        <p class="text-sm text-slate-500">Catalogo inicial de provincias argentinas disponible para relacionar ciudades.</p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        <?php foreach ($provincias as $provincia): ?>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($provincia['nombre']) ?></p>
                                        <p class="text-xs text-slate-500"><?= (int) ($ciudadesPorProvincia[(int) $provincia['id']] ?? 0) ?> ciudad(es)</p>
                                    </div>
                                    <span class="pill <?= !empty($provincia['activo']) ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-100 text-slate-700' ?>">
                                        <?= !empty($provincia['activo']) ? 'Activa' : 'Inactiva' ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </section>
        </div>
    </main>
</body>
</html>
