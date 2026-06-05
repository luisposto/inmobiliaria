<?php
require_once __DIR__ . '/../backend/funciones.php';
requerirPermisoAdmin('staff');

$tablaStaffDisponible = existeTablaStaff();
$staffItems = obtenerStaff(true);
$staffEditando = ($tablaStaffDisponible && isset($_GET['id'])) ? obtenerStaffPorId((int)$_GET['id']) : null;
$mostrarFormulario = $staffEditando !== null || isset($_GET['new']);
$errores = [
    'missing_name' => 'El nombre es obligatorio.',
    'missing_role' => 'El puesto es obligatorio.',
    'missing_description' => 'La descripcion es obligatoria.',
    'missing_table' => 'Falta crear la tabla de staff en la base de datos.',
    'upload_failed' => 'No se pudo subir la imagen.',
    'not_found' => 'El integrante solicitado no existe.',
];
$error = isset($_GET['err']) ? ($errores[$_GET['err']] ?? 'No se pudo completar la operacion.') : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Staff | Inmobiliaria</title>
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
                    <p class="app-brand-subtitle">ABM de la seccion Staff</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="../backend/logout.php" class="btn-secondary bg-white/10 text-white hover:bg-white/15 hover:text-white">Cerrar sesion</a>
            </div>
        </div>
    </header>

    <main class="admin-layout">
        <?= renderAdminSidebar('staff') ?>

        <div class="admin-content">
            <section class="hero-panel">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <span class="eyebrow">Administracion</span>
                        <h1 class="section-heading mb-2">Staff</h1>
                        <p class="section-copy">Gestiona las personas que aparecen en la home, con foto, puesto, orden y enlaces a redes.</p>
                    </div>
                    <a href="staff.php?new=1" class="btn-secondary">Nuevo integrante</a>
                </div>
            </section>

            <?php if (isset($_GET['ok'])): ?>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Integrante guardado correctamente.
                </div>
            <?php elseif (isset($_GET['del'])): ?>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Integrante eliminado correctamente.
                </div>
            <?php elseif ($error): ?>
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!$tablaStaffDisponible): ?>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    La tabla de staff todavia no existe. Ejecuta el script <code>sql/2026_06_04_staff.sql</code> para habilitar el ABM.
                </div>
            <?php endif; ?>

            <section class="admin-split">
                <?php if ($mostrarFormulario): ?>
                    <form action="../backend/<?= $staffEditando ? 'editar_staff.php' : 'crear_staff.php' ?>" method="POST" enctype="multipart/form-data" class="admin-form admin-split-form max-w-2xl space-y-4 text-sm">
                        <?php if ($staffEditando): ?>
                            <input type="hidden" name="id" value="<?= (int)$staffEditando['id'] ?>">
                        <?php endif; ?>

                        <div>
                            <span class="eyebrow"><?= $staffEditando ? 'Edicion' : 'Alta' ?></span>
                            <h2 class="section-heading mb-2 text-2xl"><?= $staffEditando ? 'Editar integrante' : 'Nuevo integrante' ?></h2>
                            <p class="section-copy">La imagen es opcional. Si no cargas una, podras mantener los assets actuales o dejar el registro sin foto.</p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="field-label">Nombre</label>
                                <input type="text" name="nombre" required value="<?= htmlspecialchars($staffEditando['nombre'] ?? '') ?>" class="field-input" placeholder="Ej. Martina Ruiz">
                            </div>
                            <div>
                                <label class="field-label">Puesto</label>
                                <input type="text" name="puesto" required value="<?= htmlspecialchars($staffEditando['puesto'] ?? '') ?>" class="field-input" placeholder="Ej. Asesora comercial">
                            </div>
                        </div>

                        <div>
                            <label class="field-label">Descripcion</label>
                            <textarea name="descripcion" rows="4" required class="field-input" placeholder="Resumen breve del perfil y su rol en la inmobiliaria."><?= htmlspecialchars($staffEditando['descripcion'] ?? '') ?></textarea>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="field-label">Facebook</label>
                                <input type="url" name="facebook_url" value="<?= htmlspecialchars($staffEditando['facebook_url'] ?? '') ?>" class="field-input" placeholder="https://facebook.com/...">
                            </div>
                            <div>
                                <label class="field-label">Twitter / X</label>
                                <input type="url" name="twitter_url" value="<?= htmlspecialchars($staffEditando['twitter_url'] ?? '') ?>" class="field-input" placeholder="https://x.com/...">
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="field-label">Instagram</label>
                                <input type="url" name="instagram_url" value="<?= htmlspecialchars($staffEditando['instagram_url'] ?? '') ?>" class="field-input" placeholder="https://instagram.com/...">
                            </div>
                            <div>
                                <label class="field-label">Orden</label>
                                <input type="number" name="orden" min="1" value="<?= htmlspecialchars((string)($staffEditando['orden'] ?? (count($staffItems) + 1))) ?>" class="field-input">
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="field-label">Foto</label>
                                <input type="file" name="imagen" accept="image/*" class="block w-full text-sm text-slate-500">
                                <p class="mt-2 text-xs text-slate-500">Se guarda en <code>public/img</code> con prefijo <code>staff_</code>.</p>
                            </div>
                            <label class="surface-card-soft flex items-center gap-3 p-4">
                                <input type="checkbox" name="activo" value="1" class="h-5 w-5 shrink-0" <?= !isset($staffEditando['activo']) || !empty($staffEditando['activo']) ? 'checked' : '' ?>>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Mostrar en la home</p>
                                    <p class="text-xs text-slate-500">Si lo desactivas, queda guardado pero no se publica.</p>
                                </div>
                            </label>
                        </div>

                        <?php $imagenPreview = staffImagenUrl($staffEditando['imagen'] ?? null); ?>
                        <div class="surface-card-soft flex items-center gap-4 p-4">
                            <?php if ($imagenPreview): ?>
                                <img src="<?= htmlspecialchars($imagenPreview) ?>" alt="" class="h-16 w-16 rounded-2xl border border-slate-200 object-cover">
                            <?php else: ?>
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-slate-200 bg-white text-xs text-slate-400">Sin foto</div>
                            <?php endif; ?>
                            <div class="text-xs text-slate-500">
                                <?php if (!empty($staffEditando['imagen'])): ?>
                                    <span class="block">Imagen actual: <strong class="text-slate-700"><?= htmlspecialchars($staffEditando['imagen']) ?></strong>.</span>
                                <?php else: ?>
                                    <span class="block">Todavia no hay una imagen cargada para este registro.</span>
                                <?php endif; ?>
                                <span class="mt-1 block">Al subir una nueva imagen, reemplaza la actual del registro.</span>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <a href="staff.php" class="btn-secondary">Cancelar</a>
                            <button type="submit" class="btn-accent" <?= $tablaStaffDisponible ? '' : 'disabled' ?>><?= $staffEditando ? 'Guardar cambios' : 'Crear integrante' ?></button>
                        </div>
                    </form>
                <?php endif; ?>

                <section class="surface-card admin-split-table p-5 md:p-6">
                    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Integrantes cargados</h2>
                            <p class="text-sm text-slate-500">El orden define la posicion en la grilla del home.</p>
                        </div>
                        <p class="shrink-0 text-sm text-slate-500"><?= count($staffItems) ?> integrante(s)</p>
                    </div>

                    <div class="table-shell w-full overflow-hidden">
                        <table>
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Nombre</th>
                                    <th>Puesto</th>
                                    <th>Ord.</th>
                                    <th>Estado</th>
                                    <th>Redes</th>
                                    <th class="text-right">Acc.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$staffItems): ?>
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-400">Todavia no hay integrantes cargados.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($staffItems as $item): ?>
                                        <?php $fotoUrl = staffImagenUrl($item['imagen'] ?? null); ?>
                                        <tr>
                                            <td>
                                                <?php if ($fotoUrl): ?>
                                                    <img src="<?= htmlspecialchars($fotoUrl) ?>" alt="" class="h-12 w-12 rounded-2xl border border-slate-200 object-cover">
                                                <?php else: ?>
                                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-xs text-slate-400">Sin foto</div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="font-semibold text-slate-800"><?= htmlspecialchars($item['nombre']) ?></td>
                                            <td class="text-sm text-slate-500"><?= htmlspecialchars($item['puesto']) ?></td>
                                            <td><?= (int)$item['orden'] ?></td>
                                            <td>
                                                <span class="pill <?= !empty($item['activo']) ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-100 text-slate-700' ?>">
                                                    <?= !empty($item['activo']) ? 'Activo' : 'Inactivo' ?>
                                                </span>
                                            </td>
                                            <td class="text-sm text-slate-500">
                                                <?php
                                                $redes = 0;
                                                foreach (['facebook_url', 'twitter_url', 'instagram_url'] as $campo) {
                                                    if (!empty($item[$campo])) {
                                                        $redes++;
                                                    }
                                                }
                                                ?>
                                                <?= $redes ?> enlace(s)
                                            </td>
                                            <td class="text-right">
                                                <?php if ($tablaStaffDisponible && !empty($item['id'])): ?>
                                                    <div class="inline-flex items-center gap-2">
                                                        <a href="staff.php?id=<?= (int)$item['id'] ?>" class="btn-primary px-3 py-2 text-xs">Editar</a>
                                                        <form action="../backend/eliminar_staff.php" method="POST" onsubmit="return confirm('¿Eliminar este integrante?');">
                                                            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
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
            </section>
        </div>
    </main>
</body>
</html>
