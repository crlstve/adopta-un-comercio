<section id="modal_form" class="hidden z-10 bg-dark-opacity fixed top-0 left-0 w-full h-full">
    <div class="flex flex-col gap-4 w-full md:w-fit h-fit bg-orange absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 px-10 py-12">
        <header class="flex flex-col w-full">
            <div class="flex fler-col justify-between">
                <span class="text-dark self-center"><?= esc_html_e('ADOPTA A','adopta'); ?></span>
                <button onclick="close_modal();" class="bg-dark px-3 py-1 w-fit h-fit text-sm text-white">X</button> 
            </div>
            <h2 id="title-form" class="font-bold"></h2>   
        </header>
        <form action="" method="post" class="flex flex-col gap-4 w-full ">
            <?php wp_nonce_field('crear_usuario', 'usuario_nonce_field'); ?>
            <input placeholder="Usuario cuenta rrss" type="text" name="username" id="username" required>
            <input placeholder="Correo electrónico" type="email" name="email" id="email" required>
            <input placeholder="Nombre" type="text" name="first_name" id="first_name" required>
            <input placeholder="Apellido" type="text" name="last_name" id="last_name" required>
            <input id="comercio-modal" class="hidden" type="comercio" name="comercio"  value="" required>
            <button type="submit" name="submit_usuario" class="bg-dark text-white w-full mx-auto py-3 px-6"><?= esc_html_e('Enviar','adopta'); ?></button>
        </form>
    </div>
</section>