    <section class="w-full md:w-4/5 mx-auto mb-12 flex flex-col gap-4 md:gap-12 mt-10 md:mt-16">
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
        <form class="flex flex-row justify-center gap-0" role="search" method="get" id="searchform" action="<?php echo home_url('/'); ?>">
            <input type="hidden" name="post_type" value="comercios" />
            <input class="border border-1 border-black" type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="Buscar en Comercios" />
            <button class="bg-dark text py-3 px-6 white" type="submit">Buscar</button>
        </form>
    </section>