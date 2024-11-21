<section class="w-full md:w-4/5 mx-auto mb-12 flex flex-col gap-4 md:gap-12 md:mt-16">
        <nav>
            <?php 
                $args = array(
                    'theme_location' => 'primary',
                    'menu_class' => 'flex flex-col md:flex-row gap-4 md:gap-12 justify-around',
                    'container' => false,
                    'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'walker' => new Custom_Walker_Nav_Menu(), 
                );
                wp_nav_menu($args); 
            ?>
        </nav>

        <div class="w-full flex flex-col md:flex-row justify-center gap-4">
        <div class="">
                <div class=" dropdown relative">

                        <button onclick="toggleDropdown()" class="dropdown-button flex justify-center w-full md:w-fit bg-dark text py-3 px-6 white">
                            <?php esc_html_e('Categorías','adopta'); ?>
                        </button>
                        <script>
                            function toggleDropdown() {
                                const dropdownMenu = document.getElementById('dropdown-menu');
                                dropdownMenu.classList.toggle('hidden');

                                if (!dropdownMenu.classList.contains('hidden')) {
                                    document.addEventListener('click', handleClickOutside);
                                }
                            }

                        </script>
                        <ul id="dropdown-menu" class="dropdown-menu absolute hidden flex flex-col w-full gap-2">
                            <?php 
                                $categorias = get_terms([ 'taxonomy' => 'comercio_etiqueta', 'hide_empty' => true, ]);
                                if (!is_wp_error($categorias) && !empty($categorias)):
                                    foreach ($categorias as $categoria): 
                                        $url = get_term_link($categoria);
                            ?>
                                   <a href="<?= esc_url($url);  ?>" class="group hover:bg-gray-200 transition-all duration-300 ease-in-out">
                                        <li class="text-dark py-3 text-center self-center">
                                            <?=esc_html($categoria->name)?>
                                        </li>
                                    </a>                                                            
                            <?php   endforeach;
                                else:
                                    echo 'No se encontraron categorías en la taxonomía comercio_etiqueta.';
                                endif;
                            ?>
                        </ul>
                </div>
            </div>

            <form class="flex flex-row justify-center gap-0" role="search" method="get" id="searchform" action="<?php echo home_url('/'); ?>">
                <input type="hidden" name="post_type" value="comercios" />
                <input class="border border-1 border-black w-1/2 md:w-full" type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="Buscar" />
                <button class="bg-dark text py-3 px-6 white" type="submit"><?php esc_html_e('Buscar','adopta'); ?></button>
            </form>


            <?php if(!is_front_page()): ?>
                <a class="flex justify-center w-full md:w-fit bg-dark text py-3 px-6 h-fit" href="<?= esc_url(home_url('/')); ?>"><?php esc_html_e('Volver','adopta'); ?></a>
            <?php endif; ?>
        </div>
    </section>