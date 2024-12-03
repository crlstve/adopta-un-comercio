<?php 
/* Template Name: UPDATE */ 
get_header(); 
include_once get_template_directory() . '/inc/update-status.php'; 
?>
<main class="contain 2xl:max-w-7xl mx-auto px-6 my-12">
    <header class="mb-12 w-full md:w-8/12 mx-auto flex flex-col gap-4 text-center">
        <h1 class="text-lg md:text-3xl text-orange"><?= __('Hola ','adopta') . $post_title; ?></h1>
        <p class="md:px-8"><?php _e('Nos gustaría saber cómo estáis. Por ello os pedimos que nos enviéis la ubicación de vuestro comercio, vuestro estado, así como una mensaje y una fotografía reciente. Esto nos ayudará actualizar la información en nuestro sitio web.', 'adopta'); ?></p>
    </header>
    <div class="mb-6 mx-auto w-full md:w-8/12">Marca en el mapa la ubicación de tu comercio:</div>
    <div id="map" class="h-auto w-full md:w-8/12" style="height: 500px; width:100%;max-width:821px;aspect-ratio: 5/9;margin:auto;"></div>
    <form id="locationForm" enctype="multipart/form-data" method="post" class="flex flex-col gap-4 w-full md:w-8/12 my-12 mx-auto">
        <?php wp_nonce_field('update_status', 'update_nonce_field'); ?>
         <label class="hidden" for="coordinates">Coordenadas seleccionadas:</label>       
         <input  class="hidden" type="text" id="coordinates" name="coordinates"  required></div>
         <div class="flex flex-col md:flex-row gap-2">
            <label class="w-full md:w-4/12 self-center" for="direccion">O escribe la dirección:</label>
            <input class="border borderborder-gray-200 w-full" type="text" id="direccion" name="direccion" placeholder="Ej: 3, Avenida de Torrente, Alfafar" required></div>
         </div>
         <div class="flex flex-col md:flex-row">
            <label class="w-full md:w-4/12 self-center" for="web">Web:</label>
            <input class="border borderborder-gray-200 w-full" type="text" name="web" id="web" placeholder="https://www.example.com" class="border borderborder-gray-200">
            </div>
        <div class="flex flex-col md:flex-row">
            <label class="w-full md:w-4/12 self-center" for="facebook">Facebook:</label>
            <input class="border borderborder-gray-200 w-full" type="text" id="facebook" name="facebook" placeholder="Ej: https://facebook.com/tu-usuario" required></div>
         </div>
         <div class="flex flex-col md:flex-row">
            <label class="w-full md:w-4/12 self-center" for="instagram">Instagram:</label>
            <input class="border borderborder-gray-200 w-full" type="text" id="instagram" name="instagram" placeholder="Ej: https://instagram.com/tu-usuario" required></div>
         </div>         
        <input class="hidden" type="number" name="auc_cr" id="auc_cr" value="<?= $commerce_id; ?>" readonly required>
        <textarea placeholder="Deja un mensaje. Lo mostraremos en nuestro mapa interactivo." class="border borderborder-gray-200" type="text" name="mensaje" id="mensaje" maxlength="300" required></textarea>
        <input class="border border-gray-200" type="file" name="open_img" id="open_img" accept="image/*">
        <select class="border borderborder-gray-200" name="status" id="status">
            <option value="abierto"><?php esc_html_e('¡Hemos abierto! :D','adopta'); ?></option>
            <option value="cerrado"><?php esc_html_e('Seguimos cerrados. :(','adopta'); ?></option>
        </select>
        <button type="submit" name="submit_update" class="bg-dark text-white w-full mx-auto py-3 px-6"><?= esc_html_e('Enviar','adopta'); ?></button>
    </form>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar el mapa
    var map = L.map('map').setView([39.416564, -0.400987], 13);

    // Agregar capa base (Carto Positron)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> ' +
                     '&copy; <a href="https://carto.com/">CARTO</a>'
    }).addTo(map);

    // Variable para almacenar el marcador actual
    var currentMarker = null;

    // Función para actualizar el marcador en el mapa
    function updateMarker(lat, lng, address) {
        // Eliminar el marcador existente si hay uno
        if (currentMarker) {
            map.removeLayer(currentMarker);
        }

        // Crear un nuevo marcador y centrar el mapa
        currentMarker = L.marker([lat, lng]).addTo(map)
            .bindPopup(address)
            .openPopup();

        map.setView([lat, lng], 15);

        // Actualizar el campo de texto con la dirección seleccionada
        document.getElementById('coordinates').value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        document.getElementById('direccion').value = address;
    }

    // Manejar clics en el mapa
    map.on('click', function (e) {
        var coords = e.latlng;

        // Realizar geocodificación inversa usando el API de Nominatim
        var url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${coords.lat}&lon=${coords.lng}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                var address = data.display_name || "Dirección no disponible";
                updateMarker(coords.lat, coords.lng, address);
            })
            .catch(error => {
                console.error('Error al obtener la dirección:', error);
                alert('No se pudo obtener la dirección para estas coordenadas.');
            });
    });

    // Manejar cambios en el campo de dirección
    document.getElementById('direccion').addEventListener('change', function () {
        var address = this.value;

        // Realizar geocodificación directa usando el API de Nominatim
        var url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    var lat = parseFloat(data[0].lat);
                    var lng = parseFloat(data[0].lon);
                    updateMarker(lat, lng, address);
                } else {
                    alert('No se pudo encontrar la ubicación especificada.');
                }
            })
            .catch(error => {
                console.error('Error al realizar la geocodificación:', error);
                alert('No se pudo obtener coordenadas para esta dirección.');
            });
    });
});

    </script>
</main>
<?php get_footer(); ?>
