<?php
/**
 * Now functions and definitions
 **/
// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) { exit; }
// Cargar estilos del tema padre y del tema hijo
   /* function twentytwentyfour_child_enqueue_styles() {
        wp_enqueue_style('parent-style', get_stylesheet_directory_uri() . '/style.css');
        wp_enqueue_style('child-style', get_stylesheet_uri(), array('parent-style'));
    }
    add_action('wp_enqueue_scripts', 'twentytwentyfour_child_enqueue_styles');*/
// Cargar estilos de Tailwindcss
    function tailwind_css() {
        wp_enqueue_style('tailwindcss', get_stylesheet_directory_uri() . '/assets/css/theme.css', array(), '1.0', 'all');
    }
    add_action('wp_enqueue_scripts', 'tailwind_css');        
// Cargar js
	function now_register_scripts() {
        if(is_front_page()){
		wp_enqueue_script( 'toggle-forms', get_stylesheet_directory_uri() . '/assets/js/toggle-forms.js', array(), '1.0', false );
        }
        wp_localize_script('more-post', 'wp_data', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'page_id' => get_the_ID() // Pasamos el ID de la página al script
        ));
        wp_enqueue_script( 'more-post', get_stylesheet_directory_uri() . '/assets/js/load-more-post.js', array('jquery'), '1.0', false );
	}
	add_action( 'wp_enqueue_scripts', 'now_register_scripts' );
// Cargar jquery
    function cargar_jquery() { if (!wp_script_is('jquery', 'enqueued')) { wp_enqueue_script('jquery'); }}
    add_action('wp_enqueue_scripts', 'cargar_jquery');
// Menú clásico de WordPress
    function child_theme_setup() {
        register_nav_menus(array(
            'primary' => __('Primary Menu', 'twentytwentyfourchild'),
        ));
    }
    add_action('after_setup_theme', 'child_theme_setup');

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
                <p>¿Qué pasa ahora? {$user_info->user_login} se va a encargar de dar voz a tu negocio y todo lo que necesita. Le hemos mandado tus datos, para que pueda ponerse en contacto contigo y así pueda informar a su comunidad sobre los avances en tu local.</p>
                <p>A partir de hoy ¡sois un equipo! Y es que ya se ha demostrado que, trabajando de la mano, llegamos más lejos.</p>
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
                    Te confirmamos que has adoptado a <b> $negocio </b> ¡ya puedes empezar a difundir y ayudar a tu comercio!
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



/*******************************************************************************
 * SEGUNDA IMAGEN DEL COMERCIO
 ******************************************************************************/
    // Añadir meta box para la imagen secundaria en el editor de WordPress
    /*function agregar_meta_box_img_2() {
        add_meta_box(
            'img_2_meta_box',
            'Imagen Secundaria',
            'mostrar_meta_box_img_2',
            'comercios',
            'side'
        );
    }
    add_action('add_meta_boxes', 'agregar_meta_box_img_2');
    function mostrar_meta_box_img_2($post) {
        $img_2_id = get_post_meta($post->ID, 'img_2', true);
        $img_2_url = $img_2_id ? wp_get_attachment_url($img_2_id) : '';

        echo '<div id="img_2_preview">';
        if ($img_2_url) {
            echo '<img src="' . esc_url($img_2_url) . '" style="max-width:100%; height:auto;" />';
        }
        echo '</div>';
        echo '<input type="hidden" id="img_2_id" name="img_2_id" value="' . esc_attr($img_2_id) . '" />';
        echo '<button type="button" class="button" id="select_img_2_button">' . __('Seleccionar Imagen Secundaria') . '</button>';
        echo '<button type="button" class="button" id="remove_img_2_button" style="display:' . ($img_2_url ? 'inline-block' : 'none') . ';">' . __('Eliminar Imagen') . '</button>';
    }
    function guardar_imagen_secundaria($post_id) {
        if (isset($_POST['img_2'])) {
            update_post_meta($post_id, 'img_2', sanitize_text_field($_POST['img_2']));
        } else {
            delete_post_meta($post_id, 'img_2');
        }
    }
    add_action('save_post', 'guardar_imagen_secundaria');

    function cargar_scripts_imagen_secundaria() {
        global $post_type;
        if ($post_type == 'comercios') {
            wp_enqueue_media();
            ?>
            <script>
            jQuery(document).ready(function($) {
                var mediaUploader;

                $('#select_img_2_button').on('click', function(e) {
                    e.preventDefault();
                    if (mediaUploader) {
                        mediaUploader.open();
                        return;
                    }

                    mediaUploader = wp.media({
                        title: 'Seleccionar Imagen Secundaria',
                        button: {
                            text: 'Usar esta imagen'
                        },
                        multiple: false
                    });

                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $('#img_2_id').val(attachment.id);
                        $('#img_2_preview').html('<img src="' + attachment.sizes.medium.url + '" style="max-width: 100%; height: auto;" />');
                        $('#select_img_2_button').hide();
                        $('#remove_img_2_button').show();
                    });

                    mediaUploader.open();
                });

                $('#remove_img_2_button').on('click', function(e) {
                    e.preventDefault();
                    $('#img_2_id').val('');
                    $('#img_2_preview').html('');
                    $('#select_img_2_button').show();
                    $('#remove_img_2_button').hide();
                });
            });
            </script>
            <?php
        }
    }
    add_action('admin_footer', 'cargar_scripts_imagen_secundaria');*/
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



/*******************************************************************************
 * CARGAR MÁS COMERCIOS EN EL TEMPLATE
 ******************************************************************************/
    add_action( 'wp_enqueue_scripts', 'cxc_theme_enqueue_script_style' );
    function cxc_theme_enqueue_script_style() {
        wp_enqueue_script( 'load-more-script', get_stylesheet_directory_uri(). '/assets/js/load-more-post.js', array('jquery') );
        // Localize the script with new data
        wp_localize_script( 'load-more-script', 'ajax_posts', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'noposts' => __( 'No older posts found', 'cxc-codexcoach' ),
        ));
    }
    add_action( 'wp_ajax_nopriv_codex_load_more_post_ajax', 'codex_load_more_post_ajax_call_back' );
    add_action( 'wp_ajax_codex_load_more_post_ajax', 'codex_load_more_post_ajax_call_back' );
    function codex_load_more_post_ajax_call_back($page_id){
        $posts_per_page = isset($_POST["posts_per_page"]) ? intval($_POST["posts_per_page"]) : 5;
        $page = isset($_POST['pageNumber']) ? intval($_POST['pageNumber']) : 2;
        //$page_id = isset($_POST['page_id']) ? intval($_POST['page_id']) : 0; // Obtenemos el ID de la página
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
        if ($page_id == 127) {
            // Excluir los posts con las categorías "adoptado" y "adoptado_void"
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'comercio_categoria',
                    'field'    => 'slug',
                    'terms'    => array('adoptado', 'adoptado_void'),
                    'operator' => 'NOT IN',
                ),
            );
        } elseif ($page_id == 121) {
            // Mostrar solo los posts con las categorías "adoptado" o "adoptado_void"
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'comercio_categoria',
                    'field'    => 'slug',
                    'terms'    => array('adoptado', 'adoptado_void'),
                    'operator' => 'IN',
                ),
            );
        }
            $the_query = new WP_Query( $args );
            $html = '';
            ob_start();
            if ( $the_query->have_posts() ) {
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
 * FILTRO DE ADOPTADOS O NO ADOPTADOS
 ******************************************************************************/
