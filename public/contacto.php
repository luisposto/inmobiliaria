<?php
$enviado = false;
$propiedadTitulo = $_POST['propiedad_titulo'] ?? '';
$tailwindHref = '/inmobiliaria/public/css/tailwind.css?v=' . (is_file(__DIR__ . '/css/tailwind.css') ? filemtime(__DIR__ . '/css/tailwind.css') : time());
$themeOverridesHref = '/inmobiliaria/public/css/theme-overrides.css?v=' . (is_file(__DIR__ . '/css/theme-overrides.css') ? filemtime(__DIR__ . '/css/theme-overrides.css') : time());
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enviado = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contacto | Inmobiliaria Argentina</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= htmlspecialchars($tailwindHref) ?>" rel="stylesheet">
    <link href="<?= htmlspecialchars($themeOverridesHref) ?>" rel="stylesheet">
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
            <a href="contacto.php">Contacto</a>
        </nav>
    </div>
</header>

<main class="app-main-narrow space-y-6">
    <section class="hero-panel">
        <span class="eyebrow">Contacto</span>
        <h1 class="section-heading mb-2">Hablar con un asesor</h1>
        <p class="section-copy">
            Completá el formulario y un asesor se pondrá en contacto con vos.
        </p>
    </section>

    <?php if ($enviado): ?>
        <div class="surface-card-soft border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            Recibimos tu consulta<?= $propiedadTitulo ? ' sobre la propiedad "'.htmlspecialchars($propiedadTitulo).'"' : '' ?>.
            Te responderemos a la brevedad.
        </div>
    <?php endif; ?>

    <section class="surface-card p-6 md:p-8">
        <form action="contacto.php" method="POST" class="grid gap-4 text-sm md:grid-cols-2">
            <div>
                <label class="field-label">Nombre y apellido</label>
                <input type="text" name="nombre" required class="field-input">
            </div>
            <div>
                <label class="field-label">Email</label>
                <input type="email" name="email" required class="field-input">
            </div>
            <div>
                <label class="field-label">Teléfono</label>
                <input type="text" name="telefono" class="field-input">
            </div>
            <div class="md:col-span-2">
                <label class="field-label">Mensaje</label>
                <textarea name="mensaje" rows="5" class="field-input"></textarea>
            </div>
            <div class="md:col-span-2 flex justify-stretch pt-2 md:justify-end">
                <button class="btn-primary w-full md:w-auto">Enviar consulta</button>
            </div>
        </form>
    </section>
</main>

<footer class="app-footer">
    <div class="app-footer-inner">
        <p>© <?= date('Y'); ?> Inmobiliaria Argentina. Todos los derechos reservados.</p>
        </div>
</footer>
</body>
</html>
