<?php
get_header(); // Llama al encabezado del tema
include_once get_stylesheet_directory() . '/inc/comercio-form.php'; 
include_once get_stylesheet_directory() . '/inc/user-form.php'; 
?>
    <main class="contain 2xl:max-w-7xl mx-auto px-6">

        <?= get_template_part('partials/forms'); ?>

        <?= get_template_part('partials/filtros'); ?> 

        <?= get_template_part('partials/grid-comercios'); ?>

    </main>
<?php get_footer(); ?>