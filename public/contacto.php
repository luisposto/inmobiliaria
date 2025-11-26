<?php
$enviado = false;
$propiedadTitulo = $_POST['propiedad_titulo'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Acá podrías enviar email o guardar en BD.
    // Por ahora solo marcamos como enviado.
    $enviado = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contacto | Inmobiliaria Argentina</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-slate-100 text-slate-900">
<header class="bg-slate-900 text-white">
    <div class="max-w-6xl mx-auto flex items-center justify-between p-4">
        <a href="index.php" class="flex items-center gap-3">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold shadow">
                IA
            </span>
            <div>
                <p class="text-sm md:text-base font-semibold leading-tight">Inmobiliaria Argentina</p>
                <p class="text-xs text-slate-300">Propiedades en CABA y GBA</p>
            </div>
        </a>
        <nav class="space-x-4 text-xs md:text-sm">
            <a href="index.php" class="hover:text-emerald-300">Inicio</a>
            <a href="propiedades.php" class="hover:text-emerald-300">Propiedades</a>
            <a href="contacto.php" class="hover:text-emerald-300">Contacto</a>
        </nav>
    </div>
</header>

<main class="max-w-4xl mx-auto px-4 py-8 space-y-6">
    <section class="space-y-2">
        <h1 class="text-xl md:text-2xl font-bold">Contacto</h1>
        <p class="text-sm text-slate-600">
            Completá el formulario y un asesor se pondrá en contacto con vos.
        </p>
    </section>

    <?php if ($enviado): ?>
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            Recibimos tu consulta<?= $propiedadTitulo ? ' sobre la propiedad "'.htmlspecialchars($propiedadTitulo).'"' : '' ?>.
            Te responderemos a la brevedad.
        </div>
    <?php endif; ?>

    <section class="bg-white rounded-2xl shadow border border-slate-100 p-5">
        <form action="contacto.php" method="POST" class="grid md:grid-cols-2 gap-3 text-xs md:text-sm">
            <div class="md:col-span-1">
                <label class="block text-slate-700 mb-1">Nombre y apellido</label>
                <input type="text" name="nombre" required
                       class="w-full rounded-lg border border-slate-200 px-3 py-2">
            </div>
            <div class="md:col-span-1">
                <label class="block text-slate-700 mb-1">Email</label>
                <input type="email" name="email" required
                       class="w-full rounded-lg border border-slate-200 px-3 py-2">
            </div>
            <div class="md:col-span-1">
                <label class="block text-slate-700 mb-1">Teléfono</label>
                <input type="text" name="telefono"
                       class="w-full rounded-lg border border-slate-200 px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <label class="block text-slate-700 mb-1">Mensaje</label>
                <textarea name="mensaje" rows="4"
                          class="w-full rounded-lg border border-slate-200 px-3 py-2"></textarea>
            </div>
            <div class="md:col-span-2 flex justify-end pt-2">
                <button class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                    Enviar consulta
                </button>
            </div>
        </form>
    </section>
</main>

<footer class="mt-10 bg-slate-900 text-slate-300">
    <div class="max-w-6xl mx-auto px-4 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3 text-xs md:text-sm">
        <p>© <?= date('Y'); ?> Inmobiliaria Argentina. Todos los derechos reservados.</p>
        <p>Panel admin:
            <a href="../admin/login.php" class="text-emerald-400 hover:underline">Acceder</a>
        </p>
    </div>
</footer>
</body>
</html>
