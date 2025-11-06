<?php if ($stream['tipo'] === SERIES_TIPO): ?>
    <input type="hidden" name="ep_id" id="ep_id">
    <input type="hidden" name="tp_id" id="tp_id">
<?php endif; ?>

<div class="video-container" id="videoContainer">
    <video id="video" poster="<?= $stream['stream_icon'] ?>" autoplay>
        <source src="<?= $stream['stream_link'] ?>">
        Seu navegador não suporta a tag de vídeo.
    </video>

    <div class="next-episode-prompt" id="nextEpisodePrompt">
        <p>Assistindo a seguir: Episódio 2</p>
        <button id="nextEpisodeBtn">
            <i class="fas fa-forward"></i> Próximo Episódio
        </button>
    </div>

    <div class="video-header">
        <h3 id="videoTitle"><?= $stream['title'] ?></h3>
    </div>

    <div class="video-controls">
        <div class="progress-bar-container" id="progressBarContainer">
            <div class="progress-bar" id="progressBar"></div>
        </div>

        <div class="controls-row">
            <div class="controls-left">
                <button id="playPauseBtn">
                    <i id="playPauseIcon" class="fas fa-play"></i>
                </button>

                <?php if ($stream['tipo'] === TV_TIPO): ?>
                    <button id="channelsBtn" onclick="VideoPlayer.openChannelsModal();">
                        <i class="fas fa-list-ul"></i>
                    </button>
                <?php endif; ?>

                <button id="muteBtn">
                    <i id="volumeIcon" class="fas fa-volume-up"></i>
                </button>

                <input type="range" id="volumeSlider" min="0" max="1" step="0.1" value="1" style="width: 80px;">
            </div>

            <div class="time-display">
                <span id="currentTime">0:00</span> / <span id="durationTime">0:00</span>
            </div>

            <div class="controls-right">
                <?php if ($stream['tipo'] === SERIES_TIPO): ?>
                    <button id="episodesBtn" onclick="VideoPlayer.openEpisodesModal();">
                        <i class="fas fa-bars"></i>
                    </button>
                <?php endif; ?>

                <button onclick="VideoPlayer.toggleFullScreen()">
                    <i class="fas fa-expand"></i>
                </button>
            </div>

            <div class="channel-modal" id="channelsModal">
                <div class="modal-content">
                    <button class="modal-close" onclick="VideoPlayer.closeChannelsModal()">
                        <i class="fas fa-times"></i>
                    </button>
                    <h2 class="modal-title">Canais ao Vivo</h2>

                    <div class="channel-list-content" id="channelListContent">
                        <p>Carregando lista de canais...</p>
                    </div>
                </div>
            </div>

            <div class="episodes-modal" id="episodesModal">
                <div class="modal-content">
                    <button class="modal-close" onclick="VideoPlayer.closeEpisodesModal()">
                        <i class="fas fa-times"></i>
                    </button>
                    <h2 class="modal-title">Temporadas e Episódios</h2>

                    <div class="season-selector">
                        <select id="seasonSelect">
                            <option value="">Carregando...</option>
                        </select>
                    </div>

                    <div class="episode-list-content" id="episodeListContent">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const videoId = <?= $stream['stream_id'] ?>;
    const urlDeSalvamento = "<?= base_url('/api/salvar-progresso') ?>";
    const tipoConteudo = "<?= $stream['tipo'] ?>";
    const episodios = <?= json_encode($episodios) ?: '{}' ?>;
    const progresso = <?= json_encode($progresso) ?: '{}' ?>;
    var tempoAssistido = progresso['ultimo_tempo_assistido'] || 0;
</script>