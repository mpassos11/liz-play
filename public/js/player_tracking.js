/**
 * player_tracking.js
 * * Responsável por rastrear o tempo de reprodução e enviar o progresso para o servidor via AJAX.
 */

// Intervalo de tempo (em segundos) entre cada salvamento do progresso
const INTERVALO_SALVAMENTO = 15;
let ultimoTempoSalvo = 0;

function enviarProgresso(tempoAtual, contentId, contentType, duracaoTotal, rotaApi) {
    if (tempoAtual < ultimoTempoSalvo + INTERVALO_SALVAMENTO && tempoAtual < duracaoTotal) {
        return; // Salva apenas a cada INTERVALO_SALVAMENTO segundos (para não sobrecarregar)
    }

    const dados = {
        content_id: contentId,
        tipo_conteudo: contentType,
        tempo: Math.floor(tempoAtual),
        duracao: duracaoTotal
    };

    // Atualiza o tempo para o próximo salvamento
    ultimoTempoSalvo = Math.floor(tempoAtual);

    const feedbackElement = document.getElementById('feedback-progresso');
    if (feedbackElement) {
        feedbackElement.classList.remove('d-none', 'alert-success', 'alert-danger');
        feedbackElement.classList.add('alert-info');
        feedbackElement.textContent = `Salvando... ${Math.floor(tempoAtual)}s`;
    }

    // Usando a API Fetch moderna
    fetch(rotaApi, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(dados),
    })
        .then(response => response.json())
        .then(data => {
            if (feedbackElement) {
                feedbackElement.classList.remove('alert-info');
                if (data.sucesso) {
                    feedbackElement.classList.add('alert-success');
                    feedbackElement.textContent = `Progresso salvo: ${Math.floor(dados.tempo)}s`;
                } else {
                    feedbackElement.classList.add('alert-danger');
                    feedbackElement.textContent = `Erro ao salvar: ${data.mensagem}`;
                }
                // Oculta a mensagem após alguns segundos
                setTimeout(() => {
                    feedbackElement.classList.add('d-none');
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Erro de rede ou API:', error);
            if (feedbackElement) {
                feedbackElement.classList.remove('alert-info');
                feedbackElement.classList.add('alert-danger');
                feedbackElement.textContent = 'Erro de conexão ao salvar progresso.';
                setTimeout(() => {
                    feedbackElement.classList.add('d-none');
                }, 3000);
            }
        });
}

/**
 * Função principal para iniciar o rastreamento.
 */
function inicializarRastreamentoPlayer(playerElement, contentId, contentType, duracaoTotal, rotaApi) {
    if (!playerElement || !contentId || !duracaoTotal || !rotaApi) {
        console.error('Parâmetros de rastreamento incompletos.');
        return;
    }

    // Define a variável global para que a função possa ser chamada por outros eventos
    window.progressoSalvo = function () {
        enviarProgresso(playerElement.currentTime, contentId, contentType, duracaoTotal, rotaApi);
    };

    // 1. Salva o progresso no evento 'timeupdate' (a cada 15 segundos, controlado internamente)
    playerElement.addEventListener('timeupdate', window.progressoSalvo);

    // 2. Salva o progresso quando o usuário pausa o vídeo
    playerElement.addEventListener('pause', window.progressoSalvo);

    // 3. Salva o progresso quando o usuário fecha a página ou navega
    window.addEventListener('beforeunload', window.progressoSalvo);
}

document.addEventListener('DOMContentLoaded', function () {
    const player = document.getElementById('playerPrincipal');
    const contentId = player.getAttribute('data-content-id');
    const contentType = player.getAttribute('data-content-type');
    const duracaoTotal = parseInt(player.getAttribute('data-content-duration'));

    // 1. Iniciar a reprodução no tempo salvo
    if (player && tempoInicial > 0) {
        player.currentTime = tempoInicial;
        console.log(`Iniciando a reprodução a partir de ${tempoInicial} segundos.`);
    }

    // 2. Lógica de Rastreamento (usa a função definida em player_tracking.js)
    if (typeof inicializarRastreamentoPlayer === 'function') {
        inicializarRastreamentoPlayer(player, contentId, contentType, duracaoTotal, URL_BASE + '/api/salvar-progresso');
    } else {
        console.error("Função 'inicializarRastreamentoPlayer' não encontrada. Verifique public/js/player_tracking.js.");
    }

    var lazyImages = [].slice.call(document.querySelectorAll("img.canal-logo"));

    if ("IntersectionObserver" in window) {
        let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    let lazyImage = entry.target;
                    lazyImage.src = lazyImage.dataset.src;
                    lazyImage.classList.remove("canal-logo");
                    lazyImageObserver.unobserve(lazyImage);
                }
            });
        });

        lazyImages.forEach(function(lazyImage) {
            lazyImageObserver.observe(lazyImage);
        });
    }
});