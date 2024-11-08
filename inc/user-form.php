<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_usuario'])) {

    // Verificar el nonce
    if (!empty($_POST['usuario_nonce_field']) && wp_verify_nonce($_POST['usuario_nonce_field'], 'crear_usuario')) {

        // Recoger datos del formulario
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        //$password = sanitize_text_field($_POST['password']); // Contraseña del usuario
        $comercio = sanitize_text_field($_POST['comercio']);
        // Crear el usuario en WordPress
        $user_id = wp_create_user($username, $comercio.'_dana_2024*', $email);
        $nombre_comercio = get_the_title($comercio);

        if (!is_wp_error($user_id)) {
            // Actualizar los campos adicionales del usuario (nombre y apellido)
            wp_update_user(array(
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'role' => 'subscriber', // Rol predeterminado 'subscriber'
            ));

            // Obtener ID de la categoría "Reservado"
            $categoria_reservado_id = get_term_by('name', 'Reservado', 'comercio_categoria');
            // Actualizar el estado y asignar la categoría al post
            $post_data = array(
                'ID' => $comercio,
            );
            // Actualizar el post
            wp_update_post($post_data);
            wp_set_object_terms($comercio, intval($categoria_reservado_id->term_id), 'comercio_categoria', false);

            update_post_meta($comercio, 'adopter',$user_id);

            // Enviar el correo con los datos del usuario (sin incluir la contraseña)
            $to = 'adopta@adoptauncomercio.com';
            $subject = 'Nuevo Usuario Creado';
            $message = "
                Se ha creado un nuevo usuario en la página.\n\n
                Nombre de usuario: $username\n
                Correo electrónico: $email\n
                Nombre: $first_name\n
                Apellido: $last_name\n
                Comercio: $nombre_comercio\n\n
            ";
            $headers = array('Content-Type: text/plain; charset=UTF-8');

            // Enviar el correo
            wp_mail($to, $subject, $message, $headers);

            

            // Confirmación en la página
            echo "<div class='contain text-center text-orange text-2xl my-12'>Gracias por colaborar. En breve nos pondremos en contacto contigo.</div>";

        } else {
            // Si hubo un error al crear el usuario
            $error = $user_id->get_error_message();
            echo "<p>Error: $error</p>";
        }
    }
}
?>
