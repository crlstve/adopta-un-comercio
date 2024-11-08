jQuery(document).ready(function ($) {
	var posts_per_page = 5;

	// Función para cargar más posts
	function cxc_load_more_posts(cxc_this, pageNumber, pageId) {
		// Evita clics múltiples y llamadas duplicadas
		if (cxc_this.hasClass('cxc-disabled') || cxc_this.hasClass('cxc-loading')) return;

		cxc_this.addClass('cxc-loading'); // Añade una clase de carga temporal
		var str = '&pageNumber=' + pageNumber + '&posts_per_page=' + posts_per_page + '&page_id=' + pageId + '&action=codex_load_more_post_ajax';

		jQuery.ajax({
			type: "POST",
			dataType: "html",
			url: ajax_posts.ajaxurl,
			data: str,
			success: function (response) {
				// Remueve las clases después de recibir la respuesta
				cxc_this.removeClass('cxc-active cxc-loading');

				// Verificar que la respuesta no esté vacía
				if (response.trim() !== "") {
					try {
						var json_html = JSON.parse(response);

						// Asegurar que la respuesta contenga HTML antes de procesar
						if (json_html && json_html.html && json_html.html.length) {
							var page_count = parseInt(pageNumber) + 1;
							cxc_this.attr('data-page', page_count);
							cxc_this.parents('.cxc-post-wrapper').find(".cxc-posts").append(json_html.html);
						} else {
							cxc_this.attr("disabled", true).addClass('cxc-disabled');
						}
					} catch (error) {
						console.error("Error al parsear JSON: ", error);
					}
				} else {
					cxc_this.attr("disabled", true).addClass('cxc-disabled');
				}
			},
			error: function () {
				// Manejo de error en la llamada AJAX
				cxc_this.removeClass('cxc-loading');
			}
		});
	}

	// Evento de clic para cargar más posts
	jQuery(document).on("click", ".codex-load-more", function () {
		var cxc_this = jQuery(this);
		var paged = cxc_this.attr('data-page');
		const pageId = window.wp_data?.page_id || 0; // Obtén el ID de la página actual o asigna 0 si no se encuentra
		cxc_this.addClass('cxc-active');
		cxc_load_more_posts(cxc_this, paged, pageId);
	});
});
