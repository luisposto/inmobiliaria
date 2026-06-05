<?php
require_once __DIR__ . '/funciones.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pass  = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && !empty($user['activo']) && verificarPasswordUsuario($pass, (string) $user['password_hash'])) {
        if (necesitaRehashPasswordUsuario((string) $user['password_hash'])) {
            actualizarHashUsuario((int) $user['id'], $pass);
        }

        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        refrescarPermisosSesion((int) $user['id']);
        header("Location: " . obtenerRutaInicioAdmin());
        exit;
    } else {
        if ($user && empty($user['activo'])) {
            $error = "Tu usuario esta inactivo.";
            header("Location: ../admin/login.php?error=" . urlencode($error));
            exit;
        }

        $error = "Email o contraseña incorrectos.";
        header("Location: ../admin/login.php?error=" . urlencode($error));
        exit;
    }
}

header("Location: ../admin/login.php");
exit;
?>
