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

/**
 * Catálogos: operaciones, tipos de propiedad, estados
 */
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

function obtenerPropiedadesDestacadas(int $limit = 6): array
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
            ORDER BY p.created_at DESC
            LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
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
        $where[] = "p.precio >= ?";
        $params[] = (float)$filtros['precio_min'];
    }
    if (!empty($filtros['precio_max'])) {
        $where[] = "p.precio <= ?";
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
    return $row ? mapearPropiedad($row) : null;
}

/**
 * CRUD desde admin
 */

function crearPropiedad(array $data, ?array $fileImagen = null): int
{
    global $pdo;

    $imagen = null;
    if ($fileImagen && isset($fileImagen['tmp_name']) && $fileImagen['tmp_name'] !== '') {
        $imagen = guardarImagen($fileImagen);
    }

    // Estado por defecto: Disponible (id = 1), si no viene nada
    $estadoId = !empty($data['estado_id']) ? (int)$data['estado_id'] : 1;

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
        ':ciudad'       => $data['ciudad'] ?? '',
        ':provincia'    => $data['provincia'] ?? '',
        ':pais'         => $data['pais'] ?? 'Argentina',
        ':tipo_id'      => !empty($data['tipo_id']) ? (int)$data['tipo_id'] : null,
        ':operacion_id' => !empty($data['operacion_id']) ? (int)$data['operacion_id'] : null,
        ':estado_id'    => $estadoId,
        ':ambientes'    => $data['ambientes'] ?? 0,
        ':banios'       => $data['banios'] ?? 0,
        ':cochera'      => !empty($data['cochera']) ? 1 : 0,
        ':superficie'   => $data['superficie'] ?? 0,
        ':lat'          => $data['lat'] ?? null,
        ':lng'          => $data['lng'] ?? null,
        ':imagen'       => $imagen,
        ':destacado'    => !empty($data['destacado']) ? 1 : 0,
    ]);

    return (int)$pdo->lastInsertId();
}

function actualizarPropiedad(int $id, array $data, ?array $fileImagen = null): bool
{
    global $pdo;

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
    return $stmt->execute([
        ':id'           => $id,
        ':titulo'       => $data['titulo'] ?? '',
        ':descripcion'  => $data['descripcion'] ?? '',
        ':precio'       => $data['precio'] ?? 0,
        ':precio_usd'   => $data['precio_usd'] ?? 0,
        ':direccion'    => $data['direccion'] ?? '',
        ':ciudad'       => $data['ciudad'] ?? '',
        ':provincia'    => $data['provincia'] ?? '',
        ':pais'         => $data['pais'] ?? 'Argentina',
        ':tipo_id'      => !empty($data['tipo_id']) ? (int)$data['tipo_id'] : null,
        ':operacion_id' => !empty($data['operacion_id']) ? (int)$data['operacion_id'] : null,
        ':estado_id'    => $estadoId,
        ':ambientes'    => $data['ambientes'] ?? 0,
        ':banios'       => $data['banios'] ?? 0,
        ':cochera'      => !empty($data['cochera']) ? 1 : 0,
        ':superficie'   => $data['superficie'] ?? 0,
        ':lat'          => $data['lat'] ?? null,
        ':lng'          => $data['lng'] ?? null,
        ':imagen'       => $imagen,
        ':destacado'    => !empty($data['destacado']) ? 1 : 0,
    ]);
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
    $stmt = $pdo->prepare("DELETE FROM propiedades WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * Manejo de imágenes
 */

/**
 * IMÁGENES ADICIONALES DE PROPIEDADES
 */

function obtenerImagenesPropiedad(int $propiedadId): array
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, propiedad_id, ruta, orden, creado_en FROM imagenes_propiedades WHERE propiedad_id = :propiedad_id ORDER BY orden ASC, id ASC");
    $stmt->execute([':propiedad_id' => $propiedadId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function obtenerImagenPropiedadPorId(int $id): ?array
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, propiedad_id, ruta, orden, creado_en FROM imagenes_propiedades WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function agregarImagenPropiedad(int $propiedadId, string $ruta, int $orden = 0): bool
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO imagenes_propiedades (propiedad_id, ruta, orden) VALUES (:propiedad_id, :ruta, :orden)");
    return $stmt->execute([
        ':propiedad_id' => $propiedadId,
        ':ruta'         => $ruta,
        ':orden'        => $orden,
    ]);
}

function eliminarImagenPropiedad(int $id): bool
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM imagenes_propiedades WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}


function guardarImagen(array $file): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nombre = uniqid('prop_') . '.' . strtolower($ext);
    $destino = __DIR__ . '/../public/img/' . $nombre;
    if (!move_uploaded_file($file['tmp_name'], $destino)) {
        return null;
    }
    return $nombre;
}
?>
