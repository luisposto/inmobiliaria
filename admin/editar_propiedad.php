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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$prop = $id ? obtenerPropiedadPorId($id) : null;
$flashActualizacion = $_SESSION['flash_actualizacion_propiedad'] ?? null;
unset($_SESSION['flash_actualizacion_propiedad']);
$actualizacionExitosa = $flashActualizacion === 'ok';
$actualizacionFallida = $flashActualizacion === 'error';
$eliminacionGaleriaExitosa = isset($_GET['gal_del']) && $_GET['gal_del'] === '1';
$eliminacionGaleriaFallida = isset($_GET['gal_del']) && $_GET['gal_del'] === '0';
if (!$prop) {
    header("Location: dashboard.php");
    exit;
}

$ciudadSeleccionada = obtenerCiudadPorNombreYProvincia((string) ($prop['ciudad'] ?? ''), (string) ($prop['provincia'] ?? ''));
$provinciaSeleccionadaId = $ciudadSeleccionada ? (int) $ciudadSeleccionada['provincia_id'] : 0;
$ciudadSeleccionadaId = $ciudadSeleccionada ? (int) $ciudadSeleccionada['id'] : 0;
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
    <title>Editar propiedad | Inmobiliaria</title>
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
                    <p class="app-brand-subtitle">Editar propiedad #<?= $prop['id'] ?></p>
                </div>
            </div>
            <a href="dashboard.php" class="btn-secondary bg-white/10 text-white hover:bg-white/15 hover:text-white">Volver al listado</a>
        </div>
    </header>

    <main class="app-main-narrow">
        <form action="../backend/editar_propiedad.php" method="POST" enctype="multipart/form-data" class="admin-form space-y-6 text-sm">
            <input type="hidden" name="id" value="<?= $prop['id'] ?>">

            <?php if ($actualizacionExitosa): ?>
                <div class="js-auto-dismiss rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Propiedad actualizada correctamente.
                </div>
            <?php elseif ($actualizacionFallida): ?>
                <div class="js-auto-dismiss rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    No se pudieron guardar los cambios de la propiedad.
                </div>
            <?php endif; ?>

            <?php if ($eliminacionGaleriaExitosa): ?>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    La foto se eliminó de la galería.
                </div>
            <?php elseif ($eliminacionGaleriaFallida): ?>
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    No se pudo eliminar la foto de la galería.
                </div>
            <?php endif; ?>

            <div>
                <span class="eyebrow">Edición</span>
                <h1 class="section-heading mb-2 text-2xl">Actualizar propiedad</h1>
                <p class="section-copy">Modificá los datos visibles del inmueble sin salir del panel.</p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-accent">Guardar</button>
            </div>

            <div>
                <label class="field-label">Título</label>
                <input type="text" name="titulo" required value="<?= htmlspecialchars($prop['titulo']) ?>" class="field-input">
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="field-label">Precio (USD)</label>
                    <input type="number" name="precio_usd" step="1000" value="<?= htmlspecialchars($prop['precio_usd']) ?>" class="field-input">
                </div>
                <div>
                <label class="field-label">Precio (ARS)</label>
                <input type="number" name="precio" step="1000" value="<?= htmlspecialchars($prop['precio']) ?>" class="field-input">
                </div>
            </div>

            <div>
                <label class="field-label">Descripción</label>
                <textarea name="descripcion" rows="5" class="field-input"><?= htmlspecialchars($prop['descripcion']) ?></textarea>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="field-label">Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($prop['direccion']) ?>" class="field-input">
                </div>
                <div>
                    <label class="field-label">Ciudad</label>
                    <select id="ciudad_id" name="ciudad_id" class="field-input" <?= $provinciaSeleccionadaId > 0 ? '' : 'disabled' ?>>
                        <option value="">Seleccionar ciudad</option>
                    </select>
                </div>
                <div>
                    <label class="field-label">Provincia</label>
                    <select id="provincia_id" name="provincia_id" class="field-input">
                        <option value="">Seleccionar provincia</option>
                        <?php foreach ($provincias as $provincia): ?>
                            <option value="<?= (int) $provincia['id'] ?>" <?= $provinciaSeleccionadaId === (int) $provincia['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($provincia['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!$ciudadSeleccionada && (($prop['ciudad'] ?? '') !== '' || ($prop['provincia'] ?? '') !== '')): ?>
                        <p class="mt-2 text-xs text-amber-700">La ubicacion guardada no coincide con el catalogo actual. Seleccionala nuevamente para normalizarla.</p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="field-label">País</label>
                    <input type="text" id="pais" name="pais" value="<?= htmlspecialchars($prop['pais']) ?>" class="field-input">
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="field-label">Tipo</label>
                    <select name="tipo_id" class="field-input">
                        <option value="">Seleccionar</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= ($prop['tipo_id'] == $t['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="field-label">Operación</label>
                    <select name="operacion_id" class="field-input">
                        <option value="">Seleccionar</option>
                        <?php foreach ($operaciones as $op): ?>
                            <option value="<?= $op['id'] ?>" <?= ($prop['operacion_id'] == $op['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($op['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="field-label">Estado</label>
                    <select name="estado_id" class="field-input">
                        <?php foreach ($estados as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= ($prop['estado_id'] == $e['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="field-label">Latitud</label>
                    <input type="number" id="lat" name="lat" step="any" value="<?= htmlspecialchars($prop['lat']) ?>" class="field-input">
                </div>
                <div>
                    <label class="field-label">Longitud</label>
                    <input type="number" id="lng" name="lng" step="any" value="<?= htmlspecialchars($prop['lng']) ?>" class="field-input">
                </div>
            </div>

            <section class="surface-card-soft p-5 space-y-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Ubicación en mapa</h2>
                        <p class="text-xs text-slate-500">Reubicá la propiedad desde la dirección o arrastrando el marcador.</p>
                    </div>
                    <button type="button" id="locate-address" class="btn-secondary text-xs">Ubicar dirección</button>
                </div>
                <p id="map-status" class="text-xs text-slate-500">Podés recalcular la ubicación o ajustar el pin manualmente.</p>
                <div id="property-location-map" aria-label="Mapa para seleccionar ubicación"></div>
            </section>

            <div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-6">
                <label class="flex items-center gap-3 text-sm text-slate-700">
                    <input type="checkbox" id="destacado" name="destacado" value="1" <?= $prop['destacado'] ? 'checked' : '' ?> class="rounded border-slate-300">
                    Marcar como destacada
                </label>
            </div>

            <div class="surface-card-soft p-5">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="field-label">Imagen actual</p>
                        <?php if (!empty($prop['imagen'])): ?>
                            <img src="../public/img/<?= htmlspecialchars($prop['imagen']) ?>" alt="" class="h-40 w-full rounded-[1.25rem] border border-slate-200 object-cover">
                        <?php else: ?>
                            <p class="text-xs text-slate-400">Sin imagen cargada.</p>
                        <?php endif; ?>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="field-label">Reemplazar imagen</label>
                            <input type="file" name="imagen" accept="image/*" class="block w-full text-sm text-slate-500">
                        </div>
                        <div>
                            <label class="field-label">Agregar fotos a la galería</label>
                            <input type="file" name="galeria[]" accept="image/*" multiple class="block w-full text-sm text-slate-500">
                            <p class="mt-2 text-xs text-slate-500">Las nuevas fotos se suman a la galería pública de la propiedad.</p>
                        </div>
                    </div>
                </div>
            </div>

            <section id="galeria-fotos" class="space-y-3">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Fotos de la galería</h2>
                    <p class="text-xs text-slate-500">La imagen principal se administra arriba. Acá podés quitar fotos secundarias.</p>
                </div>

                <?php if (!empty($prop['imagenes_secundarias'])): ?>
                    <div class="grid gap-4 md:grid-cols-3">
                        <?php foreach ($prop['imagenes_secundarias'] as $imagen): ?>
                            <article class="surface-card-soft overflow-hidden p-3">
                                <img src="../public/img/<?= htmlspecialchars($imagen['archivo']) ?>" alt="" class="h-40 w-full rounded-[1.25rem] border border-slate-200 object-cover">
                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <span class="text-sm text-slate-600">Foto secundaria</span>
                                    <button type="button" data-image-id="<?= (int)$imagen['id'] ?>" class="js-delete-gallery-image btn-danger px-3 py-2 text-xs">
                                        Eliminar foto
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">Todavía no hay fotos secundarias cargadas.</p>
                <?php endif; ?>
            </section>

            <section class="space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Características con icono</h2>
                        <p class="text-xs text-slate-500">Edita los ítems que se muestran en la ficha pública.</p>
                    </div>
                    <button type="button" id="add-feature" class="btn-secondary text-xs">Agregar característica</button>
                </div>

                <?php if (!$iconosCaracteristica): ?>
                    <p class="rounded-[1.25rem] border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        No hay iconos disponibles. Cargalos desde <a href="iconos.php" class="font-semibold underline">Administrar iconos</a>.
                    </p>
                <?php endif; ?>

                <div id="feature-list" class="space-y-3">
                    <?php foreach (($prop['caracteristicas'] ?? []) as $item): ?>
                        <div class="feature-row grid gap-3 rounded-[1.4rem] border border-slate-200 bg-slate-50/70 p-4 md:grid-cols-[1.1fr,1fr,1fr,auto]">
                            <div>
                                <label class="field-label">Ícono</label>
                                <select name="caracteristica_icono[]" class="field-input">
                                    <?php foreach ($iconosCaracteristica as $clave => $etiqueta): ?>
                                        <option value="<?= htmlspecialchars($clave) ?>" <?= ($item['icono'] === $clave) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($etiqueta) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Título</label>
                                <input type="text" name="caracteristica_titulo[]" value="<?= htmlspecialchars($item['titulo']) ?>" class="field-input">
                            </div>
                            <div>
                                <label class="field-label">Valor</label>
                                <input type="text" name="caracteristica_valor[]" value="<?= htmlspecialchars($item['valor']) ?>" class="field-input">
                            </div>
                            <div class="flex items-end">
                                <button type="button" class="remove-feature btn-danger text-xs">Quitar</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

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
                            <input type="text" name="caracteristica_titulo[]" placeholder="Ej. Estado" class="field-input">
                        </div>
                        <div>
                            <label class="field-label">Valor</label>
                            <input type="text" name="caracteristica_valor[]" placeholder="Ej. A estrenar" class="field-input">
                        </div>
                        <div class="flex items-end">
                            <button type="button" class="remove-feature btn-danger text-xs">Quitar</button>
                        </div>
                    </div>
                </template>
            </section>

            <?php if ($actualizacionExitosa): ?>
                <div class="js-auto-dismiss rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    Propiedad actualizada correctamente.
                </div>
            <?php elseif ($actualizacionFallida): ?>
                <div class="js-auto-dismiss rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    No se pudieron guardar los cambios de la propiedad.
                </div>
            <?php endif; ?>

            <div class="flex justify-end gap-3 pt-2">
                <a href="dashboard.php" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-accent">Guardar</button>
            </div>
        </form>
        <form id="delete-gallery-image-form" action="../backend/eliminar_imagen_propiedad.php" method="POST" class="hidden">
            <input type="hidden" name="propiedad_id" value="<?= (int)$prop['id'] ?>">
            <input type="hidden" name="imagen_id" value="">
        </form>
    </main>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        const featureList = document.getElementById('feature-list');
        const featureTemplate = document.getElementById('feature-template');
        const addFeatureButton = document.getElementById('add-feature');
        const deleteGalleryImageForm = document.getElementById('delete-gallery-image-form');
        const addressInput = document.getElementById('direccion');
        const cityInput = document.getElementById('ciudad_id');
        const provinceInput = document.getElementById('provincia_id');
        const countryInput = document.getElementById('pais');
        const latInput = document.getElementById('lat');
        const lngInput = document.getElementById('lng');
        const locateAddressButton = document.getElementById('locate-address');
        const mapStatus = document.getElementById('map-status');
        const cityOptions = <?= json_encode($ciudadesJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const initialProvinceId = <?= json_encode($provinciaSeleccionadaId) ?>;
        const initialCityId = <?= json_encode($ciudadSeleccionadaId) ?>;
        const defaultLat = -34.6037;
        const defaultLng = -58.3816;
        const initialLat = <?= json_encode($prop['lat'] !== null ? (float)$prop['lat'] : null) ?>;
        const initialLng = <?= json_encode($prop['lng'] !== null ? (float)$prop['lng'] : null) ?>;
        const map = L.map('property-location-map').setView(
            initialLat !== null && initialLng !== null ? [initialLat, initialLng] : [defaultLat, defaultLng],
            initialLat !== null && initialLng !== null ? 16 : 5
        );
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

        function attachRemoveHandlers(scope) {
            scope.querySelectorAll('.remove-feature').forEach((button) => {
                button.addEventListener('click', () => {
                    button.closest('.feature-row')?.remove();
                });
            });
        }

        function addFeatureRow(values = {}) {
            const fragment = featureTemplate.content.cloneNode(true);
            const row = fragment.querySelector('.feature-row');
            row.querySelector('[name="caracteristica_icono[]"]').value = values.icono || <?= json_encode($iconoCaracteristicaDefault) ?>;
            row.querySelector('[name="caracteristica_titulo[]"]').value = values.titulo || '';
            row.querySelector('[name="caracteristica_valor[]"]').value = values.valor || '';
            attachRemoveHandlers(row);
            featureList.appendChild(fragment);
        }

        addFeatureButton.addEventListener('click', () => addFeatureRow());
        attachRemoveHandlers(document);
        provinceInput.addEventListener('change', () => populateCities(provinceInput.value));
        locateAddressButton.addEventListener('click', geocodeAddress);
        latInput.addEventListener('change', syncMarkerFromInputs);
        lngInput.addEventListener('change', syncMarkerFromInputs);

        if (!featureList.children.length) {
            addFeatureRow();
        }

        populateCities(initialProvinceId, initialCityId);
        syncMarkerFromInputs();

        document.querySelectorAll('.js-delete-gallery-image').forEach((button) => {
            button.addEventListener('click', () => {
                const imageId = button.dataset.imageId || '';
                if (!imageId) {
                    return;
                }

                if (!window.confirm('¿Eliminar esta foto de la galería?')) {
                    return;
                }

                deleteGalleryImageForm.querySelector('[name="imagen_id"]').value = imageId;
                deleteGalleryImageForm.submit();
            });
        });

        window.setTimeout(() => {
            document.querySelectorAll('.js-auto-dismiss').forEach((element) => {
                element.remove();
            });
        }, 5000);
    </script>
</body>
</html>
