    <section class="w-full flex flex-col gap-4 my-10 md:my-16 md:px-16 ">
        <p class="text-center font-semibold"><?php _e('Una iniciativa solidaria, liderada por influencers y otros rostros conocidos, para visibilizar y recaudar ayuda destinada a los comercios locales que lo han perdido todo por la DANA.', 'adopta'); ?></p>
        <p class="text-orange text-center font-semibold" ><?php _e('Importante: cuando un comercio aparece señalado como "adoptado", no implica que haya recibido todavía lo que necesita, sino que el influencer responsable de ese negocio ya ha compartido, a través de sus redes sociales, la petición de ayuda.', 'adopta'); ?></p> 

        <div id="forms" class="w-full flex flex-row flex-wrap justify-center gap-24 md:gap-12 mx-auto my-12 md:my-24">
            <div class="w-full md:w-2/5 bg-orange py-12 px-6 h-fit relative">
                    <header class="mx-auto text-center mb-6">
                        <h1 class="text-3xl font-bold mx-auto"><?= _e('ADOPTA UN COMERCIO','adopta'); ?></h1>
                        <span class="mb-4 font-semibold mx-auto"><?= _e('SI ERES INFLUENCER','adopta'); ?></span>
                    </header>
                <div class="h-0 hidden transition-all transition-all duration-500 ease-in-out" data-toggle="influencer">
                    <!-- Formulario para influencers -->
                    <form id="form_usuario" action="" method="post" class="flex flex-col gap-4">
                        <?php wp_nonce_field('crear_usuario', 'usuario_nonce_field'); ?>
                        <input placeholder="Nombre de usuario // Cuenta rrss" type="text" name="username" id="username" required>
                        <input placeholder="Correo electrónico" type="email" name="email" id="email" required>
                        <input placeholder="Nombre" type="text" name="first_name" id="first_name" required>
                        <input placeholder="Apellido" type="text" name="last_name" id="last_name" required>
                            <select id="comercio" name="comercio" required>
                            <option value="" class="color-slate-600"><?php _e('Selecciona un comercio', 'adopta'); ?></option>
                            <?php
                            // Obtener comercios excluyendo los que están en categorías adoptado y adoptado_void
                            $args = array(
                                'post_type' => 'comercios',
                                'posts_per_page' => -1,
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'comercio_categoria',
                                        'field'    => 'slug',
                                        'terms'    => array('adoptado', 'adoptado_void','reservado'),
                                        'operator' => 'NOT IN',
                                    ),
                                ),
                            );
                            
                            $comercios = get_posts($args);

                            // Generar opciones del select
                            foreach ($comercios as $comercio) {
                                echo '<option value="' . esc_attr($comercio->ID) . '">' . esc_html($comercio->post_title) . '</option>';
                            }
                            ?>
                        </select>
                        <button type="submit" name="submit_usuario" class="bg-dark text-white w-full mx-auto py-3 px-6"><?= _e('Enviar','adopta'); ?></button>
                    </form>
                </div>
                <button onclick="desplegable();" class="bg-dark aspect-video px-6 max-w-24 h-fit absolute bottom-0 right-0 left-0 mx-auto translate-y-full"><div class="rotate-90 text-white font-xl transition-all transform duration-500">></div></button>
            </div>
            <div class="w-full md:w-2/5 bg-pink py-12 px-6 h-fit relative">
                <header class="mx-auto text-center mb-6">
                    <h2 class="text-3xl font-bold mx-auto"><?= _e('INSCRÍBETE','adopta'); ?></h2>
                    <span class="mb-4 font-semibold mx-auto"><?= _e('SI ERES UN COMERCIO AFECTADO','adopta'); ?></span>
                </header>
                <div class="h-0 hidden" data-toggle="comercio">
                    <!-- Formulario para enviar el comercio -->
                    <form id="form_comercio"  action="" method="post" enctype="multipart/form-data" class="flex flex-col gap-2">
                                <!-- Seguridad con Nonce -->
                                <?php wp_nonce_field('crear_comercio', 'comercio_nonce_field'); ?>
                                <!-- negocio -->
                                <input placeholder="Nombre del Comercio" type="text" id="titulo" name="titulo" required>
                                <!-- Select para etiquetas -->
                                <select name="tipo_comercio" id="tipo_comercio" required>
                                    <option value=""><?php _e('Tipo de Comercio', 'adopta'); ?></option>
                                    <?php
                                    $etiquetas = get_terms(array(
                                        'taxonomy' => 'comercio_etiqueta',
                                        'hide_empty' => false, // Muestra todas las etiquetas, incluso si no están asignadas
                                    ));
                                    
                                    foreach ($etiquetas as $etiqueta) {
                                        echo '<option value="' . esc_attr($etiqueta->term_id) . '">' . esc_html($etiqueta->name) . '</option>';
                                    }
                                    ?>
                                </select>
                                <!-- contacto -->
                                <input placeholder="Nombre de contacto" type="text" name="contact_name" id="contact_name" requiered>
                                <!-- email -->
                                <input placeholder="email" type="email" name="email" id="email" requiered>
                                <!-- localidad -->
                                <input placeholder="Localidad" type="text" name="city" id="city" requiered>
                                <!-- dirección -->
                                <input placeholder="Dirección" type="text" name="direction" id="direction" requiered>
                                <!-- cif -->
                                <input placeholder="cif" type="text" name="cif" id="cif" requiered>
                                <!-- iban -->
                                <input placeholder="iban" type="text" name="iban" id="iban" >
                                <!-- bizum -->
                                <input placeholder="bizum" type="text" name="bizum" id="bizum">
                                <!-- Historia -->
                                <textarea placeholder="Breve historia de tu negocio" id="extracto" name="extracto" required></textarea>
                                <!-- Needs -->
                                <textarea placeholder="Lo que necesito" id="contenido" name="contenido" required></textarea>
                                <!-- otros campos -->
                                <div class="flex flex-col xl:flex-row gap-4">
                                    <input type="file" name="img_1" id="img_1" accept="image/*" required>
                                </div>    
                                <div class="flex flex-col xl:flex-row gap-4">
                                    <input type="file" name="img_2" id="img_2" accept="image/*" required>
                                </div>
                                <!--button-->
                                <button type="submit" class="bg-dark text-white w-full mx-auto py-3 px-6"><?= _e('Enviar','adopta'); ?></button>
                    </form>
                </div>
                <button onclick="desplegable_2();" class="bg-dark aspect-video px-6 max-w-24 h-fit absolute bottom-0 right-0 left-0 mx-auto translate-y-full"><div class="rotate-90 text-white font-xl transition-all transform duration-500">></div></button>
            </div>
        </div>
        <div id="gracias" class="hidden w-full flex flex-col gap-4 my-16 md:px-16 ">
            <span>Gracias por colaborar. En breve nos pondremos en contacto contigo.</span>
    </section>