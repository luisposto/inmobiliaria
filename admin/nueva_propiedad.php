<?php
require_once __DIR__ . '/../backend/funciones.php';
requerirLogin();

$operaciones = obtenerOperaciones();
$tipos = obtenerTiposPropiedad();
$estados = obtenerEstadosPropiedad();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva propiedad | Inmobiliaria</title>
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
                    <p class="text-xs text-slate-300">Nueva propiedad</p>
                </div>
            </div>
            <a href="dashboard.php" class="text-xs underline hover:text-emerald-300">Volver al listado</a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-6">
        <form action="../backend/crear_propiedad.php" method="POST" enctype="multipart/form-data"
              class="bg-white rounded-2xl shadow border border-slate-100 p-5 space-y-4 text-sm">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-700 mb-1">Título</label>
                    <input type="text" name="titulo" required
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Precio (ARS)</label>
                    <input type="number" name="precio" step="1000"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
            </div>

            <div>
                <label class="block text-slate-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="4"
                          class="w-full rounded-lg border border-slate-200 px-3 py-2"></textarea>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-700 mb-1">Dirección</label>
                    <input type="text" name="direccion"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Ciudad</label>
                    <input type="text" name="ciudad"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Provincia</label>
                    <input type="text" name="provincia"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">País</label>
                    <input type="text" name="pais" value="Argentina"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-700 mb-1">Tipo</label>
                    <select name="tipo_id" class="w-full rounded-lg border border-slate-200 px-3 py-2">
                        <option value="">Seleccionar</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Operación</label>
                    <select name="operacion_id" class="w-full rounded-lg border border-slate-200 px-3 py-2">
                        <option value="">Seleccionar</option>
                        <?php foreach ($operaciones as $op): ?>
                            <option value="<?= $op['id'] ?>"><?= htmlspecialchars($op['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Estado</label>
                    <select name="estado_id" class="w-full rounded-lg border border-slate-200 px-3 py-2">
                        <?php foreach ($estados as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= $e['nombre']==='Disponible' ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-700 mb-1">Baños</label>
                    <input type="number" name="banios" min="0"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div class="flex items-center gap-2 mt-6">
                    <input type="checkbox" id="cochera" name="cochera" value="1"
                           class="rounded border-slate-300">
                    <label for="cochera" class="text-sm text-slate-700">Cochera</label>
                </div>
                <div>
                    <label class="block text-slate-700 mb-1">Superficie (m²)</label>
                    <input type="number" name="superficie" min="0"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-700 mb-1">Imagen principal</label>
                    <input type="file" name="imagen" accept="image/*"
                           class="block w-full text-sm text-slate-500">
                </div>
                <div class="flex items-center gap-2 mt-6">
                    <input type="checkbox" id="destacado" name="destacado" value="1"
                           class="rounded border-slate-300">
                    <label for="destacado" class="text-sm text-slate-700">Marcar como destacada</label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="dashboard.php"
                   class="px-4 py-2 rounded-lg border border-slate-200 text-sm">Cancelar</a>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                    Guardar propiedad
                </button>
            </div>
        </form>
    </main>
</body>
</html>
