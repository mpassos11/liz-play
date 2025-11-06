$(document).ready(function () {
    definirCarousel();
    definirLazyLoad();

    var ajax = null;
    $('#input_pesquisa').on('input', function () {
        const texto = $(this).val().trim();
        if (ajax && ajax.hasOwnProperty('abort')) {
            ajax.abort();
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
