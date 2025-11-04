$(document).ready(function () {
    definirCarousel();
});

// Obtenha o input e os elementos de item
const searchInput = document.getElementById('searchInput');
var items = document.querySelectorAll('.items');

// Função para remover acentuacões
function removeAccentuation(text) {
	return text.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
}

function definirCarousel() {
	$('.slick-carousel').slick({
		infinite: true,      // Loop infinito
		slidesToShow: 8,     // Quantidade de cards visíveis
		slidesToScroll: 1,   // Quantidade de cards rolados a cada clique
		arrows: true,        // Mostra setas de navegação
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
