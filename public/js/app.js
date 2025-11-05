$(document).ready(function () {
    definirCarousel();
    definirLazyLoad();

    if (typeof CONTINUACAO_HTML != 'undefined' && CONTINUACAO_HTML) {
        // quando chegar no final da página, carregar mais um indice do continuacao_html
        setTimeout(function () {
            $(window).scroll(function () {
                if ($(window).scrollTop() >= $(document).height() - $(window).height()) {
                    // pega o primeiro indice do array
                    let append = CONTINUACAO_HTML.shift();
                    $('#conteudo').append(append);
                    definirCarousel();
                    definirLazyLoad();
                }
            });
        }, 2500);
    }
});

// Obtenha o input e os elementos de item
const searchInput = document.getElementById('searchInput');
var items = document.querySelectorAll('.items');

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

if (searchInput) {
	// Função de filtro para esconder/exibir os itens
	searchInput.addEventListener('input', () => {
		const filter = removeAccentuation(searchInput.value.toLowerCase());
		
		items.forEach(item => {
			const text = removeAccentuation(item.textContent.toLowerCase());
			
			// Verifica se o texto do item inclui o texto do filtro
			if (text.includes(filter)) {
				item.parentElement.classList.remove('d-none');
			} else {
				item.parentElement.classList.add('d-none');
			}
		});
	});
}

function showLoading() {
	$('#loading').show();
}

function hideLoading() {
	$('#loading').hide();
}

// Identificar se está em uma TV pelo userAgent
function isTV() {
	const tvKeywords = ["TV", "SmartTV", "Tizen", "Web0S", "NetCast"];
	return tvKeywords.some(keyword => navigator.userAgent.includes(keyword));
}
