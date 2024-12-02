<?php
// Obtener el ID del comercio
    $commerce_id = isset($_GET['auc_cr']) ? absint($_GET['auc_cr']) : null;
    if ($commerce_id) {
        $post = get_post($commerce_id);
        $post_title = $post ? $post->post_title : '';  // Valor predeterminado
    } else {
        echo '
        <main class="contain 2xl:max-w-7xl mx-auto px-6 my-12">
            <p class="text-center">' 
            . __('Lo sentimos, algo no ha salido bien.', 'adopta') . 
            '</p>
        </main>';
        get_footer();
        exit;
    }

// Procesar el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['update_nonce_field']) && wp_verify_nonce($_POST['update_nonce_field'], 'update_status')) {
        // Sanitizar y obtener los valores del formulario
        $update_status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        $update_coordinates = isset($_POST['coordinates']) ? sanitize_text_field($_POST['coordinates']) : '';
        $update_direccion = isset($_POST['direccion']) ? sanitize_textarea_field($_POST['direccion']) : '';
        $update_auc_cr = isset($_POST['auc_cr']) ? sanitize_text_field($_POST['auc_cr']) : '';
        $update_mensaje = isset($_POST['mensaje']) ? sanitize_textarea_field($_POST['mensaje']) : '';
        $update_web = isset($_POST['web']) ? sanitize_textarea_field($_POST['web']) : '';
        // Subida de la imagen
            $uploaded_image = null;  // Inicializar la variable
            if (isset($_FILES['open_img']) && $_FILES['open_img']['error'] === UPLOAD_ERR_OK) {
                // Subida de la imagen
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');

                $attachment_id = media_handle_upload('open_img', $commerce_id);

                if (is_wp_error($attachment_id)) {
                    error_log('Error al subir la imagen: ' . $attachment_id->get_error_message());
                } else {
                    $uploaded_image = $attachment_id; // Asignar el ID del archivo subido a la variable
                    update_post_meta($commerce_id, 'image', $attachment_id);
                }
            }

        // Actualizar los metadatos
        if ($commerce_id) {
            update_post_meta($commerce_id, 'coordinates', $update_coordinates);
            update_post_meta($commerce_id, 'web', $update_web);
            update_post_meta($commerce_id, 'comerce_data_direction', $update_direccion);
            update_post_meta($commerce_id, 'message', $update_mensaje);
        }

        // Actualizar el estado del post si corresponde
        if ($update_status === 'abierto' && $commerce_id) {
            // Obtener la categoría 'abierto'
            $categoria_abierto = get_term_by('name', 'abierto', 'comercio_categoria');
            if ($categoria_abierto) {
                // Obtener las categorías actuales del post
                $categorias_actuales = wp_get_object_terms($commerce_id, 'comercio_categoria', array('fields' => 'ids'));

                // Si la categoría 'abierto' no está en las categorías actuales, agregarla
                if (!in_array($categoria_abierto->term_id, $categorias_actuales)) {
                    // Agregar la categoría 'abierto' sin eliminar las existentes
                    wp_set_object_terms($commerce_id, array_merge($categorias_actuales, array(intval($categoria_abierto->term_id))), 'comercio_categoria');
                }
            }
        }



        //
            echo"<div class='w-full flex flex-col gap-4 my-8 md:px-16 mx-auto text-center text-orange'>
                    <span style='font-weight:bold;padding:1rem;font-size:1.5rem;'>Gracias por actualizar tu información. Mucho ánimo.</span>
                </div>
                <div class='w-full flex flex-col justify-center'>
                    <a class='bg-dark w-fit h-fit px-6 text-white px-6 py-3 mx-auto' href=". home_url() . ">
                        Volver a la página principal
                    </a>
                </div>
                " ;
            get_footer();
            exit;
    }
?>
<script>
    console.log(<?php echo $attachment_id; ?>);
</script>