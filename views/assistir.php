<h1 class="mb-4 text-light"><?= $stream['title'] ?></h1>
<?php if ($stream['tipo'] === SERIES_TIPO): ?>
    <input type="hidden" name="ep_id" id="ep_id">
    <input type="hidden" name="tp_id" id="tp_id">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3" id="episodiosAccordion">
        <?php foreach ($episodios as $temporada => $eps): ?>
            <div class="col">
                <div class="accordion" id="accordionTemporada<?= $temporada ?>">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $temporada ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $temporada ?>" aria-expanded="false" aria-controls="collapse<?= $temporada ?>">
                                Temporada <?= $temporada ?>
                            </button>
                        </h2>
                        <div id="collapse<?= $temporada ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $temporada ?>" data-bs-parent="#episodiosAccordion">
                            <div class="accordion-body p-0">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($eps as $episodio): ?>
                                        <a href="#" class="list-group-item list-group-item-action ep-list" data-id="<?= $episodio['id'] ?>" data-temporada="<?= $temporada ?>">
                                            <?= $episodio['title'] ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <hr>
<?php endif; ?>

<div class="row">
    <div class="col-lg-12">
        <div class="ratio ratio-16x9 mb-4 bg-black" style="height: 75vh !important;">
            <video
                    id="video"
                    controls
                    autoplay
                    src="<?= $stream['stream_link'] ?>"
                    poster="<?= $stream['stream_icon'] ?>"
            ></video>
        </div>
    </div>
</div>
<script>
    const videoId = <?= $stream['stream_id'] ?>;
    const urlDeSalvamento = "<?= base_url('/api/salvar-progresso') ?>";
    const tipoConteudo = "<?= $stream['tipo'] ?>";
    const episodios = <?= json_encode($episodios) ?: '{}' ?>;
    const progresso = <?= json_encode($progresso) ?: '{}' ?>;
    const tempoAssistido = progresso['ultimo_tempo_assistido'] || 0;
</script>