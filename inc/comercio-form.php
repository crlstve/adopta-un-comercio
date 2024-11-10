<?php
if (!defined('ABSPATH')) {exit; }
// Verificar si el formulario se envía y el nonce es válido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['comercio_nonce_field']) && wp_verify_nonce($_POST['comercio_nonce_field'], 'crear_comercio')) {

    // Recoger datos del formulario
    $titulo = sanitize_text_field($_POST['titulo']);
    $contenido = sanitize_textarea_field($_POST['contenido']);
    $extracto = sanitize_text_field($_POST['extracto']);
    $tipo_comercio = sanitize_text_field($_POST['tipo_comercio']);
    $contact_name = !empty($_POST['contact_name']) ? sanitize_text_field($_POST['contact_name']) : '';
    $email = !empty($_POST['email']) ? sanitize_text_field($_POST['email']) : '';
    $city = !empty($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
    $direction = !empty($_POST['direction']) ? sanitize_text_field($_POST['direction']) : '';
    $cif = !empty($_POST['cif']) ? sanitize_text_field($_POST['cif']) : '';
    $iban = !empty($_POST['iban']) ? sanitize_text_field($_POST['iban']) : '';
    $bizum = !empty($_POST['bizum']) ? sanitize_text_field($_POST['bizum']) : '';

    // Subir las imágenes
    if (isset($_FILES['img_1']) && isset($_FILES['img_2'])) {
        if ($_FILES['img_1']['error'] === UPLOAD_ERR_OK && $_FILES['img_2']['error'] === UPLOAD_ERR_OK) {
            // Subir la primera imagen
            $image_1_id = media_handle_upload('img_1', 0);
            // Subir la segunda imagen
            $image_2_id = media_handle_upload('img_2', 0);

            // Verificar si se subieron correctamente
            if (is_wp_error($image_1_id)) {
                echo 'Error al subir la primera imagen: ' . $image_1_id->get_error_message();
            }
            if (is_wp_error($image_2_id)) {
                echo 'Error al subir la segunda imagen: ' . $image_2_id->get_error_message();
            }
        } else {
            echo 'Hubo un error al subir las imágenes.';
        }
    } else {
        echo 'No se recibieron las imágenes.';
    }

    // Crear el post
        $nuevo_post = array(
            'post_title'    => $titulo,
            'post_content'  => $contenido,
            'post_excerpt'  => $extracto,
            'post_status'   => 'draft', // Guardar como borrador
            'post_type'     => 'comercios'
        );

    $post_id = wp_insert_post($nuevo_post);

        // Si el post se crea correctamente, asociar las imágenes
        if ($post_id) {
            // Vincular la primera imagen como imagen destacada
            if (isset($image_1_id) && !is_wp_error($image_1_id)) {
                set_post_thumbnail($post_id, $image_1_id);
            }

            // Vincular la segunda imagen como campo personalizado
            if (isset($image_2_id) && !is_wp_error($image_2_id)) {
                update_post_meta($post_id, 'img_2', $image_2_id); // 'img_2' es el nombre del campo personalizado
            }

            // Actualizar los campos ACF con los datos del formulario
            update_post_meta($post_id, 'comerce_data_contact_name', $contact_name);
            update_post_meta($post_id, 'comerce_data_email', $email);
            update_post_meta($post_id, 'comerce_data_city', $city);
            update_post_meta($post_id, 'comerce_data_direction', $direction);
            update_post_meta($post_id, 'comerce_data_cif', $cif);
            update_post_meta($post_id, 'comerce_data_iban', $iban);
            update_post_meta($post_id, 'comerce_data_bizum', $bizum);

            if (!empty($tipo_comercio)) {
                $etiqueta_id = intval($tipo_comercio); // Obtener el ID de la etiqueta desde el formulario
                // Asignar la etiqueta al post (comercio_etiqueta es la taxonomía)
                wp_set_object_terms($post_id, $etiqueta_id, 'comercio_etiqueta');
            }
    }

    // Configurar y enviar el correo
    $to = $admin_mail;
    $subject = 'NUEVO COMERCIO';
    $message = "Se ha recibido un nuevo comercio pendiente de revisión.\n\n" . 
               "Nombre del Comercio: $titulo\n" .
               "Información de Contacto:\n" . 
               "- Nombre de Contacto: $contact_name\n" . 
               "- Email: $email\n" . 
               "- Ciudad: $city\n" . 
               "- Dirección: $direction\n" . 
               "- CIF: $cif\n" . 
               "- IBAN: $iban\n" . 
               "- Bizum: $bizum\n" .
               "Descripción: $contenido\n" .
               "Extracto: $extracto\n\n" . 
               "<a href='https://adoptauncomercio.com/wp-admin/edit.php?post_type=comercios'>VALIDAR</a>";
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    wp_mail($to, $subject, $message, $headers);

    echo "<div style='width:100%;max-width:1000px;margin:5rem 0rem;display:flex;flex-firection:column'>
        <div style='width:100%;padding:2rem 0rem;'>
            <span style='font-weight:bold;padding:1rem;font-size:1.5rem;'>Gracias por inscribirte. En breve nos pondremos en contacto contigo, mucho ánimo.</span><br>
            <a style='text-decoration:none;color:white;font-size:1.2rem;' href=". home_url() . ">
            <button style='background-color:#232323;color:white;padding:1.2rem;width:fit-content;cursor:pointer;margin-top:1rem;'>Volver a la página principal</button>
            </a>
        </div>
    </div>";
    
    exit;
}
?>
