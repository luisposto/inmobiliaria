<?php
require_once __DIR__ . '/../backend/funciones.php';
requerirPermisoAdmin('propiedades');

$operaciones = obtenerOperaciones();
$tipos = obtenerTiposPropiedad();
$estados = obtenerEstadosPropiedad();
$iconosCaracteristica = obtenerOpcionesIconosCaracteristica(true);
$iconoCaracteristicaDefault = array_key_first($iconosCaracteristica) ?: 'check';
$provincias = obtenerProvincias(false);
$ciudades = obtenerCiudades(false);
$ciudadesJson = [];
foreach ($ciudades as $ciudad) {
    $ciudadesJson[] = [
        'id' => (int) $ciudad['id'],
        'nombre' => (string) $ciudad['nombre'],
        'provincia_id' => (int) $ciudad['provincia_id'],
        'provincia_nombre' => (string) $ciudad['provincia_nombre'],
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva propiedad | Inmobiliaria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/tailwind.css')) ?>?v=<?= publicAssetVersion('css/tailwind.css') ?>" rel="stylesheet">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/theme-overrides.css')) ?>?v=<?= publicAssetVersion('css/theme-overrides.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        #property-location-map {
            height: 320px;
            border-radius: 1rem;
        }
    </style>
</head>
<body>
    <header class="app-header">
        <div class="app-header-inner max-w-4xl">
            <div class="app-brand">
                <span class="app-brand-mark">IA</span>
                <div class="app-brand-copy">
                    <p class="app-brand-title">Inmobiliaria Argentina</p>
                    <p class="app-brand-subtitle">Nueva propiedad</p>
                </div>
            </div>
            <a href="dashboard.php" class="btn-secondary bg-white/10 text-white hover:bg-white/15 hover:text-white">Volver al listado</a>
        </div>
    </header>

    <main class="app-main-narrow">
        <form action="../backend/crear_propiedad.php" method="POST" enctype="multipart/form-data" class="admin-form space-y-6 text-sm">
            <div>
                <span class="eyebrow">Alta</span>
                <h1 class="section-heading mb-2 text-2xl">Cargar nueva propiedad</h1>
                <p class="section-copy">Completá los datos del inmueble y sus atributos visibles en la ficha pública.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="field-label">Título</label>
                    <input type="text" name="titulo" required class="field-input">
                </div>
                <div>
                    <label class="field-label">Precio (USD)</label>
                    <input type="number" name="precio_usd" step="1000" class="field-input">
                </div>
            </div>

            <div>
                <label class="field-label">Precio (ARS)</label>
                <input type="number" name="precio" step="1000" class="field-input">
            </div>

            <div>
                <label class="field-label">Descripción</label>
                <textarea name="descripcion" rows="5" class="field-input"></textarea>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="field-label">Dirección</label>
                    <input type="text" id="direccion" name="direccion" class="field-input">
                </div>
                <div>
                    <label class="field-label">Provincia</label>
                    <select id="provincia_id" name="provincia_id" class="field-input">
                        <option value="">Seleccionar provincia</option>
                        <?php foreach ($provincias as $provincia): ?>
                            <option value="<?= (int) $provincia['id'] ?>"><?= htmlspecialchars($provincia['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="field-label">Ciudad</label>
                    <select id="ciudad_id" name="ciudad_id" class="field-input" disabled>
                        <option value="">Seleccionar ciudad</option>
                    </select>
                </div>
                <div>
                    <label class="field-label">País</label>
                    <input type="text" id="pais" name="pais" value="Argentina" class="field-input">
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="field-label">Tipo</label>
                    <select name="tipo_id" class="field-input">
                        <option value="">Seleccionar</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="field-label">Operación</label>
                    <select name="operacion_id" class="field-input">
                        <option value="">Seleccionar</option>
                        <?php foreach ($operaciones as $op): ?>
                            <option value="<?= $op['id'] ?>"><?= htmlspecialchars($op['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="field-label">Estado</label>
                    <select name="estado_id" class="field-input">
                        <?php foreach ($estados as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= $e['nombre'] === 'Disponible' ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="field-label">Latitud</label>
                    <input type="number" id="lat" name="lat" step="any" class="field-input">
                </div>
                <div>
                    <label class="field-label">Longitud</label>
                    <input type="number" id="lng" name="lng" step="any" class="field-input">
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <label class="flex items-center gap-3 text-sm text-slate-700">
                    <input type="checkbox" id="destacado" name="destacado" value="1" class="rounded border-slate-300">
                    Marcar como destacada
                </label>
            </div>

            <section class="surface-card-soft p-5 space-y-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Ubicación en mapa</h2>
                        <p class="text-xs text-slate-500">Buscá la dirección y ajustá el pin si necesitás corregir las coordenadas.</p>
                    </div>
                    <button type="button" id="locate-address" class="btn-secondary text-xs">Ubicar dirección</button>
                </div>
                <p id="map-status" class="text-xs text-slate-500">Completá la dirección y presioná "Ubicar dirección".</p>
                <div id="property-location-map" aria-label="Mapa para seleccionar ubicación"></div>
            </section>

            <div class="surface-card-soft p-5">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="field-label">Imagen principal</label>
                        <input type="file" name="imagen" accept="image/*" class="block w-full text-sm text-slate-500">
                    </div>
                    <div>
                        <label class="field-label">Galería de fotos</label>
                        <input type="file" name="galeria[]" accept="image/*" multiple class="block w-full text-sm text-slate-500">
                        <p class="mt-2 text-xs text-slate-500">Podés cargar varias fotos para la grilla de la ficha pública.</p>
                    </div>
                </div>
            </div>

            <section class="space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Características con icono</h2>
                        <p class="text-xs text-slate-500">Se muestran en la ficha pública de la propiedad.</p>
                    </div>
                    <button type="button" id="add-feature" class="btn-secondary text-xs">Agregar característica</button>
                </div>

                <?php if (!$iconosCaracteristica): ?>
                    <p class="rounded-[1.25rem] border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        No hay iconos disponibles. Cargalos desde <a href="iconos.php" class="font-semibold underline">Administrar iconos</a>.
                    </p>
                <?php endif; ?>

                <div id="feature-list" class="space-y-3"></div>

                <template id="feature-template">
                    <div class="feature-row grid gap-3 rounded-[1.4rem] border border-slate-200 bg-slate-50/70 p-4 md:grid-cols-[1.1fr,1fr,1fr,auto]">
                        <div>
                            <label class="field-label">Ícono</label>
                            <select name="caracteristica_icono[]" class="field-input">
                                <?php foreach ($iconosCaracteristica as $clave => $etiqueta): ?>
                                    <option value="<?= htmlspecialchars($clave) ?>"><?= htmlspecialchars($etiqueta) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="field-label">Título</label>
                            <input type="text" name="caracteristica_titulo[]" placeholder="Ej. Dormitorios" class="field-input">
                        </div>
                        <div>
                            <label class="field-label">Valor</label>
                            <input type="text" name="caracteristica_valor[]" placeholder="Ej. 3 dorm." class="field-input">
                        </div>
                        <div class="flex items-end">
                            <button type="button" class="remove-feature btn-danger text-xs">Quitar</button>
                        </div>
                    </div>
                </template>
            </section>

            <div class="flex justify-end gap-3 pt-2">
                <a href="dashboard.php" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-accent">Guardar propiedad</button>
            </div>
        </form>
    </main>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        const featureList = document.getElementById('feature-list');
        const featureTemplate = document.getElementById('feature-template');
        const addFeatureButton = document.getElementById('add-feature');
        const addressInput = document.getElementById('direccion');
        const cityInput = document.getElementById('ciudad_id');
        const provinceInput = document.getElementById('provincia_id');
        const countryInput = document.getElementById('pais');
        const latInput = document.getElementById('lat');
        const lngInput = document.getElementById('lng');
        const locateAddressButton = document.getElementById('locate-address');
        const mapStatus = document.getElementById('map-status');
        const cityOptions = <?= json_encode($ciudadesJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const defaultLat = -34.6037;
        const defaultLng = -58.3816;
        const map = L.map('property-location-map').setView([defaultLat, defaultLng], 5);
        let marker = null;

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        function setStatus(message, isError = false) {
            mapStatus.textContent = message;
            mapStatus.className = `text-xs ${isError ? 'text-red-700' : 'text-slate-500'}`;
        }

        function updateCoordinateInputs(lat, lng) {
            latInput.value = Number(lat).toFixed(6);
            lngInput.value = Number(lng).toFixed(6);
        }

        function setMarker(lat, lng, zoom = 16) {
            const position = [Number(lat), Number(lng)];
            if (!marker) {
                marker = L.marker(position, { draggable: true }).addTo(map);
                marker.on('dragend', () => {
                    const current = marker.getLatLng();
                    updateCoordinateInputs(current.lat, current.lng);
                    setStatus('Coordenadas actualizadas desde el marcador.');
                });
            } else {
                marker.setLatLng(position);
            }

            updateCoordinateInputs(position[0], position[1]);
            map.setView(position, zoom);
        }

        function getSelectedOptionText(select) {
            return select?.options?.[select.selectedIndex]?.text?.trim() || '';
        }

        function populateCities(selectedProvinceId, selectedCityId = '') {
            const targetCityId = selectedCityId || cityInput.value;
            cityInput.innerHTML = '<option value="">Seleccionar ciudad</option>';

            const filteredCities = cityOptions.filter((city) => String(city.provincia_id) === String(selectedProvinceId));
            filteredCities.forEach((city) => {
                const option = document.createElement('option');
                option.value = String(city.id);
                option.textContent = city.nombre;
                option.selected = String(city.id) === String(targetCityId);
                cityInput.appendChild(option);
            });

            cityInput.disabled = filteredCities.length === 0;
            if (!filteredCities.length) {
                cityInput.value = '';
            }
        }

        function buildAddressQuery() {
            return [
                addressInput.value,
                getSelectedOptionText(cityInput),
                getSelectedOptionText(provinceInput),
                countryInput.value
            ]
                .map((value) => value.trim())
                .filter(Boolean)
                .join(', ');
        }

        async function geocodeAddress() {
            const query = buildAddressQuery();
            if (!query) {
                setStatus('Ingresá una dirección antes de ubicarla en el mapa.', true);
                addressInput.focus();
                return;
            }

            locateAddressButton.disabled = true;
            setStatus('Buscando la dirección en el mapa...');

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('search_failed');
                }

                const results = await response.json();
                if (!Array.isArray(results) || !results.length) {
                    setStatus('No se encontró una ubicación para esa dirección. Probá con más detalle.', true);
                    return;
                }

                const firstResult = results[0];
                setMarker(firstResult.lat, firstResult.lon);
                setStatus('Ubicación encontrada. Podés arrastrar el pin para ajustar las coordenadas.');
            } catch (error) {
                setStatus('No se pudo consultar el mapa en este momento.', true);
            } finally {
                locateAddressButton.disabled = false;
            }
        }

        function syncMarkerFromInputs() {
            const lat = latInput.value.trim();
            const lng = lngInput.value.trim();
            if (lat === '' || lng === '') {
                return;
            }

            setMarker(lat, lng, 16);
        }

        function addFeatureRow(values = {}) {
            const fragment = featureTemplate.content.cloneNode(true);
            const row = fragment.querySelector('.feature-row');
            const iconSelect = row.querySelector('[name="caracteristica_icono[]"]');
            const titleInput = row.querySelector('[name="caracteristica_titulo[]"]');
            const valueInput = row.querySelector('[name="caracteristica_valor[]"]');

            iconSelect.value = values.icono || <?= json_encode($iconoCaracteristicaDefault) ?>;
            titleInput.value = values.titulo || '';
            valueInput.value = values.valor || '';

            row.querySelector('.remove-feature').addEventListener('click', () => {
                row.remove();
            });

            featureList.appendChild(fragment);
        }

        addFeatureButton.addEventListener('click', () => addFeatureRow());
        addFeatureRow();
        provinceInput.addEventListener('change', () => populateCities(provinceInput.value));
        locateAddressButton.addEventListener('click', geocodeAddress);
        latInput.addEventListener('change', syncMarkerFromInputs);
        lngInput.addEventListener('change', syncMarkerFromInputs);
        populateCities(provinceInput.value);
        syncMarkerFromInputs();
    </script>
</body>
</html>
