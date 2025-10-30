<?php
/**
 * Variáveis injetadas pelo ControladorConteudo->assistir():
 * $tituloPagina
 * $conteudo (array com os detalhes do item)
 * $tempoInicial (em segundos, de onde o player deve iniciar)
 * $usuarioId (ID do usuário logado)
 */

$idConteudo = htmlspecialchars($conteudo['stream_id'] ?? 'N/A');
$tipoConteudo = htmlspecialchars($conteudo['tipo_conteudo'] ?? 'filme');
$titulo = htmlspecialchars($conteudo['title'] ?? 'Conteúdo Desconhecido');

$link = $conteudo['stream_link'];
if (stripos($link, 'http://') !== FALSE) {
    //$link = base64_encode($link);
    //$link = baseUrl("/proxy?url_proxy={$link}");
}
?>

<div class="container">
    <h1 class="mb-4 text-light"><?= $titulo ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="ratio ratio-16x9 mb-4 bg-black">
                <!-- Este é o contêiner onde o Clappr será injetado -->
                <video id="video" controls autoplay poster="<?= $conteudo['stream_icon'] ?? '' ?>" src="<?= $link ?>" preload="metadata"></video>
            </div>

            <p class="text-muted">Detalhes do Conteúdo: ID #<?= $idConteudo ?> | Tipo: <?= ucfirst($tipoConteudo) ?></p>
            <p id="feedback-progresso" class="alert alert-info d-none">Salvando progresso...</p>

            <h2 class="mt-5 text-white">Sinopse</h2>
            <p><?= htmlspecialchars($conteudo['description'] ?? 'Sem sinopse disponível.') ?></p>
        </div>
    </div>
</div>
<script>
    const URL_BASE = "<?= $_ENV['URL_BASE'] ?>";
    const tipo = "<?= $tipoConteudo ?>";
</script>