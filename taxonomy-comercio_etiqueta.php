<?php
get_header(); // Llama al encabezado del tema
?>
    <main class="contain 2xl:max-w-7xl mx-auto px-6">

        <?= get_template_part('partials/filtros'); ?> 

        <?= get_template_part('partials/grid-comercios'); ?>

    </main>
<?php get_footer(); ?>