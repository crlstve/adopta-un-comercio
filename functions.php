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
        }
        add_action('wp_enqueue_scripts', 'register_styles');        
    // Cargar js
        function register_scripts() {
            if(is_front_page() ||  is_front_page() && is_paged()){
                wp_enqueue_script( 'toggle-forms', get_stylesheet_directory_uri() . '/assets/js/toggle-forms.js', array(), '1.0', false );
            }    
        }
        add_action( 'wp_enqueue_scripts', 'register_scripts' );
    // Mapa leaflet
        function enqueue_local_leaflet_files() {
            wp_enqueue_style('leaflet-css', get_stylesheet_directory_uri() . '/inc/leaflet/leaflet.css');
            wp_enqueue_script('leaflet-js', get_stylesheet_directory_uri() . '/inc/leaflet/leaflet.js');
        }
        add_action('wp_enqueue_scripts', 'enqueue_local_leaflet_files');        
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
                $classes = 'text-gray-400 hover:text-black text-base md:text-xl px-6 py-3 w-full';
                $output .= sprintf('<li class="border border-1 border-black text-center flex"><a href="%s" class="%s">%s</a></li>',
                    esc_url($item->url),
                    esc_attr($classes),
                    esc_html($item->title)
                );
            }
        }
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
            $id = get_the_ID();
            $email_comercio = get_field('comerce_data_email', $post->ID);
            $slug = get_post_field('post_name', $id);
            if ($email_comercio) {
                // Configurar el correo
                $to = $email_comercio;
                $subject = 'Tu Comercio ha sido Publicado';
                $message = "
                <div style='text-align: left;margin: 20px auto;width: 80%;max-width: 600px;padding: 20px;border-radius: 5px;'>
                    <img src='https://adoptauncomercio.com/wp-content/uploads/2024/11/Recurso-6assets.png' alt='Logo' style='width: 250px;height: 100px;margin:2rem auto;display: block;'>
                    <h2 style='text-align:center;margin-top:2.5rem,margin-bottom:1.5rem'>Tu comercio ha sido registrado en nuestra web.</h2>
                    <p>Nos ponemos manos a la obra para que pueda ser adoptado lo más pronto posible. Recuerda que una vez esté subido a la plataforma, las personas ya pueden empezar a ayudarte, no hace falta estar adoptado por un influencer.</p>
                    <p>Esta iniciativa se ha creado de forma solidaria, para ayudar a cada uno de los comercios locales que lo necesitan. Nos alegra poder servirte de altavoz y te damos mucho ánimo desde diferentes empresas que, como tú, somos de la terreta.</p>
                    <hr style='margin:2rem'>
                    <h2 style='text-align:center;margin-bottom:1.5rem'>¿Tu comercio ya está abierto?</h2>
                                <p>
                                   <b>Si tu comercio ya está abierto o vende de forma online</b> ¡inscríbete en el siguiente formulario! Tras hacerlo, aparecerás en el nuevo apartado de 'Comercios abiertos' y los usuarios podrán comprar tus productos para regalar esta Navidad.  
                                </p>
                                <p>
                                    <b>¡Ah! Pero antes, un consejo: si no tienes web, utiliza tus redes sociales para publicar los productos en venta o packs preparados para Navidad ¡así será más fácil ver qué ofreces!</b>
                                </p>
                                <table style='background-color: white;margin: 3rem auto;text-align: center;justify-content: center;width: 100%;'>
                                    <tr>
                                        <td style='margin:1.2rem auto;'><a style='background-color:#e7a300;color:white;padding:0.6rem 1.2rem;' href='https://adoptauncomercio.com/update-status/?auc_cr=$id&auc_slg=$slug'>¡Apúntate como Comercio Abierto!</a></td>
                                    </tr>
                                </table>
                                <p>Sabemos que queda mucho camino por delante. Desde Adopta te mandamos un abrazo y mucha fuerza. </p>
                    



                        <table style='background-color:#232323;color:#ffffff;margin-top:3rem;padding:2rem 1rem; font-size:0.8rem;width:100%'>
                            <tr>
                                <td style='width:33%;text-align:center;'><image src='https://adoptauncomercio.com/wp-content/uploads/2024/12/siberia.png' alt='Logo' style='height:16px;margin:1.6rem 0rem'></td>
                                <td style='width:33%;text-align:center;'><image src='https://adoptauncomercio.com/wp-content/uploads/2024/12/fiftykey.png' alt='Logo' style='height:16px;margin:1.6rem 0rem'></td>
                                <td style='width:33%;text-align:center;'><image src='https://adoptauncomercio.com/wp-content/uploads/2024/12/idital.png' alt='Logo' style='height:16px;margin:1.6rem 0rem'></td>
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
        if (has_term('adoptado', 'comercio_categoria', $post)&&!has_term('abierto','comercio_categoria', $post)) {
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
                            <td><img src='https://adoptauncomercio.com/wp-content/uploads/2024/12/siberia.png' alt='Logo' style='height:16px; margin: 1.6rem 0'></td>
                            <td><img src='https://adoptauncomercio.com/wp-content/uploads/2024/12/fiftykey.png' alt='Logo' style='height:16px; margin: 1.6rem 0'></td>
                            <td><img src='https://adoptauncomercio.com/wp-content/uploads/2024/12/idital.png' alt='Logo' style='height:16px; margin: 1.6rem 0;'></td>
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
                                <td><img src='https://adoptauncomercio.com/wp-content/uploads/2024/12/siberia.png' alt='Logo' style='height:16px; margin: 1.6rem 0'></td>
                                <td><img src='https://adoptauncomercio.com/wp-content/uploads/2024/12/fiftykey.png' alt='Logo' style='height:16px; margin: 1.6rem 0'></td>
                                <td><img src='https://adoptauncomercio.com/wp-content/uploads/2024/12/idital.png' alt='Logo' style='height:16px; margin: 1.6rem 0;'></td>
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
 * MAPA COMERCIOS
 ******************************************************************************/
 // Módulo para envío de comercios
    include_once get_template_directory() . '/inc/comercio-mail-sender.php';  
