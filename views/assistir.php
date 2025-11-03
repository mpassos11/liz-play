<h1 class="mb-4 text-light"><?= $stream['title'] ?></h1>

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
    const tempoAssistido = <?= $stream['ultimo_tempo_assistido'] ?: 0 ?>;
    const urlDeSalvamento = "<?= base_url('/api/salvar-progresso') ?>";
</script>