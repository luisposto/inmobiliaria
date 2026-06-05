<?php
require_once __DIR__ . '/../backend/funciones.php';
$destacadas = obtenerPropiedadesDestacadas();
$staffItems = obtenerStaffPublico();
$operaciones = obtenerOperaciones();
$tipos = obtenerTiposPropiedad();
$siteSettings = obtenerAjustesSitio();
$videoHome = obtenerVideoHomeConfig();
$socialLinks = [
    'instagram' => trim((string) ($siteSettings['home_instagram_url'] ?? '')),
    'facebook' => trim((string) ($siteSettings['home_facebook_url'] ?? '')),
    'whatsapp' => trim((string) ($siteSettings['home_whatsapp_url'] ?? '')),
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inmobiliaria Argentina | Inicio</title>
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
                <p class="app-brand-subtitle">Propiedades en CABA y GBA</p>
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

<main class="app-main space-y-8">
    <section class="hero-video-shell">
        <video class="hero-video-media" autoplay muted loop playsinline preload="metadata" poster="<?= htmlspecialchars(publicAssetUrl('img/hero-poster.jpg')) ?>">
            <?php if (!empty($videoHome['local_url'])): ?>
                <source src="<?= htmlspecialchars($videoHome['local_url']) ?>" type="<?= htmlspecialchars($videoHome['local_type']) ?>">
            <?php endif; ?>
            <source src="<?= htmlspecialchars($videoHome['fallback_url']) ?>" type="<?= htmlspecialchars($videoHome['fallback_type']) ?>">
        </video>
        <div class="hero-video-overlay"></div>

        <div class="hero-video-grid">
            <div class="hero-video-content">
                <span class="eyebrow">Busqueda inmobiliaria</span>
                <h1 class="section-heading mb-4">Encontra tu proxima propiedad en Rosario</h1>
                <p class="section-copy mb-6 max-w-2xl">
                    Casas, departamentos y PH en venta y alquiler. Filtra por zona, precio y tipo de propiedad.
                </p>
                <div class="flex flex-wrap gap-3 text-xs">
                    <a href="propiedades.php?operacion=Venta" class="btn-secondary hero-video-action">En venta</a>
                    <a href="propiedades.php?operacion=Alquiler" class="btn-secondary hero-video-action">En alquiler</a>
                </div>
            </div>

            <div class="surface-card hero-search-card bg-slate-950/90 p-5 text-white md:p-6">
                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-300">Buscador rapido</p>
                <form action="propiedades.php" method="GET" class="space-y-3 text-xs">
                    <div>
                        <label class="mb-1 block text-slate-200">Ciudad / barrio</label>
                        <input type="text" name="ciudad" placeholder="Ej: Palermo, San Isidro"
                            class="field-input border-slate-700 bg-slate-900/80 text-white placeholder:text-slate-500">
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-slate-200">Operacion</label>
                            <select name="operacion_id" class="field-input border-slate-700 bg-slate-900/80 text-white">
                                <option value="">Cualquiera</option>
                                <?php foreach ($operaciones as $op): ?>
                                    <option value="<?= $op['id'] ?>"><?= htmlspecialchars($op['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-slate-200">Tipo</label>
                            <select name="tipo_id" class="field-input border-slate-700 bg-slate-900/80 text-white">
                                <option value="">Cualquiera</option>
                                <?php foreach ($tipos as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <span class="mb-2 block text-slate-200">Moneda</span>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-white">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="moneda" value="ARS" checked style="accent-color: #f97316;">
                                <span>Pesos</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="moneda" value="USD" style="accent-color: #f97316;">
                                <span>USD</span>
                            </label>
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-slate-200">Precio min.</label>
                            <input type="number" name="precio_min" class="field-input border-slate-700 bg-slate-900/80 text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-slate-200">Precio max.</label>
                            <input type="number" name="precio_max" class="field-input border-slate-700 bg-slate-900/80 text-white">
                        </div>
                    </div>
                    <button class="btn-accent mt-1 w-full rounded-2xl">Buscar propiedades</button>
                </form>
            </div>
        </div>
    </section>

    <section class="space-y-4">
        <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-semibold text-slate-950 md:text-xl">Propiedades destacadas</h2>
            <a href="propiedades.php" class="btn-secondary px-3 py-2 text-xs">Ver todas</a>
        </div>

        <?php if (!$destacadas): ?>
            <p class="surface-card-soft px-5 py-4 text-sm text-slate-500">Aun no cargaste propiedades destacadas. Marca alguna desde el panel admin.</p>
        <?php else: ?>
            <div class="grid gap-4 md:grid-cols-3">
                <?php foreach ($destacadas as $p): ?>
                    <article class="listing-card flex flex-col">
                        <?php if (!empty($p['imagen'])): ?>
                            <img src="img/<?= htmlspecialchars($p['imagen']) ?>" alt="" class="listing-image">
                        <?php else: ?>
                            <div class="listing-placeholder">Sin imagen</div>
                        <?php endif; ?>
                        <div class="flex flex-1 flex-col gap-2 p-5">
                            <h3 class="text-sm font-semibold text-slate-900 md:text-base">
                                <?= htmlspecialchars($p['titulo']) ?>
                            </h3>
                            <p class="text-xs text-slate-500">
                                <?= htmlspecialchars($p['ciudad'] ?? '') ?><?= $p['provincia'] ? ', ' . htmlspecialchars($p['provincia']) : '' ?>
                            </p>
                            <p class="text-xs text-slate-600 line-clamp-2">
                                <?= nl2br(htmlspecialchars(substr($p['descripcion'] ?? '', 0, 120))) ?>...
                            </p>
                            <div class="mt-auto flex flex-col items-start gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                                <span class="text-sm font-bold text-emerald-700">
                                    <?php if (!empty($p['precio_usd'])): ?>
                                        U$S <?= number_format($p['precio_usd'], 0, ',', '.') ?>
                                    <?php else: ?>
                                        $<?= number_format($p['precio'], 0, ',', '.') ?>
                                    <?php endif; ?>
                                </span>
                                <a href="propiedad.php?id=<?= $p['id'] ?>" class="btn-secondary px-3 py-1.5 text-xs">
                                    Ver detalle
                                </a>
                            </div>
                            <p class="text-sm font-bold text-emerald-700">
                                <?php if (!empty($p['precio_usd'])): ?>
                                    <span class="block text-[11px] text-slate-500">
                                        $<?= number_format($p['precio'], 0, ',', '.') ?>
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-white text-slate-950 shadow-[0_30px_80px_rgba(15,23,42,0.08)]">
        <div class="absolute inset-x-0 top-0 h-40 bg-[radial-gradient(circle_at_top_left,_rgba(242,255,0,0.95),_rgba(242,255,0,0.62)_35%,_transparent_72%)]"></div>
        <div class="absolute right-0 top-24 h-52 w-52 rounded-full bg-emerald-400/10 blur-3xl"></div>
        <div class="absolute -left-16 bottom-0 h-48 w-48 rounded-full bg-cyan-300/10 blur-3xl"></div>

        <div class="relative px-6 py-10 md:px-12 md:py-12">
            <div class="max-w-3xl space-y-4">
                <h2 class="max-w-2xl text-3xl font-extrabold tracking-tight text-slate-950 md:text-5xl">
                    Nuestro equipo
                </h2>
                <div class="max-w-3xl space-y-4 text-sm leading-7 text-slate-600 md:text-base">
                    <p class="text-lg font-semibold text-slate-900 md:text-2xl">
                       Acompanamiento cercano en cada etapa
                    </p>
                    <p>
                        Nuestro equipo combina experiencia comercial, conocimiento del mercado local y atencion personalizada para ayudarte a encontrar, vender o alquilar una propiedad con seguridad y confianza.
                    </p>
                    <p>
                        Te acompanamos desde la primera consulta hasta el cierre de la operacion, resolviendo dudas y proponiendo opciones alineadas con lo que realmente necesitas.
                    </p>
                </div>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-3">
                <?php foreach ($staffItems as $item): ?>
                    <?php
                    $fotoUrl = staffImagenUrl($item['imagen'] ?? null);
                    $redes = [
                        'facebook' => $item['facebook_url'] ?? null,
                        'twitter' => $item['twitter_url'] ?? null,
                        'instagram' => $item['instagram_url'] ?? null,
                    ];
                    ?>
                    <article class="group overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-[0_18px_45px_rgba(2,6,23,0.08)] transition duration-300 hover:-translate-y-1 hover:border-lime-300 hover:shadow-[0_22px_55px_rgba(2,6,23,0.12)]">
                        <div class="relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 to-transparent"></div>
                            <?php if ($fotoUrl): ?>
                                <img
                                    src="<?= htmlspecialchars($fotoUrl) ?>"
                                    alt="<?= htmlspecialchars(($item['nombre'] ?? 'Staff') . ', ' . ($item['puesto'] ?? 'integrante del staff')) ?>"
                                    class="h-80 w-full object-cover grayscale transition duration-500 group-hover:scale-[1.03] group-hover:grayscale-0"
                                >
                            <?php else: ?>
                                <div class="flex h-80 w-full items-center justify-center bg-slate-100 text-sm font-semibold text-slate-400">Sin foto</div>
                            <?php endif; ?>
                        </div>
                        <div class="space-y-4 p-6">
                            <div class="space-y-2">
                                <p class="text-xs font-bold uppercase tracking-[0.24em] text-lime-700"><?= htmlspecialchars($item['puesto']) ?></p>
                                <h3 class="text-2xl font-extrabold tracking-tight text-slate-950"><?= htmlspecialchars($item['nombre']) ?></h3>
                            </div>
                            <p class="text-sm leading-7 text-slate-600">
                                <?= htmlspecialchars($item['descripcion']) ?>
                            </p>
                            <?php if (array_filter($redes)): ?>
                                <div class="flex items-center gap-3 text-slate-900">
                                    <?php foreach ($redes as $red => $url): ?>
                                        <?php if (!$url): continue; endif; ?>
                                        <a href="<?= htmlspecialchars($url) ?>" aria-label="<?= htmlspecialchars(ucfirst($red)) ?>" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-slate-50 transition hover:border-lime-300 hover:text-lime-700" target="_blank" rel="noreferrer noopener">
                                            <?= renderizarIconoRedSocialStaff($red) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<footer class="app-footer border-t-0 bg-gradient-to-br from-cyan-50 via-white to-sky-100 text-slate-700">
    <div class="app-footer-inner block space-y-6">
        <div class="grid gap-6 md:grid-cols-3">
            <section class="social-footer-card space-y-3 rounded-2xl border border-white/80 bg-white/80 p-5 shadow-sm backdrop-filter">
                <h2 class="text-sm font-semibold text-cyan-800">Acerca de nosotros</h2>
                <p class="max-w-md text-sm leading-6 text-slate-600">
                    Somos una inmobiliaria enfocada en propiedades en Rosario, con asesoramiento cercano para compra, venta y alquiler.
                </p>
            </section>

            <section class="space-y-3 rounded-2xl border border-white/80 bg-white/80 p-5 shadow-sm backdrop-filter">
                <h2 class="text-sm font-semibold text-teal-800">Contactos</h2>
                <div class="space-y-2 text-sm text-slate-600">
                    <p>Telefono: <?= htmlspecialchars($siteSettings['home_phone'] ?? '') ?></p>
                    <p>Email: <?= htmlspecialchars($siteSettings['home_email'] ?? '') ?></p>
                    <p>Direccion: <?= htmlspecialchars($siteSettings['home_address'] ?? '') ?></p>
                </div>
            </section>

            <section class="space-y-3 rounded-2xl border border-white/80 bg-white/80 p-5 shadow-sm backdrop-filter">
                <h2 class="text-sm font-semibold text-sky-800">Seguinos</h2>
                <div class="flex flex-wrap gap-3">
                    <?php if (!empty($socialLinks['instagram'])): ?>
                        <a href="<?= htmlspecialchars($socialLinks['instagram']) ?>" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-rose-200 bg-rose-50 text-rose-500 transition hover:bg-rose-100 hover:text-rose-600" aria-label="Instagram" target="_blank" rel="noreferrer noopener">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="3.5" y="3.5" width="17" height="17" rx="4"></rect>
                                <circle cx="12" cy="12" r="4"></circle>
                                <circle cx="17.5" cy="6.5" r="1"></circle>
                            </svg>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($socialLinks['facebook'])): ?>
                        <a href="<?= htmlspecialchars($socialLinks['facebook']) ?>" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-sky-200 bg-sky-50 text-sky-500 transition hover:bg-sky-100 hover:text-sky-600" aria-label="Facebook" target="_blank" rel="noreferrer noopener">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M13.5 21v-7h2.4l.4-3h-2.8V9.2c0-.9.3-1.5 1.6-1.5H16V5.1c-.2 0-1-.1-2-.1-2 0-3.4 1.2-3.4 3.5V11H8v3h2.6v7h2.9Z"></path>
                            </svg>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($socialLinks['whatsapp'])): ?>
                        <a href="<?= htmlspecialchars($socialLinks['whatsapp']) ?>" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 text-emerald-500 transition hover:bg-emerald-100 hover:text-emerald-600" aria-label="WhatsApp" target="_blank" rel="noreferrer noopener">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M20 11.9A8 8 0 0 1 8.3 19l-3.3.9.9-3.2A8 8 0 1 1 20 11.9Zm-4.7 1.7-.6-.3-1.1-.5c-.3-.1-.5-.1-.7.2l-.3.4c-.2.3-.4.3-.7.1a6.6 6.6 0 0 1-2-1.2 7.3 7.3 0 0 1-1.4-1.8c-.1-.3 0-.5.1-.7l.3-.3.2-.3.1-.3a.8.8 0 0 0 0-.4l-.5-1.2c-.2-.4-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.4 2.2 2.2 0 0 0-.7 1.6c0 1 .7 2 1 2.4.1.2 1.6 2.5 4 3.4 2.4 1 2.4.7 2.9.7.4 0 1.5-.6 1.7-1.3.2-.6.2-1.2.2-1.3 0-.1-.2-.2-.4-.3Z"></path>
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <div class="flex flex-col gap-3 border-t border-sky-100 pt-4 text-xs text-slate-500 md:flex-row md:items-center md:justify-between">
            <p>&copy; <?= date('Y'); ?> Inmobiliaria Argentina. Todos los derechos reservados.</p>
    
        </div>
    </div>
</footer>
</body>
</html>
