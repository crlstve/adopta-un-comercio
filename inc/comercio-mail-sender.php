<?php
// Sección en el menú de administración
function send_commerce_mail() {
    add_menu_page(
        'Envio de Correos',           
        'Envio de Correos',           
        'manage_options',           
        'commerce-mail',           
        'commerce_mail',    
        'dashicons-email-alt2',    
        25                          
    );
}
add_action('admin_menu', 'send_commerce_mail');

// Contenido de la página de administración
function commerce_mail() {
    // Obtener las categorías de "comercio_categoria"
    $categories = get_terms(array(
        'taxonomy' => 'comercio_categoria', 
        'hide_empty' => false,
    ));

    // Formulario para seleccionar categoría y correo
    ?>
    <div class="wrap">
        <h1>Envio de Correos a Comercios</h1>
        <form method="POST" style="display: flex; flex-direction: column; gap: 10px;width: fit-content;min-width: 600px;">
            <label for="category">Seleccionar Categoría:</label>
            <select name="category" id="category">
                <option value="">Todos los Comercios</option>
                <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo esc_attr($category->term_id); ?>">
                        <?php echo esc_html($category->name); ?>
                    </option>
                <?php } ?>
            </select>
            <label for="email_subject">Asunto del Correo:</label>
            <input name="email_subject" id="email_subject">
            <label for="email_content">Contenido del Correo:</label>
            <textarea name="email_content" id="email_content" rows="12"></textarea>
            <input type="submit" name="send_mail" value="Enviar Correo">
        </form>

        <?php
        if (isset($_POST['send_mail'])) {
            $category_id = isset($_POST['category']) ? intval($_POST['category']) : '';
            $email_subject = sanitize_text_field($_POST['email_subject']);
            $email_content = sanitize_text_field($_POST['email_content']);

            // Consultar los comercios según la categoría seleccionada
            $args = array(
                'post_type' => 'comercios', 
                'posts_per_page' => -1, // Mostrar todos los posts
            );

            if ($category_id) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'comercio_categoria',
                        'field' => 'term_id',
                        'terms' => $category_id,
                    ),
                );
            }

            $query = new WP_Query($args);
            $commerces = array();

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                        $id = get_the_ID();
                        $data = get_field('comerce_data', $id);
                        $email = $data['email'] ?? '';
                    if ($email) {
                        $commerces[] = array(
                            'title' => get_the_title(),
                            'email' => $email,
                        );
                    }
                }

                // Enviar el correo
                foreach ($commerces as $commerce) {
                    wp_mail(
                        $commerce['email'], // Dirección de correo del comercio
                        $email_subject, // Asunto del correo
                        // Cuerpo del mensaje en formato HTML
                        "
                        <html>
                        <head>
                            <style>
                                /* Aquí puedes agregar tus estilos personalizados */
                                body { font-family: Arial, sans-serif; }
                                h2 { color: #0073aa; }
                                p { font-size: 1rem; line-height: 1.5; }
                                a { color: #0073aa; text-decoration: none; }
                                table { width: 100%; border-spacing: 0; margin-top: 3rem; padding: 2rem 1rem; font-size: 0.8rem; background-color: #232323; color: #ffffff; }
                                td { text-align: center; }
                                .footer { background-color: #e7a300; color: #232323; padding: 2rem 1rem; font-size: 0.8rem; }
                            </style>
                        </head>
                        <body>
                            <div style='text-align: left; margin: 20px auto; width: 80%; max-width: 600px; padding: 20px; border-radius: 5px;'>
                                <img src='https://adoptauncomercio.com/wp-content/uploads/2024/11/Recurso-6assets.png' alt='Logo' style='width: 250px; height: 100px; margin: 2rem auto; display: block;'>
                                <p>$email_content</p>
                                <a href='https://adoptauncomercio.com/update-status/?auc_cr=$id'>Actualiza tu estado aquí</a>
                                
                                <table>
                                    <tr>
                                        <td><img src='https://adoptauncomercio.com/wp-content/uploads/2024/12/siberia.png' alt='Logo' style='height:16px; margin: 1.6rem 0;'></td>
                                        <td><img src='https://adoptauncomercio.com/wp-content/uploads/2024/12/fiftykey.png' alt='Logo' style='height:16px; margin: 1.6rem 0;'></td>
                                        <td><img src='https://adoptauncomercio.com/wp-content/uploads/2024/12/idital.png' alt='Logo' style='height:16px; margin: 1.6rem 0;'></td>
                                    </tr>
                                </table>

                                <div class='footer'>
                                    <p>NOTA LEGAL: Este mensaje y sus archivos adjuntos van dirigidos exclusivamente a su destinatario, pudiendo contener información confidencial sometida a secreto profesional. No está permitida su reproducción o distribución sin la autorización expresa de INTERTRAFOR S.L. Si usted no es el destinatario final por favor elimínelo e infórmenos por esta vía.</p>
                                    <p>Así mismo le informamos que tratamos los datos que usted nos ha facilitado para realizar la gestión administrativa, contable y fiscal, así como enviarle comunicaciones comerciales sobre nuestros productos y/o servicios. Legitimación: consentimiento del interesado y/o ejecución de un contrato y/o interés legítimo del responsable. No se cederán datos a terceros. Tiene derecho a acceder, rectificar y suprimir los datos, así como otros derechos, indicados en la información adicional, que podrá consultar en: el AVISO LEGAL de nuestra página web: <a href='https://www.adoptauncomercio.com'>www.adoptauncomercio.com</a>. Si usted no desea recibir nuestra información, póngase en contacto con nosotros enviando un correo electrónico a la siguiente dirección: <a href='mailto:adopta@adoptauncomercio.com'>adopta@adoptauncomercio.com</a>.</p>
                                </div>
                            </div>
                        </body>
                        </html>",
                        // Encabezados del correo para asegurarse de que el correo es en formato HTML
                        array(
                            'Content-Type: text/html; charset=UTF-8', // Indica que el contenido es HTML
                            'From: tu-email@dominio.com',            // Dirección de "from"
                            'Reply-To: tu-email@dominio.com'         // Dirección de respuesta
                        )
                    );

                }

                echo '<p>Correos enviados exitosamente.</p>';
            } else {
                echo '<p>No se encontraron comercios para la categoría seleccionada.</p>';
            }

            wp_reset_postdata();
        }
        ?>
    </div>
    <?php
}
