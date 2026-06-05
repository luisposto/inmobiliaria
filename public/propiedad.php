<?php
require_once __DIR__ . '/../backend/funciones.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$prop = $id ? obtenerPropiedadPorId($id) : null;

if (!$prop) {
    header("Location: propiedades.php");
    exit;
}

$ubicacion = array_values(array_filter([
    $prop['direccion'] ?? '',
    $prop['ciudad'] ?? '',
    $prop['provincia'] ?? '',
]));

$ubicacionTexto = $ubicacion ? implode(', ', $ubicacion) : 'Ubicación no informada';
$tipoNombre = trim((string)($prop['tipo_nombre'] ?? 'Propiedad'));
$operacionNombre = trim((string)($prop['operacion_nombre'] ?? 'Operación'));
$estadoNombre = trim((string)($prop['estado_nombre'] ?? ''));
$galeria = $prop['galeria'] ?? [];
$galeriaSecundaria = array_slice($galeria, 1, 4);
$galeriaTotal = count($galeria);
$imagenPrincipal = !empty($galeria[0]['archivo']) ? 'img/' . rawurlencode($galeria[0]['archivo']) : null;
$precioArs = '$' . number_format((float)$prop['precio'], 0, ',', '.');
$precioUsd = !empty($prop['precio_usd']) ? 'U$S ' . number_format((float)$prop['precio_usd'], 0, ',', '.') : '';
$precioPrincipal = $precioUsd !== '' ? $precioUsd : $precioArs;
$precioSecundario = $precioUsd !== '' ? $precioArs : '';
$superficie = !empty($prop['superficie']) ? (int)$prop['superficie'] . ' m2 totales' : 'Superficie no informada';
$ambientes = !empty($prop['ambientes']) ? (int)$prop['ambientes'] . ' ambientes' : 'Ambientes no informados';
$banios = !empty($prop['banios']) ? (int)$prop['banios'] . ' baño' . ((int)$prop['banios'] !== 1 ? 's' : '') : 'Baños no informados';
$cochera = !empty($prop['cochera']) ? 'Con cochera' : 'Sin cochera informada';
$caracteristicas = $prop['caracteristicas'] ?? [];

$badgeOperacionClass = $operacionNombre === 'Venta' ? 'detail-badge detail-badge-sale' : 'detail-badge detail-badge-rent';
$badgeEstadoClass = 'detail-badge detail-badge-neutral';
if ($estadoNombre === 'Disponible') {
    $badgeEstadoClass = 'detail-badge detail-badge-available';
} elseif ($estadoNombre === 'Reservada') {
    $badgeEstadoClass = 'detail-badge detail-badge-reserved';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($prop['titulo']) ?> | Inmobiliaria Argentina</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/tailwind.css')) ?>?v=<?= publicAssetVersion('css/tailwind.css') ?>" rel="stylesheet">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/theme-overrides.css')) ?>?v=<?= publicAssetVersion('css/theme-overrides.css') ?>" rel="stylesheet">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/icon-font.css')) ?>?v=<?= publicAssetVersion('css/icon-font.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        :root {
            --page-bg: #f4f5f7;
            --surface: #ffffff;
            --surface-alt: #fafbfc;
            --line: #e6e9ef;
            --text: #1f2937;
            --muted: #6b7280;
            --muted-strong: #4b5563;
            --brand: #0a58ca;
            --brand-soft: #eaf2ff;
            --success: #0f9f6e;
            --success-soft: #e8f7f0;
            --warning: #b7791f;
            --warning-soft: #fff6df;
            --shadow-lg: 0 22px 48px rgba(15, 23, 42, 0.08);
            --shadow-md: 0 10px 28px rgba(15, 23, 42, 0.06);
        }

        body.property-page {
            margin: 0;
            background:
                radial-gradient(circle at top left, rgba(10, 88, 202, 0.08), transparent 22%),
                linear-gradient(180deg, #f8f9fb 0%, var(--page-bg) 340px, var(--page-bg) 100%);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
        }

        .site-header {
            background: rgba(255, 255, 255, 0.94);
            border-bottom: 1px solid rgba(230, 233, 239, 0.95);
            backdrop-filter: blur(14px);
            position: sticky;
            top: 0;
            z-index: 30;
        }

        .header-wrap,
        .footer-wrap,
        .page-wrap {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        .header-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            min-height: 76px;
        }

        .brand-link,
        .top-nav {
            display: flex;
            align-items: center;
        }

        .brand-link {
            gap: 12px;
            color: var(--text);
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #0a58ca, #0d6efd);
            box-shadow: 0 12px 28px rgba(13, 110, 253, 0.28);
        }

        .brand-copy strong {
            display: block;
            font-size: 15px;
            line-height: 1.1;
        }

        .brand-copy span {
            display: block;
            margin-top: 3px;
            font-size: 12px;
            color: var(--muted);
        }

        .top-nav {
            gap: 18px;
            flex-wrap: wrap;
            font-size: 14px;
            color: var(--muted-strong);
        }

        .top-nav a:hover {
            color: var(--brand);
        }

        .page-wrap {
            padding: 28px 0 56px;
        }

        .breadcrumbs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            margin-bottom: 18px;
            font-size: 13px;
            color: var(--muted);
        }

        .breadcrumbs a:hover {
            color: var(--brand);
        }

        .hero-card,
        .detail-card,
        .contact-card,
        .mini-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 24px;
            box-shadow: var(--shadow-md);
        }

        .hero-card {
            padding: 28px;
            margin-bottom: 24px;
        }

        .gallery-shell {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.95fr);
            gap: 10px;
        }

        .gallery-main,
        .gallery-thumb {
            position: relative;
            overflow: hidden;
            border: 0;
            padding: 0;
            background: #dde4ee;
            cursor: pointer;
        }

        .gallery-main {
            min-height: 560px;
            border-radius: 28px;
        }

        .gallery-side {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .gallery-thumb {
            min-height: 275px;
            border-radius: 22px;
        }

        .gallery-main img,
        .gallery-thumb img,
        .hero-placeholder {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 320px;
            background:
                linear-gradient(135deg, rgba(10, 88, 202, 0.18), rgba(255, 255, 255, 0.7)),
                #dfe6ef;
            color: var(--muted-strong);
            font-size: 16px;
            font-weight: 600;
            border-radius: 28px;
        }

        .gallery-main::after,
        .gallery-thumb::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.04), rgba(15, 23, 42, 0.18));
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .gallery-main:hover::after,
        .gallery-thumb:hover::after {
            opacity: 1;
        }

        .gallery-count,
        .gallery-open {
            position: absolute;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 0;
            font-weight: 700;
        }

        .gallery-count {
            left: 16px;
            bottom: 16px;
            min-height: 38px;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.82);
            color: #fff;
            font-size: 14px;
        }

        .gallery-open {
            right: 16px;
            bottom: 16px;
            min-height: 52px;
            padding: 0 18px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            color: #111827;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.18);
        }

        .hero-summary {
            margin-top: 22px;
        }

        .hero-topline,
        .form-grid {
            display: flex;
            flex-wrap: wrap;
        }

        .hero-topline {
            gap: 10px;
            align-items: center;
            margin-bottom: 16px;
        }

        .detail-badge {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            border-radius: 999px;
            padding: 0 14px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .detail-badge-sale {
            background: rgba(16, 185, 129, 0.18);
            color: #d7ffea;
        }

        .detail-badge-rent {
            background: rgba(59, 130, 246, 0.18);
            color: #e3efff;
        }

        .detail-badge-available {
            background: var(--success-soft);
            color: var(--success);
        }

        .detail-badge-reserved {
            background: var(--warning-soft);
            color: var(--warning);
        }

        .detail-badge-neutral {
            background: #eef2f7;
            color: var(--muted-strong);
        }

        .hero-kicker {
            margin-bottom: 12px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--brand);
        }

        .hero-title {
            margin: 0;
            max-width: 760px;
            font-size: clamp(28px, 4vw, 40px);
            line-height: 1.04;
            font-weight: 700;
        }

        .hero-location {
            margin-top: 12px;
            max-width: 760px;
            font-size: 15px;
            color: var(--muted-strong);
        }

        .gallery-modal[hidden] {
            display: none;
        }

        .gallery-modal {
            position: fixed;
            inset: 0;
            z-index: 120;
            padding: 28px 18px;
            background: rgba(15, 23, 42, 0.84);
            backdrop-filter: blur(10px);
        }

        .gallery-modal-dialog {
            width: min(1180px, 100%);
            max-height: calc(100vh - 56px);
            margin: 0 auto;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
            gap: 16px;
            padding: 20px;
            border-radius: 28px;
            background: rgba(10, 15, 24, 0.94);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .gallery-modal-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            color: #fff;
        }

        .gallery-modal-top strong {
            font-size: 18px;
        }

        .gallery-modal-close {
            min-height: 42px;
            padding: 0 16px;
            border: 0;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .gallery-modal-stage {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 0;
            border-radius: 22px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.04);
        }

        .gallery-modal-stage img {
            width: 100%;
            height: 100%;
            max-height: 68vh;
            object-fit: contain;
        }

        .gallery-modal-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            overflow-y: auto;
        }

        .gallery-modal-thumb {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 0;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.03);
            cursor: pointer;
        }

        .gallery-modal-thumb.is-active {
            border-color: rgba(255, 255, 255, 0.7);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.18);
        }

        .gallery-modal-thumb img {
            width: 100%;
            height: 90px;
            object-fit: cover;
            display: block;
        }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(320px, 0.9fr);
            gap: 24px;
        }

        .content-main,
        .content-side {
            min-width: 0;
        }

        .detail-card,
        .contact-card,
        .mini-card {
            padding: 26px;
        }

        .detail-card + .detail-card,
        .mini-card + .contact-card {
            margin-top: 20px;
        }

        .section-title {
            margin: 0 0 6px;
            font-size: 25px;
            line-height: 1.1;
            font-weight: 700;
            color: #18202f;
        }

        .section-subtitle {
            margin: 0;
            font-size: 14px;
            color: var(--muted);
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .price-stack strong {
            display: block;
            font-size: clamp(30px, 4vw, 42px);
            line-height: 1;
            color: #16243d;
        }

        .price-stack span {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            color: var(--muted);
        }

        .info-row strong {
            display: block;
            font-size: 16px;
            font-weight: 700;
            color: #192435;
        }

        .info-row span,
        .contact-hint,
        .note {
            display: block;
            margin-top: 4px;
            font-size: 13px;
            color: var(--muted);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .info-row {
            padding: 16px 18px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: var(--surface-alt);
        }

        .description-text {
            margin-top: 20px;
            font-size: 15px;
            line-height: 1.75;
            color: #314155;
            white-space: pre-line;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
            margin-top: 22px;
        }

        .feature-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 140px;
            padding: 18px 14px;
            text-align: center;
            border-radius: 20px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .feature-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border-radius: 16px;
            color: #18202f;
            background: #f1f5f9;
        }

        .feature-card strong {
            font-size: 14px;
            font-weight: 700;
            color: #172235;
        }

        .feature-card span {
            font-size: 24px;
            line-height: 1.1;
            color: #0f172a;
        }

        .map-box {
            margin-top: 20px;
            padding: 22px;
            border-radius: 22px;
            border: 1px dashed #c7d4e8;
            background:
                linear-gradient(135deg, rgba(10, 88, 202, 0.05), rgba(10, 88, 202, 0.01)),
                #f8fbff;
        }

        .map-box strong {
            display: block;
            font-size: 17px;
            margin-bottom: 8px;
            color: #173051;
        }

        #property-map {
            margin-top: 16px;
            height: 360px;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #d7e2f0;
        }

        .sidebar-stick {
            position: sticky;
            top: 96px;
        }

        .contact-price {
            margin: 0;
            font-size: 32px;
            line-height: 1;
            font-weight: 700;
            color: #142136;
        }

        .contact-subprice {
            margin-top: 8px;
            font-size: 14px;
            color: var(--muted);
        }

        .agency-box {
            display: flex;
            gap: 14px;
            align-items: center;
            margin: 20px 0;
            padding: 16px;
            border-radius: 18px;
            background: var(--surface-alt);
            border: 1px solid var(--line);
        }

        .agency-mark {
            width: 52px;
            height: 52px;
            flex: 0 0 52px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #0f172a, #344256);
        }

        .agency-copy strong {
            display: block;
            font-size: 15px;
            color: #172235;
        }

        .agency-copy span {
            display: block;
            margin-top: 3px;
            font-size: 13px;
            color: var(--muted);
        }

        .form-grid {
            gap: 14px;
        }

        .field {
            width: 100%;
        }

        .field label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 700;
            color: #243143;
        }

        .field input,
        .field textarea {
            width: 100%;
            border: 1px solid #d5dde8;
            border-radius: 16px;
            background: #fff;
            padding: 13px 15px;
            font-size: 14px;
            color: var(--text);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .field input:focus,
        .field textarea:focus {
            outline: none;
            border-color: #98bbff;
            box-shadow: 0 0 0 4px rgba(10, 88, 202, 0.12);
        }

        .field textarea {
            min-height: 132px;
            resize: vertical;
        }

        .primary-button {
            width: 100%;
            min-height: 52px;
            border: 0;
            border-radius: 18px;
            padding: 0 18px;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #0a58ca, #0d6efd);
            box-shadow: 0 16px 28px rgba(13, 110, 253, 0.25);
        }

        .primary-button:hover {
            filter: brightness(1.03);
        }

        .mini-card {
            background:
                linear-gradient(180deg, #fdfefe 0%, #f7f9fc 100%);
        }

        .footer-wrap {
            padding: 28px 0 42px;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            font-size: 13px;
            color: var(--muted);
        }

        .footer-wrap a {
            color: var(--brand);
        }

        @media (max-width: 980px) {
            .page-wrap,
            .header-wrap,
            .footer-wrap {
                width: min(100% - 24px, 1180px);
            }

            .gallery-shell {
                grid-template-columns: 1fr;
            }

            .gallery-main {
                min-height: 380px;
            }

            .gallery-thumb {
                min-height: 180px;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .sidebar-stick {
                position: static;
            }

            .price-row {
                flex-direction: column;
            }
        }

        @media (max-width: 720px) {
            .header-wrap {
                flex-direction: column;
                align-items: flex-start;
                padding: 14px 0;
            }

            .page-wrap,
            .header-wrap,
            .footer-wrap {
                width: min(100% - 20px, 1180px);
            }

            .hero-card,
            .gallery-main,
            .hero-placeholder {
                min-height: 320px;
            }

            .detail-card,
            .contact-card,
            .mini-card {
                padding: 20px;
            }

            .hero-highlights,
            .info-grid,
            .feature-grid {
                grid-template-columns: 1fr;
            }

            .info-grid {
                display: grid;
            }

            .section-title {
                font-size: 22px;
            }

            .top-nav {
                gap: 12px;
                font-size: 13px;
            }

            .gallery-side {
                grid-template-columns: 1fr 1fr;
            }

            .gallery-open {
                left: 12px;
                right: 12px;
                bottom: 12px;
                justify-content: center;
                min-height: 46px;
                padding: 0 14px;
                font-size: 13px;
            }

            .gallery-count {
                left: 12px;
                bottom: 12px;
                min-height: 34px;
                padding: 0 12px;
                font-size: 12px;
            }

            .price-stack strong,
            .contact-price {
                font-size: 28px;
            }

            #property-map {
                height: 300px;
            }

            .gallery-modal {
                padding: 16px;
            }

            .gallery-modal-dialog {
                max-height: calc(100vh - 32px);
                padding: 16px;
            }
        }

        @media (max-width: 980px) {
            .feature-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 560px) {
            .hero-card {
                padding: 18px;
            }

            .gallery-main,
            .hero-placeholder {
                min-height: 240px;
                border-radius: 22px;
            }

            .gallery-thumb {
                min-height: 140px;
                border-radius: 18px;
            }

            .gallery-side {
                grid-template-columns: 1fr;
            }

            .hero-title {
                font-size: 26px;
            }

            .info-row {
                padding: 14px 16px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .agency-box {
                align-items: flex-start;
            }

            .gallery-modal-stage {
                min-height: 220px;
            }
        }
    </style>
</head>
<body class="property-page">
<header class="site-header">
    <div class="header-wrap">
        <a href="index.php" class="brand-link">
            <span class="brand-mark">IA</span>
            <span class="brand-copy">
                <strong>Inmobiliaria Argentina</strong>
                <span>Propiedades en CABA y GBA</span>
            </span>
        </a>
        <nav class="top-nav">
            <a href="index.php">Inicio</a>
            <a href="propiedades.php">Propiedades</a>
            <a href="mapa.php">Mapa</a>
            <a href="contacto.php">Contacto</a>
        </nav>
    </div>
</header>

<main class="page-wrap">
    <div class="breadcrumbs">
        <a href="index.php">Inicio</a>
        <span>/</span>
        <a href="propiedades.php">Propiedades</a>
        <span>/</span>
        <span><?= htmlspecialchars($tipoNombre) ?></span>
    </div>

    <article class="hero-card">
        <div class="gallery-shell">
            <div>
                <?php if ($imagenPrincipal): ?>
                    <button type="button" class="gallery-main gallery-trigger" data-gallery-index="0" aria-label="Abrir galería de fotos">
                        <img src="<?= htmlspecialchars($imagenPrincipal) ?>" alt="<?= htmlspecialchars($prop['titulo']) ?>">
                        <?php if ($galeriaTotal > 0): ?>
                            <span class="gallery-count"><?= $galeriaTotal ?> fotos</span>
                        <?php endif; ?>
                    </button>
                <?php else: ?>
                    <div class="hero-placeholder">Sin imagen cargada</div>
                <?php endif; ?>
            </div>

            <?php if ($galeriaSecundaria !== []): ?>
                <div class="gallery-side">
                    <?php foreach ($galeriaSecundaria as $index => $foto): ?>
                        <?php $fotoSrc = 'img/' . rawurlencode($foto['archivo']); ?>
                        <button type="button" class="gallery-thumb gallery-trigger" data-gallery-index="<?= $index + 1 ?>" aria-label="Ver foto <?= $index + 2 ?>">
                            <img src="<?= htmlspecialchars($fotoSrc) ?>" alt="<?= htmlspecialchars($prop['titulo']) ?> foto <?= $index + 2 ?>">
                            <?php if ($index === count($galeriaSecundaria) - 1): ?>
                                <span class="gallery-open">Ver todas las fotos</span>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="hero-summary">
            <div class="hero-topline">
                <span class="<?= htmlspecialchars($badgeOperacionClass) ?>"><?= htmlspecialchars($operacionNombre) ?></span>
                <?php if ($estadoNombre !== ''): ?>
                    <span class="<?= htmlspecialchars($badgeEstadoClass) ?>"><?= htmlspecialchars($estadoNombre) ?></span>
                <?php endif; ?>
            </div>

            <div class="hero-kicker"><?= htmlspecialchars($tipoNombre) ?></div>
            <h1 class="hero-title"><?= htmlspecialchars($prop['titulo']) ?></h1>
            <p class="hero-location"><?= htmlspecialchars($ubicacionTexto) ?></p>
        </div>
    </article>

    <div class="content-grid">
        <div class="content-main">
            <section class="detail-card">
                <div class="price-row">
                    <div class="price-stack">
                        <strong><?= htmlspecialchars($precioPrincipal) ?></strong>
                        <?php if ($precioSecundario !== ''): ?>
                            <span><?= htmlspecialchars($precioSecundario) ?></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2 class="section-title"><?= htmlspecialchars($tipoNombre) ?> en <?= htmlspecialchars($operacionNombre) ?></h2>
                        <p class="section-subtitle"><?= htmlspecialchars($ubicacionTexto) ?></p>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-row">
                        <strong><?= htmlspecialchars($operacionNombre) ?></strong>
                        <span>Tipo de operación</span>
                    </div>
                    <div class="info-row">
                        <strong><?= htmlspecialchars($tipoNombre) ?></strong>
                        <span>Categoría del inmueble</span>
                    </div>
                    <div class="info-row">
                        <strong><?= htmlspecialchars($estadoNombre !== '' ? $estadoNombre : 'Sin estado') ?></strong>
                        <span>Estado comercial</span>
                    </div>
                    <div class="info-row">
                        <strong><?= htmlspecialchars($prop['pais'] ?? 'Argentina') ?></strong>
                        <span>País</span>
                    </div>
                </div>
            </section>

            <?php if ($caracteristicas !== []): ?>
                <section class="detail-card">
                    <h2 class="section-title">Características</h2>
                    <p class="section-subtitle">Ítems destacados cargados desde el panel admin</p>

                    <div class="feature-grid">
                        <?php foreach ($caracteristicas as $item): ?>
                            <article class="feature-card">
                                <div class="feature-icon"><?= renderizarIconoCaracteristica((string)$item['icono']) ?></div>
                                <span><?= htmlspecialchars($item['valor']) ?></span>
                                <strong><?= htmlspecialchars($item['titulo']) ?></strong>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <section class="detail-card">
                <h2 class="section-title">Descripción</h2>
                <p class="section-subtitle">Información general de la propiedad</p>
                <div class="description-text"><?= nl2br(htmlspecialchars($prop['descripcion'] ?? '')) ?></div>
            </section>

            <section class="detail-card">
                <h2 class="section-title">Ubicación</h2>
                <p class="section-subtitle">Referencia de localización publicada</p>

                <div class="map-box">
                    <strong><?= htmlspecialchars($ubicacionTexto) ?></strong>
                    <span class="note">La ubicación se muestra según los datos cargados en la ficha de la propiedad.</span>
                    <?php if (!empty($prop['lat']) && !empty($prop['lng'])): ?>
                        <span class="note">Coordenadas: <?= htmlspecialchars((string)$prop['lat']) ?>, <?= htmlspecialchars((string)$prop['lng']) ?></span>
                        <div id="property-map"></div>
                    <?php else: ?>
                        <span class="note">No hay coordenadas cargadas para mostrar el mapa.</span>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <aside class="content-side">
            <div class="sidebar-stick">
                <section class="mini-card">
                    <p class="contact-price"><?= htmlspecialchars($precioPrincipal) ?></p>
                    <?php if ($precioSecundario !== ''): ?>
                        <p class="contact-subprice"><?= htmlspecialchars($precioSecundario) ?></p>
                    <?php endif; ?>
                    <div class="agency-box">
                        <div class="agency-mark">IA</div>
                        <div class="agency-copy">
                            <strong>Inmobiliaria Argentina</strong>
                            <span>Equipo comercial disponible para consultas y visitas.</span>
                        </div>
                    </div>
                    <span class="contact-hint">Completá el formulario y derivamos tu consulta a la propiedad seleccionada.</span>
                </section>

                <section class="contact-card">
                    <h2 class="section-title">Contactar anunciante</h2>
                    <p class="section-subtitle">Sin alterar el circuito actual de contacto.</p>

                    <form action="contacto.php" method="POST">
                        <input type="hidden" name="propiedad_id" value="<?= $prop['id'] ?>">
                        <input type="hidden" name="propiedad_titulo" value="<?= htmlspecialchars($prop['titulo']) ?>">

                        <div class="form-grid">
                            <div class="field">
                                <label for="nombre">Nombre y apellido</label>
                                <input id="nombre" type="text" name="nombre" required>
                            </div>

                            <div class="field">
                                <label for="email">Email</label>
                                <input id="email" type="email" name="email" required>
                            </div>

                            <div class="field">
                                <label for="telefono">Telefono</label>
                                <input id="telefono" type="text" name="telefono">
                            </div>

                            <div class="field">
                                <label for="mensaje">Mensaje</label>
                                <textarea id="mensaje" name="mensaje" rows="5">Hola, estoy interesado en la propiedad "<?= htmlspecialchars($prop['titulo']) ?>". Me gustaria recibir mas informacion.</textarea>
                            </div>

                            <button class="primary-button" type="submit">Enviar consulta</button>
                        </div>
                    </form>
                </section>
            </div>
        </aside>
    </div>
</main>

<?php if ($galeriaTotal > 0): ?>
    <div class="gallery-modal" id="gallery-modal" hidden>
        <div class="gallery-modal-dialog" role="dialog" aria-modal="true" aria-label="Galeria de fotos">
            <div class="gallery-modal-top">
                <div>
                    <strong><?= htmlspecialchars($prop['titulo']) ?></strong>
                    <p><?= $galeriaTotal ?> fotos disponibles</p>
                </div>
                <button type="button" class="gallery-modal-close" id="gallery-close">Cerrar</button>
            </div>
            <div class="gallery-modal-stage">
                <img id="gallery-modal-image" src="" alt="">
            </div>
            <div class="gallery-modal-strip">
                <?php foreach ($galeria as $index => $foto): ?>
                    <?php $fotoSrc = 'img/' . rawurlencode($foto['archivo']); ?>
                    <button type="button" class="gallery-modal-thumb" data-gallery-select="<?= $index ?>" aria-label="Seleccionar foto <?= $index + 1 ?>">
                        <img src="<?= htmlspecialchars($fotoSrc) ?>" alt="<?= htmlspecialchars($prop['titulo']) ?> miniatura <?= $index + 1 ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<footer>
    <div class="footer-wrap">
        <p>&copy; <?= date('Y'); ?> Inmobiliaria Argentina. Todos los derechos reservados.</p>
    </div>
</footer>
<?php if ($galeriaTotal > 0): ?>
    <script>
        const galleryItems = <?= json_encode(array_map(
            static fn(array $foto): array => [
                'src' => 'img/' . rawurlencode($foto['archivo']),
                'alt' => $prop['titulo'],
            ],
            $galeria
        )) ?>;

        const galleryModal = document.getElementById('gallery-modal');
        const galleryModalImage = document.getElementById('gallery-modal-image');
        const galleryCloseButton = document.getElementById('gallery-close');
        const galleryTriggers = document.querySelectorAll('.gallery-trigger');
        const galleryThumbs = document.querySelectorAll('[data-gallery-select]');
        let activeGalleryIndex = 0;

        function renderGalleryImage(index) {
            activeGalleryIndex = index;
            const current = galleryItems[index];
            if (!current) {
                return;
            }

            galleryModalImage.src = current.src;
            galleryModalImage.alt = `${current.alt} foto ${index + 1}`;

            galleryThumbs.forEach((thumb) => {
                thumb.classList.toggle('is-active', Number(thumb.dataset.gallerySelect) === index);
            });
        }

        function openGallery(index) {
            renderGalleryImage(index);
            galleryModal.hidden = false;
            document.body.style.overflow = 'hidden';
        }

        function closeGallery() {
            galleryModal.hidden = true;
            document.body.style.overflow = '';
        }

        galleryTriggers.forEach((button) => {
            button.addEventListener('click', () => {
                openGallery(Number(button.dataset.galleryIndex || 0));
            });
        });

        galleryThumbs.forEach((button) => {
            button.addEventListener('click', () => {
                renderGalleryImage(Number(button.dataset.gallerySelect || 0));
            });
        });

        galleryCloseButton.addEventListener('click', closeGallery);
        galleryModal.addEventListener('click', (event) => {
            if (event.target === galleryModal) {
                closeGallery();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (galleryModal.hidden) {
                return;
            }

            if (event.key === 'Escape') {
                closeGallery();
            }

            if (event.key === 'ArrowRight') {
                renderGalleryImage((activeGalleryIndex + 1) % galleryItems.length);
            }

            if (event.key === 'ArrowLeft') {
                renderGalleryImage((activeGalleryIndex - 1 + galleryItems.length) % galleryItems.length);
            }
        });
    </script>
<?php endif; ?>
<?php if (!empty($prop['lat']) && !empty($prop['lng'])): ?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <script>
        const propertyLat = <?= json_encode((float)$prop['lat']) ?>;
        const propertyLng = <?= json_encode((float)$prop['lng']) ?>;
        const propertyTitle = <?= json_encode($prop['titulo']) ?>;
        const propertyAddress = <?= json_encode($ubicacionTexto) ?>;

        const map = L.map('property-map', {
            scrollWheelZoom: false
        }).setView([propertyLat, propertyLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        L.marker([propertyLat, propertyLng])
            .addTo(map)
            .bindPopup(`<strong>${propertyTitle}</strong><br>${propertyAddress}`)
            .openPopup();
    </script>
<?php endif; ?>
</body>
</html>
