<?php
/* Template Name: POR ADOPTAR */ 
get_header();
?>
<main class="contain 2xl:max-w-7xl mx-auto px-6">
    <div class="cxc-post-wrapper">
        <div id="cxc-posts" class="cxc-posts">
            <?php 
                // Obtener el número de página actual para la paginación
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $args = array(
                    'post_type' => 'comercios', // Define el custom post type
                    'posts_per_page' => 4, // Número de posts por página (ajustable)
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'paged' => $paged, // Agregar paginación
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'comercio_categoria', // Taxonomía de la categoría de comercio
                            'field'    => 'slug',  // Puede ser 'term_id', 'slug', o 'name'
                            'terms'    => array('adoptado', 'adoptado_void'), // Las categorías que queremos excluir
                            'operator' => 'NOT IN', // Excluye las categorías indicadas
                        ),
                    ),
                );
                $comercios_query = new WP_Query($args); // Crea la consulta
                if ($comercios_query->have_posts()) : ?>
                    <div class="comercios-list grid grid-cols-1 gap-12 md:gap-16">
                        <?php while ($comercios_query->have_posts()) : $comercios_query->the_post(); 
                            $id_post = get_the_ID();
                            $data = get_field('comerce_data', $id_post);
                            $contacto = $data['contact_name'] ?? '';
                            $email = $data['email'] ?? '';
                            $localidad = $data['city'] ?? '';
                            $direccion = $data['direction'] ?? '';
                            $cif = $data['cif'] ?? '';
                            $iban = $data['iban'] ?? '';
                            $bizum = $data['bizum'] ?? ''; 
                            $bg_color = (has_term(['adoptado', 'adoptado_void'], 'comercio_categoria', $post)) ? 'bg-dark' : 'bg-pink';
                            $title_color = (has_term(['adoptado', 'adoptado_void'], 'comercio_categoria', $post)) ? 'text-white' : 'text-dark';
                            //adopter
                            $user_rrss = get_field('rrss_adopter');
                        ?>
                            <div class="comercio-item">
                                <div class="bg-dark flex flex-col md:flex-row justify-between">
                                <!-- Imagen destacada -->
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>
                                    <div class="w-full md:w-4/12 min-h-64" style="background-image: url('<?php echo esc_url($featured_image_url); ?>'); background-size: cover; background-position: center;">
                                        <!-- Puedes añadir contenido aquí si lo necesitas -->
                                    </div>
                                <?php endif; ?>
                                    <article class="w-full py-6 flex flex-col gap-4 justify-between px-6">
                                        <h2 class="text-white text-3xl font-bold"><?php the_title(); ?></h2>
                                        <div class="w-full flex flex-col md:flex-row justify-between">
                                            <div class="w-full md:w-7/12 flex flex-col gap-3 self-start">
                                                <span class="text-white"><?= $contacto; ?>  |  <?= $email; ?></span>
                                                <ul class="grid grid-cols-3 w-full">
                                                    <li><?php _e('Localidad','adopta'); ?></li>
                                                    <li class="col-span-2"><?= $localidad; ?></li>
                                                    <li><?php _e('Dirección','adopta'); ?></li>
                                                    <li class="col-span-2"><?= $direccion; ?></li>
                                                    <?php if($cif): ?>
                                                    <li><?php _e('Cif','adopta'); ?></li>
                                                    <li class="col-span-2"><?= $cif; ?></li>
                                                    <?php endif; ?>                                            
                                                    <?php if($iban): ?>
                                                    <li><?php _e('IBAN','adopta'); ?></li>
                                                    <li class="col-span-2"><?= $iban; ?></li>
                                                    <?php endif; ?>
                                                    <?php if($bizum): ?>
                                                    <li><?php _e('Bizum','adopta'); ?></li>
                                                    <li class="col-span-2"><?= $bizum; ?></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                            <div class="w-full md:w-5/12 md:px-2 flex flex-col gap-4 text-white">
                                                <?php the_excerpt(); ?>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                                <div class="<?= $bg_color; ?> flex flex-col md:flex-row justify-between">
                                    <!-- Imagen adicional 2 (campo personalizado img_2) -->
                                    <?php $img_2_id = get_post_meta(get_the_ID(), 'img_2', true);
                                    if ($img_2_id) : 
                                        $img_2_url = wp_get_attachment_image_url($img_2_id, 'medium'); // Cambiar a wp_get_attachment_image_url para obtener la URL
                                    ?>
                                        <div class="w-full md:w-4/12 min-h-64" style="background-image: url('<?= esc_url($img_2_url); ?>'); background-size: cover; background-position: center;">
                                            <!-- Aquí puedes añadir contenido adicional si es necesario -->
                                        </div>
                                    <?php endif; ?>
                                    <article class="needs w-full py-6 flex flex-col gap-4 justify-start px-6">
                                        <h2 class="<?= $title_color ?> text-3xl font-bold"><?php _e('¿Qué necesita?','adopta'); ?></h2>
                                        <div class="<?=$title_color ?> gap-2 list-disc">
                                            <?php the_content(); ?>
                                        </div>
                                    </article>
                                </div>
                                <?php if(has_term(['adoptado', 'adoptado_void'], 'comercio_categoria', $post)):?>
                                    <div class="bg-orange flex flex-row justify-center w-full py-8 gap-6">
                                        <?= wp_get_attachment_image(66, 'thumb', true, ['class' => 'w-12 h-12 self-center']); ?>
                                        <div class="self-center">
                                            <span class="text-xs font-light"><?php _e('COMERCIO ADOPTADO POR','adopta'); ?></span>
                                            <h2 class="font-bold text-lg"><?= $user_rrss; ?></h2>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <button class="w-fit bg-dark py-3 px-6 text-white flex self-end ml-auto mr-0 justify-end flex" onclick="copyToClipboard('<?php the_permalink(); ?>')">Difunde en instagram</button>
                                <script>
                                    function copyToClipboard(text) {
                                        navigator.clipboard.writeText(text).then(() => {
                                            alert('¡Enlace copiado! Puedes pegarlo en Instagram.');
                                        });
                                    }
                                </script>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <!-- Paginación -->
                <div class="pagination">
                    <?php
                    // Paginación
                   /* echo paginate_links(array(
                        'total' => $comercios_query->max_num_pages, // Total de páginas
                        'current' => $paged, // Página actual
                        'mid_size' => 2, // Páginas a la izquierda y derecha del número actual
                        'prev_text' => __('&laquo; Anterior', 'adopta'),
                        'next_text' => __('Siguiente &raquo;', 'adopta'),
                    ));*/
                    ?>
                </div>
            <?php else : ?>
                <p><?php _e('No hay comercios disponibles.', 'adopta'); ?></p>
            <?php endif; ?>
        </div> 

    <a id="codex-load-more" class="codex-load-more w-fit bg-dark text-white mx-auto text-center cursor-pointer px-6 py-3" data-page="2" style="display: table;">
    <?php _e('Más Comercios','adopta'); ?>
    </a>

    </div>
</main>
<?php get_footer(); ?>
