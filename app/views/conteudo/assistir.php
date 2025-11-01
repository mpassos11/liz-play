<?php
$idConteudo = htmlspecialchars($conteudo['stream_id'] ?? 'N/A');
$tipoConteudo = htmlspecialchars($conteudo['tipo_conteudo'] ?? 'filme');
$titulo = htmlspecialchars($conteudo['title'] ?? 'Conteúdo Desconhecido');

$link = $conteudo['stream_link'];
?>

<div class="container">
    <h1 class="mb-4 text-light"><?= $titulo ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="ratio ratio-16x9 mb-4 bg-black">
                <video
                        id="video"
                        controls
                        autoplay
                        src="<?= $link ?>"
                        poster="<?= $conteudo['stream_icon'] ?>"
                ></video>
            </div>

            <p class="text-muted">Detalhes do Conteúdo: ID #<?= $idConteudo ?> | Tipo: <?= ucfirst($tipoConteudo) ?></p>
            <p id="feedback-progresso" class="alert alert-info d-none">Salvando progresso...</p>

            <h2 class="mt-5 text-white">Sinopse</h2>
            <p><?= htmlspecialchars($conteudo['description'] ?? 'Sem sinopse disponível.') ?></p>
        </div>
    </div>
</div>
<script>
    const videoId = <?= $idConteudo ?>;
    const tempoAssistido = <?= $tempoInicial ?>;
    const urlDeSalvamento = "<?= baseUrl('/api/salvar-progresso') ?>";
</script>