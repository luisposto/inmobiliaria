<?php
require_once __DIR__ . '/conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pass  = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && hash('sha256', $pass) === $user['password_hash']) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        header("Location: ../admin/dashboard.php");
        exit;
    } else {
        $error = "Email o contraseña incorrectos.";
        header("Location: ../admin/login.php?error=" . urlencode($error));
        exit;
    }
}

header("Location: ../admin/login.php");
exit;
?>
