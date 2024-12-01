<?php 
/* Template Name: MAP */ 
get_header();

    $args = array(
        'post_type' => 'comercios', // Cambia 'comercio' por tu custom post type
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'coordinates', // Asegúrate de que este sea el nombre exacto del campo ACF
                'compare' => 'EXISTS',
            ),
        ),
    );
    $query = new WP_Query($args);
    $commerces = array();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $coordinates = get_field('coordinates'); // Campo ACF para las coordenadas
            $image_id = get_field('image'); // Obtén el ID del campo de imagen
            $new_img_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : ''; // URL de la imagen
            if ($coordinates) {
                list($lat, $lng) = explode(',', $coordinates);
                $commerces[] = array(
                    'title' => get_the_title(),
                    'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                    'new_image' => $new_img_url, // Usa la nueva URL de imagen
                    'message' => get_field('message'), // Cambia por el campo ACF de descripción
                    'lat' => trim($lat),
                    'lng' => trim($lng),
                );
            }
        }
    }
wp_reset_postdata();
?>

<main class="contain 2xl:max-w-7xl mx-auto px-6 my-12">
    <header class="mb-12 w-full md:w-8/12 mx-auto flex flex-col gap-4 text-center">
        <h1 class="text-2xl font-bold">Mapa de Comercios</h1>
    </header>
    <div id="map" class="h-auto w-full md:w-8/12" style="height: 500px; width:100%; max-width:900px; aspect-ratio: 5/9; margin:auto;"></div>

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

            // Marcadores recuperados de PHP
            var commerces = <?= json_encode($commerces); ?>;

            commerces.forEach(function(commerce) {
                var marker = L.marker([commerce.lat, commerce.lng]).addTo(map);

                // Agregar popup con información
                var popupContent = `
                    <div style="text-align: center;">
                        <h3>${commerce.title}</h3>
                        <img src="${commerce.new_image}" alt="${commerce.title}" style="width: 100px; height: auto; margin: 5px 0;">
                        <p>${commerce.message}</p>
                    </div>
                `;
                marker.bindPopup(popupContent);
            });
        });
    </script>

</main>

<?php get_footer(); ?>
