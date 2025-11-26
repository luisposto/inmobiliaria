<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit;
}
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Inmobiliaria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white text-center">
                    <h1 class="h5 mb-0">Panel de administración</h1>
                    <small>Inmobiliaria Argentina</small>
                </div>
                <div class="card-body">

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 small">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="../backend/login_procesar.php" class="mt-2">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="admin@inmobiliaria.com"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="******"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Iniciar sesión
                        </button>
                    </form>

                    <p class="mt-3 mb-0 text-center text-muted small">
                        Usuario demo: <strong>admin@inmobiliaria.com</strong><br>
                        Pass: <strong>123456</strong>
                    </p>
                </div>
            </div>

            <p class="text-center text-muted small mt-3">
                &copy; <?= date('Y') ?> Inmobiliaria Argentina
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
