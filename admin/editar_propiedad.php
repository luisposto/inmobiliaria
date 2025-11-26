<?php
require_once __DIR__ . '/../backend/funciones.php';
requerirLogin();

$operaciones = obtenerOperaciones();
$tipos = obtenerTiposPropiedad();
$estados = obtenerEstadosPropiedad();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$prop = $id ? obtenerPropiedadPorId($id) : null;
if (!$prop) {
    header("Location: dashboard.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar propiedad | Inmobiliaria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../public/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-slate-100 text-slate-900">
    <header class="bg-slate-900 text-white">
        <div class="max-w-4xl mx-auto flex items-center justify-between p-4">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold">IA</span>
                <div>
                    <p class="text-sm font-semibold leading-tight">Inmobiliaria Argentina</p>
                    <p class="text-xs text-slate-300">Editar propiedad #<?= $prop['id'] ?></p>
                </div>
            </div>
            <a href="dashboard.php" class="text-xs underline hover:text-emerald-300">Volver al listado</a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-6">
        <form action="../backend/editar_propiedad.php" method="POST" enctype="multipart/form-data"
              class="bg-white rounded-2xl shadow border border-slate-100 p-5 space-y-4 text-sm">
            <input type="hidden" name="id" value="<?= $prop['id'] ?>">

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-700 mb-1">Título</label>
                    <input type="text" name="titulo" required
                           value="<?= htmlspecialchars($prop['titulo']) ?>"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Precio (ARS)</label>
                    <input type="number" name="precio" step="1000"
                           value="<?= htmlspecialchars($prop['precio']) ?>"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>

                <div>
    <label class="block text-slate-700 mb-1">Precio (USD)</label>
    <input type="number" name="precio_usd" min="0" step="0.01"
           value="<?= htmlspecialchars($prop['precio_usd']) ?>"
           class="w-full rounded-lg border border-slate-200 px-3 py-2">
</div>

            </div>

            <div>
                <label class="block text-slate-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="4"
                          class="w-full rounded-lg border border-slate-200 px-3 py-2"><?= htmlspecialchars($prop['descripcion']) ?></textarea>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-700 mb-1">Dirección</label>
                    <input type="text" name="direccion"
                           value="<?= htmlspecialchars($prop['direccion']) ?>"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Ciudad</label>
                    <input type="text" name="ciudad"
                           value="<?= htmlspecialchars($prop['ciudad']) ?>"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Provincia</label>
                    <input type="text" name="provincia"
                           value="<?= htmlspecialchars($prop['provincia']) ?>"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">País</label>
                    <input type="text" name="pais"
                           value="<?= htmlspecialchars($prop['pais']) ?>"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Latitud</label>
                    <input type="text" name="lat" value="<?= number_format((float)$prop['lat'], 7, '.', '') ?>"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Longitud</label>
                    <input type="text" name="lng" value="<?= number_format((float)$prop['lng'], 7, '.', '') ?>"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
    
            </div>

            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-700 mb-1">Tipo</label>
                    <select name="tipo_id" class="w-full rounded-lg border border-slate-200 px-3 py-2">
                        <option value="">Seleccionar</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= ($prop['tipo_id'] == $t['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Operación</label>
                    <select name="operacion_id" class="w-full rounded-lg border border-slate-200 px-3 py-2">
                        <option value="">Seleccionar</option>
                        <?php foreach ($operaciones as $op): ?>
                            <option value="<?= $op['id'] ?>" <?= ($prop['operacion_id'] == $op['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($op['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Estado</label>
                    <select name="estado_id" class="w-full rounded-lg border border-slate-200 px-3 py-2">
                        <?php foreach ($estados as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= ($prop['estado_id'] == $e['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-slate-700 mb-1">Ambientes</label>
                    <input type="number" name="ambientes" min="0"
                           value="<?= htmlspecialchars($prop['ambientes']) ?>"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Baños</label>
                    <input type="number" name="banios" min="0"
                           value="<?= htmlspecialchars($prop['banios']) ?>"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Superficie (m²)</label>
                    <input type="number" name="superficie" min="0"
                           value="<?= htmlspecialchars($prop['superficie']) ?>"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div class="flex items-center gap-2 mt-6">
                    <input type="checkbox" id="cochera" name="cochera" value="1"
                           <?= $prop['cochera'] ? 'checked' : '' ?>
                           class="rounded border-slate-300">
                    <label for="cochera" class="text-sm text-slate-700">Cochera</label>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="block text-slate-700 mb-1">Imagen actual</p>
                    <?php if (!empty($prop['imagen'])): ?>
                        <img src="../public/img/<?= htmlspecialchars($prop['imagen']) ?>" alt=""
                             class="h-32 w-full object-cover rounded-lg border border-slate-200">
                    <?php else: ?>
                        <p class="text-xs text-slate-400">Sin imagen cargada.</p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Reemplazar imagen</label>
                    <input type="file" name="imagen" accept="image/*"
                           class="block w-full text-sm text-slate-500">
                    <div class="flex items-center gap-2 mt-4">
                        <input type="checkbox" id="destacado" name="destacado" value="1"
                               <?= $prop['destacado'] ? 'checked' : '' ?>
                               class="rounded border-slate-300">
                        <label for="destacado" class="text-sm text-slate-700">Marcar como destacada</label>
                    </div>

                <div class="md:col-span-2 mt-4">
                    <div class="border border-slate-200 rounded-xl p-3 bg-slate-50 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                        <div>
                            <p class="text-sm font-medium text-slate-700">Imágenes adicionales</p>
                            <p class="text-xs text-slate-500">
                                Podés cargar hasta varias fotos extra para el carrusel de esta propiedad.
                            </p>
                        </div>
                        <div>
                            <a href="imagenes_propiedad.php?id=<?= $prop['id'] ?>"
                               class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700">
                                Gestionar imágenes
                            </a>
                        </div>
                    </div>
                </div>

                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="dashboard.php"
                   class="px-4 py-2 rounded-lg border border-slate-200 text-sm">Cancelar</a>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                    Guardar cambios
                </button>
            </div>
        </form>
    </main>
</body>
</html>
