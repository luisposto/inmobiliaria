<?php
session_start();
require_once __DIR__ . '/conexion.php';

/**
 * AUTH
 */
function estaLogueado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function requerirLogin(): void
{
    if (!estaLogueado()) {
        header("Location: ../admin/login.php");
        exit;
    }
}

function obtenerSeccionesAdmin(): array
{
    return [
        'propiedades' => [
            'label' => 'Propiedades',
            'nav_label' => 'Propiedades',
            'route' => 'dashboard.php',
        ],
        'iconos' => [
            'label' => 'Admin de iconos',
            'nav_label' => 'Administrar iconos',
            'route' => 'iconos.php',
        ],
        'staff' => [
            'label' => 'Admin de staff',
            'nav_label' => 'Administrar staff',
            'route' => 'staff.php',
        ],
        'ubicaciones' => [
            'label' => 'Admin de ubicaciones',
            'nav_label' => 'Ciudades y provincias',
            'route' => 'ciudades.php',
        ],
        'usuarios' => [
            'label' => 'Admin de usuarios',
            'nav_label' => 'Administrar usuarios',
            'route' => 'usuarios.php',
        ],
        'configuraciones' => [
            'label' => 'Configuraciones',
            'nav_label' => 'Configuraciones',
            'route' => 'settings.php',
        ],
    ];
}

function existeTablaPermisosUsuarios(): bool
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    global $pdo;

    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'usuario_permisos'");
        $cache = (bool) $stmt->fetchColumn();
    } catch (Throwable $e) {
        $cache = false;
    }

    return $cache;
}

function normalizarPermisosUsuario(array $permisos): array
{
    $permitidos = array_keys(obtenerSeccionesAdmin());
    $normalizados = [];

    foreach ($permisos as $permiso) {
        $permiso = trim((string) $permiso);
        if ($permiso !== '' && in_array($permiso, $permitidos, true)) {
            $normalizados[$permiso] = true;
        }
    }

    return array_keys($normalizados);
}

function obtenerPermisosUsuarioPorId(int $usuarioId): array
{
    if ($usuarioId <= 0) {
        return [];
    }

    if (!existeTablaPermisosUsuarios()) {
        return array_keys(obtenerSeccionesAdmin());
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT seccion
         FROM usuario_permisos
         WHERE usuario_id = ?"
    );
    $stmt->execute([$usuarioId]);
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

    return normalizarPermisosUsuario($rows);
}

function guardarPermisosUsuario(int $usuarioId, array $permisos): void
{
    if ($usuarioId <= 0 || !existeTablaPermisosUsuarios()) {
        return;
    }

    global $pdo;
    $permisos = normalizarPermisosUsuario($permisos);

    $stmtDelete = $pdo->prepare("DELETE FROM usuario_permisos WHERE usuario_id = ?");
    $stmtDelete->execute([$usuarioId]);

    if ($permisos === []) {
        return;
    }

    $stmtInsert = $pdo->prepare(
        "INSERT INTO usuario_permisos (usuario_id, seccion)
         VALUES (:usuario_id, :seccion)"
    );

    foreach ($permisos as $permiso) {
        $stmtInsert->execute([
            ':usuario_id' => $usuarioId,
            ':seccion' => $permiso,
        ]);
    }
}

function refrescarPermisosSesion(?int $usuarioId = null): array
{
    $usuarioId = $usuarioId ?? (int) ($_SESSION['usuario_id'] ?? 0);
    $permisos = obtenerPermisosUsuarioPorId($usuarioId);
    $_SESSION['usuario_permisos'] = $permisos;

    return $permisos;
}

function obtenerPermisosSesion(): array
{
    if (!isset($_SESSION['usuario_id'])) {
        return [];
    }

    if (!isset($_SESSION['usuario_permisos']) || !is_array($_SESSION['usuario_permisos'])) {
        return refrescarPermisosSesion((int) $_SESSION['usuario_id']);
    }

    return normalizarPermisosUsuario($_SESSION['usuario_permisos']);
}

function usuarioPuedeAcceder(string $seccion): bool
{
    return in_array($seccion, obtenerPermisosSesion(), true);
}

function obtenerRutaInicioAdmin(): string
{
    $secciones = obtenerSeccionesAdmin();
    foreach (obtenerPermisosSesion() as $permiso) {
        if (isset($secciones[$permiso]['route'])) {
            return '../admin/' . $secciones[$permiso]['route'];
        }
    }

    return '../admin/login.php?error=' . urlencode('Tu usuario no tiene secciones habilitadas.');
}

function obtenerRutaInicioAdminInterna(): string
{
    $secciones = obtenerSeccionesAdmin();
    foreach (obtenerPermisosSesion() as $permiso) {
        if (isset($secciones[$permiso]['route'])) {
            return $secciones[$permiso]['route'];
        }
    }

    return 'login.php?error=' . urlencode('Tu usuario no tiene secciones habilitadas.');
}

function requerirPermisoAdmin(string $seccion): void
{
    requerirLogin();

    if (!usuarioPuedeAcceder($seccion)) {
        header('Location: ' . obtenerRutaInicioAdmin());
        exit;
    }
}

function renderAdminSidebar(string $seccionActiva): string
{
    $items = [];
    $secciones = obtenerSeccionesAdmin();

    foreach ($secciones as $clave => $meta) {
        if (!usuarioPuedeAcceder($clave)) {
            continue;
        }

        $clase = 'admin-sidebar-link' . ($seccionActiva === $clave ? ' admin-sidebar-link-active' : '');
        $items[] = '<a href="' . htmlspecialchars($meta['route'], ENT_QUOTES, 'UTF-8') . '" class="' . $clase . '">'
            . htmlspecialchars($meta['nav_label'], ENT_QUOTES, 'UTF-8')
            . '</a>';
    }

    return '<aside class="admin-sidebar"><p class="admin-sidebar-title">Panel admin</p><nav class="admin-sidebar-nav">'
        . implode('', $items)
        . '</nav></aside>';
}

function existeTablaUsuarios(): bool
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    global $pdo;

    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
        $cache = (bool) $stmt->fetchColumn();
    } catch (Throwable $e) {
        $cache = false;
    }

    return $cache;
}

function hashPasswordUsuario(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function verificarPasswordUsuario(string $password, string $hash): bool
{
    $info = password_get_info($hash);
    if (!empty($info['algo'])) {
        return password_verify($password, $hash);
    }

    return hash('sha256', $password) === $hash;
}

function necesitaRehashPasswordUsuario(string $hash): bool
{
    $info = password_get_info($hash);
    if (empty($info['algo'])) {
        return true;
    }

    return password_needs_rehash($hash, PASSWORD_DEFAULT);
}

function actualizarHashUsuario(int $id, string $password): void
{
    if ($id <= 0 || !existeTablaUsuarios()) {
        return;
    }

    global $pdo;
    $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
    $stmt->execute([hashPasswordUsuario($password), $id]);
}

function obtenerUsuarios(): array
{
    if (!existeTablaUsuarios()) {
        return [];
    }

    global $pdo;
    $stmt = $pdo->query(
        "SELECT id, nombre, email, activo, created_at
         FROM usuarios
         ORDER BY created_at DESC, id DESC"
    );
    $rows = $stmt->fetchAll() ?: [];

    foreach ($rows as &$row) {
        $row['permisos'] = obtenerPermisosUsuarioPorId((int) ($row['id'] ?? 0));
    }
    unset($row);

    return $rows;
}

function obtenerUsuarioPorId(int $id): ?array
{
    if ($id <= 0 || !existeTablaUsuarios()) {
        return null;
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT id, nombre, email, password_hash, activo, created_at
         FROM usuarios
         WHERE id = ?
         LIMIT 1"
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $row['permisos'] = obtenerPermisosUsuarioPorId($id);
    }

    return $row ?: null;
}

function emailUsuarioDisponible(string $email, ?int $ignorarId = null): bool
{
    if (!existeTablaUsuarios()) {
        return false;
    }

    global $pdo;

    if ($ignorarId && $ignorarId > 0) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id <> ? LIMIT 1");
        $stmt->execute([$email, $ignorarId]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
    }

    return !$stmt->fetch();
}

function prepararDatosUsuario(array $data, bool $passwordObligatoria = true, ?int $usuarioId = null): array
{
    $nombre = trim((string) ($data['nombre'] ?? ''));
    $email = strtolower(trim((string) ($data['email'] ?? '')));
    $password = (string) ($data['password'] ?? '');
    $passwordConfirm = (string) ($data['password_confirm'] ?? '');
    $activo = array_key_exists('activo', $data) ? (!empty($data['activo']) ? 1 : 0) : 1;
    $permisos = normalizarPermisosUsuario((array) ($data['permisos'] ?? []));

    if ($nombre === '') {
        return ['ok' => false, 'error' => 'missing_name'];
    }

    if ($email === '') {
        return ['ok' => false, 'error' => 'missing_email'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'invalid_email'];
    }

    if (!existeTablaUsuarios()) {
        return ['ok' => false, 'error' => 'missing_table'];
    }

    if (!emailUsuarioDisponible($email, $usuarioId)) {
        return ['ok' => false, 'error' => 'email_taken'];
    }

    if ($permisos === []) {
        return ['ok' => false, 'error' => 'missing_permissions'];
    }

    if ($passwordObligatoria && $password === '') {
        return ['ok' => false, 'error' => 'missing_password'];
    }

    if ($password !== '' || $passwordConfirm !== '') {
        if (strlen($password) < 6) {
            return ['ok' => false, 'error' => 'password_short'];
        }

        if ($password !== $passwordConfirm) {
            return ['ok' => false, 'error' => 'password_mismatch'];
        }
    }

    return [
        'ok' => true,
        'nombre' => $nombre,
        'email' => $email,
        'password_hash' => $password !== '' ? hashPasswordUsuario($password) : null,
        'activo' => $activo,
        'permisos' => $permisos,
    ];
}

function crearUsuario(array $data): array
{
    $payload = prepararDatosUsuario($data, true);
    if (!$payload['ok']) {
        return $payload;
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "INSERT INTO usuarios (nombre, email, password_hash, activo)
         VALUES (:nombre, :email, :password_hash, :activo)"
    );
    $stmt->execute([
        ':nombre' => $payload['nombre'],
        ':email' => $payload['email'],
        ':password_hash' => $payload['password_hash'],
        ':activo' => $payload['activo'],
    ]);

    $usuarioId = (int) $pdo->lastInsertId();
    guardarPermisosUsuario($usuarioId, $payload['permisos']);

    return ['ok' => true, 'id' => $usuarioId];
}

function actualizarUsuario(int $id, array $data): array
{
    $actual = obtenerUsuarioPorId($id);
    if (!$actual) {
        return ['ok' => false, 'error' => 'not_found'];
    }

    $payload = prepararDatosUsuario($data, false, $id);
    if (!$payload['ok']) {
        return $payload;
    }

    global $pdo;
    if ($payload['password_hash'] !== null) {
        $stmt = $pdo->prepare(
            "UPDATE usuarios
             SET nombre = :nombre, email = :email, password_hash = :password_hash, activo = :activo
             WHERE id = :id"
        );
        $stmt->execute([
            ':nombre' => $payload['nombre'],
            ':email' => $payload['email'],
            ':password_hash' => $payload['password_hash'],
            ':activo' => $payload['activo'],
            ':id' => $id,
        ]);
    } else {
        $stmt = $pdo->prepare(
            "UPDATE usuarios
             SET nombre = :nombre, email = :email, activo = :activo
             WHERE id = :id"
        );
        $stmt->execute([
            ':nombre' => $payload['nombre'],
            ':email' => $payload['email'],
            ':activo' => $payload['activo'],
            ':id' => $id,
        ]);
    }

    guardarPermisosUsuario($id, $payload['permisos']);

    if (($actual['id'] ?? null) == ($_SESSION['usuario_id'] ?? null)) {
        $_SESSION['usuario_nombre'] = $payload['nombre'];
        refrescarPermisosSesion((int) $id);
    }

    return ['ok' => true];
}

function eliminarUsuario(int $id): array
{
    if ($id <= 0) {
        return ['ok' => false, 'error' => 'not_found'];
    }

    if (($id === (int) ($_SESSION['usuario_id'] ?? 0))) {
        return ['ok' => false, 'error' => 'self_delete'];
    }

    $actual = obtenerUsuarioPorId($id);
    if (!$actual) {
        return ['ok' => false, 'error' => 'not_found'];
    }

    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);

    return ['ok' => true];
}

/**
 * Catálogos: operaciones, tipos de propiedad, estados
 */
function proyectoBaseUrl(): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $base = preg_replace('#/(public|admin)(/.*)?$#', '', $scriptName);
    $base = rtrim((string)$base, '/');
    return $base !== '' ? $base : '';
}

function publicAssetUrl(string $path): string
{
    return proyectoBaseUrl() . '/public/' . ltrim($path, '/');
}

function publicAssetVersion(string $path): int
{
    $fullPath = __DIR__ . '/../public/' . ltrim($path, '/');
    return is_file($fullPath) ? (int) filemtime($fullPath) : time();
}

function obtenerAjustesSitioPorDefecto(): array
{
    return [
        'home_phone' => '+54 341 555-1234',
        'home_email' => 'contacto@inmobiliariaargentina.com',
        'home_address' => 'Bv. Oroño 845, Rosario',
        'home_instagram_url' => '#',
        'home_facebook_url' => '#',
        'home_whatsapp_url' => '#',
        'home_video_file' => 'hero-home.mp4',
    ];
}

function existeTablaSiteSettings(): bool
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    global $pdo;

    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'site_settings'");
        $cache = (bool) $stmt->fetchColumn();
    } catch (Throwable $e) {
        $cache = false;
    }

    return $cache;
}

function obtenerAjustesSitio(): array
{
    $defaults = obtenerAjustesSitioPorDefecto();

    if (!existeTablaSiteSettings()) {
        return $defaults;
    }

    global $pdo;

    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
    $rows = $stmt->fetchAll() ?: [];

    $settings = $defaults;
    foreach ($rows as $row) {
        $key = (string) ($row['setting_key'] ?? '');
        if ($key === '' || !array_key_exists($key, $defaults)) {
            continue;
        }

        $settings[$key] = (string) ($row['setting_value'] ?? '');
    }

    return $settings;
}

function obtenerAjusteSitio(string $key, ?string $default = null): ?string
{
    $settings = obtenerAjustesSitio();
    if (array_key_exists($key, $settings)) {
        return (string) $settings[$key];
    }

    return $default;
}

function normalizarValorAjusteSitio(?string $value): string
{
    return trim((string) $value);
}

function esVideoHomeAdministrable(?string $archivo): bool
{
    $archivo = trim((string) $archivo);
    return $archivo !== '' && str_starts_with($archivo, 'hero_');
}

function guardarVideoHome(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $ext = strtolower((string) pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    $permitidos = ['mp4', 'webm', 'ogg'];

    if (!in_array($ext, $permitidos, true)) {
        return null;
    }

    return guardarArchivoSubido($file, 'hero_', 'video');
}

function obtenerMimeVideoPorArchivo(?string $archivo): string
{
    $ext = strtolower((string) pathinfo((string) $archivo, PATHINFO_EXTENSION));

    switch ($ext) {
        case 'webm':
            return 'video/webm';
        case 'ogg':
        case 'ogv':
            return 'video/ogg';
        default:
            return 'video/mp4';
    }
}

function obtenerVideoHomeConfig(): array
{
    $archivo = obtenerAjusteSitio('home_video_file', 'hero-home.mp4');
    $archivo = trim((string) $archivo);

    if ($archivo !== '' && is_file(__DIR__ . '/../public/video/' . $archivo)) {
        return [
            'local_url' => publicAssetUrl('video/' . $archivo),
            'local_type' => obtenerMimeVideoPorArchivo($archivo),
            'fallback_url' => 'https://www.w3schools.com/howto/rain.mp4',
            'fallback_type' => 'video/mp4',
            'file' => $archivo,
        ];
    }

    return [
        'local_url' => null,
        'local_type' => null,
        'fallback_url' => 'https://www.w3schools.com/howto/rain.mp4',
        'fallback_type' => 'video/mp4',
        'file' => $archivo,
    ];
}

function guardarAjustesSitio(array $data, ?array $fileVideo = null): array
{
    if (!existeTablaSiteSettings()) {
        return ['ok' => false, 'error' => 'missing_table'];
    }

    $defaults = obtenerAjustesSitioPorDefecto();
    $settings = [
        'home_phone' => normalizarValorAjusteSitio($data['home_phone'] ?? null),
        'home_email' => normalizarValorAjusteSitio($data['home_email'] ?? null),
        'home_address' => normalizarValorAjusteSitio($data['home_address'] ?? null),
        'home_instagram_url' => normalizarValorAjusteSitio($data['home_instagram_url'] ?? null),
        'home_facebook_url' => normalizarValorAjusteSitio($data['home_facebook_url'] ?? null),
        'home_whatsapp_url' => normalizarValorAjusteSitio($data['home_whatsapp_url'] ?? null),
    ];

    $videoActual = obtenerAjusteSitio('home_video_file', $defaults['home_video_file']);
    $settings['home_video_file'] = (string) $videoActual;

    if ($fileVideo && ($fileVideo['tmp_name'] ?? '') !== '') {
        $nuevoVideo = guardarVideoHome($fileVideo);
        if ($nuevoVideo === null) {
            return ['ok' => false, 'error' => 'invalid_video'];
        }

        if (esVideoHomeAdministrable($videoActual)) {
            $rutaAnterior = __DIR__ . '/../public/video/' . $videoActual;
            if (is_file($rutaAnterior)) {
                @unlink($rutaAnterior);
            }
        }

        $settings['home_video_file'] = $nuevoVideo;
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "INSERT INTO site_settings (setting_key, setting_value)
         VALUES (:setting_key, :setting_value)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP"
    );

    foreach ($defaults as $key => $_defaultValue) {
        $stmt->execute([
            ':setting_key' => $key,
            ':setting_value' => (string) ($settings[$key] ?? ''),
        ]);
    }

    return ['ok' => true];
}

function obtenerOperaciones(): array
{
    global $pdo;
    $stmt = $pdo->query("SELECT id, nombre FROM operaciones ORDER BY nombre");
    return $stmt->fetchAll();
}

function obtenerTiposPropiedad(): array
{
    global $pdo;
    $stmt = $pdo->query("SELECT id, nombre FROM tipos_propiedad ORDER BY nombre");
    return $stmt->fetchAll();
}

function obtenerEstadosPropiedad(): array
{
    global $pdo;
    $stmt = $pdo->query("SELECT id, nombre FROM estados_propiedad ORDER BY id");
    return $stmt->fetchAll();
}

function opcionesStaffPorDefecto(): array
{
    return [
        [
            'id' => null,
            'nombre' => 'Martina Ruiz',
            'puesto' => 'Asesora comercial',
            'descripcion' => 'Acompana la busqueda de propiedades, coordina visitas y ordena cada oportunidad para que el proceso sea simple y concreto.',
            'imagen' => 'staff-martina.png',
            'facebook_url' => '#',
            'twitter_url' => '#',
            'instagram_url' => '#',
            'orden' => 1,
            'activo' => 1,
        ],
        [
            'id' => null,
            'nombre' => 'Valentina Gomez',
            'puesto' => 'Especialista en alquileres',
            'descripcion' => 'Trabaja con propietarios e inquilinos para resolver dudas rapido, ordenar condiciones y encontrar opciones acordes a cada necesidad.',
            'imagen' => 'staff-valentina.png',
            'facebook_url' => '#',
            'twitter_url' => '#',
            'instagram_url' => '#',
            'orden' => 2,
            'activo' => 1,
        ],
        [
            'id' => null,
            'nombre' => 'Leonardo Perez',
            'puesto' => 'Director de operaciones',
            'descripcion' => 'Coordina negociaciones, documentacion y cierres para que cada operacion avance con orden, respaldo y tiempos claros.',
            'imagen' => 'staff-leonardo.png',
            'facebook_url' => '#',
            'twitter_url' => '#',
            'instagram_url' => '#',
            'orden' => 3,
            'activo' => 1,
        ],
    ];
}

function opcionesIconosCaracteristicaPorDefecto(): array
{
    $items = [
        'rooms' => 'Ambientes',
        'bed' => 'Dormitorio',
        'bath' => 'Bano',
        'garage' => 'Cochera',
        'area' => 'Superficie',
        'calendar' => 'Estado',
        'view' => 'Vista',
        'home' => 'General',
        'building' => 'Edificio',
        'check' => 'Check',
    ];

    $orden = 1;
    $normalizados = [];
    foreach ($items as $clave => $nombre) {
        $normalizados[] = [
            'id' => null,
            'clave' => $clave,
            'nombre' => $nombre,
            'archivo' => null,
            'orden' => $orden++,
            'activo' => 1,
        ];
    }

    return $normalizados;
}

function obtenerCatalogoIconosFuente(): array
{
    return [
        'icon-acciones' => 'Acciones',
        'icon-aceptar' => 'Aceptar',
        'icon-agregar' => 'Agregar',
        'icon-ambientes' => 'Ambientes',
        'icon-balconterraza' => 'Balcon/Terraza',
        'icon-barrio' => 'Barrio',
        'icon-banos' => 'Banos',
        'icon-buscar' => 'Buscar',
        'icon-calendario' => 'Calendario',
        'icon-camara' => 'Camara',
        'icon-casa' => 'Casa',
        'icon-cochera' => 'Cochera',
        'icon-compartir' => 'Compartir',
        'icon-comprar' => 'Comprar',
        'icon-configuracion' => 'Configuracion',
        'icon-consultas' => 'Consultas',
        'icon-copiar' => 'Copiar',
        'icon-cubierta' => 'Cubierta',
        'icon-dashboard' => 'Dashboard',
        'icon-descargar' => 'Descargar',
        'icon-destaque' => 'Destaque',
        'icon-difusion' => 'Difusion',
        'icon-disponibilidad' => 'Disponibilidad',
        'icon-dormitorios' => 'Dormitorios',
        'icon-editar' => 'Editar',
        'icon-edificio' => 'Edificio',
        'icon-electricidad' => 'Electricidad',
        'icon-eliminar' => 'Eliminar',
        'icon-email' => 'Email',
        'icon-empresa' => 'Empresa',
        'icon-enviar' => 'Enviar',
        'icon-error' => 'Error',
        'icon-estadisticas' => 'Estadisticas',
        'icon-expensas' => 'Expensas',
        'icon-facebook' => 'Facebook',
        'icon-favoritos' => 'Favoritos',
        'icon-filtros' => 'Filtros',
        'icon-frente' => 'Frente',
        'icon-gas' => 'Gas',
        'icon-gimnasio' => 'Gimnasio',
        'icon-guardar' => 'Guardar',
        'icon-imagenes' => 'Imagenes',
        'icon-inversion' => 'Inversion',
        'icon-info' => 'Info',
        'icon-instagram' => 'Instagram',
        'icon-llaves' => 'Llaves',
        'icon-local' => 'Local comercial',
        'icon-lista' => 'Lista',
        'icon-mapa' => 'Mapa',
        'icon-notificaciones' => 'Notificaciones',
        'icon-orientacion' => 'Orientacion',
        'icon-parrilla' => 'Parrilla',
        'icon-pdf' => 'PDF',
        'icon-perfil' => 'Perfil',
        'icon-permisos' => 'Permisos',
        'icon-pileta' => 'Pileta',
        'icon-plantas' => 'Plantas',
        'icon-play' => 'Play',
        'icon-premium' => 'Premium',
        'icon-propiedades' => 'Propiedades',
        'icon-propietario' => 'Propietario',
        'icon-reservas' => 'Reservas',
        'icon-seguimiento' => 'Seguimiento',
        'icon-seguridad' => 'Seguridad',
        'icon-superficie' => 'Superficie',
        'icon-telefono' => 'Telefono',
        'icon-terreno' => 'Terreno',
        'icon-totalconstruido' => 'Total construido',
        'icon-ubicacion' => 'Ubicacion',
        'icon-video' => 'Video',
        'icon-whatsapp' => 'WhatsApp',
        'icon-wifi' => 'Wifi',
        'icon-x' => 'Cerrar',
        'icon-youtube' => 'YouTube',
        'icon-zona' => 'Zona',
        'icon-zonificacion' => 'Zonificacion',
        'icon-zoom-in' => 'Zoom in',
        'icon-zoom-out' => 'Zoom out',
    ];
}

function obtenerClasesIconosFuente(): array
{
    return array_keys(obtenerCatalogoIconosFuente());
}

function obtenerOpcionesIconosFuente(): array
{
    return obtenerCatalogoIconosFuente();
}

function normalizarClaseIconoFuente(string $texto): ?string
{
    $texto = strtolower(trim($texto));
    $texto = str_replace([' ', '_'], '-', $texto);
    $texto = preg_replace('/-+/', '-', $texto) ?? '';
    $texto = trim($texto, '-');

    if ($texto === '') {
        return null;
    }

    $candidatos = [$texto];
    if (!str_starts_with($texto, 'icon-')) {
        $candidatos[] = 'icon-' . $texto;
    }

    static $mapa = null;
    if ($mapa === null) {
        $mapa = array_fill_keys(obtenerClasesIconosFuente(), true);
    }

    foreach ($candidatos as $candidato) {
        if (isset($mapa[$candidato])) {
            return $candidato;
        }
    }

    return null;
}

function existeTablaIconosCaracteristica(): bool
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    global $pdo;

    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'iconos_caracteristica'");
        $cache = (bool)$stmt->fetchColumn();
    } catch (Throwable $e) {
        $cache = false;
    }

    return $cache;
}

function existeTablaStaff(): bool
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    global $pdo;

    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'staff'");
        $cache = (bool)$stmt->fetchColumn();
    } catch (Throwable $e) {
        $cache = false;
    }

    return $cache;
}

function obtenerStaff(bool $incluirInactivos = true): array
{
    if (!existeTablaStaff()) {
        return opcionesStaffPorDefecto();
    }

    global $pdo;

    $sql = "SELECT id, nombre, puesto, descripcion, imagen, facebook_url, twitter_url, instagram_url, orden, activo
            FROM staff";
    if (!$incluirInactivos) {
        $sql .= " WHERE activo = 1";
    }
    $sql .= " ORDER BY orden ASC, nombre ASC, id ASC";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll() ?: [];
}

function obtenerStaffPublico(): array
{
    $staff = obtenerStaff(false);
    return $staff !== [] ? $staff : opcionesStaffPorDefecto();
}

function obtenerStaffPorId(int $id): ?array
{
    if ($id <= 0 || !existeTablaStaff()) {
        return null;
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT id, nombre, puesto, descripcion, imagen, facebook_url, twitter_url, instagram_url, orden, activo
         FROM staff
         WHERE id = ?
         LIMIT 1"
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function normalizarUrlStaff(?string $url): ?string
{
    $url = trim((string)$url);
    return $url !== '' ? $url : null;
}

function prepararDatosStaff(array $data): array
{
    $nombre = trim((string)($data['nombre'] ?? ''));
    $puesto = trim((string)($data['puesto'] ?? ''));
    $descripcion = trim((string)($data['descripcion'] ?? ''));
    $orden = isset($data['orden']) && $data['orden'] !== '' ? max(1, (int)$data['orden']) : 1;
    $activo = array_key_exists('activo', $data) ? (!empty($data['activo']) ? 1 : 0) : 1;

    if ($nombre === '') {
        return ['ok' => false, 'error' => 'missing_name'];
    }

    if ($puesto === '') {
        return ['ok' => false, 'error' => 'missing_role'];
    }

    if ($descripcion === '') {
        return ['ok' => false, 'error' => 'missing_description'];
    }

    if (!existeTablaStaff()) {
        return ['ok' => false, 'error' => 'missing_table'];
    }

    return [
        'ok' => true,
        'nombre' => $nombre,
        'puesto' => $puesto,
        'descripcion' => $descripcion,
        'facebook_url' => normalizarUrlStaff($data['facebook_url'] ?? null),
        'twitter_url' => normalizarUrlStaff($data['twitter_url'] ?? null),
        'instagram_url' => normalizarUrlStaff($data['instagram_url'] ?? null),
        'orden' => $orden,
        'activo' => $activo,
    ];
}

function crearStaff(array $data, ?array $fileImagen = null): array
{
    $payload = prepararDatosStaff($data);
    if (!$payload['ok']) {
        return $payload;
    }

    $imagen = null;
    if ($fileImagen && isset($fileImagen['tmp_name']) && $fileImagen['tmp_name'] !== '') {
        $imagen = guardarImagenStaff($fileImagen);
        if ($imagen === null) {
            return ['ok' => false, 'error' => 'upload_failed'];
        }
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "INSERT INTO staff (nombre, puesto, descripcion, imagen, facebook_url, twitter_url, instagram_url, orden, activo)
         VALUES (:nombre, :puesto, :descripcion, :imagen, :facebook_url, :twitter_url, :instagram_url, :orden, :activo)"
    );
    $stmt->execute([
        ':nombre' => $payload['nombre'],
        ':puesto' => $payload['puesto'],
        ':descripcion' => $payload['descripcion'],
        ':imagen' => $imagen,
        ':facebook_url' => $payload['facebook_url'],
        ':twitter_url' => $payload['twitter_url'],
        ':instagram_url' => $payload['instagram_url'],
        ':orden' => $payload['orden'],
        ':activo' => $payload['activo'],
    ]);

    return ['ok' => true, 'id' => (int)$pdo->lastInsertId()];
}

function esImagenStaffAdministrable(?string $archivo): bool
{
    $archivo = trim((string)$archivo);
    return $archivo !== '' && str_starts_with($archivo, 'staff_');
}

function actualizarStaff(int $id, array $data, ?array $fileImagen = null): array
{
    $actual = obtenerStaffPorId($id);
    if (!$actual) {
        return ['ok' => false, 'error' => 'not_found'];
    }

    $payload = prepararDatosStaff($data);
    if (!$payload['ok']) {
        return $payload;
    }

    $imagen = $actual['imagen'];
    if ($fileImagen && isset($fileImagen['tmp_name']) && $fileImagen['tmp_name'] !== '') {
        $nuevaImagen = guardarImagenStaff($fileImagen);
        if ($nuevaImagen === null) {
            return ['ok' => false, 'error' => 'upload_failed'];
        }

        if (esImagenStaffAdministrable($imagen)) {
            $rutaAnterior = __DIR__ . '/../public/img/' . $imagen;
            if (is_file($rutaAnterior)) {
                @unlink($rutaAnterior);
            }
        }

        $imagen = $nuevaImagen;
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "UPDATE staff
         SET nombre = :nombre,
             puesto = :puesto,
             descripcion = :descripcion,
             imagen = :imagen,
             facebook_url = :facebook_url,
             twitter_url = :twitter_url,
             instagram_url = :instagram_url,
             orden = :orden,
             activo = :activo
         WHERE id = :id"
    );
    $ok = $stmt->execute([
        ':id' => $id,
        ':nombre' => $payload['nombre'],
        ':puesto' => $payload['puesto'],
        ':descripcion' => $payload['descripcion'],
        ':imagen' => $imagen,
        ':facebook_url' => $payload['facebook_url'],
        ':twitter_url' => $payload['twitter_url'],
        ':instagram_url' => $payload['instagram_url'],
        ':orden' => $payload['orden'],
        ':activo' => $payload['activo'],
    ]);

    return ['ok' => $ok];
}

function eliminarStaff(int $id): array
{
    $actual = obtenerStaffPorId($id);
    if (!$actual) {
        return ['ok' => false, 'error' => 'not_found'];
    }

    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM staff WHERE id = ?");
    $ok = $stmt->execute([$id]);

    if ($ok && esImagenStaffAdministrable($actual['imagen'] ?? null)) {
        $ruta = __DIR__ . '/../public/img/' . $actual['imagen'];
        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }

    return ['ok' => $ok];
}

function staffImagenUrl(?string $archivo): ?string
{
    $archivo = trim((string)$archivo);
    if ($archivo === '') {
        return null;
    }

    return publicAssetUrl('img/' . $archivo);
}

function renderizarIconoRedSocialStaff(string $red): string
{
    switch ($red) {
        case 'facebook':
            return '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-7h2.4l.4-3h-2.8V9.2c0-.9.3-1.5 1.6-1.5H16V5.1c-.2 0-1-.1-2-.1-2 0-3.4 1.2-3.4 3.5V11H8v3h2.6v7h2.9Z"></path></svg>';
        case 'twitter':
            return '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.9 7.2c.8-.1 1.5-.4 2.1-.8-.3.8-.9 1.5-1.6 2 .8 0 1.5-.3 2.1-.6-.5.8-1.2 1.5-2 2A8.7 8.7 0 0 1 6.7 17a6.2 6.2 0 0 1-2.7-.8 6.2 6.2 0 0 0 4.6-1.3 3.1 3.1 0 0 1-2.9-2.1c.5.1 1 .1 1.4 0A3.1 3.1 0 0 1 4.6 9v-.1c.4.2.9.4 1.4.4A3.1 3.1 0 0 1 5 5.1a8.7 8.7 0 0 0 6.4 3.3 3.6 3.6 0 0 1-.1-.7 3.1 3.1 0 0 1 5.4-2.1c.7-.1 1.4-.4 2-.8-.2.8-.7 1.4-1.4 1.8Z"></path></svg>';
        case 'instagram':
            return '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="4"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1"></circle></svg>';
        default:
            return '';
    }
}

function normalizarClaveIconoCaracteristica(string $texto): string
{
    $texto = trim($texto);
    if ($texto === '') {
        return 'icono';
    }

    if (function_exists('iconv')) {
        $convertido = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
        if (is_string($convertido) && $convertido !== '') {
            $texto = $convertido;
        }
    }

    $iconClass = normalizarClaseIconoFuente($texto);
    if ($iconClass !== null) {
        return $iconClass;
    }

    $texto = strtolower($texto);
    $texto = preg_replace('/[^a-z0-9-]+/', '-', $texto) ?? '';
    $texto = preg_replace('/-+/', '-', $texto) ?? '';
    $texto = trim($texto, '-');

    return $texto !== '' ? $texto : 'icono';
}

function obtenerIconosCaracteristica(bool $incluirInactivos = true): array
{
    if (!existeTablaIconosCaracteristica()) {
        return opcionesIconosCaracteristicaPorDefecto();
    }

    global $pdo;

    $sql = "SELECT id, clave, nombre, archivo, orden, activo
            FROM iconos_caracteristica";
    if (!$incluirInactivos) {
        $sql .= " WHERE activo = 1";
    }
    $sql .= " ORDER BY orden ASC, nombre ASC, id ASC";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll() ?: [];

    return $rows !== [] ? $rows : opcionesIconosCaracteristicaPorDefecto();
}

function obtenerOpcionesIconosCaracteristica(bool $incluirInactivos = true): array
{
    $opciones = [];

    foreach (obtenerIconosCaracteristica($incluirInactivos) as $icono) {
        if (!$incluirInactivos && empty($icono['activo'])) {
            continue;
        }
        $opciones[(string)$icono['clave']] = (string)$icono['nombre'];
    }

    return $opciones;
}

function obtenerIconoCaracteristicaPorId(int $id): ?array
{
    if ($id <= 0 || !existeTablaIconosCaracteristica()) {
        return null;
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT id, clave, nombre, archivo, orden, activo
         FROM iconos_caracteristica
         WHERE id = ?
         LIMIT 1"
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function contarUsoIconoCaracteristica(string $clave): int
{
    if ($clave === '' || !existeTablaPropiedadCaracteristicas()) {
        return 0;
    }

    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM propiedad_caracteristicas WHERE icono = ?");
    $stmt->execute([$clave]);
    return (int)$stmt->fetchColumn();
}

function prepararDatosIconoCaracteristica(array $data, ?int $ignorarId = null): array
{
    $nombre = trim((string)($data['nombre'] ?? ''));
    $claveBase = trim((string)($data['clave'] ?? ''));
    $clave = normalizarClaveIconoCaracteristica($claveBase !== '' ? $claveBase : $nombre);
    $orden = isset($data['orden']) && $data['orden'] !== '' ? max(1, (int)$data['orden']) : 1;
    $activo = array_key_exists('activo', $data) ? (!empty($data['activo']) ? 1 : 0) : 1;

    if ($nombre === '') {
        return ['ok' => false, 'error' => 'missing_name'];
    }

    if (!existeTablaIconosCaracteristica()) {
        return ['ok' => false, 'error' => 'missing_table'];
    }

    global $pdo;
    $sql = "SELECT id FROM iconos_caracteristica WHERE clave = ?";
    $params = [$clave];
    if ($ignorarId !== null && $ignorarId > 0) {
        $sql .= " AND id <> ?";
        $params[] = $ignorarId;
    }

    $stmt = $pdo->prepare($sql . " LIMIT 1");
    $stmt->execute($params);
    if ($stmt->fetch()) {
        return ['ok' => false, 'error' => 'duplicate_key'];
    }

    return [
        'ok' => true,
        'nombre' => $nombre,
        'clave' => $clave,
        'orden' => $orden,
        'activo' => $activo,
    ];
}

function crearIconoCaracteristica(array $data, ?array $fileImagen = null): array
{
    $payload = prepararDatosIconoCaracteristica($data);
    if (!$payload['ok']) {
        return $payload;
    }

    $archivo = null;
    if ($fileImagen && isset($fileImagen['tmp_name']) && $fileImagen['tmp_name'] !== '') {
        $archivo = guardarImagenIconoCaracteristica($fileImagen);
        if ($archivo === null) {
            return ['ok' => false, 'error' => 'upload_failed'];
        }
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "INSERT INTO iconos_caracteristica (clave, nombre, archivo, orden, activo)
         VALUES (:clave, :nombre, :archivo, :orden, :activo)"
    );
    $stmt->execute([
        ':clave' => $payload['clave'],
        ':nombre' => $payload['nombre'],
        ':archivo' => $archivo,
        ':orden' => $payload['orden'],
        ':activo' => $payload['activo'],
    ]);

    return ['ok' => true, 'id' => (int)$pdo->lastInsertId()];
}

function actualizarIconoCaracteristica(int $id, array $data, ?array $fileImagen = null): array
{
    $actual = obtenerIconoCaracteristicaPorId($id);
    if (!$actual) {
        return ['ok' => false, 'error' => 'not_found'];
    }

    $payload = prepararDatosIconoCaracteristica($data, $id);
    if (!$payload['ok']) {
        return $payload;
    }

    $archivo = $actual['archivo'];
    if ($fileImagen && isset($fileImagen['tmp_name']) && $fileImagen['tmp_name'] !== '') {
        $nuevoArchivo = guardarImagenIconoCaracteristica($fileImagen);
        if ($nuevoArchivo === null) {
            return ['ok' => false, 'error' => 'upload_failed'];
        }

        if (!empty($archivo)) {
            $rutaAnterior = __DIR__ . '/../public/img/iconos/' . $archivo;
            if (is_file($rutaAnterior)) {
                @unlink($rutaAnterior);
            }
        }

        $archivo = $nuevoArchivo;
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "UPDATE iconos_caracteristica
         SET clave = :clave,
             nombre = :nombre,
             archivo = :archivo,
             orden = :orden,
             activo = :activo
         WHERE id = :id"
    );
    $ok = $stmt->execute([
        ':id' => $id,
        ':clave' => $payload['clave'],
        ':nombre' => $payload['nombre'],
        ':archivo' => $archivo,
        ':orden' => $payload['orden'],
        ':activo' => $payload['activo'],
    ]);

    if ($ok && $actual['clave'] !== $payload['clave'] && existeTablaPropiedadCaracteristicas()) {
        $stmtProp = $pdo->prepare(
            "UPDATE propiedad_caracteristicas
             SET icono = ?
             WHERE icono = ?"
        );
        $stmtProp->execute([$payload['clave'], $actual['clave']]);
    }

    return ['ok' => $ok];
}

function eliminarIconoCaracteristica(int $id): array
{
    $actual = obtenerIconoCaracteristicaPorId($id);
    if (!$actual) {
        return ['ok' => false, 'error' => 'not_found'];
    }

    if (contarUsoIconoCaracteristica((string)$actual['clave']) > 0) {
        return ['ok' => false, 'error' => 'in_use'];
    }

    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM iconos_caracteristica WHERE id = ?");
    $ok = $stmt->execute([$id]);

    if ($ok && !empty($actual['archivo'])) {
        $ruta = __DIR__ . '/../public/img/iconos/' . $actual['archivo'];
        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }

    return ['ok' => $ok];
}

function existeTablaProvincias(): bool
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    global $pdo;

    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'provincias'");
        $cache = (bool) $stmt->fetchColumn();
    } catch (Throwable $e) {
        $cache = false;
    }

    return $cache;
}

function existeTablaCiudades(): bool
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    global $pdo;

    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'ciudades'");
        $cache = (bool) $stmt->fetchColumn();
    } catch (Throwable $e) {
        $cache = false;
    }

    return $cache;
}

function obtenerProvincias(bool $incluirInactivas = true): array
{
    if (!existeTablaProvincias()) {
        return [];
    }

    global $pdo;

    $sql = "SELECT id, nombre, orden, activo
            FROM provincias";
    if (!$incluirInactivas) {
        $sql .= " WHERE activo = 1";
    }
    $sql .= " ORDER BY orden ASC, nombre ASC, id ASC";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll() ?: [];
}

function obtenerProvinciaPorId(int $id): ?array
{
    if ($id <= 0 || !existeTablaProvincias()) {
        return null;
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT id, nombre, orden, activo
         FROM provincias
         WHERE id = ?
         LIMIT 1"
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function obtenerCiudades(bool $incluirInactivas = true): array
{
    if (!existeTablaCiudades() || !existeTablaProvincias()) {
        return [];
    }

    global $pdo;

    $sql = "SELECT c.id, c.nombre, c.provincia_id, c.activo, c.created_at,
                   p.nombre AS provincia_nombre, p.orden AS provincia_orden
            FROM ciudades c
            INNER JOIN provincias p ON p.id = c.provincia_id";
    if (!$incluirInactivas) {
        $sql .= " WHERE c.activo = 1";
    }
    $sql .= " ORDER BY p.orden ASC, p.nombre ASC, c.nombre ASC, c.id ASC";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll() ?: [];
}

function obtenerCiudadPorId(int $id): ?array
{
    if ($id <= 0 || !existeTablaCiudades() || !existeTablaProvincias()) {
        return null;
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT c.id, c.nombre, c.provincia_id, c.activo, c.created_at,
                p.nombre AS provincia_nombre, p.orden AS provincia_orden
         FROM ciudades c
         INNER JOIN provincias p ON p.id = c.provincia_id
         WHERE c.id = ?
         LIMIT 1"
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function obtenerCiudadPorNombreYProvincia(string $nombreCiudad, string $nombreProvincia): ?array
{
    $nombreCiudad = normalizarNombreUbicacion($nombreCiudad);
    $nombreProvincia = normalizarNombreUbicacion($nombreProvincia);

    if ($nombreCiudad === '' || $nombreProvincia === '' || !existeTablaCiudades() || !existeTablaProvincias()) {
        return null;
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT c.id, c.nombre, c.provincia_id, c.activo, c.created_at,
                p.nombre AS provincia_nombre, p.orden AS provincia_orden
         FROM ciudades c
         INNER JOIN provincias p ON p.id = c.provincia_id
         WHERE LOWER(c.nombre) = LOWER(:ciudad)
           AND LOWER(p.nombre) = LOWER(:provincia)
         LIMIT 1"
    );
    $stmt->execute([
        ':ciudad' => $nombreCiudad,
        ':provincia' => $nombreProvincia,
    ]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function normalizarNombreUbicacion(string $texto): string
{
    $texto = trim($texto);
    $texto = preg_replace('/\s+/', ' ', $texto) ?? '';

    return $texto;
}

function resolverUbicacionPropiedad(array $data): array
{
    $pais = trim((string) ($data['pais'] ?? 'Argentina'));
    $ciudad = normalizarNombreUbicacion((string) ($data['ciudad'] ?? ''));
    $provincia = normalizarNombreUbicacion((string) ($data['provincia'] ?? ''));
    $ciudadId = isset($data['ciudad_id']) ? (int) $data['ciudad_id'] : 0;
    $provinciaId = isset($data['provincia_id']) ? (int) $data['provincia_id'] : 0;

    if ($ciudadId > 0) {
        $ciudadRow = obtenerCiudadPorId($ciudadId);
        if ($ciudadRow) {
            $ciudad = (string) $ciudadRow['nombre'];
            $provincia = (string) $ciudadRow['provincia_nombre'];
            $provinciaId = (int) $ciudadRow['provincia_id'];
        }
    }

    if ($provinciaId > 0 && $provincia === '') {
        $provinciaRow = obtenerProvinciaPorId($provinciaId);
        if ($provinciaRow) {
            $provincia = (string) $provinciaRow['nombre'];
        }
    }

    return [
        'ciudad' => $ciudad,
        'provincia' => $provincia,
        'pais' => $pais !== '' ? $pais : 'Argentina',
    ];
}

function prepararDatosCiudad(array $data, ?int $ignorarId = null): array
{
    $nombre = normalizarNombreUbicacion((string) ($data['nombre'] ?? ''));
    $provinciaId = isset($data['provincia_id']) ? (int) $data['provincia_id'] : 0;
    $activo = array_key_exists('activo', $data) ? (!empty($data['activo']) ? 1 : 0) : 1;

    if ($nombre === '') {
        return ['ok' => false, 'error' => 'missing_name'];
    }

    if (!existeTablaProvincias() || !existeTablaCiudades()) {
        return ['ok' => false, 'error' => 'missing_table'];
    }

    if ($provinciaId <= 0) {
        return ['ok' => false, 'error' => 'missing_province'];
    }

    $provincia = obtenerProvinciaPorId($provinciaId);
    if (!$provincia) {
        return ['ok' => false, 'error' => 'invalid_province'];
    }

    global $pdo;
    $sql = "SELECT id
            FROM ciudades
            WHERE provincia_id = :provincia_id
              AND LOWER(nombre) = LOWER(:nombre)";
    $params = [
        ':provincia_id' => $provinciaId,
        ':nombre' => $nombre,
    ];

    if ($ignorarId !== null && $ignorarId > 0) {
        $sql .= " AND id <> :id";
        $params[':id'] = $ignorarId;
    }

    $stmt = $pdo->prepare($sql . " LIMIT 1");
    $stmt->execute($params);
    if ($stmt->fetch()) {
        return ['ok' => false, 'error' => 'duplicate_city'];
    }

    return [
        'ok' => true,
        'nombre' => $nombre,
        'provincia_id' => $provinciaId,
        'activo' => $activo,
    ];
}

function crearCiudad(array $data): array
{
    $payload = prepararDatosCiudad($data);
    if (!$payload['ok']) {
        return $payload;
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "INSERT INTO ciudades (provincia_id, nombre, activo)
         VALUES (:provincia_id, :nombre, :activo)"
    );
    $stmt->execute([
        ':provincia_id' => $payload['provincia_id'],
        ':nombre' => $payload['nombre'],
        ':activo' => $payload['activo'],
    ]);

    return ['ok' => true, 'id' => (int) $pdo->lastInsertId()];
}

function actualizarCiudad(int $id, array $data): array
{
    $actual = obtenerCiudadPorId($id);
    if (!$actual) {
        return ['ok' => false, 'error' => 'not_found'];
    }

    $payload = prepararDatosCiudad($data, $id);
    if (!$payload['ok']) {
        return $payload;
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "UPDATE ciudades
         SET provincia_id = :provincia_id,
             nombre = :nombre,
             activo = :activo
         WHERE id = :id"
    );
    $ok = $stmt->execute([
        ':id' => $id,
        ':provincia_id' => $payload['provincia_id'],
        ':nombre' => $payload['nombre'],
        ':activo' => $payload['activo'],
    ]);

    return ['ok' => $ok];
}

function eliminarCiudad(int $id): array
{
    $actual = obtenerCiudadPorId($id);
    if (!$actual) {
        return ['ok' => false, 'error' => 'not_found'];
    }

    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM ciudades WHERE id = ?");
    $ok = $stmt->execute([$id]);

    return ['ok' => $ok];
}

function existeTablaPropiedadCaracteristicas(): bool
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    global $pdo;

    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'propiedad_caracteristicas'");
        $cache = (bool)$stmt->fetchColumn();
    } catch (Throwable $e) {
        $cache = false;
    }

    return $cache;
}

function existeTablaPropiedadImagenes(): bool
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    global $pdo;

    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'propiedad_imagenes'");
        $cache = (bool)$stmt->fetchColumn();
    } catch (Throwable $e) {
        $cache = false;
    }

    return $cache;
}

/**
 * PROPIEDADES (Modelo básico)
 */

function mapearPropiedad(array $row): array
{
    $row['precio'] = isset($row['precio']) ? (float)$row['precio'] : 0;
    $row['precio_usd'] = isset($row['precio_usd']) ? (float)$row['precio_usd'] : 0;
    $row['ambientes'] = isset($row['ambientes']) ? (int)$row['ambientes'] : 0;
    $row['banios'] = isset($row['banios']) ? (int)$row['banios'] : 0;
    $row['cochera'] = isset($row['cochera']) ? (int)$row['cochera'] : 0;
    $row['superficie'] = isset($row['superficie']) ? (int)$row['superficie'] : 0;
    $row['destacado'] = isset($row['destacado']) ? (int)$row['destacado'] : 0;
    $row['tipo_id'] = isset($row['tipo_id']) ? (int)$row['tipo_id'] : null;
    $row['operacion_id'] = isset($row['operacion_id']) ? (int)$row['operacion_id'] : null;
    $row['estado_id'] = isset($row['estado_id']) ? (int)$row['estado_id'] : null;
    $row['lat'] = isset($row['lat']) ? (float)$row['lat'] : null;
    $row['lng'] = isset($row['lng']) ? (float)$row['lng'] : null;
    return $row;
}

function obtenerCaracteristicasPropiedad(int $propiedadId): array
{
    if (!existeTablaPropiedadCaracteristicas()) {
        return [];
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT id, icono, titulo, valor, orden
         FROM propiedad_caracteristicas
         WHERE propiedad_id = ?
         ORDER BY orden ASC, id ASC"
    );
    $stmt->execute([$propiedadId]);
    return $stmt->fetchAll() ?: [];
}

function obtenerImagenesPropiedad(int $propiedadId): array
{
    if (!existeTablaPropiedadImagenes()) {
        return [];
    }

    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT id, archivo, orden
         FROM propiedad_imagenes
         WHERE propiedad_id = ?
         ORDER BY orden ASC, id ASC"
    );
    $stmt->execute([$propiedadId]);
    return $stmt->fetchAll() ?: [];
}

function normalizarCaracteristicasPropiedad(array $data): array
{
    $iconos = $data['caracteristica_icono'] ?? [];
    $titulos = $data['caracteristica_titulo'] ?? [];
    $valores = $data['caracteristica_valor'] ?? [];
    $opcionesIcono = obtenerOpcionesIconosCaracteristica(true);
    $iconoDefault = array_key_first($opcionesIcono) ?: 'check';

    if (!is_array($iconos) || !is_array($titulos) || !is_array($valores)) {
        return [];
    }

    $total = max(count($iconos), count($titulos), count($valores));
    $items = [];

    for ($i = 0; $i < $total; $i++) {
        $icono = trim((string)($iconos[$i] ?? ''));
        $titulo = trim((string)($titulos[$i] ?? ''));
        $valor = trim((string)($valores[$i] ?? ''));

        if ($titulo === '' && $valor === '') {
            continue;
        }

        if ($icono === '' || !isset($opcionesIcono[$icono])) {
            $icono = $iconoDefault;
        }

        $items[] = [
            'icono' => $icono,
            'titulo' => $titulo !== '' ? $titulo : 'Caracteristica',
            'valor' => $valor !== '' ? $valor : '-',
            'orden' => count($items) + 1,
        ];
    }

    return $items;
}

function guardarCaracteristicasPropiedad(int $propiedadId, array $caracteristicas): void
{
    if (!existeTablaPropiedadCaracteristicas()) {
        return;
    }

    global $pdo;

    $pdo->prepare("DELETE FROM propiedad_caracteristicas WHERE propiedad_id = ?")
        ->execute([$propiedadId]);

    if ($caracteristicas === []) {
        return;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO propiedad_caracteristicas (propiedad_id, icono, titulo, valor, orden)
         VALUES (:propiedad_id, :icono, :titulo, :valor, :orden)"
    );

    foreach ($caracteristicas as $item) {
        $stmt->execute([
            ':propiedad_id' => $propiedadId,
            ':icono' => $item['icono'],
            ':titulo' => $item['titulo'],
            ':valor' => $item['valor'],
            ':orden' => $item['orden'],
        ]);
    }
}

function normalizarArchivosMultiples(?array $files): array
{
    if (!$files || !isset($files['name']) || !is_array($files['name'])) {
        return [];
    }

    $normalizados = [];
    $total = count($files['name']);

    for ($i = 0; $i < $total; $i++) {
        $error = $files['error'][$i] ?? UPLOAD_ERR_NO_FILE;
        $tmpName = $files['tmp_name'][$i] ?? '';

        if ($error === UPLOAD_ERR_NO_FILE || $tmpName === '') {
            continue;
        }

        $normalizados[] = [
            'name' => $files['name'][$i] ?? '',
            'type' => $files['type'][$i] ?? '',
            'tmp_name' => $tmpName,
            'error' => $error,
            'size' => $files['size'][$i] ?? 0,
        ];
    }

    return $normalizados;
}

function guardarImagenesPropiedad(int $propiedadId, ?array $files): void
{
    if (!existeTablaPropiedadImagenes()) {
        return;
    }

    $archivos = normalizarArchivosMultiples($files);
    if ($archivos === []) {
        return;
    }

    global $pdo;

    $stmtOrden = $pdo->prepare("SELECT COALESCE(MAX(orden), 0) FROM propiedad_imagenes WHERE propiedad_id = ?");
    $stmtOrden->execute([$propiedadId]);
    $orden = (int)$stmtOrden->fetchColumn();

    $stmt = $pdo->prepare(
        "INSERT INTO propiedad_imagenes (propiedad_id, archivo, orden)
         VALUES (:propiedad_id, :archivo, :orden)"
    );

    foreach ($archivos as $archivo) {
        $nombre = guardarImagen($archivo);
        if ($nombre === null) {
            continue;
        }

        $orden++;
        $stmt->execute([
            ':propiedad_id' => $propiedadId,
            ':archivo' => $nombre,
            ':orden' => $orden,
        ]);
    }
}

function eliminarImagenesPropiedadPorIds(int $propiedadId, array $ids): void
{
    if (!existeTablaPropiedadImagenes() || $ids === []) {
        return;
    }

    $ids = array_values(array_unique(array_filter(array_map('intval', $ids), fn($id) => $id > 0)));
    if ($ids === []) {
        return;
    }

    global $pdo;

    $placeholders = implode(', ', array_fill(0, count($ids), '?'));
    $params = array_merge([$propiedadId], $ids);

    $stmt = $pdo->prepare(
        "SELECT archivo FROM propiedad_imagenes
         WHERE propiedad_id = ? AND id IN ($placeholders)"
    );
    $stmt->execute($params);
    $imagenes = $stmt->fetchAll() ?: [];

    $stmtDelete = $pdo->prepare(
        "DELETE FROM propiedad_imagenes
         WHERE propiedad_id = ? AND id IN ($placeholders)"
    );
    $stmtDelete->execute($params);

    foreach ($imagenes as $imagen) {
        if (empty($imagen['archivo'])) {
            continue;
        }

        $ruta = __DIR__ . '/../public/img/' . $imagen['archivo'];
        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }
}

function construirGaleriaPropiedad(array $propiedad): array
{
    $galeria = [];

    if (!empty($propiedad['imagen'])) {
        $galeria[] = [
            'id' => null,
            'archivo' => $propiedad['imagen'],
            'orden' => 0,
            'principal' => true,
        ];
    }

    foreach ($propiedad['imagenes_secundarias'] ?? [] as $imagen) {
        if (empty($imagen['archivo']) || $imagen['archivo'] === ($propiedad['imagen'] ?? null)) {
            continue;
        }

        $imagen['principal'] = false;
        $galeria[] = $imagen;
    }

    return $galeria;
}

function renderizarIconoCaracteristica(string $icono): string
{
    static $mapaIconos = null;

    if ($mapaIconos === null) {
        $mapaIconos = [];
        foreach (obtenerIconosCaracteristica(true) as $item) {
            $mapaIconos[(string)$item['clave']] = $item;
        }
    }

    $claseIcono = normalizarClaseIconoFuente($icono);
    if ($claseIcono !== null) {
        return '<span class="' . htmlspecialchars($claseIcono, ENT_QUOTES, 'UTF-8') . '" aria-hidden="true"></span>';
    }

    if (isset($mapaIconos[$icono]) && !empty($mapaIconos[$icono]['archivo'])) {
        $src = publicAssetUrl('img/iconos/' . $mapaIconos[$icono]['archivo']);
        return '<img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" alt="" class="h-7 w-7 object-contain" loading="lazy">';
    }

    $svgBase = 'width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
        . 'stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';

    switch ($icono) {
        case 'rooms':
            return '<svg ' . $svgBase . '><path d="M4 20V8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12"/><path d="M4 12h16"/><path d="M9 8v4"/><path d="M15 8v4"/></svg>';
        case 'bed':
            return '<svg ' . $svgBase . '><path d="M3 19v-8"/><path d="M21 19v-6a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v6"/><path d="M3 19h18"/><path d="M7 11V8a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3"/></svg>';
        case 'bath':
            return '<svg ' . $svgBase . '><path d="M4 13h16"/><path d="M6 13V8a2 2 0 0 1 2-2h3"/><path d="M17 13v3a3 3 0 0 1-3 3H10a3 3 0 0 1-3-3v-3"/><path d="M15 7h2a2 2 0 0 1 0 4h-1"/></svg>';
        case 'garage':
            return '<svg ' . $svgBase . '><path d="M3 11 12 4l9 7"/><path d="M5 10v9h14v-9"/><path d="M8 19v-5h8v5"/></svg>';
        case 'area':
            return '<svg ' . $svgBase . '><path d="M4 4h6v6H4z"/><path d="M14 4h6v6h-6z"/><path d="M4 14h6v6H4z"/><path d="M14 14h6v6h-6z"/></svg>';
        case 'calendar':
            return '<svg ' . $svgBase . '><path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/></svg>';
        case 'view':
            return '<svg ' . $svgBase . '><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>';
        case 'home':
            return '<svg ' . $svgBase . '><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/></svg>';
        case 'building':
            return '<svg ' . $svgBase . '><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 7h.01"/><path d="M12 7h.01"/><path d="M16 7h.01"/><path d="M8 11h.01"/><path d="M12 11h.01"/><path d="M16 11h.01"/><path d="M10 21v-4h4v4"/></svg>';
        default:
            return '<svg ' . $svgBase . '><path d="m5 12 4 4L19 6"/></svg>';
    }
}

function obtenerPropiedadesDestacadas(?int $limit = null): array
{
    global $pdo;
    $sql = "SELECT p.*, 
                   o.nombre AS operacion_nombre, 
                   t.nombre AS tipo_nombre,
                   e.nombre AS estado_nombre
            FROM propiedades p
            LEFT JOIN operaciones o ON p.operacion_id = o.id
            LEFT JOIN tipos_propiedad t ON p.tipo_id = t.id
            LEFT JOIN estados_propiedad e ON p.estado_id = e.id
            WHERE p.destacado = 1
            ORDER BY p.created_at DESC";
    if ($limit !== null && $limit > 0) {
        $sql .= " LIMIT ?";
    }
    $stmt = $pdo->prepare($sql);
    if ($limit !== null && $limit > 0) {
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll();
    return array_map('mapearPropiedad', $rows);
}

/**
 * Lista de propiedades con filtros opcionales.
 */
function buscarPropiedades(array $filtros = []): array
{
    global $pdo;

    $where = [];
    $params = [];
    $moneda = strtoupper((string)($filtros['moneda'] ?? 'ARS'));
    $columnaPrecio = $moneda === 'USD' ? 'p.precio_usd' : 'p.precio';

    if (!empty($filtros['ciudad'])) {
        $where[] = "p.ciudad LIKE ?";
        $params[] = '%' . $filtros['ciudad'] . '%';
    }
    if (!empty($filtros['operacion_id'])) {
        $where[] = "p.operacion_id = ?";
        $params[] = (int)$filtros['operacion_id'];
    }
    if (!empty($filtros['tipo_id'])) {
        $where[] = "p.tipo_id = ?";
        $params[] = (int)$filtros['tipo_id'];
    }
    if (!empty($filtros['estado_id'])) {
        $where[] = "p.estado_id = ?";
        $params[] = (int)$filtros['estado_id'];
    }
    if (!empty($filtros['precio_min'])) {
        $where[] = "{$columnaPrecio} >= ?";
        $params[] = (float)$filtros['precio_min'];
    }
    if (!empty($filtros['precio_max'])) {
        $where[] = "{$columnaPrecio} <= ?";
        $params[] = (float)$filtros['precio_max'];
    }

    $sql = "SELECT p.*, 
               o.nombre AS operacion_nombre, 
               t.nombre AS tipo_nombre,
               e.nombre AS estado_nombre
        FROM propiedades p
        LEFT JOIN operaciones o ON p.operacion_id = o.id
        LEFT JOIN tipos_propiedad t ON p.tipo_id = t.id
        LEFT JOIN estados_propiedad e ON p.estado_id = e.id";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY p.destacado DESC, p.created_at DESC";


    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    return array_map('mapearPropiedad', $rows);
}

function obtenerPropiedadPorId(int $id): ?array
{
    global $pdo;
    $sql = "SELECT p.*, 
                   o.nombre AS operacion_nombre, 
                   t.nombre AS tipo_nombre,
                   e.nombre AS estado_nombre
            FROM propiedades p
            LEFT JOIN operaciones o ON p.operacion_id = o.id
            LEFT JOIN tipos_propiedad t ON p.tipo_id = t.id
            LEFT JOIN estados_propiedad e ON p.estado_id = e.id
            WHERE p.id = ?
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }

    $propiedad = mapearPropiedad($row);
    $propiedad['caracteristicas'] = obtenerCaracteristicasPropiedad($id);
    $propiedad['imagenes_secundarias'] = obtenerImagenesPropiedad($id);
    $propiedad['galeria'] = construirGaleriaPropiedad($propiedad);
    return $propiedad;
}

function normalizarCoordenada($value): ?float
{
    if ($value === null) {
        return null;
    }

    $value = trim((string)$value);
    if ($value === '') {
        return null;
    }

    $value = str_replace(',', '.', $value);
    return is_numeric($value) ? (float)$value : null;
}

/**
 * CRUD desde admin
 */

function crearPropiedad(array $data, ?array $fileImagen = null, ?array $filesGaleria = null): int
{
    global $pdo;
    $ubicacion = resolverUbicacionPropiedad($data);

    $imagen = null;
    if ($fileImagen && isset($fileImagen['tmp_name']) && $fileImagen['tmp_name'] !== '') {
        $imagen = guardarImagen($fileImagen);
    }

    // Estado por defecto: Disponible (id = 1), si no viene nada
    $estadoId = !empty($data['estado_id']) ? (int)$data['estado_id'] : 1;
    $lat = normalizarCoordenada($data['lat'] ?? null);
    $lng = normalizarCoordenada($data['lng'] ?? null);

    $sql = "INSERT INTO propiedades 
        (titulo, descripcion, precio, precio_usd, direccion, ciudad, provincia, pais, 
         tipo_id, operacion_id, estado_id,
         ambientes, banios, cochera, superficie, lat, lng, imagen, destacado)
        VALUES
        (:titulo, :descripcion, :precio, :precio_usd, :direccion, :ciudad, :provincia, :pais,
         :tipo_id, :operacion_id, :estado_id,
         :ambientes, :banios, :cochera, :superficie, :lat, :lng, :imagen, :destacado)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titulo'       => $data['titulo'] ?? '',
        ':descripcion'  => $data['descripcion'] ?? '',
        ':precio'       => $data['precio'] ?? 0,
        ':precio_usd'   => $data['precio_usd'] ?? 0,
        ':direccion'    => $data['direccion'] ?? '',
        ':ciudad'       => $ubicacion['ciudad'],
        ':provincia'    => $ubicacion['provincia'],
        ':pais'         => $ubicacion['pais'],
        ':tipo_id'      => !empty($data['tipo_id']) ? (int)$data['tipo_id'] : null,
        ':operacion_id' => !empty($data['operacion_id']) ? (int)$data['operacion_id'] : null,
        ':estado_id'    => $estadoId,
        ':ambientes'    => $data['ambientes'] ?? 0,
        ':banios'       => $data['banios'] ?? 0,
        ':cochera'      => !empty($data['cochera']) ? 1 : 0,
        ':superficie'   => $data['superficie'] ?? 0,
        ':lat'          => $lat,
        ':lng'          => $lng,
        ':imagen'       => $imagen,
        ':destacado'    => !empty($data['destacado']) ? 1 : 0,
    ]);

    $propiedadId = (int)$pdo->lastInsertId();
    guardarCaracteristicasPropiedad($propiedadId, normalizarCaracteristicasPropiedad($data));
    guardarImagenesPropiedad($propiedadId, $filesGaleria);

    return $propiedadId;
}

function actualizarPropiedad(int $id, array $data, ?array $fileImagen = null, ?array $filesGaleria = null): bool
{
    global $pdo;
    $ubicacion = resolverUbicacionPropiedad($data);

    $prop = obtenerPropiedadPorId($id);
    if (!$prop) return false;

    $imagen = $prop['imagen'];

    if ($fileImagen && isset($fileImagen['tmp_name']) && $fileImagen['tmp_name'] !== '') {
        if ($imagen) {
            $rutaAnterior = __DIR__ . '/../public/img/' . $imagen;
            if (file_exists($rutaAnterior)) {
                @unlink($rutaAnterior);
            }
        }
        $imagen = guardarImagen($fileImagen);
    }

    $estadoId = !empty($data['estado_id']) ? (int)$data['estado_id'] : $prop['estado_id'];
    $lat = normalizarCoordenada($data['lat'] ?? null);
    $lng = normalizarCoordenada($data['lng'] ?? null);

    $sql = "UPDATE propiedades SET
        titulo = :titulo,
        descripcion = :descripcion,
        precio = :precio,
        precio_usd = :precio_usd,
        direccion = :direccion,
        ciudad = :ciudad,
        provincia = :provincia,
        pais = :pais,
        tipo_id = :tipo_id,
        operacion_id = :operacion_id,
        estado_id = :estado_id,
        ambientes = :ambientes,
        banios = :banios,
        cochera = :cochera,
        superficie = :superficie,
        lat = :lat,
        lng = :lng,
        imagen = :imagen,
        destacado = :destacado
        WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        ':id'           => $id,
        ':titulo'       => $data['titulo'] ?? '',
        ':descripcion'  => $data['descripcion'] ?? '',
        ':precio'       => $data['precio'] ?? 0,
        ':precio_usd'   => $data['precio_usd'] ?? 0,
        ':direccion'    => $data['direccion'] ?? '',
        ':ciudad'       => $ubicacion['ciudad'],
        ':provincia'    => $ubicacion['provincia'],
        ':pais'         => $ubicacion['pais'],
        ':tipo_id'      => !empty($data['tipo_id']) ? (int)$data['tipo_id'] : null,
        ':operacion_id' => !empty($data['operacion_id']) ? (int)$data['operacion_id'] : null,
        ':estado_id'    => $estadoId,
        ':ambientes'    => array_key_exists('ambientes', $data) ? (int)$data['ambientes'] : (int)$prop['ambientes'],
        ':banios'       => array_key_exists('banios', $data) ? (int)$data['banios'] : (int)$prop['banios'],
        ':cochera'      => array_key_exists('cochera', $data) ? (!empty($data['cochera']) ? 1 : 0) : (int)$prop['cochera'],
        ':superficie'   => array_key_exists('superficie', $data) ? (int)$data['superficie'] : (int)$prop['superficie'],
        ':lat'          => $lat,
        ':lng'          => $lng,
        ':imagen'       => $imagen,
        ':destacado'    => !empty($data['destacado']) ? 1 : 0,
    ]);

    if ($ok) {
        guardarCaracteristicasPropiedad($id, normalizarCaracteristicasPropiedad($data));
        eliminarImagenesPropiedadPorIds($id, $data['eliminar_galeria_ids'] ?? []);
        guardarImagenesPropiedad($id, $filesGaleria);
    }

    return $ok;
}

function eliminarPropiedad(int $id): bool
{
    global $pdo;
    $prop = obtenerPropiedadPorId($id);
    if ($prop && !empty($prop['imagen'])) {
        $ruta = __DIR__ . '/../public/img/' . $prop['imagen'];
        if (file_exists($ruta)) {
            @unlink($ruta);
        }
    }
    foreach (($prop['imagenes_secundarias'] ?? []) as $imagen) {
        if (empty($imagen['archivo'])) {
            continue;
        }

        $ruta = __DIR__ . '/../public/img/' . $imagen['archivo'];
        if (file_exists($ruta)) {
            @unlink($ruta);
        }
    }
    $pdo->prepare("DELETE FROM propiedad_imagenes WHERE propiedad_id = ?")
        ->execute([$id]);
    $pdo->prepare("DELETE FROM propiedad_caracteristicas WHERE propiedad_id = ?")
        ->execute([$id]);
    $stmt = $pdo->prepare("DELETE FROM propiedades WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * Manejo de imágenes
 */
function asegurarDirectorio(string $ruta): void
{
    if (!is_dir($ruta)) {
        @mkdir($ruta, 0775, true);
    }
}

function guardarArchivoSubido(array $file, string $prefijo, string $directorioRelativo): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $ext = strtolower((string)$ext);
    if ($ext === '') {
        return null;
    }

    $directorio = __DIR__ . '/../public/' . trim($directorioRelativo, '/');
    asegurarDirectorio($directorio);

    $nombre = uniqid($prefijo, true) . '.' . $ext;
    $destino = $directorio . '/' . $nombre;
    if (!move_uploaded_file($file['tmp_name'], $destino)) {
        return null;
    }

    return $nombre;
}

function guardarImagen(array $file): ?string
{
    return guardarArchivoSubido($file, 'prop_', 'img');
}

function guardarImagenIconoCaracteristica(array $file): ?string
{
    return guardarArchivoSubido($file, 'icono_', 'img/iconos');
}

function guardarImagenStaff(array $file): ?string
{
    return guardarArchivoSubido($file, 'staff_', 'img');
}
?>
