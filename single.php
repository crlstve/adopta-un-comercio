<?php get_header(); 
    $data = get_field('comerce_data');
    $contacto = $data['contact_name'] ?? '';
    $email = $data['email'] ?? '';
    $localidad = $data['city'] ?? '';
    $direccion = $data['direction'] ?? '';
    $cif = $data['cif'] ?? '';
    $iban = $data['iban'] ?? '';
    $bizum = $data['bizum'] ?? ''; 
    $bg_color = (has_term(['adoptado', 'adoptado_void'], 'comercio_categoria', $post)) ? 'bg-dark' : 'bg-pink';
    $title_color = (has_term(['adoptado', 'adoptado_void'], 'comercio_categoria', $post)) ? 'text-white' : 'text-dark';
    $user_rrss = get_field('rrss_adopter');
    $adopter = get_field('adopter'); 
    // Obtener el ID de los usuarios desde el campo ACF 'adopter', ahora $user_id es un array
        $user_ids = get_field('adopter'); // Obtener el array de IDs de los usuarios
        // Crear un array para almacenar los correos electrónicos
        $user_name = array();
        foreach ($user_ids as $user_id) {
            // Obtener la información del usuario usando get_userdata
            $user_info = get_userdata($user_id);
            // Verificar si la información del usuario existe y agregar el correo al array
            if ($user_info) {
                // Agregar el correo al array
                $user_name[] = $user_info->user_nicename;
            }
        }
?>
<main class="contain max-w-7xl mx-auto px-6 mt-12 md:mt-24">
    <div class="comercio-item">
       <div class="bg-dark flex flex-col md:flex-row justify-between">
       <!-- Imagen destacada -->
       <?php if (has_post_thumbnail()) : ?>
                   <?php $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>
                   <div class="w-full md:w-4/12 min-h-64" style="background-image: url('<?php echo esc_url($featured_image_url); ?>'); background-size: cover; background-position: center;">
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
                                   <li><?php _e('Cif','adopta'); ?></li>
                                   <li class="col-span-2"><?= $cif; ?></li>
                                   <li><?php _e('IBAN','adopta'); ?></li>
                                   <?php if($iban): ?>
                                   <li class="col-span-2"><?= $iban; ?></li>
                                   <?php endif; ?>
                                   <?php if($bizum): ?>
                                   <li><?php _e('Bizum','adopta'); ?></li>
                                   <?php endif; ?>
                                   <li class="col-span-2"><?= $bizum; ?></li>
                               </ul>
                           </div>
                           <div class="w-full md:w-5/12 px-2 flex flex-col gap-4 text-white">
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
                       <div class="gap-2 list-disc">
                           <?php the_content(); ?>
                       </div>
                   </article>
       </div>
       <?php if(has_term(['adoptado', 'adoptado_void'], 'comercio_categoria', $post)):?>
                   <div class="bg-orange flex flex-row justify-center w-full py-8 gap-6">
                       <?= wp_get_attachment_image(66, 'thumb', true, ['class' => 'w-12 h-12 self-center']); ?>
                       <div class="self-center">
                           <span class="text-xs font-light"><?php _e('COMERCIO ADOPTADO POR','adopta'); ?></span>
                           <h2 class="font-bold text-lg"><?= implode(', ', $user_name); ?></h2>
                       </div>
                   </div>
       <?php endif; ?>
   </div>

</main>
<?php get_footer(); ?>