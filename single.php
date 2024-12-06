<?php get_header(); 
    $data = get_field('comerce_data');
    $id_post = get_the_ID();
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
    //New Data
    $message = get_field('message');
    $new_image = get_field('image');
    $new_img_url = $new_image ? wp_get_attachment_image_url($new_image, 'medium') : '';
    $web = get_field('web');
    $facebook = get_field('facebook');
    $instagram = get_field('instagram');
        //adopter
        $user_rrss = get_field('rrss_adopter');
        if(!$user_rrss){ 
            $user_ids = get_field('adopter'); 
            $user_name = array(); 
            if(is_array($user_ids)) {
                foreach ($user_ids as $user_id) { 
                    $user_info = get_userdata($user_id); 
                    if ($user_info) { 
                        $user_name[] = $user_info->nickname; 
                    } 
                } $user_rrss = implode(', ', $user_name); 
            }
        } 

?>
<main class="contain max-w-7xl mx-auto px-6 mt-12 md:mt-24">

    <?= get_template_part('partials/filtros'); ?>

    <div class="comercio-item">
       <div class="bg-dark flex flex-col justify-between">
                        <?php if(wp_is_mobile()): ?>
                            <div class="w-full grid <?php if($new_image): ?>grid-cols-1<?php else: ?>grid-cols-2<?php endif; ?>">
                        <?php else: ?>
                                <div class="bg-dark flex flex-col md:flex-row justify-between">
                        <?php endif; ?>
                                <!-- Imagen destacada -->
                                <?php if ($new_image): ?>
                                    <div class="w-full md:w-4/12 min-h-64" style="background-image: url('<?php echo esc_url($new_img_url); ?>'); background-size: cover; background-position: center;">
                                        <!-- Puedes añadir contenido aquí si lo necesitas -->
                                    </div>
                                <?php elseif (has_post_thumbnail()): ?>
                                    <?php $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>
                                    <div class="w-full md:w-4/12 min-h-64" style="background-image: url('<?php echo esc_url($featured_image_url); ?>'); background-size: cover; background-position: center;">
                                    </div>
                                <?php endif; ?>
                            <?php if(wp_is_mobile()):?>
                                <?php $img_2_id = get_post_meta(get_the_ID(), 'img_2', true);
                                    if ($img_2_id): 
                                        $img_2_url = wp_get_attachment_image_url($img_2_id, 'medium'); // Cambiar a wp_get_attachment_image_url para obtener la URL
                                    ?>
                                        <div class="w-full md:w-4/12 min-h-64" style="background-image: url('<?= esc_url($img_2_url); ?>'); background-size: cover; background-position: center;">
                                            <!-- Aquí puedes añadir contenido adicional si es necesario -->
                                        </div>
                                    <?php endif; ?>
                            <?php endif; ?>
                            <?php if(wp_is_mobile()):?>
                                </div>
                                <details class="bg-dark flex flex-col md:flex-row justify-between">
                                        <summary><h2 class="text-white text-2xl font-bold"><?php the_title(); ?></h2></summary>
                            <?php endif; ?>
                                    <article class="w-full py-6 flex flex-col gap-4 justify-start px-6">
                            <?php if(!wp_is_mobile()):?>
                                        <h2 class="text-white text-3xl font-bold"><?php the_title(); ?></h2>
                            <?php endif; ?>
                                        <div class="w-full flex flex-col gap-2 md:flex-row justify-between">
                                            <div class="w-full md:w-7/12 flex flex-col gap-3 self-start">
                                                <span class="text-white"><?= $contacto; ?>  |  <?= $email; ?></span>
                                                <ul class="grid grid-cols-3 w-full">
                                                    <li><?php esc_html_e('Localidad','adopta'); ?></li>
                                                    <li class="col-span-2"><?= $localidad; ?></li>
                                                    <li><?php esc_html_e('Dirección','adopta'); ?></li>
                                                    <li class="col-span-2"><?= $direccion; ?></li>
                                                    <?php if($cif): ?>
                                                    <li><?php esc_html_e('Cif','adopta'); ?></li>
                                                    <li class="col-span-2"><?= $cif; ?></li>
                                                    <?php endif; ?>                                            
                                                    <?php if($iban && !has_term('abierto','comercio_categoria', $post)): ?>
                                                    <li><?php esc_html_e('IBAN','adopta'); ?></li>
                                                    <li class="col-span-2"><?= $iban; ?></li>
                                                    <?php endif; ?>
                                                    <?php if($bizum && !has_term('abierto','comercio_categoria', $post)): ?>
                                                    <li><?php esc_html_e('Bizum','adopta'); ?></li>
                                                    <li class="col-span-2"><?= $bizum; ?></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                            <div class="w-full md:w-5/12 md:px-2 flex flex-col gap-4 text-white">
                                                <?php if($message): ?>
                                                    <?= $message; ?>
                                                <?php else: ?>
                                                    <?php the_excerpt(); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php if($web || $facebook || $instagram): ?>
                                <div class="bg-pink py-6 px-6">
                                    <ul class="flex flex-row justify-center gap-4">
                                    <?php if($web): ?>
                                        <li>
                                            <a href="<?= esc_url($web) ?>" target="_blank" rel="noopener nofollow noreferrer">                                                 
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill:white;transform: ;msFilter:;"><path d="M4.222 19.778a4.983 4.983 0 0 0 3.535 1.462 4.986 4.986 0 0 0 3.536-1.462l2.828-2.829-1.414-1.414-2.828 2.829a3.007 3.007 0 0 1-4.243 0 3.005 3.005 0 0 1 0-4.243l2.829-2.828-1.414-1.414-2.829 2.828a5.006 5.006 0 0 0 0 7.071zm15.556-8.485a5.008 5.008 0 0 0 0-7.071 5.006 5.006 0 0 0-7.071 0L9.879 7.051l1.414 1.414 2.828-2.829a3.007 3.007 0 0 1 4.243 0 3.005 3.005 0 0 1 0 4.243l-2.829 2.828 1.414 1.414 2.829-2.828z"></path><path d="m8.464 16.95-1.415-1.414 8.487-8.486 1.414 1.415z"></path></svg>
                                            </a>
                                        </li>
                                    <?php endif; ?>                                                                              
                                    <?php if($facebook): 
                                        $facebook_url = strpos($facebook, 'http') === 0 ? $facebook : 'https://facebook.com/' . ltrim($facebook, '/');
                                        ?>
                                        <li>
                                            <a href="<?= esc_url($facebook_url) ?>" target="_blank" rel="noopener nofollow noreferrer">                                                                                            
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill:white;transform: ;msFilter:;"><path d="M12.001 2.002c-5.522 0-9.999 4.477-9.999 9.999 0 4.99 3.656 9.126 8.437 9.879v-6.988h-2.54v-2.891h2.54V9.798c0-2.508 1.493-3.891 3.776-3.891 1.094 0 2.24.195 2.24.195v2.459h-1.264c-1.24 0-1.628.772-1.628 1.563v1.875h2.771l-.443 2.891h-2.328v6.988C18.344 21.129 22 16.992 22 12.001c0-5.522-4.477-9.999-9.999-9.999z"></path></svg>
                                            </a>                                                
                                        </li>
                                    <?php endif; ?>
                                    <?php if($instagram): 
                                         $instagram_url = strpos($instagram, 'http') === 0 ? $instagram : 'https://instagram.com/' . ltrim($instagram, '/');
                                        ?>
                                        <li>
                                            <a href="<?= esc_url($instagram_url) ?>" target="_blank" rel="noopener nofollow noreferrer">                                                                                            
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill:white;transform: ;msFilter:;"><path d="M11.999 7.377a4.623 4.623 0 1 0 0 9.248 4.623 4.623 0 0 0 0-9.248zm0 7.627a3.004 3.004 0 1 1 0-6.008 3.004 3.004 0 0 1 0 6.008z"></path><circle cx="16.806" cy="7.207" r="1.078"></circle><path d="M20.533 6.111A4.605 4.605 0 0 0 17.9 3.479a6.606 6.606 0 0 0-2.186-.42c-.963-.042-1.268-.054-3.71-.054s-2.755 0-3.71.054a6.554 6.554 0 0 0-2.184.42 4.6 4.6 0 0 0-2.633 2.632 6.585 6.585 0 0 0-.419 2.186c-.043.962-.056 1.267-.056 3.71 0 2.442 0 2.753.056 3.71.015.748.156 1.486.419 2.187a4.61 4.61 0 0 0 2.634 2.632 6.584 6.584 0 0 0 2.185.45c.963.042 1.268.055 3.71.055s2.755 0 3.71-.055a6.615 6.615 0 0 0 2.186-.419 4.613 4.613 0 0 0 2.633-2.633c.263-.7.404-1.438.419-2.186.043-.962.056-1.267.056-3.71s0-2.753-.056-3.71a6.581 6.581 0 0 0-.421-2.217zm-1.218 9.532a5.043 5.043 0 0 1-.311 1.688 2.987 2.987 0 0 1-1.712 1.711 4.985 4.985 0 0 1-1.67.311c-.95.044-1.218.055-3.654.055-2.438 0-2.687 0-3.655-.055a4.96 4.96 0 0 1-1.669-.311 2.985 2.985 0 0 1-1.719-1.711 5.08 5.08 0 0 1-.311-1.669c-.043-.95-.053-1.218-.053-3.654 0-2.437 0-2.686.053-3.655a5.038 5.038 0 0 1 .311-1.687c.305-.789.93-1.41 1.719-1.712a5.01 5.01 0 0 1 1.669-.311c.951-.043 1.218-.055 3.655-.055s2.687 0 3.654.055a4.96 4.96 0 0 1 1.67.311 2.991 2.991 0 0 1 1.712 1.712 5.08 5.08 0 0 1 .311 1.669c.043.951.054 1.218.054 3.655 0 2.436 0 2.698-.043 3.654h-.011z"></path></svg>
                                            </a>
                                        </li>
                                        <?php endif; ?>                                        
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <?php if(!has_term('abierto','comercio_categoria', $post)) : ?>
                                <div class="<?= $bg_color; ?> flex flex-col md:flex-row justify-between">
                                    <?php if(!wp_is_mobile()):?>
                                    <!-- Imagen adicional 2 (campo personalizado img_2) -->
                                    <?php $img_2_id = get_post_meta(get_the_ID(), 'img_2', true);
                                    if ($img_2_id && !has_term('abierto','comercio_categoria', $post)): 
                                        $img_2_url = wp_get_attachment_image_url($img_2_id, 'medium'); // Cambiar a wp_get_attachment_image_url para obtener la URL
                                    ?>
                                        <div class="w-full md:w-4/12 min-h-64" style="background-image: url('<?= esc_url($img_2_url); ?>'); background-size: cover; background-position: center;">
                                            <!-- Aquí puedes añadir contenido adicional si es necesario -->
                                        </div>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    <article class="needs w-full py-6 flex flex-col gap-4 justify-start px-6">
                                        <h2 class="<?= $title_color ?> text-3xl font-bold"><?php esc_html_e('¿Qué necesita?','adopta'); ?></h2>
                                        <div class="<?=$title_color ?> gap-2 list-disc">
                                            <?php the_content(); ?>
                                        </div>
                                    </article>
                                </div>
                            <?php endif; ?>
                                <?php if(wp_is_mobile()):?>
                                    </details>
                                    <?php if(!has_term(['adoptado', 'adoptado_void'], 'comercio_categoria', $post)): ?>
                                        <div class="bg-pink w-full h-6"> 
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if(has_term(['adoptado', 'adoptado_void'], 'comercio_categoria', $post)):?>
                                    <div class="bg-orange flex flex-row justify-center w-full py-8 gap-6">
                                        <?= wp_get_attachment_image(66, 'thumb', true, ['class' => 'w-12 h-12 self-center']); ?>
                                        <div class="self-center">
                                            <span class="text-xs font-light"><?php esc_html_e('COMERCIO ADOPTADO POR','adopta'); ?></span>
                                            <h2 class="font-bold text-lg"><?= $user_rrss; ?></h2>
                                        </div>
                                    </div>
                                <?php endif; ?>
                        <div class="w-full flex flex-row justify-between">
                                <?php if(!has_term(['adoptado', 'adoptado_void', 'reservado'], 'comercio_categoria', $post)):?>
                                    <button onclick="modal_form(<?php echo $id_post ?>, '<?php the_title(); ?>');" class="bg-orange text-dark py-3 px-6 w-fit">
                                        <?php esc_html_e('Adopta este comercio','adopta'); ?>
                                    </button>
                                <?php endif; ?>
                                 <?php if (!has_term('abierto','comercio_categoria', $post)): ?>
                                    <button class="w-fit bg-dark py-3 px-6 text-white flex flex-row gap-3 self-end ml-auto mr-0 justify-end flex" onclick="copyToClipboard('<?php the_permalink(); ?>')"><?php esc_html_e('Difunde el negocio','adopta'); ?>
                                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 512"><path fill="#fff" fill-rule="nonzero" d="M170.663 256.157c-.083-47.121 38.055-85.4 85.167-85.483 47.121-.092 85.407 38.03 85.499 85.16.091 47.129-38.047 85.4-85.176 85.492-47.112.09-85.399-38.039-85.49-85.169zm-46.108.091c.141 72.602 59.106 131.327 131.69 131.186 72.592-.141 131.35-59.09 131.209-131.692-.141-72.577-59.114-131.335-131.715-131.194-72.585.141-131.325 59.115-131.184 131.7zm237.104-137.091c.033 16.953 13.817 30.681 30.772 30.648 16.961-.033 30.689-13.811 30.664-30.764-.033-16.954-13.818-30.69-30.78-30.657-16.962.033-30.689 13.818-30.656 30.773zm-208.696 345.4c-24.958-1.087-38.511-5.234-47.543-8.709-11.961-4.629-20.496-10.178-29.479-19.094-8.966-8.95-14.532-17.46-19.202-29.397-3.508-9.032-7.73-22.569-8.9-47.527-1.269-26.982-1.559-35.077-1.683-103.432-.133-68.339.116-76.434 1.294-103.441 1.069-24.942 5.242-38.512 8.709-47.536 4.628-11.977 10.161-20.496 19.094-29.479 8.949-8.982 17.459-14.532 29.403-19.202 9.025-3.525 22.561-7.714 47.511-8.9 26.998-1.277 35.085-1.551 103.423-1.684 68.353-.132 76.448.108 103.456 1.295 24.94 1.086 38.51 5.217 47.527 8.709 11.968 4.628 20.503 10.144 29.478 19.094 8.974 8.95 14.54 17.443 19.21 29.412 3.524 9 7.714 22.553 8.892 47.494 1.285 26.999 1.576 35.095 1.7 103.433.132 68.355-.117 76.451-1.302 103.441-1.087 24.958-5.226 38.52-8.709 47.561-4.629 11.952-10.161 20.487-19.103 29.471-8.941 8.949-17.451 14.531-29.403 19.201-9.009 3.517-22.561 7.714-47.494 8.9-26.998 1.269-35.086 1.559-103.448 1.684-68.338.132-76.424-.125-103.431-1.294zM149.977 1.773c-27.239 1.285-45.843 5.648-62.101 12.018-16.829 6.561-31.095 15.354-45.286 29.604C28.381 57.653 19.655 71.944 13.144 88.79c-6.303 16.299-10.575 34.912-11.778 62.168C.172 178.264-.102 186.973.031 256.489c.133 69.508.439 78.234 1.741 105.547 1.302 27.231 5.649 45.828 12.019 62.093 6.569 16.83 15.353 31.088 29.611 45.288 14.25 14.201 28.55 22.918 45.404 29.438 16.282 6.295 34.902 10.583 62.15 11.778 27.305 1.203 36.022 1.468 105.521 1.335 69.532-.132 78.25-.439 105.555-1.733 27.239-1.303 45.826-5.665 62.1-12.019 16.829-6.586 31.095-15.353 45.288-29.611 14.191-14.251 22.917-28.55 29.428-45.405 6.304-16.282 10.592-34.903 11.777-62.134 1.195-27.322 1.478-36.048 1.344-105.556-.133-69.516-.447-78.225-1.741-105.523-1.294-27.255-5.657-45.844-12.019-62.118-6.577-16.829-15.352-31.079-29.602-45.287-14.25-14.192-28.55-22.935-45.404-29.429-16.29-6.305-34.903-10.601-62.15-11.779C333.747.164 325.03-.102 255.506.031c-69.507.133-78.224.431-105.529 1.742z"/></svg>
                                    </button>
                                <?php else: ?>
                                    <span class="w-full bg-dark text-white py-3 px-6 text-center">
                                        <?php esc_html_e('¡Este comercio está abierto de nuevo!','adopta'); ?>
                                    </span>
                                <?php endif; ?>
                        </div>
    </div>
</main>
<?= get_template_part('partials/modal-form'); ?>
<?php get_footer(); ?>