<?php 
/* Template Name: MAP */ 
get_header();

// Recuperar los parámetros de la URL
$etiqueta = isset($_GET['comercio_etiqueta']) ? $_GET['comercio_etiqueta'] : '';
$search_text = isset($_GET['search_text']) ? $_GET['search_text'] : '';

// Consulta de comercios con los filtros
$args = array(
    'post_type' => 'comercios',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'coordinates',
            'compare' => 'EXISTS',
        ),
    ),
    'tax_query' => array(), // Filtros de taxonomía
);

// Filtrar por etiqueta (comercio_etiqueta)
if (!empty($etiqueta)) {
    $args['tax_query'][] = array(
        'taxonomy' => 'comercio_etiqueta',
        'field'    => 'id',
        'terms'    => $etiqueta,
    );
}

// Filtrar por texto de búsqueda (search_text) en varios campos ACF
if (!empty($search_text)) {
    $args['meta_query'][] = array(
        'relation' => 'OR', // Usamos OR para que coincida en al menos uno de los campos
        array(
            'key' => 'comerce_data_city',
            'value' => $search_text,
            'compare' => 'LIKE',
        ),
        array(
            'key' => 'comerce_data_direction',
            'value' => $search_text,
            'compare' => 'LIKE',
        ),
        array(
            'key' => 'comerce_data_email',
            'value' => $search_text,
            'compare' => 'LIKE',
        ),
        array(
            'key' => 'comerce_data_cif',
            'value' => $search_text,
            'compare' => 'LIKE',
        ),
        array(
            'key' => 'message',
            'value' => $search_text,
            'compare' => 'LIKE',
        ),
        array(
            'key' => 'web',
            'value' => $search_text,
            'compare' => 'LIKE',
        ),
        array(
            'key' => 'post_excerpt', // Aquí agregamos el campo extracto
            'value' => $search_text,
            'compare' => 'LIKE',
        ),        
    );
}

// Ejecutar la consulta con los filtros
$query = new WP_Query($args);
$commerces = array();

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $coordinates = get_field('coordinates');
        $image_id = get_field('image');
        $new_img_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
        if ($coordinates) {
            list($lat, $lng) = explode(',', $coordinates);
            $commerces[] = array(
                'title' => get_the_title(),
                'city' => get_field('comerce_data_city'),
                'direction' => get_field('comerce_data_direction'),
                'email' => get_field('comerce_data_email'),
                'cif' => get_field('comerce_data_cif'),
                'message' => get_field('message'),
                'web' => get_field('web'),
                'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                'url' => get_permalink(),
                'new_image' => $new_img_url,
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
<section>
    <!-- Filtros de Taxonomía -->
    <form id="filterForm" class="flex flex-col justify-center md:flex-row gap-4 w-full md:w-8/12 mb-6 mx-auto" method="GET">
        <?php
            $terms = get_terms(array( 
                'taxonomy' => 'comercio_etiqueta', 
                'orderby' => 'name',
                'order' => 'ASC',
            ));
            if (!empty($terms) && !is_wp_error($terms)) {
                echo '<select id="comercio_etiqueta" name="comercio_etiqueta" class="border border-gray-200">';
                echo '<option value="">Sector</option>';
                foreach ($terms as $term) {
                    echo '<option value="' . $term->term_id . '" ' . selected( isset($_GET['comercio_etiqueta']) && $_GET['comercio_etiqueta'] == $term->term_id, true, false ) . '>' . $term->name . '</option>';
                }
                echo '</select>';
            }
        ?>
        <input type="text" id="search_text" name="search_text" class="border border-gray-200 px-2 py-2" placeholder="Buscar por contenido" value="<?= isset($_GET['search_text']) ? esc_attr($_GET['search_text']) : ''; ?>">
        <button type="submit" class="bg-dark text-white px-6 py-3">Filtrar</button>
    </form>

    <div id="map" class="h-auto w-full md:w-8/12" style="height:auto; min-height: 640px; width:100%; max-width:980px; margin:auto;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar el mapa
            var map = L.map('map').setView([39.418000, -0.401900], 14);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>'
            }).addTo(map);

            // Función para actualizar el mapa con los comercios filtrados
            function updateMap(filteredCommerces) {
                // Limpiar los marcadores previos
                map.eachLayer(function(layer) {
                    if (layer instanceof L.Marker) {
                        map.removeLayer(layer);
                    }
                });

                // Agregar nuevos marcadores
                filteredCommerces.forEach(function(commerce) {
                    var marker = L.marker([commerce.lat, commerce.lng]).addTo(map);
                    var web = commerce.web ? `<p>Web: <a href="${commerce.web}" target="_blank">${commerce.web}</a></p>` : '';
                    var popupContent = `
                        <div>
                            <h3 class="text-center">${commerce.title}</h3>
                            <a href="${commerce.url}">
                            <p>${commerce.message}</p>
                            <img src="${commerce.new_image}" alt="${commerce.title}" style="width: 100%; height: auto; margin: 2px 0;">
                            <p>${commerce.city} | ${commerce.direction}</p>
                            </a>
                            ${web}
                        </div>
                    `;
                    marker.bindPopup(popupContent);
                });
            }

            // Inicializar el mapa con los comercios filtrados
            updateMap(<?= json_encode($commerces); ?>);
        });
    </script>
</section>


<section id="posts-container">
        <?php get_template_part('partials/grid-comercios'); ?>
</section>




</main>

<?php get_footer(); ?>
