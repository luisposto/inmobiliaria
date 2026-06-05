<?php
require_once __DIR__ . '/../backend/funciones.php';
requerirPermisoAdmin('configuraciones');

$tablaSettingsDisponible = existeTablaSiteSettings();
$settings = obtenerAjustesSitio();
$videoHome = obtenerVideoHomeConfig();
$errores = [
    'missing_table' => 'Falta crear la tabla de settings en la base de datos.',
    'invalid_video' => 'El video debe estar en formato MP4, WEBM u OGG.',
];
$error = isset($_GET['err']) ? ($errores[$_GET['err']] ?? 'No se pudo guardar la configuracion.') : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuraciones | Inmobiliaria</title>
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
                    <p class="app-brand-subtitle">Configuracion del home</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="../backend/logout.php" class="btn-secondary bg-white/10 text-white hover:bg-white/15 hover:text-white">Cerrar sesion</a>
            </div>
        </div>
    </header>

    <main class="admin-layout">
        <?= renderAdminSidebar('configuraciones') ?>

        <div class="admin-content">
            <section class="hero-panel">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <span class="eyebrow">Administracion</span>
                        <h1 class="section-heading mb-2">Configuraciones</h1>
                        <p class="section-copy">Administra el video del home, el telefono, los datos de contacto y las redes sociales del index.</p>
                    </div>
                </div>
            </section>

            <?php if (isset($_GET['ok'])): ?>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Configuracion guardada correctamente.
                </div>
            <?php elseif ($error): ?>
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!$tablaSettingsDisponible): ?>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    La tabla de settings todavia no existe. Ejecuta el script <code>sql/2026_06_04_site_settings.sql</code> para habilitar esta pantalla.
                </div>
            <?php endif; ?>

            <form action="../backend/guardar_settings.php" method="POST" enctype="multipart/form-data" class="admin-form max-w-4xl space-y-6 text-sm">
                <div>
                    <span class="eyebrow">Home</span>
                    <h2 class="section-heading mb-2 text-2xl">Video principal</h2>
                    <p class="section-copy">Sube el video que se reproduce en la cabecera del home. Si no hay un archivo local valido, se mantiene el fallback remoto.</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="field-label">Nuevo video</label>
                        <input type="file" name="home_video" accept="video/mp4,video/webm,video/ogg" class="block w-full text-sm text-slate-500">
                        <p class="mt-2 text-xs text-slate-500">Formatos permitidos: MP4, WEBM y OGG. Se guarda en <code>public/video</code>.</p>
                    </div>
                    <div class="surface-card-soft space-y-2 p-4">
                        <p class="text-sm font-semibold text-slate-800">Archivo actual</p>
                        <p class="font-mono text-xs text-slate-600"><?= htmlspecialchars($videoHome['file'] ?: 'Sin archivo configurado') ?></p>
                        <p class="text-xs text-slate-500">
                            <?= !empty($videoHome['local_url']) ? 'El home esta usando un video local guardado.' : 'No se detecto un video local; se usara el fallback configurado en el index.' ?>
                        </p>
                    </div>
                </div>

                <div>
                    <span class="eyebrow">Contacto</span>
                    <h2 class="section-heading mb-2 text-2xl">Datos visibles en el footer</h2>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="field-label">Telefono</label>
                        <input type="text" name="home_phone" value="<?= htmlspecialchars($settings['home_phone'] ?? '') ?>" class="field-input" placeholder="+54 341 ...">
                    </div>
                    <div>
                        <label class="field-label">Email</label>
                        <input type="email" name="home_email" value="<?= htmlspecialchars($settings['home_email'] ?? '') ?>" class="field-input" placeholder="contacto@...">
                    </div>
                </div>

                <div>
                    <label class="field-label">Direccion</label>
                    <input type="text" name="home_address" value="<?= htmlspecialchars($settings['home_address'] ?? '') ?>" class="field-input" placeholder="Direccion comercial">
                </div>

                <div>
                    <span class="eyebrow">Redes</span>
                    <h2 class="section-heading mb-2 text-2xl">Enlaces del index</h2>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="field-label">Instagram</label>
                        <input type="url" name="home_instagram_url" value="<?= htmlspecialchars($settings['home_instagram_url'] ?? '') ?>" class="field-input" placeholder="https://instagram.com/...">
                    </div>
                    <div>
                        <label class="field-label">Facebook</label>
                        <input type="url" name="home_facebook_url" value="<?= htmlspecialchars($settings['home_facebook_url'] ?? '') ?>" class="field-input" placeholder="https://facebook.com/...">
                    </div>
                    <div>
                        <label class="field-label">WhatsApp</label>
                        <input type="url" name="home_whatsapp_url" value="<?= htmlspecialchars($settings['home_whatsapp_url'] ?? '') ?>" class="field-input" placeholder="https://wa.me/...">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="dashboard.php" class="btn-secondary">Volver</a>
                    <button type="submit" class="btn-accent" <?= $tablaSettingsDisponible ? '' : 'disabled' ?>>Guardar</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
