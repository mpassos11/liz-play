<?php
/**
 * Variáveis injetadas pelo ControladorConteudo->assistir():
 * $tituloPagina
 * $conteudo (array com os detalhes do item)
 * $tempoInicial (em segundos, de onde o player deve iniciar)
 * $usuarioId (ID do usuário logado)
 */

$idConteudo = htmlspecialchars($conteudo['id'] ?? 'N/A');
$tipoConteudo = htmlspecialchars($conteudo['tipo_conteudo'] ?? 'filme');
$titulo = htmlspecialchars($conteudo['title'] ?? 'Conteúdo Desconhecido');
$duracaoSimulada = 1800; // Simula 30 minutos em segundos para o exemplo
?>

<div class="container">
    <h1 class="mb-4 text-light"><?= $titulo ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="ratio ratio-16x9 mb-4 bg-black">
                <video id="playerPrincipal" controls autoplay
                       data-content-id="<?= $idConteudo ?>"
                       data-content-type="<?= $tipoConteudo ?>"
                       data-content-duration="<?= $duracaoSimulada ?>"
                       poster="https://via.placeholder.com/1280x720/000000/FFFFFF?text=PLAYER+LIZ+PLAY"
                       style="width: 100%; height: 100%;">

                    <source src="URL_DO_STREAMING_AQUI" type="video/mp4">
                    Seu navegador não suporta a tag de vídeo.
                </video>
            </div>

            <p class="text-muted">Detalhes do Conteúdo: ID #<?= $idConteudo ?> | Tipo: <?= ucfirst($tipoConteudo) ?></p>
            <p id="feedback-progresso" class="alert alert-info d-none">Salvando progresso...</p>

            <h2 class="mt-5 text-white">Sinopse</h2>
            <p><?= htmlspecialchars($conteudo['description'] ?? 'Sem sinopse disponível.') ?></p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const player = document.getElementById('playerPrincipal');
        const contentId = player.getAttribute('data-content-id');
        const contentType = player.getAttribute('data-content-type');
        const duracaoTotal = parseInt(player.getAttribute('data-content-duration'));
        const tempoInicial = <?= (int)$tempoInicial ?>; // Injetado pelo Controller

        // 1. Iniciar a reprodução no tempo salvo
        if (player && tempoInicial > 0) {
            player.currentTime = tempoInicial;
            console.log(`Iniciando a reprodução a partir de ${tempoInicial} segundos.`);
        }

        // 2. Lógica de Rastreamento (usa a função definida em player_tracking.js)
        if (typeof inicializarRastreamentoPlayer === 'function') {
            inicializarRastreamentoPlayer(player, contentId, contentType, duracaoTotal, '<?= $caminhoBase ?>api/salvar-progresso');
        } else {
            console.error("Função 'inicializarRastreamentoPlayer' não encontrada. Verifique public/js/player_tracking.js.");
        }
    });
</script>