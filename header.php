<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	 <!--<script src="https://cdn.tailwindcss.com"></script>-->
	<?php wp_head(); ?>
	<!-- Google tag (gtag.js) 
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-GTKGPXZPZG"></script>-->
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
		<div class="mx-auto py-3 flex flex-row justify-between">
			<div id="logo" class="flex flex-row w-1/2 md:w-2/6 mx-auto">
				<?= (has_custom_logo()) ? the_custom_logo() : ''; ?>
			</div>
		</div>
	</header>
<<<<<<< HEAD
	
=======
>>>>>>> 9efb7ec451e0e157a598e11af6662037c4328fad
