<?php
require_once __DIR__ . '/../backend/funciones.php';
requerirLogin();

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
    <title>Dashboard | Inmobiliaria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../public/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-slate-100 text-slate-900">
    <header class="bg-slate-900 text-white">
        <div class="max-w-6xl mx-auto flex items-center justify-between p-4">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold">IA</span>
                <div>
                    <p class="text-sm font-semibold leading-tight">Inmobiliaria Argentina</p>
                    <p class="text-xs text-slate-300">Panel de administración</p>
                </div>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span>Hola, <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin') ?></span>
                <a href="../backend/logout.php" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700">Cerrar sesión</a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-6 space-y-6">
        <section class="grid gap-4 md:grid-cols-4">
            <div class="rounded-xl bg-white shadow border border-slate-100 p-4">
                <p class="text-xs text-slate-500 mb-1">Propiedades totales</p>
                <p class="text-2xl font-bold"><?= $total ?></p>
            </div>
            <div class="rounded-xl bg-white shadow border border-slate-100 p-4">
                <p class="text-xs text-slate-500 mb-1">En venta</p>
                <p class="text-2xl font-bold text-emerald-600"><?= $venta ?></p>
            </div>
            <div class="rounded-xl bg-white shadow border border-slate-100 p-4">
                <p class="text-xs text-slate-500 mb-1">En alquiler</p>
                <p class="text-2xl font-bold text-sky-600"><?= $alquiler ?></p>
            </div>
            <div class="rounded-xl bg-white shadow border border-slate-100 p-4">
                <p class="text-xs text-slate-500 mb-1">Destacadas</p>
                <p class="text-2xl font-bold text-amber-500"><?= $destacadas ?></p>
            </div>
        </section>

        <section class="flex items-center justify-between gap-3">
            <h1 class="text-xl md:text-2xl font-bold">Propiedades</h1>
            <div class="flex items-center gap-2">
                <a href="../backend/export_propiedades_csv.php?<?= htmlspecialchars($queryString) ?>"
                   class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-2 text-xs md:text-sm text-slate-700 hover:bg-slate-50">
                    Exportar CSV
                </a>
                <a href="nueva_propiedad.php"
                   class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-2 text-xs md:text-sm font-semibold text-white hover:bg-emerald-700">
                    + Nueva propiedad
                </a>
            </div>
        </section>

        <section class="bg-white rounded-xl shadow border border-slate-100 p-4 space-y-4">
            <form method="GET" class="grid gap-3 md:grid-cols-5 text-xs md:text-sm">
                <input type="text" name="ciudad" value="<?= htmlspecialchars($filtros['ciudad']) ?>" placeholder="Ciudad"
                       class="rounded-lg border border-slate-200 px-2 py-1.5">
                <select name="operacion_id" class="rounded-lg border border-slate-200 px-2 py-1.5">
                    <option value="">Operación</option>
                    <?php foreach ($operaciones as $op): ?>
                        <option value="<?= $op['id'] ?>" <?= ($filtros['operacion_id'] == $op['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($op['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="tipo_id" class="rounded-lg border border-slate-200 px-2 py-1.5">
                    <option value="">Tipo</option>
                    <?php foreach ($tipos as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($filtros['tipo_id'] == $t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="estado_id" class="rounded-lg border border-slate-200 px-2 py-1.5">
                    <option value="">Estado</option>
                    <?php foreach ($estados as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= ($filtros['estado_id'] == $e['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="precio_min" value="<?= htmlspecialchars($filtros['precio_min']) ?>" placeholder="Precio mín."
                       class="rounded-lg border border-slate-200 px-2 py-1.5">
                <input type="number" name="precio_max" value="<?= htmlspecialchars($filtros['precio_max']) ?>" placeholder="Precio máx."
                       class="rounded-lg border border-slate-200 px-2 py-1.5">
                <div class="md:col-span-5 flex justify-end gap-2">
                    <a href="dashboard.php" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs">Limpiar</a>
                    <button class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs">Filtrar</button>
                </div>
            </form>

            <?php if (isset($_GET['ok'])): ?>
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-2 text-xs">
                    Propiedad creada correctamente.
                </div>
            <?php elseif (isset($_GET['upd'])): ?>
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-2 text-xs">
                    Propiedad actualizada correctamente.
                </div>
            <?php elseif (isset($_GET['del'])): ?>
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-2 text-xs">
                    Propiedad eliminada correctamente.
                </div>
            <?php endif; ?>

            <div class="overflow-x-auto">
                <table class="w-full text-xs md:text-sm border-separate border-spacing-y-1">
                    <thead class="text-left text-slate-500">
                        <tr>
                            <th class="px-2 py-1">#</th>
                            <th class="px-2 py-1">Título</th>
                            <th class="px-2 py-1">Ciudad</th>
                            <th class="px-2 py-1">Operación</th>
                            <th class="px-2 py-1">Estado</th>
                            <th class="px-2 py-1">Precio</th>
                            <th class="px-2 py-1">Dest.</th>
                            <th class="px-2 py-1 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$propiedades): ?>
                            <tr>
                                <td colspan="7" class="px-2 py-3 text-center text-slate-400">
                                    No hay propiedades cargadas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($propiedades as $p): ?>
                                <tr class="bg-white hover:bg-slate-50 shadow-sm">
                                    <td class="px-2 py-2 align-middle text-slate-400 text-xs">
                                        #<?= $p['id'] ?>
                                    </td>
                                    <td class="px-2 py-2 align-middle">
                                        <div class="font-medium text-slate-800"><?= htmlspecialchars($p['titulo']) ?></div>
                                        <div class="text-[11px] text-slate-500">
                                            <?= htmlspecialchars($p['direccion'] ?? '') ?>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2 align-middle text-xs">
                                        <?= htmlspecialchars($p['ciudad'] ?? '') ?>
                                    </td>
                                    <td class="px-2 py-2 align-middle text-xs">
                                        <?php if (!empty($p['operacion_nombre'])): ?>
                                            <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium
                                                <?= $p['operacion_nombre'] === 'Venta' ? 'bg-emerald-50 text-emerald-700' : 'bg-sky-50 text-sky-700' ?>">
                                                <?= htmlspecialchars($p['operacion_nombre']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-2 py-2 align-middle text-xs">
                                        <?php if (!empty($p['estado_nombre'])): ?>
                                            <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] text-[11px] font-medium
                                                <?= $p['estado_nombre'] === 'Disponible' ? 'bg-emerald-50 text-emerald-700' : ($p['estado_nombre'] === 'Reservada' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700') ?>">
                                                <?= htmlspecialchars($p['estado_nombre']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-2 py-2 align-middle text-xs font-semibold">
                                        $<?= number_format($p['precio'], 0, ',', '.') ?>
                                        <?php if (!empty($p['precio_usd'])): ?>
                                            <div class="text-[11px] text-slate-500">
                                                U$S <?= number_format($p['precio_usd'], 0, ',', '.') ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-2 py-2 align-middle text-center text-xs">
                                        <?php if ($p['destacado']): ?>
                                            ⭐
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-2 py-2 align-middle text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="editar_propiedad.php?id=<?= $p['id'] ?>"
                                               class="px-2 py-1 rounded bg-slate-900 text-white text-[11px] hover:bg-slate-700">
                                                Editar
                                            </a>
                                            <form action="../backend/eliminar_propiedad.php" method="POST"
                                                  onsubmit="return confirm('¿Eliminar esta propiedad?');">
                                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                <button class="px-2 py-1 rounded bg-red-50 text-red-700 text-[11px] hover:bg-red-100">
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
    </main>
</body>
</html>
