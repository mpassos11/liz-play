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
                       poster="<?= $conteudo['stream_icon'] ?>"
                       style="width: 100%; height: 100%;">

                    <source src="<?= $conteudo['live_source'] ?>" type="video/mp4">
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
    const tempoInicial = <?= (int)$tempoInicial ?>; // Injetado pelo Controller
    const URL_BASE = "<?= $_ENV['URL_BASE'] ?>";
</script>