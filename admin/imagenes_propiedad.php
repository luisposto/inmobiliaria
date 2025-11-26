<?php
require_once __DIR__ . '/../backend/funciones.php';
requerirLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$prop = $id ? obtenerPropiedadPorId($id) : null;

if (!$prop) {
    header("Location: dashboard.php");
    exit;
}

$imagenes = obtenerImagenesPropiedad($id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imágenes de la propiedad #<?= $prop['id'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800">
<header class="bg-slate-900 text-white shadow">
    <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="space-y-1">
            <p class="text-[11px] uppercase tracking-[0.2em] text-emerald-300/80">Gestión de imágenes</p>
            <h1 class="text-lg md:text-xl font-semibold flex items-center gap-2">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300 text-xs">
                    IMG
                </span>
                Propiedad #<?= $prop['id'] ?>
            </h1>
            <p class="text-xs text-slate-300 line-clamp-1">
                <?= htmlspecialchars($prop['titulo']) ?> ·
                <?= htmlspecialchars(trim(($prop['direccion'] ?? '') . ' ' . ($prop['ciudad'] ?? ''))) ?>
            </p>
        </div>
        <div class="flex flex-wrap gap-2 justify-end">
            <a href="editar_propiedad.php?id=<?= $prop['id'] ?>"
               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-800 text-xs hover:bg-slate-700 transition">
                ← Volver a la propiedad
            </a>
            <a href="dashboard.php"
               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-500/70 text-xs hover:bg-slate-800 hover:text-white transition">
                Panel principal
            </a>
        </div>
    </div>
    <div class="border-t border-slate-800/80">
        <div class="max-w-6xl mx-auto px-4 py-2 flex items-center gap-2 text-[11px] text-slate-300">
            <span class="opacity-70">Home</span>
            <span class="opacity-50">/</span>
            <a href="dashboard.php" class="hover:text-emerald-300 transition">Dashboard</a>
            <span class="opacity-50">/</span>
            <a href="editar_propiedad.php?id=<?= $prop['id'] ?>" class="hover:text-emerald-300 transition">Propiedad #<?= $prop['id'] ?></a>
            <span class="opacity-50">/</span>
            <span class="text-emerald-300 font-medium">Imágenes</span>
        </div>
    </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-6 space-y-6">
    <!-- Bloque para subir nueva imagen -->
    <section class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 md:p-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <div>
                <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-lg bg-emerald-50 text-emerald-600 text-xs">+</span>
                    Agregar nueva imagen
                </h2>
                <p class="text-xs text-slate-500 mt-1 max-w-md">
                    Cargá fotos adicionales para que aparezcan en el carrusel de la propiedad.
                </p>
            </div>
            <div class="inline-flex items-center gap-2 text-[11px] bg-slate-50 border border-slate-200 rounded-full px-3 py-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="font-medium">Carrusel activo</span>
            </div>
        </div>

        <form action="../backend/agregar_imagen_propiedad.php" method="POST" enctype="multipart/form-data"
              class="grid gap-4 md:grid-cols-4 items-end text-sm">
            <input type="hidden" name="propiedad_id" value="<?= $prop['id'] ?>">

            <div class="md:col-span-2">
                <label class="block text-slate-700 mb-1 text-xs font-medium">Archivo de imagen</label>
                <input type="file" name="imagen" accept="image/*"
                       class="block w-full text-xs text-slate-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                <p class="text-[11px] text-slate-400 mt-1">
                    Formatos recomendados: JPG / PNG. Peso máx. sugerido: 1 MB.
                </p>
            </div>

            <div>
                <label class="block text-slate-700 mb-1 text-xs font-medium">Orden en el carrusel</label>
                <input type="number" name="orden" min="0" value="0"
                       class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/70 focus:border-emerald-500">
                <p class="text-[11px] text-slate-400 mt-1">
                    0 se mostrará después de la imagen principal. Podés repetir números.
                </p>
            </div>

            <div class="flex justify-end md:justify-start">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 focus:ring-offset-emerald-50 transition">
                    <span>Subir imagen</span>
                </button>
            </div>
        </form>
    </section>

    <!-- Listado de imágenes -->
    <section class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 md:p-5">
        <div class="flex items-center justify-between gap-2 mb-4">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Imágenes cargadas</h2>
                <p class="text-[11px] text-slate-500 mt-1">
                    Estas imágenes se mostrarán en el carrusel junto con la imagen principal.
                </p>
            </div>
            <div class="flex items-center gap-2 text-[11px] text-slate-500">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-50 border border-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    Total: <?= count($imagenes) ?> imagen(es)
                </span>
            </div>
        </div>

        <?php if (!$imagenes): ?>
            <div class="border border-dashed border-slate-200 rounded-xl p-6 text-center bg-slate-50/60">
                <p class="text-xs text-slate-500 mb-1">Todavía no cargaste imágenes adicionales para esta propiedad.</p>
                <p class="text-[11px] text-slate-400">
                    Usá el formulario superior para agregar la primera imagen del carrusel.
                </p>
            </div>
        <?php else: ?>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($imagenes as $img): ?>
                    <div class="group border border-slate-200 rounded-xl overflow-hidden bg-slate-50 flex flex-col shadow-sm hover:shadow-md transition">
                        <div class="relative aspect-video bg-slate-200 overflow-hidden">
                            <img src="../public/img/<?= htmlspecialchars($img['ruta']) ?>"
                                 alt=""
                                 class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-200">
                            <div class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-900/70 text-[10px] text-slate-100">
                                ID #<?= $img['id'] ?>
                            </div>
                            <div class="absolute bottom-2 right-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/90 text-[10px] text-white">
                                Orden <?= (int)$img['orden'] ?>
                            </div>
                        </div>
                        <div class="p-3 space-y-2 text-xs">
                            <p class="text-[11px] text-slate-500 break-all">
                                <?= htmlspecialchars($img['ruta']) ?>
                            </p>
                            <p class="text-[11px] text-slate-400">
                                Cargada el <?= htmlspecialchars($img['creado_en']) ?>
                            </p>
                            <form action="../backend/eliminar_imagen_propiedad.php" method="POST"
                                  onsubmit="return confirm('¿Eliminar esta imagen?');"
                                  class="pt-1">
                                <input type="hidden" name="id" value="<?= $img['id'] ?>">
                                <input type="hidden" name="propiedad_id" value="<?= $prop['id'] ?>">
                                <button class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-[11px] font-medium hover:bg-red-100 border border-red-100 transition">
                                    Eliminar imagen
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
