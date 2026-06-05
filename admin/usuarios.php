<?php
require_once __DIR__ . '/../backend/funciones.php';
requerirPermisoAdmin('usuarios');

$tablaUsuariosDisponible = existeTablaUsuarios();
$usuarios = obtenerUsuarios();
$usuarioEditando = ($tablaUsuariosDisponible && isset($_GET['id'])) ? obtenerUsuarioPorId((int) $_GET['id']) : null;
$mostrarFormulario = $usuarioEditando !== null || isset($_GET['new']);
$seccionesAdmin = obtenerSeccionesAdmin();
$errores = [
    'missing_name' => 'El nombre es obligatorio.',
    'missing_email' => 'El email es obligatorio.',
    'invalid_email' => 'El email no tiene un formato valido.',
    'email_taken' => 'Ya existe un usuario con ese email.',
    'missing_permissions' => 'Debes habilitar al menos una seccion para el usuario.',
    'missing_password' => 'La contraseña es obligatoria.',
    'password_short' => 'La contraseña debe tener al menos 6 caracteres.',
    'password_mismatch' => 'Las contraseñas no coinciden.',
    'missing_table' => 'Falta crear la tabla de usuarios en la base de datos.',
    'not_found' => 'El usuario solicitado no existe.',
    'self_delete' => 'No podes eliminar el usuario con la sesion actual.',
];
$error = isset($_GET['err']) ? ($errores[$_GET['err']] ?? 'No se pudo completar la operacion.') : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios | Inmobiliaria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/tailwind.css')) ?>?v=<?= publicAssetVersion('css/tailwind.css') ?>" rel="stylesheet">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/theme-overrides.css')) ?>?v=<?= publicAssetVersion('css/theme-overrides.css') ?>" rel="stylesheet">
    <style>
        .password-toggle-field {
            position: relative;
        }

        .password-toggle-input {
            padding-right: 3.25rem;
        }

        .password-toggle-button {
            position: absolute;
            top: 50%;
            right: 0.85rem;
            transform: translateY(-50%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border: 0;
            background: transparent;
            color: rgb(100 116 139);
            cursor: pointer;
        }

        .password-toggle-button:hover {
            color: rgb(15 23 42);
        }
    </style>
</head>
<body>
    <header class="app-header">
        <div class="app-header-inner">
            <div class="app-brand">
                <span class="app-brand-mark">IA</span>
                <div class="app-brand-copy">
                    <p class="app-brand-title">Inmobiliaria Argentina</p>
                    <p class="app-brand-subtitle">ABM de usuarios administradores</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-xs md:text-sm">
                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-2 text-slate-200">
                    Hola, <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin') ?>
                </span>
                <a href="../backend/logout.php" class="btn-secondary bg-white/10 text-white hover:bg-white/15 hover:text-white">
                    Cerrar sesion
                </a>
            </div>
        </div>
    </header>

    <main class="admin-layout">
        <?= renderAdminSidebar('usuarios') ?>

        <div class="admin-content">
            <section class="hero-panel">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <span class="eyebrow">Administracion</span>
                        <h1 class="section-heading mb-2">Usuarios</h1>
                        <p class="section-copy">Da de alta accesos al panel, actualiza credenciales y controla quienes pueden ingresar al admin.</p>
                    </div>
                    <a href="usuarios.php?new=1" class="btn-secondary">Nuevo usuario</a>
                </div>
            </section>

            <?php if (isset($_GET['ok'])): ?>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Usuario guardado correctamente.
                </div>
            <?php elseif (isset($_GET['del'])): ?>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Usuario eliminado correctamente.
                </div>
            <?php elseif ($error): ?>
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!$tablaUsuariosDisponible): ?>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    La tabla de usuarios todavia no existe en la base de datos.
                </div>
            <?php endif; ?>

            <section class="admin-split">
                <?php if ($mostrarFormulario): ?>
                    <form action="../backend/<?= $usuarioEditando ? 'editar_usuario.php' : 'crear_usuario.php' ?>" method="POST" class="admin-form admin-split-form max-w-2xl space-y-4 text-sm">
                        <?php if ($usuarioEditando): ?>
                            <input type="hidden" name="id" value="<?= (int) $usuarioEditando['id'] ?>">
                        <?php endif; ?>

                        <div>
                            <span class="eyebrow"><?= $usuarioEditando ? 'Edicion' : 'Alta' ?></span>
                            <h2 class="section-heading mb-2 text-2xl"><?= $usuarioEditando ? 'Editar usuario' : 'Nuevo usuario' ?></h2>
                            <p class="section-copy">
                                <?= $usuarioEditando ? 'Podes cambiar nombre y email. La contraseña solo se actualiza si completas ambos campos.' : 'Crea un nuevo acceso para el panel administrativo.' ?>
                            </p>
                        </div>

                        <div>
                            <label class="field-label">Nombre</label>
                            <input type="text" name="nombre" required value="<?= htmlspecialchars($usuarioEditando['nombre'] ?? '') ?>" class="field-input" placeholder="Ej. Laura Garcia">
                        </div>

                        <div>
                            <label class="field-label">Email</label>
                            <input type="email" name="email" required value="<?= htmlspecialchars($usuarioEditando['email'] ?? '') ?>" class="field-input" placeholder="admin@dominio.com">
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="field-label"><?= $usuarioEditando ? 'Nueva contraseña' : 'Contraseña' ?></label>
                                <div class="password-toggle-field">
                                    <input type="password" name="password" <?= $usuarioEditando ? '' : 'required' ?> class="field-input password-toggle-input" placeholder="Minimo 6 caracteres" data-password-toggle-target>
                                    <button type="button" class="password-toggle-button" data-password-toggle aria-label="Mostrar contraseña" aria-pressed="false">
                                        <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="m3 3 18 18"></path>
                                            <path d="M10.6 10.7a3 3 0 0 0 4.2 4.2"></path>
                                            <path d="M9.4 5.1A10.9 10.9 0 0 1 12 5c6.5 0 10 7 10 7a13.2 13.2 0 0 1-4 4.9"></path>
                                            <path d="M6.6 6.7C4 8.6 2 12 2 12a13.3 13.3 0 0 0 10 7 10.7 10.7 0 0 0 2.5-.3"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="field-label"><?= $usuarioEditando ? 'Repetir nueva contraseña' : 'Repetir contraseña' ?></label>
                                <div class="password-toggle-field">
                                    <input type="password" name="password_confirm" <?= $usuarioEditando ? '' : 'required' ?> class="field-input password-toggle-input" placeholder="Repeti la contraseña" data-password-toggle-target>
                                    <button type="button" class="password-toggle-button" data-password-toggle aria-label="Mostrar contraseña" aria-pressed="false">
                                        <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="m3 3 18 18"></path>
                                            <path d="M10.6 10.7a3 3 0 0 0 4.2 4.2"></path>
                                            <path d="M9.4 5.1A10.9 10.9 0 0 1 12 5c6.5 0 10 7 10 7a13.2 13.2 0 0 1-4 4.9"></path>
                                            <path d="M6.6 6.7C4 8.6 2 12 2 12a13.3 13.3 0 0 0 10 7 10.7 10.7 0 0 0 2.5-.3"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <?php if ($usuarioEditando): ?>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600">
                                La contraseña actual no se puede mostrar porque se guarda de forma segura. Si completas estos campos, reemplazas la contraseña existente.
                            </div>
                        <?php endif; ?>

                        <label class="surface-card-soft flex items-center gap-3 p-4">
                            <input type="checkbox" name="activo" value="1" class="h-5 w-5 shrink-0" <?= !isset($usuarioEditando['activo']) || !empty($usuarioEditando['activo']) ? 'checked' : '' ?>>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Usuario activo</p>
                                <p class="text-xs text-slate-500">Si lo desactivas, el usuario queda guardado pero no puede iniciar sesion.</p>
                            </div>
                        </label>

                        <div class="space-y-3">
                            <div>
                                <label class="field-label">Secciones habilitadas</label>
                                <p class="text-xs text-slate-500">Definen a que partes del panel puede entrar este usuario.</p>
                            </div>
                            <div class="grid gap-3 md:grid-cols-2">
                                <?php $permisosSeleccionados = $usuarioEditando['permisos'] ?? array_keys($seccionesAdmin); ?>
                                <?php foreach ($seccionesAdmin as $clave => $meta): ?>
                                    <label class="surface-card-soft flex items-center gap-3 p-4">
                                        <input
                                            type="checkbox"
                                            name="permisos[]"
                                            value="<?= htmlspecialchars($clave) ?>"
                                            class="h-5 w-5 shrink-0"
                                            <?= in_array($clave, $permisosSeleccionados, true) ? 'checked' : '' ?>
                                        >
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($meta['label']) ?></p>
                                            <p class="text-xs text-slate-500"><?= htmlspecialchars($meta['route']) ?></p>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="surface-card-soft p-4 text-xs text-slate-500">
                            <?php if ($usuarioEditando): ?>
                                El usuario fue creado el <?= htmlspecialchars((string) ($usuarioEditando['created_at'] ?? '')) ?>.
                            <?php else: ?>
                                La contraseña se guardara de forma segura para futuros ingresos al panel.
                            <?php endif; ?>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <a href="usuarios.php" class="btn-secondary">Cancelar</a>
                            <button type="submit" class="btn-accent" <?= $tablaUsuariosDisponible ? '' : 'disabled' ?>>
                                <?= $usuarioEditando ? 'Guardar cambios' : 'Crear usuario' ?>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <section class="surface-card admin-split-table p-5 md:p-6">
                    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Usuarios cargados</h2>
                            <p class="text-sm text-slate-500">Todos los registros listados pueden iniciar sesion en el panel.</p>
                        </div>
                        <p class="shrink-0 text-sm text-slate-500"><?= count($usuarios) ?> usuario(s)</p>
                    </div>

                    <div class="table-shell w-full overflow-hidden">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Estado</th>
                                    <th>Accesos</th>
                                    <th>Alta</th>
                                    <th class="text-right">Acc.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$usuarios): ?>
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-400">Todavia no hay usuarios cargados.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($usuarios as $item): ?>
                                        <tr>
                                            <td class="text-xs text-slate-400">#<?= (int) $item['id'] ?></td>
                                            <td class="font-semibold text-slate-800">
                                                <?= htmlspecialchars($item['nombre']) ?>
                                                <?php if ((int) $item['id'] === (int) ($_SESSION['usuario_id'] ?? 0)): ?>
                                                    <div class="text-xs font-medium text-slate-400">Sesion actual</div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-sm text-slate-500"><?= htmlspecialchars($item['email']) ?></td>
                                            <td>
                                                <span class="pill <?= !empty($item['activo']) ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-100 text-slate-700' ?>">
                                                    <?= !empty($item['activo']) ? 'Activo' : 'Inactivo' ?>
                                                </span>
                                            </td>
                                            <td class="text-sm text-slate-500">
                                                <?= htmlspecialchars(implode(', ', array_map(
                                                    fn($permiso) => $seccionesAdmin[$permiso]['nav_label'] ?? $permiso,
                                                    $item['permisos'] ?? []
                                                ))) ?>
                                            </td>
                                            <td class="text-sm text-slate-500"><?= htmlspecialchars((string) ($item['created_at'] ?? '')) ?></td>
                                            <td class="text-right">
                                                <div class="inline-flex items-center gap-2">
                                                    <a href="usuarios.php?id=<?= (int) $item['id'] ?>" class="btn-primary px-3 py-2 text-xs">Editar</a>
                                                    <?php if ((int) $item['id'] !== (int) ($_SESSION['usuario_id'] ?? 0)): ?>
                                                        <form action="../backend/eliminar_usuario.php" method="POST" onsubmit="return confirm('¿Eliminar este usuario?');">
                                                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                                            <button class="btn-danger px-3 py-2 text-xs">Eliminar</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-xs text-slate-400">Sin borrado</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>
        </div>
    </main>
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            const wrapper = button.parentElement;
            const input = wrapper ? wrapper.querySelector('[data-password-toggle-target]') : null;
            if (!input) {
                return;
            }

            button.addEventListener('click', () => {
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                button.setAttribute('aria-label', isHidden ? 'Ocultar contraseña' : 'Mostrar contraseña');
                button.setAttribute('aria-pressed', isHidden ? 'true' : 'false');

                const eyeOpen = button.querySelector('[data-eye-open]');
                const eyeClosed = button.querySelector('[data-eye-closed]');
                if (eyeOpen && eyeClosed) {
                    eyeOpen.classList.toggle('hidden', isHidden);
                    eyeClosed.classList.toggle('hidden', !isHidden);
                }
            });
        });
    </script>
</body>
</html>
