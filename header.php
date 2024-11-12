<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php bloginfo( 'name' ); ?></title>
	<?php wp_head(); ?>
	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-GTKGPXZPZG"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	gtag('config', 'G-GTKGPXZPZG');
	</script>
</head>
<body <?php body_class(''); ?>>
	<?php wp_body_open(); ?>
	<header id="header" class="contain 2xl:max-w-7xl mx-auto px-6">
		<div class="mx-auto py-3 flex flex-row justify-between gap-4">
			<div id="logo" class="relative flex flex-col w-1/2 md:w-2/6 mx-auto gap-6">
				<?= (has_custom_logo()) ? the_custom_logo() : ''; ?>
				<span class="bg-orange text-dark md:text-lg font-bold w-fit self-center px-6 py-3 text-center">
				<?php $categoria_slug = ['adopta', 'adoptado_void']; $args = [ 'post_type'      => 'comercios', 'posts_per_page' => -1, 'tax_query'      => [ [ 'taxonomy' => 'comercio_categoria', 'field'    => 'slug', 'terms'    => array('adoptado','adoptado_void'), 'operator' => 'IN', ], ], 'fields' => 'ids', ]; $query = new WP_Query($args); $total_entradas = $query->found_posts; ?>
				<?= esc_html('¡Ya son ' . $total_entradas.' comercios adoptados!'); ?>
			</span>
			</div>
		</div>
	</header>