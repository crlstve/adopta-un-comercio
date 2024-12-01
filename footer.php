        <footer class="w-full pb-6 bg-dark mt-12 md:mt-24">
            <section class="w-full py-8 flex flex-row justify-center mx-auto" style="background-color:#948781;">
                    <a href="https://marinadeempresas.es/alcem-se/" target="_blank" rel="nofollow" class="flex justify-center w-fit"><img src="<?= get_stylesheet_directory_uri() . '/assets/images/marina-empresa.webp'; ?>" title="Alcem-se" alt="Marina de Empresas Alcem-se" class="w-full md:w-2/3 h-full" aspect-ratio="1080/607"></a>
            </section>
            <div class="contain mt-12 mb-8 flex flex-row flex-wrap justify-center gap-12 mx-auto">
                <div class="grid gird-cols-1 md:grid-cols-2 gap-8">
                    <div class="flex flex-col justify-center gap-4">
                        <span class="text-white text-base text-center"><?php esc_html_e('Una iniciativa de', 'adopta'); ?></span>
                        <a rel="nofollow" href="<?= esc_url('https://siberia.es/')?>" target="_blank" class="self-center"><?= wp_get_attachment_image(69, 'thumb', true,['class'=>'w-fit h-5 self-center']); ?></a>
                    </div>
                    <div class="flex flex-col justify-center gap-4">
                        <span class="text-white text-base text-center"><?php esc_html_e('con la colaboración de', 'adopta'); ?></span>
                        <div class="flex flex-row gap-4">
                            <a rel="nofollow" href="<?= esc_url('https://www.fiftykey.com/')?>" target="_blank" class="self-center"><?= wp_get_attachment_image(67, 'thumb', true,['class'=>'w-fit h-3 self-center']); ?></a>
                            <a rel="nofollow" href="<?= esc_url('https://idital.com/')?>" target="_blank" class="self-center"><?= wp_get_attachment_image(68, 'thumb', true,['class'=>'w-fit h-5 self-center']); ?></a> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="contain 2xl:max-w-5xl mb-8 flex flex-row flex-wrap justify-center gap-12 mx-auto my-8">
                <div class="px-6 md:px-0">
                    <p class="text-xs text-white text-center"><?php esc_html_e('SIBERIA, promotora de la plataforma ADOPTA UN COMERCIO, única y exclusivamente pone en contacto a los posibles afectados con los diferentes influencers que están tratando de dar visibilidad a los afectados y/o ayudarlos, por tanto cualquier ayuda que se reciba se deberá ingresar o entregar directamente a los afectados.','adopta');?>
                    </p>
                    <p class="text-xs text-white text-center">
                    <?php esc_html_e('En el supuesto de que el beneficiario reciba una ayuda económica de un tercero (ya sea una entidad pública, privada o particular) destinada a mitigar o compensar los daños sufridos por la DANA, el beneficiario asume de forma expresa la obligación de cumplir con las obligaciones tributarias que correspondan. Esto incluye, pero no se limita, al cumplimiento de los deberes fiscales en materia de impuestos sobre la renta, patrimonio u otros tributos aplicables, conforme a la legislación vigente. El beneficiario declara estar informado de que dicha ayuda económica puede constituir una ganancia patrimonial o renta imponible sujeta a tributación y que será responsable de realizar las declaraciones y pagos que correspondan ante las autoridades fiscales competentes.','adopta'); ?>
                    
                  </p>
                </div>
            </div>
            <hr class="contain 2xl:max-w-7xl mx-auto my-8">
            <nav class="contain 2xl:max-w-7xl mx-auto my-8 flex flex-row flex-wrap justify-center gap-3 text-white">
                <a href="<?= esc_url('https://siberia.es/aviso-legal/')?>" target="_blank" class="text-sm self-center"><?php esc_html_e('AVISO LEGAL','adopta'); ?></a>
                <span class="text-sm text-white">|</span>
                <a href="<?= esc_url('https://siberia.es/politica-de-privacidad/')?>" target="_blank" class="text-sm self-center"><?php esc_html_e('POLÍTICA DE PRIVACIDAD','adopta'); ?></a>
                <span class="text-sm text-white">|</span>
                <a href="<?= esc_url('https://siberia.es/politica-de-cookies/')?>" target="_blank" class="text-sm self-center"><?php esc_html_e('POLÍTICA DE COOKIES','adopta'); ?></a>
            </nav>
            <script>
                    function copyToClipboard(text) {
                        navigator.clipboard.writeText(text).then(() => {
                            alert('¡Enlace copiado!');            
                        });
                    }
                    function modal_form(id, title){
                        let modal = document.getElementById('modal_form');
                        let comercio = document.getElementById('comercio-modal');
                        modal.classList.remove('hidden');
                        document.getElementById('title-form').innerHTML = title;
                        comercio.value = id;                   
                    }
                    function close_modal(){
                        let modal = document.getElementById('modal_form');
                        modal.classList.add('hidden');
                    }
            </script>
            <script>console.log('Si has visto algún error o tienes alguna sugerencia,\npor favor, házmelo llegar. Gracias.\nhttps://carlesteve.dev/\n%cc.esteve','color: #6ee7b7;');</script>
        </footer>
    </body>
</html>