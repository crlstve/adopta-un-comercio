<?php
// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) { exit; }
/*******************************************************************************
 *  INCLUDES
 ******************************************************************************/
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
    add_theme_support('post-thumbnails');
/*******************************************************************************
 *  CARGA DE ESTILOS Y SCRIPTS
 ******************************************************************************/
    // Cargar estilos
        function register_styles() {
            wp_enqueue_style('tailwindcss', get_stylesheet_directory_uri() . '/assets/css/theme.css', array(), '1.0', 'all');
            if(wp_is_mobile()){
                wp_enqueue_style('splidecss', get_stylesheet_directory_uri() . '/assets/css/splide.min.css', array(), '4.1.3');
                wp_script_add_data( 'splidecss', 'defer', true );
            }
        }
        add_action('wp_enqueue_scripts', 'register_styles');        
    // Cargar js
        function register_scripts() {
            if(!is_archive() || !is_tax()){
                wp_enqueue_script( 'toggle-forms', get_stylesheet_directory_uri() . '/assets/js/toggle-forms.js', array(), '1.0', false );
            }     
            if(wp_is_mobile()){
                wp_enqueue_script( 'splidejs', get_stylesheet_directory_uri() . '/assets/js/splide.min.js', array(), '4.1.3');
                wp_enqueue_script( 'filter-slider', get_stylesheet_directory_uri() . '/assets/js/filter-slider.js', array(), '4.1.3');
                wp_script_add_data( 'splidejs', 'defer', true );        
            }
        }
        add_action( 'wp_enqueue_scripts', 'register_scripts' );
    // Menú clásico de WordPress
        function child_theme_setup() {
            register_nav_menus(array(
                'primary' => __('Primary Menu', 'twentytwentyfourchild'),
            ));
        }
        add_action('after_setup_theme', 'child_theme_setup');
    // Personalización del Wawlker para el Menú
        class Custom_Walker_Nav_Menu extends Walker_Nav_Menu {
            public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
                $classes = 'text-base md:text-lg text-gray-400 hover:text-black text-base md:text-xl';
                $output .= sprintf('<li class="border border-1 border-black py-2 px-4 text-center"><a href="%s" class="%s">%s</a></li>',
                    esc_url($item->url),
                    esc_attr($classes),
                    esc_html($item->title)
                );
            }
        }
    //  Buscador de Comercios
        function custom_search_only_titles($query) {
            if (!is_admin() && $query->is_main_query() && $query->is_search()) {
                $query->set('post_type', 'comercios'); // Filtra al CPT `comercios`
                add_filter('posts_search', function($search, $wp_query) {
                    global $wpdb;
                    if ($wp_query->is_search()) {
                        $search_term = esc_sql($wp_query->query_vars['s']);
                        $search = "AND ({$wpdb->posts}.post_title LIKE '%{$search_term}%')";
                    }
                    return $search;
                }, 10, 2);
            }
        }
        add_action('pre_get_posts', 'custom_search_only_titles');

/*******************************************************************************
 *  COMERCIOS
 ******************************************************************************/
    function custom_post_type_comercios() {
        $labels = array(
            'name'               => 'Comercios',
            'singular_name'      => 'Comercio',
            'menu_name'          => 'Comercios',
            'name_admin_bar'     => 'Comercio',
            'add_new'            => 'Añadir Nuevo',
            'add_new_item'       => 'Añadir Nuevo Comercio',
            'new_item'           => 'Nuevo Comercio',
            'edit_item'          => 'Editar Comercio',
            'view_item'          => 'Ver Comercio',
            'all_items'          => 'Todos los Comercios',
            'search_items'       => 'Buscar Comercios',
            'not_found'          => 'No se encontraron Comercios',
            'not_found_in_trash' => 'No hay Comercios en la Papelera'
        );
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'show_in_nav_menus' => true,
            'has_archive'        => 'comercios',
            'rewrite'            => array('slug' => 'comercios'),
            'show_in_rest'       => false,
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
            'taxonomies'         => array('comercio_categoria', 'comercio_etiqueta'),
            'menu_icon'          => 'dashicons-store',
        );
        register_post_type('comercios', $args);

        // Registrar Categoría Personalizada para Comercios
        register_taxonomy(
            'comercio_categoria',
            'comercios',
            array(
                'label'        => 'Categorías de Comercios',
                'rewrite'      => array('slug' => 'comercio'),
                'hierarchical' => true,
                'show_in_rest' => false,
                'show_in_nav_menus' => true,
            )
        );

        // Registrar la taxonomía de Etiqueta para Comercios
        register_taxonomy(
            'comercio_etiqueta',
            'comercios',
            array(
                'label'        => 'Etiquetas de Comercios',
                'rewrite'      => array('slug' => 'comercio-etiqueta'),
                'hierarchical' => false, 
                'show_in_rest' => false,
                'show_in_nav_menus' => true, 
            )
        );
    }
    add_action('init', 'custom_post_type_comercios');
    add_post_type_support('comercios', 'thumbnail');
/*******************************************************************************
 * CORREO CUANDO SE PUBLICA EL COMERCIO
 ******************************************************************************/
    function enviar_correo_cuando_se_publique_comercio($new_status, $old_status, $post) {
        // Verificar si es el post tipo 'comercios' y si el estado cambia a 'publish'
        if ($post->post_type == 'comercios' && $new_status == 'publish' && $old_status != 'publish') {
            // Obtener el email del campo ACF dentro del grupo 'comerce_data'
            $email_comercio = get_field('comerce_data_email', $post->ID);
            if ($email_comercio) {
                // Configurar el correo
                $to = $email_comercio;
                $subject = 'Tu Comercio ha sido Publicado';
                $message = "
                <div style='text-align: left;margin: 20px auto;width: 80%;max-width: 600px;padding: 20px;border-radius: 5px;'>
                    <img src='https://adoptauncomercio.com/wp-content/uploads/2024/11/Recurso-6assets.png' alt='Logo' style='width: 250px;height: 100px;margin:2rem auto;display: block;'>
                    <h2>Tu comercio ha sido registrado en nuestra web.</h2>
                    <p>Nos ponemos manos a la obra para que pueda ser adoptado lo más pronto posible. Recuerda que una vez esté subido a la plataforma, las personas ya pueden empezar a ayudarte, no hace falta estar adoptado por un influencer.</p>
                    <p>Esta iniciativa se ha creado de forma solidaria, para ayudar a cada uno de los comercios locales que lo necesitan. Nos alegra poder servirte de altavoz y te damos mucho ánimo desde diferentes empresas que, como tú, somos de la terreta.</p>
                    <p>Gracias por confiar en nosotros.</p>
                    <p>¡Un abrazo!</p>
                        <table style='background-color:#232323;color:#ffffff;margin-top:3rem;padding:2rem 1rem; font-size:0.8rem;width:100%'>
                            <tr>
                                <td style='width:33%;text-align:center;'><image src='https://adoptauncomercio.com/wp-content/uploads/2024/11/siberia.webp' alt='Logo' style='height:16px;margin:1.6rem 0rem'></td>
                                <td style='width:33%;text-align:center;'><image src='https://adoptauncomercio.com/wp-content/uploads/2024/11/fiftykey.webp' alt='Logo' style='height:16px;margin:1.6rem 0rem'></td>
                                <td style='width:33%;text-align:center;'><image src='https://adoptauncomercio.com/wp-content/uploads/2024/11/idital.webp' alt='Logo' style='height:16px;margin:1.6rem 0rem'></td>
                            </tr>
                        </table>
                    <div style='background-color:#e7a300;color:#232323;padding: 2rem 1rem; font-size:0.8rem;'>
                        <p>NOTA LEGAL:  Este mensaje y sus archivos adjuntos van dirigidos exclusivamente a su destinatario, pudiendo contener información confidencial sometida a secreto profesional. No está permitida su reproducción o distribución sin la autorización expresa de INTERTRAFOR S.L. Si usted no es el destinatario final por favor elimínelo e infórmenos por esta vía.</p>
                        <p>Así mismo le informamos que tratamos los datos que usted nos ha facilitado para realizar la gestión administrativa, contable y fiscal, así como enviarle comunicaciones comerciales sobre nuestros productos y/o servicios. Legitimación: consentimiento del interesado y/o ejecución de un contrato y/o interés legítimo del responsable. No se cederán datos a terceros. Tiene derecho a acceder, rectificar y suprimir los datos, así como otros derechos, indicados en la información adicional, que podrá consultar en: el AVISO LEGAL de nuestra página web: <a href='www.adoptauncomercio.com'>www.adoptauncomercio.com</a>. Si usted no desea recibir nuestra información, póngase en contacto con nosotros enviando un correo electrónico a la siguiente dirección: <a href='mailto:adopta@adoptauncomercio.com'>adopta@adoptauncomercio.com</a>.</p>
                    </div>
                </div>";
        $headers = array(
            'Content-Type: text/html; charset=UTF-8', // Esto asegura que el correo sea en HTML
            'From' => 'noreply@tudominio.com',      // Remitente
        );
                // Enviar el correo
                wp_mail($to, $subject, $message, $headers);
            }
        }
    }
    add_action('transition_post_status', 'enviar_correo_cuando_se_publique_comercio', 10, 3);

/*******************************************************************************
 * CORREO CUANDO SE ADOPTA EL COMERCIO
 ******************************************************************************/
    function enviar_correo_post_adoptado($post_id, $post, $update) {
        // No hacer nada si el post es un autoguardado, no es de tipo 'comercios' o no está publicado
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if ($post->post_type !== 'comercios') return;
        if ($post->post_status !== 'publish') return; // Asegurarse de que el post esté publicado

        // Verificar si el post tiene la categoría 'adoptado'
        if (has_term('adoptado', 'comercio_categoria', $post)) {
            // Obtener el campo de email de ACF dentro del grupo 'comerce_data'
            $email_comercio = get_field('comerce_data_email', $post_id);

            // Obtener el ID del usuario desde el campo ACF 'adopter'
            $user_ids = get_field('adopter'); // Obtener el array de IDs de los usuarios

            // Crear un array para almacenar los correos electrónicos
            $user_emails = array();
            foreach ($user_ids as $user_id) {
                // Obtener la información del usuario usando get_userdata
                $user_info = get_userdata($user_id);
                // Verificar si la información del usuario existe y agregar el correo al array
                if ($user_info) {
                    // Agregar el correo al array
                    $user_emails[] = $user_info->user_email;
                }
            }

            // Obtener los datos del usuario principal
            $user_info = isset($user_ids[0]) ? get_userdata($user_ids[0]) : null;

            // Si no se encuentra el email del comercio, salimos de la función
            if (empty($email_comercio)) return;
                           
            // Preparar el correo al comercio
            $to = $email_comercio;
            $subject = 'Negocio adoptado';
            $message = "
                <div style='text-align: left;margin: 20px auto;width: 80%;max-width: 600px;padding: 20px;border-radius: 5px;'>
                <img src='https://adoptauncomercio.com/wp-content/uploads/2024/11/Recurso-6assets.png' alt='Logo' style='width: 250px;height: 100px;margin:2rem auto;display: block;'>
                <h2>¡Tú negocio ha sido adoptado por {$user_info->user_login}!</h2>
                <p>¿Qué pasa ahora? {$user_info->user_login} se va a encargar de dar voz a tu negocio y todo lo que necesita. Ya puede ponerse en contacto contigo y así informar a su comunidad sobre los avances en tu local. </p>
                <p>A partir de hoy ¡sois un equipo! Y es que ya se ha demostrado que, trabajando de la mano, llegamos más lejos. </p>
                <p>No olvides compartir en tus redes sociales lo que tu influencer suba ¡así llegará a más personas y quien está al otro lado de la pantalla podrá seguirlo todo de cerca!</p>
                <p>¡Un abrazo!</p>
                    <table style='background-color:#232323;color:#ffffff;margin-top:3rem;padding:2rem 1rem; font-size:0.8rem;width:100%'>
                        <tr>
                            <td style='width:33%;text-align:center;'><image src='https://adoptauncomercio.com/wp-content/uploads/2024/11/siberia.webp' alt='Logo' style='height:16px;margin:1.6rem 0rem'></td>
                            <td style='width:33%;text-align:center;'><image src='https://adoptauncomercio.com/wp-content/uploads/2024/11/fiftykey.webp' alt='Logo' style='height:16px;margin:1.6rem 0rem'></td>
                            <td style='width:33%;text-align:center;'><image src='https://adoptauncomercio.com/wp-content/uploads/2024/11/idital.webp' alt='Logo' style='height:16px;margin:1.6rem 0rem'></td>
                        </tr>
                    </table>
                    <div style='background-color:#e7a300;color:#232323;padding: 2rem 1rem; font-size:0.8rem;'>
                        <p>NOTA LEGAL:  Este mensaje y sus archivos adjuntos van dirigidos exclusivamente a su destinatario, pudiendo contener información confidencial sometida a secreto profesional. No está permitida su reproducción o distribución sin la autorización expresa de INTERTRAFOR S.L. Si usted no es el destinatario final por favor elimínelo e infórmenos por esta vía.</p>
                        <p>Así mismo le informamos que tratamos los datos que usted nos ha facilitado para realizar la gestión administrativa, contable y fiscal, así como enviarle comunicaciones comerciales sobre nuestros productos y/o servicios. Legitimación: consentimiento del interesado y/o ejecución de un contrato y/o interés legítimo del responsable. No se cederán datos a terceros. Tiene derecho a acceder, rectificar y suprimir los datos, así como otros derechos, indicados en la información adicional, que podrá consultar en: el AVISO LEGAL de nuestra página web: <a href='www.adoptauncomercio.com'>www.adoptauncomercio.com</a>. Si usted no desea recibir nuestra información, póngase en contacto con nosotros enviando un correo electrónico a la siguiente dirección: <a href='mailto:adopta@adoptauncomercio.com'>adopta@adoptauncomercio.com</a>.</p>
                    </div>
                </div>";

            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From' => 'noreply@tudominio.com',
            );

            // Enviar el correo al comercio
            wp_mail($to, $subject, $message, $headers);

            // Enviar segundo correo con contenido distinto a los adoptantes
            if (!empty($user_emails)) {
                // Preparar el segundo correo
                $negocio = get_the_title($post_id);
                $subject_second = 'Has adoptado un negocio';
                $message_second = "
                    <div style='text-align: left;margin: 20px auto;width: 80%;max-width: 600px;padding: 20px;border-radius: 5px;'>
                    <img src='https://adoptauncomercio.com/wp-content/uploads/2024/11/Recurso-6assets.png' alt='Logo' style='width: 250px;height: 100px;margin:2rem auto;display: block;'>
                    <h2>¡ENHORABUENA! Tu solicitud se ha confirmado.</h2>
                    <p>Te confirmamos que has adoptado a <b> $negocio </b> ¡ya puedes empezar a difundir y ayudar a tu comercio! Solo tienes que poner su nombre en el buscador de la web y verás toda su información.</p>	
                    <p>Gracias por apoyar a un comercio local. Ahora tú y el negocio sois un equipo.</p>
                    <p>Muchísimas gracias por apoyar esta inciativa.</p>
                    <p>Volvemos a demostrar que en equipo vamos a llegar muy lejos</p>
                    <p>¡Un abrazo!</p>
                        <table style='background-color:#232323;color:#ffffff;margin-top:3rem;padding:2rem 1rem; font-size:0.8rem;width:100%'>
                            <tr>
                                <td style='width:33%;text-align:center;'><image src='https://adoptauncomercio.com/wp-content/uploads/2024/11/siberia.webp' alt='Logo' style='height:16px;margin:1.6rem 0rem'></td>
                                <td style='width:33%;text-align:center;'><image src='https://adoptauncomercio.com/wp-content/uploads/2024/11/fiftykey.webp' alt='Logo' style='height:16px;margin:1.6rem 0rem'></td>
                                <td style='width:33%;text-align:center;'><image src='https://adoptauncomercio.com/wp-content/uploads/2024/11/idital.webp' alt='Logo' style='height:16px;margin:1.6rem 0rem'></td>
                            </tr>
                        </table>
                    <div style='background-color:#e7a300;color:#232323;padding: 2rem 1rem; font-size:0.8rem;'>
                        <p>NOTA LEGAL:  Este mensaje y sus archivos adjuntos van dirigidos exclusivamente a su destinatario, pudiendo contener información confidencial sometida a secreto profesional. No está permitida su reproducción o distribución sin la autorización expresa de INTERTRAFOR S.L. Si usted no es el destinatario final por favor elimínelo e infórmenos por esta vía.</p>
                        <p>Así mismo le informamos que tratamos los datos que usted nos ha facilitado para realizar la gestión administrativa, contable y fiscal, así como enviarle comunicaciones comerciales sobre nuestros productos y/o servicios. Legitimación: consentimiento del interesado y/o ejecución de un contrato y/o interés legítimo del responsable. No se cederán datos a terceros. Tiene derecho a acceder, rectificar y suprimir los datos, así como otros derechos, indicados en la información adicional, que podrá consultar en: el AVISO LEGAL de nuestra página web: <a href='www.adoptauncomercio.com'>www.adoptauncomercio.com</a>. Si usted no desea recibir nuestra información, póngase en contacto con nosotros enviando un correo electrónico a la siguiente dirección: <a href='mailto:adopta@adoptauncomercio.com'>adopta@adoptauncomercio.com</a>.</p>
                    </div>
                </div>";

                $headers_second = array(
                    'Content-Type: text/html; charset=UTF-8',
                    'From' => 'noreply@tudominio.com',
                );

                foreach ($user_emails as $email) {
                    wp_mail($email, $subject_second, $message_second, $headers_second);
                }
            }
        }
    }
    add_action('save_post', 'enviar_correo_post_adoptado', 10, 3);

    // Añade una columna personalizada para mostrar el post vinculado
    function agregar_columna_post_vinculado($columns) {
        $columns['attached_post'] = 'Post Vinculado';
        return $columns;
    }
    add_filter('manage_media_columns', 'agregar_columna_post_vinculado');
    // Rellena la columna con el post al que el medio está vinculado
    function mostrar_post_vinculado($column_name, $post_id) {
        if ($column_name === 'attached_post') {
            $post_parent_id = wp_get_post_parent_id($post_id);

            if ($post_parent_id) {
                $parent_title = get_the_title($post_parent_id);
                $parent_link = get_edit_post_link($post_parent_id);
                echo '<a href="' . esc_url($parent_link) . '">' . esc_html($parent_title) . '</a>';
            } else {
                echo 'No vinculado';
            }
        }
    }
    add_action('manage_media_custom_column', 'mostrar_post_vinculado', 10, 2);
    add_action( 'wp_ajax_nopriv_codex_load_more_post_ajax', 'codex_load_more_post_ajax_call_back' );
    add_action( 'wp_ajax_codex_load_more_post_ajax', 'codex_load_more_post_ajax_call_back' );
    function codex_load_more_post_ajax_call_back($page_id){
        $posts_per_page = isset($_GET["posts_per_page"]) ? intval($_GET["posts_per_page"]) : 5;
        $page = isset($_GET['pageNumber']) ? intval($_GET['pageNumber']) : 2;
        $search_city = $_GET['search_city'] ?? '';
        $page_id = get_queried_object_id();
        
        // Verificar si el ID de la página es correcto
        error_log("ID de la página recibido: " . $page_id);

        $args = array(
            'post_type' => 'comercios',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => $posts_per_page,
            'post_status' => 'publish',
            'paged' => $page,
        );

        // Filtro por ciudad
        if (!empty($search_city)) {
            $args['meta_query'] = array(
                array(
                    'key' => 'comerce_data_city',
                    'value' => $search_city,
                    'compare' => 'LIKE'
                )
            );
        }

        // Filtro por categoría según el ID de página
        if ($page_id == 127) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'comercio_categoria',
                    'field'    => 'slug',
                    'terms'    => array('adoptado', 'adoptado_void'),
                    'operator' => 'NOT IN',
                ),
            );
        } elseif ($page_id == 121) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'comercio_categoria',
                    'field'    => 'slug',
                    'terms'    => array('adoptado', 'adoptado_void'),
                    'operator' => 'IN',
                ),
            );
        }

        $the_query = new WP_Query($args);
        $html = '';
        ob_start();
        if ($the_query->have_posts()) {
                while ( $the_query->have_posts()) { $the_query->the_post();
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
                    if(!$user_rrss){ $user_ids = get_field('adopter'); $user_name = array(); foreach ($user_ids as $user_id) { $user_info = get_userdata($user_id); if ($user_info) { $user_name[] = $user_info->user_nicename; } } $user_rrss = implode(', ', $user_name); } 
                ?>
                    <li class="comercio-item mb-4 md:mb-8">
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
                                                            <li><?php esc_html_e('Localidad','adopta'); ?></li>
                                                            <li class="col-span-2"><?= $localidad; ?></li>
                                                            <li><?php esc_html_e('Dirección','adopta'); ?></li>
                                                            <li class="col-span-2"><?= $direccion; ?></li>
                                                            <?php if($cif): ?>
                                                            <li><?php esc_html_e('Cif','adopta'); ?></li>
                                                            <li class="col-span-2"><?= $cif; ?></li>
                                                            <?php endif; ?>                                            
                                                            <?php if($iban): ?>
                                                            <li><?php esc_html_e('IBAN','adopta'); ?></li>
                                                            <li class="col-span-2"><?= $iban; ?></li>
                                                            <?php endif; ?>
                                                            <?php if($bizum): ?>
                                                            <li><?php esc_html_e('Bizum','adopta'); ?></li>
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
                                                </div>
                                            <?php endif; ?>
                                            <article class="needs w-full py-6 flex flex-col gap-4 justify-start px-6">
                                                <h2 class="<?= $title_color ?> text-3xl font-bold"><?php esc_html_e('¿Qué necesita?','adopta'); ?></h2>
                                                <div class="<?=$title_color ?> gap-2 list-disc">
                                                    <?php the_content(); ?>
                                                </div>
                                            </article>
                                        </div>
                                        <?php if(has_term(['adoptado', 'adoptado_void'], 'comercio_categoria', $post)):?>
                                            <div class="bg-orange flex flex-row justify-center w-full py-8 gap-6">
                                                <?= wp_get_attachment_image(66, 'thumb', true, ['class' => 'w-12 h-12 self-center']); ?>
                                                <div class="self-center">
                                                    <span class="text-xs font-light"><?php esc_html_e('COMERCIO ADOPTADO POR','adopta'); ?></span>
                                                    <h2 class="font-bold text-lg"><?= $user_rrss; ?></h2>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <button class="w-fit bg-dark py-3 px-6 text-white flex flex-row gap-3 self-end ml-auto mr-0 justify-end flex" onclick="copyToClipboard('<?php the_permalink(); ?>')">Difunde el negocio
                                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 512"><path fill="#fff" fill-rule="nonzero" d="M170.663 256.157c-.083-47.121 38.055-85.4 85.167-85.483 47.121-.092 85.407 38.03 85.499 85.16.091 47.129-38.047 85.4-85.176 85.492-47.112.09-85.399-38.039-85.49-85.169zm-46.108.091c.141 72.602 59.106 131.327 131.69 131.186 72.592-.141 131.35-59.09 131.209-131.692-.141-72.577-59.114-131.335-131.715-131.194-72.585.141-131.325 59.115-131.184 131.7zm237.104-137.091c.033 16.953 13.817 30.681 30.772 30.648 16.961-.033 30.689-13.811 30.664-30.764-.033-16.954-13.818-30.69-30.78-30.657-16.962.033-30.689 13.818-30.656 30.773zm-208.696 345.4c-24.958-1.087-38.511-5.234-47.543-8.709-11.961-4.629-20.496-10.178-29.479-19.094-8.966-8.95-14.532-17.46-19.202-29.397-3.508-9.032-7.73-22.569-8.9-47.527-1.269-26.982-1.559-35.077-1.683-103.432-.133-68.339.116-76.434 1.294-103.441 1.069-24.942 5.242-38.512 8.709-47.536 4.628-11.977 10.161-20.496 19.094-29.479 8.949-8.982 17.459-14.532 29.403-19.202 9.025-3.525 22.561-7.714 47.511-8.9 26.998-1.277 35.085-1.551 103.423-1.684 68.353-.132 76.448.108 103.456 1.295 24.94 1.086 38.51 5.217 47.527 8.709 11.968 4.628 20.503 10.144 29.478 19.094 8.974 8.95 14.54 17.443 19.21 29.412 3.524 9 7.714 22.553 8.892 47.494 1.285 26.999 1.576 35.095 1.7 103.433.132 68.355-.117 76.451-1.302 103.441-1.087 24.958-5.226 38.52-8.709 47.561-4.629 11.952-10.161 20.487-19.103 29.471-8.941 8.949-17.451 14.531-29.403 19.201-9.009 3.517-22.561 7.714-47.494 8.9-26.998 1.269-35.086 1.559-103.448 1.684-68.338.132-76.424-.125-103.431-1.294zM149.977 1.773c-27.239 1.285-45.843 5.648-62.101 12.018-16.829 6.561-31.095 15.354-45.286 29.604C28.381 57.653 19.655 71.944 13.144 88.79c-6.303 16.299-10.575 34.912-11.778 62.168C.172 178.264-.102 186.973.031 256.489c.133 69.508.439 78.234 1.741 105.547 1.302 27.231 5.649 45.828 12.019 62.093 6.569 16.83 15.353 31.088 29.611 45.288 14.25 14.201 28.55 22.918 45.404 29.438 16.282 6.295 34.902 10.583 62.15 11.778 27.305 1.203 36.022 1.468 105.521 1.335 69.532-.132 78.25-.439 105.555-1.733 27.239-1.303 45.826-5.665 62.1-12.019 16.829-6.586 31.095-15.353 45.288-29.611 14.191-14.251 22.917-28.55 29.428-45.405 6.304-16.282 10.592-34.903 11.777-62.134 1.195-27.322 1.478-36.048 1.344-105.556-.133-69.516-.447-78.225-1.741-105.523-1.294-27.255-5.657-45.844-12.019-62.118-6.577-16.829-15.352-31.079-29.602-45.287-14.25-14.192-28.55-22.935-45.404-29.429-16.29-6.305-34.903-10.601-62.15-11.779C333.747.164 325.03-.102 255.506.031c-69.507.133-78.224.431-105.529 1.742z"/></svg>
                                        </button>
                                        <script>
                                            function copyToClipboard(text) {
                                                navigator.clipboard.writeText(text).then(() => {
                                                    alert('¡Enlace copiado! Puedes pegarlo en Instagram.');
                                                });
                                            }
                                        </script>
                    </li>
                <?php
                }
            } 
            wp_reset_postdata();
            $html .= ob_get_clean();
            wp_send_json( array( 'html' => $html ) );
    }


/*******************************************************************************
 * INFORMACIÓN EN TABLAS DE ADMINISTRACIÓN
 ******************************************************************************/
    // 1. Añadir columna "Padrino" en la tabla de administración del CPT 'comercios'
        add_filter('manage_comercios_posts_columns', 'agregar_columna_acf_adopter');
        function agregar_columna_acf_adopter($columns) {
            $columns['adopter'] = __('Influencer', 'adopta');
            return $columns;
        }
    // 2. Mostrar el valor del campo ACF en la columna
        add_action('manage_comercios_posts_custom_column', 'mostrar_valor_acf_adopter', 10, 2);
        function mostrar_valor_acf_adopter($column, $post_id) {
            if ($column === 'adopter') {
                $adopter_id = get_field('adopter', $post_id);
                $adopter = isset($adopter_id[0]) ? get_user_by('id', $adopter_id[0]) : null;
                $adopter_name = $adopter ? $adopter->user_nicename : 'No definido';
                $adopter_url = $adopter ? get_edit_user_link($adopter->ID) : '';
                echo '<a href="' . esc_url($adopter_url) . '" target="_blank">' . esc_html($adopter_name) . '</a>';
            }
        }
    // 3. Hacer que la columna sea ordenable
        add_filter('manage_edit-comercios_sortable_columns', 'hacer_columna_adopter_ordenable');
        function hacer_columna_adopter_ordenable($sortable_columns) {
            $sortable_columns['adopter'] = 'adopter';
            return $sortable_columns;
        }
    // 4. Ordenar por el valor de "Padrino"
        add_action('pre_get_posts', 'ordenar_por_adopter');
        function ordenar_por_adopter($query) {
            if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'comercios') {
                return;
            }
            if ('adopter' === $query->get('orderby')) {
                $query->set('meta_key', 'adopter');
                $query->set('orderby', 'meta_value');
            }
        }
    // 1. Añadir columna "Comercio Asociado" en la tabla de usuarios
            add_filter('manage_users_columns', 'agregar_columna_comercio_en_usuarios');
            function agregar_columna_comercio_en_usuarios($columns) {
                $columns['comercio_asociado'] = __('Comercio Asociado', 'adopta');
                return $columns;
            }
    // 2. Mostrar el nombre del comercio asociado al usuario
            add_action('manage_users_custom_column', 'mostrar_comercio_asociado', 10, 3);
            function mostrar_comercio_asociado($value, $column_name, $user_id) {
                if ($column_name === 'comercio_asociado') {
                    $args = [
                        'post_type'   => 'comercios',
                        'post_status' => 'publish',
                        'meta_query'  => [
                            [
                                'key'     => 'adopter',
                                'value'   => $user_id,
                                'compare' => 'LIKE'
                            ]
                        ]
                    ];
                    $query = new WP_Query($args);

                    if ($query->have_posts()) {
                        $comercios = [];
                        while ($query->have_posts()) {
                            $query->the_post();
                             $comercios[] = '<a href="' . esc_url(get_edit_post_link()) . '" target="_blank">' . get_the_title() . '</a>';
                        }
                        wp_reset_postdata();
                        return implode(', ', $comercios);
                    } else {
                        return __('Sin comercio asociado', 'adopta');
                    }
                }
                return $value;
            }
    // 3. Hacer que la columna sea ordenable
            add_filter('manage_users_sortable_columns', 'hacer_columna_comercio_ordenable');
            function hacer_columna_comercio_ordenable($sortable_columns) {
                $sortable_columns['comercio_asociado'] = 'comercio_asociado';
                return $sortable_columns;
            }
    // 4. Ordenar por "Comercio Asociado"
            add_action('pre_get_users', 'ordenar_por_comercio_asociado');
            function ordenar_por_comercio_asociado($query) {
                if (!is_admin() || !$query->is_main_query()) {
                    return;
                }
                if ('comercio_asociado' === $query->get('orderby')) {
                    $query->set('meta_key', 'adopter');
                    $query->set('orderby', 'meta_value');
                }
            }

/*******************************************************************************
 * APIS
 ******************************************************************************/
  // API para obtener los usuarios suscriptores
        function obtener_usuarios_suscriptores() {
            // Verificar permisos (opcional)
            if (!current_user_can('manage_options')) {
                return new WP_Error('permiso_denegado', 'No tienes permisos para acceder a esta información.', array('status' => 403));
            }
            // Obtener los usuarios con el rol 'subscriber'
            $usuarios = get_users(array(
                'role' => 'subscriber'
            ));
            // Crear el array de respuesta
            $datos_usuarios = array();
            foreach ($usuarios as $usuario) {
                $datos_usuarios[] = array(
                    'ID' => $usuario->ID,
                    'username' => $usuario->user_login,
                    'email' => $usuario->user_email,
                    'nombre' => $usuario->display_name,
                );
            }
            return rest_ensure_response($datos_usuarios);
        }
    // Registrar la API
        function registrar_api_usuarios_suscriptores() {
            register_rest_route('miapi/v1', '/suscriptores', array(
                'methods' => 'GET',
                'callback' => 'obtener_usuarios_suscriptores',
                'permission_callback' => '__return_true', // Cambiar a validación si es necesario
            ));
        }
        add_action('rest_api_init', 'registrar_api_usuarios_suscriptores');

/*******************************************************************************
 * BUSCADOR POR CIUDAD
 ******************************************************************************/
    add_action('pre_get_posts', 'filtrar_comercios_por_ciudad');
    function filtrar_comercios_por_ciudad($query) {
        // Verificamos que estamos en la query principal y en el archivo de archivo de comercios
        if (!is_admin() && $query->is_main_query() && is_post_type_archive('comercios')) {
            
            // Verificamos si existe el parámetro de búsqueda por ciudad
            if (!empty($_GET['search_city'])) {
                $city = sanitize_text_field($_GET['search_city']);
                // Añadimos la meta query para el subfield 'city' dentro del grupo 'comerce_data'
                $query->set('meta_query', [
                    [
                        'key'     => 'comerce_data_city', // Nombre del campo en ACF (combinación de grupo y subcampo)
                        'value'   => $city,
                        'compare' => 'LIKE',
                    ]
                ]);
            }
        }
    }
