<?php get_header(); ?>

    <main class="contain 2xl:max-w-7xl mx-auto px-6">

        <?= get_template_part('partials/forms'); ?>

        <?= get_template_part('partials/filtros'); ?> 

        <?= get_template_part('partials/grid-comercios'); ?>

        <?= get_template_part('partials/modal-form'); ?>

    </main>
    
<?php get_footer(); ?>