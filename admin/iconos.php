<?php
require_once __DIR__ . '/../backend/funciones.php';
requerirPermisoAdmin('iconos');

$tablaIconosDisponible = existeTablaIconosCaracteristica();
$iconos = obtenerIconosCaracteristica(true);
$iconoEditando = ($tablaIconosDisponible && isset($_GET['id'])) ? obtenerIconoCaracteristicaPorId((int)$_GET['id']) : null;
$mostrarFormulario = $iconoEditando !== null || isset($_GET['new']);
$iconosFuente = obtenerOpcionesIconosFuente();
$errores = [
    'missing_name' => 'El nombre es obligatorio.',
    'duplicate_key' => 'La clase del icono ya existe. Usa otra distinta.',
    'in_use' => 'No se puede eliminar porque el icono ya esta usado en propiedades.',
    'missing_table' => 'Falta crear la tabla de iconos en la base de datos.',
    'not_found' => 'El icono solicitado no existe.',
];
$error = isset($_GET['err']) ? ($errores[$_GET['err']] ?? 'No se pudo completar la operacion.') : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iconos | Inmobiliaria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/tailwind.css')) ?>?v=<?= publicAssetVersion('css/tailwind.css') ?>" rel="stylesheet">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/theme-overrides.css')) ?>?v=<?= publicAssetVersion('css/theme-overrides.css') ?>" rel="stylesheet">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/icon-font.css')) ?>?v=<?= publicAssetVersion('css/icon-font.css') ?>" rel="stylesheet">
</head>
<body>
    <header class="app-header">
        <div class="app-header-inner">
            <div class="app-brand">
                <span class="app-brand-mark">IA</span>
                <div class="app-brand-copy">
                    <p class="app-brand-title">Inmobiliaria Argentina</p>
                    <p class="app-brand-subtitle">ABM de iconos para propiedades</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="../backend/logout.php" class="btn-secondary bg-white/10 text-white hover:bg-white/15 hover:text-white">Cerrar sesion</a>
            </div>
        </div>
    </header>

    <main class="admin-layout">
        <?= renderAdminSidebar('iconos') ?>

        <div class="admin-content">
            <section class="hero-panel">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <span class="eyebrow">Administracion</span>
                        <h1 class="section-heading mb-2">Iconos de caracteristicas</h1>
                        <p class="section-copy">La gestion vive en el panel central y el acceso queda fijo en el lateral.</p>
                    </div>
                    <a href="iconos.php?new=1" class="btn-secondary">Nuevo icono</a>
                </div>
            </section>

            <?php if (isset($_GET['ok'])): ?>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Icono guardado correctamente.
                </div>
            <?php elseif (isset($_GET['del'])): ?>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Icono eliminado correctamente.
                </div>
            <?php elseif ($error): ?>
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!$tablaIconosDisponible): ?>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    La tabla de iconos todavia no existe. Ejecuta el script <code>sql/2026_05_29_iconos_caracteristica.sql</code> para habilitar el ABM.
                </div>
            <?php endif; ?>

            <section class="admin-split">
                <?php if ($mostrarFormulario): ?>
                    <form action="../backend/<?= $iconoEditando ? 'editar_icono_caracteristica.php' : 'crear_icono_caracteristica.php' ?>" method="POST" class="admin-form admin-split-form max-w-xl space-y-4 text-sm">
                        <?php if ($iconoEditando): ?>
                            <input type="hidden" name="id" value="<?= (int)$iconoEditando['id'] ?>">
                        <?php endif; ?>

                        <div>
                            <span class="eyebrow"><?= $iconoEditando ? 'Edicion' : 'Alta' ?></span>
                            <h2 class="section-heading mb-2 text-2xl"><?= $iconoEditando ? 'Editar icono' : 'Nuevo icono' ?></h2>
                            <p class="section-copy">La clave se usa internamente en las caracteristicas de cada propiedad.</p>
                        </div>

                        <div>
                            <label class="field-label">Nombre</label>
                            <input type="text" name="nombre" required value="<?= htmlspecialchars($iconoEditando['nombre'] ?? '') ?>" class="field-input" placeholder="Ej. Piscina">
                        </div>

                        <div>
                            <label class="field-label">Clase del icono</label>
                            <input type="text" name="clave" list="icon-font-list" value="<?= htmlspecialchars($iconoEditando['clave'] ?? '') ?>" class="field-input" placeholder="Ej. icon-pileta">
                            <datalist id="icon-font-list">
                                <?php foreach ($iconosFuente as $clase => $etiqueta): ?>
                                    <option value="<?= htmlspecialchars($clase) ?>"><?= htmlspecialchars($etiqueta) ?></option>
                                <?php endforeach; ?>
                            </datalist>
                            <p class="mt-2 text-xs text-slate-500">Usa una clase `icon-*` del set de fuente. Si escribis `pileta` o `banos`, se normaliza automaticamente.</p>
                        </div>

                        <div>
                            <label class="field-label">Orden</label>
                            <input type="number" name="orden" min="1" value="<?= htmlspecialchars((string)($iconoEditando['orden'] ?? (count($iconos) + 1))) ?>" class="field-input max-w-[160px]">
                        </div>

                        <div class="space-y-3">
                            <div class="surface-card-soft flex items-center gap-4 p-4">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-slate-200 bg-white text-[1.75rem] text-slate-700">
                                    <?= renderizarIconoCaracteristica((string)($iconoEditando['clave'] ?? 'icon-check')) ?>
                                </div>
                                <div class="text-xs text-slate-500">
                                    Vista previa del icono seleccionado.
                                    <?php if (!empty($iconoEditando['archivo'])): ?>
                                        <span class="mt-1 block">Este registro tenia una imagen previa guardada: <strong class="text-slate-700"><?= htmlspecialchars($iconoEditando['archivo']) ?></strong>.</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <a href="iconos.php" class="btn-secondary">Cancelar</a>
                            <button type="submit" class="btn-accent" <?= $tablaIconosDisponible ? '' : 'disabled' ?>><?= $iconoEditando ? 'Guardar cambios' : 'Crear icono' ?></button>
                        </div>
                    </form>
                <?php endif; ?>

                <section class="surface-card admin-split-table p-5 md:p-6">
                    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Iconos cargados</h2>
                            <p class="text-sm text-slate-500">Los inactivos se conservan para no perder historial, pero no aparecen como opcion nueva.</p>
                        </div>
                        <p class="shrink-0 text-sm text-slate-500"><?= count($iconos) ?> icono(s)</p>
                    </div>

                    <div class="table-shell w-full overflow-hidden">
                        <table class="table-compact">
                            <thead>
                                <tr>
                                    <th>Icono</th>
                                    <th>Nombre</th>
                                    <th>Clave</th>
                                    <th>Ord.</th>
                                    <th>Estado</th>
                                    <th class="text-right">Acc.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$iconos): ?>
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-400">Todavia no hay iconos cargados.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($iconos as $icono): ?>
                                        <tr>
                                            <td>
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 p-2 text-slate-700">
                                                    <?= renderizarIconoCaracteristica((string)$icono['clave']) ?>
                                                </div>
                                            </td>
                                            <td class="font-semibold text-slate-800"><?= htmlspecialchars($icono['nombre']) ?></td>
                                            <td class="truncate text-sm text-slate-500" title="<?= htmlspecialchars($icono['clave']) ?>"><?= htmlspecialchars($icono['clave']) ?></td>
                                            <td><?= (int)$icono['orden'] ?></td>
                                            <td>
                                                <span class="pill <?= !empty($icono['activo']) ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-100 text-slate-700' ?>">
                                                    <?= !empty($icono['activo']) ? 'Activo' : 'Inactivo' ?>
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                <?php if ($tablaIconosDisponible && !empty($icono['id'])): ?>
                                                    <div class="inline-flex items-center gap-2">
                                                        <a href="iconos.php?id=<?= (int)$icono['id'] ?>" class="btn-primary px-3 py-2 text-xs">Editar</a>
                                                        <form action="../backend/eliminar_icono_caracteristica.php" method="POST" onsubmit="return confirm('¿Eliminar este icono?');">
                                                            <input type="hidden" name="id" value="<?= (int)$icono['id'] ?>">
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
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold text-slate-900">Catalogo de iconos</h2>
                        <p class="text-sm text-slate-500">Referencia rapida para copiar la clase exacta dentro del ABM.</p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        <?php foreach ($iconosFuente as $clase => $etiqueta): ?>
                            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-xl text-slate-700">
                                    <span class="<?= htmlspecialchars($clase) ?>" aria-hidden="true"></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-800"><?= htmlspecialchars($etiqueta) ?></p>
                                    <p class="truncate font-mono text-xs text-slate-500"><?= htmlspecialchars($clase) ?></p>
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
