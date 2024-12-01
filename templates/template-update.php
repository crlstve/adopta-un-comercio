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
    <div id="map" class="h-auto w-full md:w-8/12" style="height: 500px; width:100%;max-width:900px;aspect-ratio: 5/9;margin:auto;"></div>
    <form id="locationForm" enctype="multipart/form-data" method="post" class="flex flex-col gap-4 w-full md:w-8/12 my-12 mx-auto">
        <?php wp_nonce_field('update_status', 'update_nonce_field'); ?>
         <label class="hidden" for="coordinates">Coordenadas seleccionadas:</label>       
         <input class="hidden" type="text" id="coordinates" name="coordinates" readonly required></div>
        <input class="hidden" type="number" name="auc_cr" id="auc_cr" value="<?= $commerce_id; ?>" readonly required>
        <textarea placeholder="Deja un mensaje. Lo mostraremos en nuestro mapa interactivo." class="border borderborder-gray-200" type="text" name="mensaje" id="mensaje" maxlength="600" required></textarea>
        <input class="border border-gray-200" type="file" name="open_img" id="open_img" accept="image/*" required>
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

            // Manejar clics en el mapa
            map.on('click', function(e) {
                var coords = e.latlng;

                // Eliminar el marcador existente si hay uno
                if (currentMarker) {
                    map.removeLayer(currentMarker);
                }

                // Crear un nuevo marcador y asignarlo a la variable
                currentMarker = L.marker([coords.lat, coords.lng]).addTo(map)
                    .bindPopup('Ubicación: ' + coords.lat.toFixed(6) + ', ' + coords.lng.toFixed(6))
                    .openPopup();

                // Actualizar el campo de texto con las coordenadas seleccionadas
                document.getElementById('coordinates').value = coords.lat.toFixed(6) + ', ' + coords.lng.toFixed(6);


            });
        });
    </script>
</main>
<?php get_footer(); ?>
