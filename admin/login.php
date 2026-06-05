<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    require_once __DIR__ . '/../backend/funciones.php';
    header("Location: " . obtenerRutaInicioAdminInterna());
    exit;
}
$error = $_GET['error'] ?? '';
$tailwindHref = '/inmobiliaria/public/css/tailwind.css?v=' . (is_file(__DIR__ . '/../public/css/tailwind.css') ? filemtime(__DIR__ . '/../public/css/tailwind.css') : time());
$themeOverridesHref = '/inmobiliaria/public/css/theme-overrides.css?v=' . (is_file(__DIR__ . '/../public/css/theme-overrides.css') ? filemtime(__DIR__ . '/../public/css/theme-overrides.css') : time());
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Inmobiliaria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= htmlspecialchars($tailwindHref) ?>" rel="stylesheet">
    <link href="<?= htmlspecialchars($themeOverridesHref) ?>" rel="stylesheet">
</head>
<body class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <div>
            <div class="mb-6 flex flex-col items-center text-center">
                <span class="app-brand-mark mb-4">IA</span>
                <h1 class="text-2xl font-extrabold text-slate-950">Panel administrador</h1>
                <p class="mt-2 text-sm text-slate-500">Ingresá con tu usuario para gestionar las propiedades.</p>
            </div>

            <?php if ($error): ?>
                <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="../backend/login_procesar.php" method="POST" class="space-y-4">
                <div>
                    <label class="field-label">Email</label>
                    <input type="email" name="email" required class="field-input" placeholder="admin@inmobiliaria.com">
                </div>
                <div>
                    <label class="field-label">Contraseña</label>
                    <input type="password" name="password" required class="field-input" placeholder="******">
                </div>
                <button type="submit" class="btn-accent w-full rounded-2xl">Iniciar sesión</button>
            </form>

            <p class="mt-6 text-center text-xs text-slate-400">
                Usuario demo: admin@inmobiliaria.com · Pass: 123456
            </p>
        </div>
    </div>
</body>
</html>
