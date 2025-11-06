$(document).ready(function () {
    hideWindowLoading();
    definirCarousel();
    definirLazyLoad();
    definirLoading();

    var ajax = null;
    $('#input_pesquisa').on('input', function () {
        const texto = $(this).val().trim();
        if (ajax && ajax.hasOwnProperty('abort')) {
            ajax.abort();
        }

        if (texto.length > 3) {
            $('#conteudo').html('');
        }

        ajax = $.ajax({
            url: COMMON_URL + 'pesquisar',
            method: 'POST',
            data: {
                texto,
                tipo,
            },
            success: function (response) {
                $('#conteudo').html(response);
                definirCarousel();
                definirLazyLoad();
            }
        });
    });
});

function showWindowLoading() {
    if ($('#loading-overlay').length === 0) {
        // Recria a estrutura se ela foi removida pelo carregamento inicial
        $('body').prepend(`
                <div id="loading-overlay">
                    <div id="loading-spinner"></div>
                </div>
            `);
    }

    $('#loading-overlay').removeClass('loaded');
}

function hideWindowLoading() {
    const $overlay = $('#loading-overlay');

    // Inicia o fade-out
    $overlay.addClass('loaded');

    // Remove o elemento após a transição
    setTimeout(function() {
        $overlay.remove();
    }, 300);
}

let activeRequests = 0;
function definirLoading() {

}

// Função para remover acentuacões
function removeAccentuation(text) {
	return text.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
}

function definirLazyLoad() {
    $('.lazy').lazy({
        effect: 'fadeIn',
        visibleOnly: true,
        onError: function(element) {
            element
                .data('src', COMMON_URL + 'public/imagens/img.png')
                .attr('src', COMMON_URL + 'public/imagens/img.png');
        }
    });
}

function definirCarousel() {
	$('.slick-carousel').slick({
		infinite: true,      // Loop infinito
		slidesToShow: 8,     // Quantidade de cards visíveis
		slidesToScroll: 1,   // Quantidade de cards rolados a cada clique
		arrows: true,        // Mostra setas de navegação
        variableWidth: true,
        lazyLoad: 'ondemand',
		responsive: [        // Ajuste de responsividade
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 4,
				}
			},
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 2,
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1,
				}
			}
		]
	});
}

// Identificar se está em uma TV pelo userAgent
function isTV() {
	const tvKeywords = ["TV", "SmartTV", "Tizen", "Web0S", "NetCast"];
	return tvKeywords.some(keyword => navigator.userAgent.includes(keyword));
}
