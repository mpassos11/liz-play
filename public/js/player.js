// -------------------------------------
// JAVASCRIPT / LÓGICA DO PLAYER (em jQuery)
// -------------------------------------

/**
 * Módulo principal do Video Player.
 * Encapsula todas as funcionalidades do player.
 */
const VideoPlayer = {
    // === 1. Propriedades e Cache de Elementos (usando jQuery) ===
    $video: $('#video'),
    $container: $('#videoContainer'),
    $playPauseBtn: $('#playPauseBtn'),
    $playPauseIcon: $('#playPauseIcon'), // Assume que você tem um ID para o ícone
    $progressBar: $('#progressBar'),
    $progressBarContainer: $('#progressBarContainer'),
    $currentTimeEl: $('#currentTime'),
    $durationTimeEl: $('#durationTime'),
    $volumeIcon: $('#volumeIcon'),
    $volumeSlider: $('#volumeSlider'),
    $nextEpisodePrompt: $('#nextEpisodePrompt'),
    $nextEpisodeBtn: $('#nextEpisodeBtn'),
    $chaneelsChangeBtn: $('#channelsBtn'),
    $channelsModal: $('#channelsModal'),
    $channelListContent: $('#channelListContent'),
    $episodesChangeBtn: $('#episodesBtn'),
    $episodesModal: $('#episodesModal'),
    $seasonSelect: $('#seasonSelect'),
    $episodeListContent: $('#episodeListContent'),
    $videoTitle: $('#videoTitle'),
    $epIdHidden: $('#ep_id'),
    $tpIdHidden: $('#tp_id'),

    END_THRESHOLD: 60, // Mostrar o prompt nos últimos 60 segundos do vídeo

    controlsTimeout: null, // Variável para o timer de ocultar os controles
    currentSeriesData: null, // Para armazenar os dados da série
    intervaloSalvamento: 10, // Salvar a cada 10 segundos
    ultimoTempoSalvo: 0,

    // --- 2. Funções de Ajuda (Helpers) ---

    // Função para formatar o tempo (segundos para MM:SS)
    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        const paddedSeconds = remainingSeconds < 10 ? '0' + remainingSeconds : remainingSeconds;
        return `${minutes}:${paddedSeconds}`;
    },

    // Função para reiniciar o timer de ocultar controles
    resetControlsTimeout() {
        clearTimeout(VideoPlayer.controlsTimeout);
        VideoPlayer.$container.addClass('controls-visible');

        // Define um novo timer para ocultar os controles após 3 segundos
        VideoPlayer.controlsTimeout = setTimeout(() => {
            if (!VideoPlayer.$video[0].paused) {
                VideoPlayer.$container.removeClass('controls-visible');
            }
        }, 3000);
    },

    // --- 3. Métodos de Ação do Player ---

    togglePlayPause() {
        const videoElement = VideoPlayer.$video[0];
        if (videoElement.paused || videoElement.ended) {
            videoElement.play();
        } else {
            videoElement.pause();
        }
        VideoPlayer.resetControlsTimeout();
    },

    toggleMute() {
        const videoElement = VideoPlayer.$video[0];
        videoElement.muted = !videoElement.muted;
        VideoPlayer.updateVolumeIcon();

        // Se desmutar e o volume for 0, ajusta para 0.5
        if (!videoElement.muted && videoElement.volume === 0) {
            videoElement.volume = 0.5;
            VideoPlayer.$volumeSlider.val(0.5);
        }
        VideoPlayer.resetControlsTimeout();
    },

    updateVolumeIcon() {
        const videoElement = VideoPlayer.$video[0];
        VideoPlayer.$volumeIcon.removeClass('fa-volume-up fa-volume-down fa-volume-mute');

        if (videoElement.muted || videoElement.volume === 0) {
            VideoPlayer.$volumeIcon.addClass('fa-volume-mute');
        } else if (videoElement.volume < 0.5) {
            VideoPlayer.$volumeIcon.addClass('fa-volume-down');
        } else {
            VideoPlayer.$volumeIcon.addClass('fa-volume-up');
        }
    },

    // Lógica de "seek" (arrastar/clicar na barra de progresso)
    seekVideo(e) {
        // e.offsetX está disponível no evento nativo que o jQuery passa
        const clickPosition = e.offsetX;
        const totalWidth = VideoPlayer.$progressBarContainer.width();
        const clickPercentage = clickPosition / totalWidth;

        VideoPlayer.$video[0].currentTime = clickPercentage * VideoPlayer.$video[0].duration;
        VideoPlayer.resetControlsTimeout();
    },

    toggleFullScreen() {
        if (!document.fullscreenElement) {
            VideoPlayer.$container[0].requestFullscreen().catch(err => {
                alert(`Erro ao tentar modo tela cheia: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    },

    reproduzirEpisodio(idEpisodio, temporada) {
        // Certifique-se que 'episodios' (variável global) esteja disponível
        const temps = episodios[temporada];
        if (typeof temps === 'undefined') {
            console.error(`Temporada ${temporada} não encontrada nos dados globais.`);
            return;
        }

        let episodio = null;
        for (const ep of temps) {
            if (ep['id'] == idEpisodio) {
                episodio = ep;
                break;
            }
        }

        if (episodio === null) {
            console.error(`Episódio ${idEpisodio} não encontrado na temporada ${temporada}.`);
            return;
        }

        // Atualiza a fonte do vídeo e os metadados
        VideoPlayer.$video
            .attr('poster', episodio['info']['movie_image'])
            .attr('src', episodio['stream_link'])

        VideoPlayer.$video[0].currentTime = 0;

        VideoPlayer.$videoTitle.text(episodio['title']);
        VideoPlayer.$epIdHidden.val(idEpisodio);
        VideoPlayer.$tpIdHidden.val(temporada);

        // Zera o tempo salvo para que loadedmetadata carregue o novo progresso
        VideoPlayer.ultimoTempoSalvo = 0;
        VideoPlayer.$video[0].load(); // Recarrega o vídeo com a nova fonte

        // Se estiver no modal, feche-o
        VideoPlayer.closeEpisodesModal();
    },

    encontrarProximoEpisodio() {
        // Certifique-se que 'episodios' (variável global) esteja disponível
        if (typeof episodios === 'undefined') return null;

        // 1. Obter a Posição Atual do HTML
        const currentSeasonStr = VideoPlayer.$tpIdHidden.val();
        const currentEpId = parseInt(VideoPlayer.$epIdHidden.val()); // ID do episódio atual

        if (!currentSeasonStr || isNaN(currentEpId)) return null;

        const episodiosNaTemporada = episodios[currentSeasonStr];

        // 2. Tentar encontrar o próximo episódio na TEMPORADA ATUAL
        let proximoEpisodioNaTemporada = null;
        let foundCurrent = false;

        if (episodiosNaTemporada) {
            for (const ep of episodiosNaTemporada) {
                if (foundCurrent) {
                    // Encontrou o próximo após o atual
                    proximoEpisodioNaTemporada = ep;
                    break;
                }
                if (parseInt(ep.id) === currentEpId) {
                    foundCurrent = true;
                }
            }
        }

        if (proximoEpisodioNaTemporada) {
            console.log("Próximo Ep encontrado:", proximoEpisodioNaTemporada.title);
            // Adiciona a chave 'season' para consistência (necessário para reproduzirEpisodio)
            proximoEpisodioNaTemporada.season = currentSeasonStr;
            proximoEpisodioNaTemporada.stream_id = proximoEpisodioNaTemporada.id;
            return proximoEpisodioNaTemporada;
        }

        // 3. Se não encontrou, procurar o próximo episódio na PRÓXIMA TEMPORADA
        const temporadasOrdenadas = Object.keys(episodios)
            .map(key => parseInt(key))
            .sort((a, b) => a - b);

        const currentSeasonNum = parseInt(currentSeasonStr);
        const indexTemporadaAtual = temporadasOrdenadas.indexOf(currentSeasonNum);

        if (indexTemporadaAtual !== -1 && indexTemporadaAtual < temporadasOrdenadas.length - 1) {
            const proximaTemporadaNum = temporadasOrdenadas[indexTemporadaAtual + 1];
            const proximaTemporadaStr = String(proximaTemporadaNum);
            const epsProximaTemporada = episodios[proximaTemporadaStr];

            if (epsProximaTemporada && epsProximaTemporada.length > 0) {
                const primeiroEpProximaTemporada = epsProximaTemporada[0];
                console.log("Próxima Temporada encontrada:", primeiroEpProximaTemporada.title);
                primeiroEpProximaTemporada.season = proximaTemporadaStr;
                primeiroEpProximaTemporada.stream_id = primeiroEpProximaTemporada.id;
                return primeiroEpProximaTemporada;
            }
        }

        console.log("Não há mais episódios disponíveis.");
        return null;
    },

    salvarProgresso(tempoAtual, videoTerminou = false) {
        if (tempoAtual <= 0 || tipoConteudo === 'tv' || typeof urlDeSalvamento === 'undefined') return;

        if (Math.abs(tempoAtual - VideoPlayer.ultimoTempoSalvo) < 5 && !videoTerminou) {
            return;
        }

        const epId = VideoPlayer.$epIdHidden.val() || false;
        const tpId = VideoPlayer.$tpIdHidden.val() || false;

        $.ajax({
            url: urlDeSalvamento,
            method: 'POST',
            data: {
                tipo: tipoConteudo,
                content_id: videoId,
                tempo: tempoAtual,
                completo: videoTerminou ? 1 : 0,
                ep_id: epId,
                tp_id: tpId
            }
        });

        VideoPlayer.ultimoTempoSalvo = tempoAtual;
    },

    // --- 4. Métodos de Modais (Canais e Episódios) ---

    // === Canais Modal ===
    openChannelsModal() {
        VideoPlayer.$channelsModal.addClass('show-modal');
        VideoPlayer.loadChannelList();
    },

    closeChannelsModal() {
        VideoPlayer.$channelsModal.removeClass('show-modal');
    },

    loadChannelList() {
        // Checagem de cache simples via .data() do jQuery
        if (VideoPlayer.$channelListContent.data('loaded')) {
            return;
        }

        VideoPlayer.$channelListContent.html('Carregando lista de canais...');

        // Usando o $.ajax do jQuery para Fetch/AJAX
        $.ajax({
            url: COMMON_URL + 'conteudos/tv',
            method: 'GET',
            dataType: 'html', // Espera HTML
            headers: {
                'Source': 'ajax'
            },
            success: function(htmlContent) {
                VideoPlayer.$channelListContent.html(htmlContent);
                VideoPlayer.$channelListContent.data('loaded', true); // Marca como carregado
                VideoPlayer.addChannelClickListeners();
                definirCarousel();
                definirLazyLoad();
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                VideoPlayer.$channelListContent.html('<p style="color: red;">Não foi possível carregar os canais. Tente novamente.</p>');
            }
        });
    },

    addChannelClickListeners() {
        // Usa o delegado de evento do jQuery para ser mais eficiente
        VideoPlayer.$channelListContent.off('click', '.channel-link').on('click', '.channel-link', function(e) {
            e.preventDefault();
            const newStreamUrl = $(this).attr('href');
            window.location.href = newStreamUrl;
            VideoPlayer.closeChannelsModal();
        });
    },


    // === Episódios Modal ===
    openEpisodesModal() {
        VideoPlayer.$video[0].pause();
        VideoPlayer.$episodesModal.addClass('show-modal');

        if (!VideoPlayer.currentSeriesData) {
            VideoPlayer.loadSeriesData();
        } else {
            VideoPlayer.renderSeasonSelector();
        }
    },

    closeEpisodesModal() {
        VideoPlayer.$episodesModal.removeClass('show-modal');
    },

    loadSeriesData() {
        // Coloca o estado de carregamento
        VideoPlayer.$seasonSelect.html('<option value="">Carregando...</option>');
        VideoPlayer.$episodeListContent.html('<p style="text-align: center;">Buscando dados da série...</p>');

        // Usa o $.ajax do jQuery
        $.ajax({
            url: COMMON_URL + 'episodios/series/' + videoId, // Assumindo que videoId existe globalmente
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                VideoPlayer.currentSeriesData = data;
                VideoPlayer.renderSeasonSelector();
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar dados da série:', error);
                VideoPlayer.$seasonSelect.html('<option value="">Erro ao carregar</option>');
                VideoPlayer.$episodeListContent.html('<p style="color: red; text-align: center;">Falha ao carregar a lista de episódios.</p>');
            }
        });
    },

    renderSeasonSelector() {
        const data = VideoPlayer.currentSeriesData;
        if (!data || !data.seasons) return;

        // Converte para array se não for
        const seasonsArray = Object.values(data.seasons);
        let optionsHtml = '';

        seasonsArray.forEach(season => {
            optionsHtml += `<option value="${season.id}">${season.name}</option>`;
        });

        VideoPlayer.$seasonSelect.html(optionsHtml);

        // Mantém a primeira temporada como default
        const defaultSeasonId = seasonsArray.length > 0 ? seasonsArray[0].id : null;
        if(defaultSeasonId) {
            VideoPlayer.$seasonSelect.val(defaultSeasonId);
        }

        VideoPlayer.renderEpisodes();
    },

    renderEpisodes() {
        const selectedSeasonId = parseInt(VideoPlayer.$seasonSelect.val());
        const data = VideoPlayer.currentSeriesData;
        const seasonsArray = Object.values(data.seasons);
        const selectedSeason = seasonsArray.find(s => s.id === selectedSeasonId);

        if (!selectedSeason || !selectedSeason.episodes) {
            VideoPlayer.$episodeListContent.html('<p style="text-align: center;">Nenhum episódio encontrado para esta temporada.</p>');
            return;
        }

        // Lógica de desduplicação (mantida)
        var episodios = [];
        selectedSeason.episodes.forEach((ep) => {
            if (!episodios.some(e => e.title === ep.title)) {
                episodios.push(ep);
            }
        });

        let episodesHtml = '';
        const currentVideoSrc = VideoPlayer.$video.attr('src');

        $.each(episodios, function(index, ep) {
            const isCurrent = ep.stream_link === currentVideoSrc;
            const currentClass = isCurrent ? ' style="border: 2px solid #e50914;"' : '';

            episodesHtml += `
                <div class="episode-item" data-id="${ep.id}" data-temporada="${selectedSeason.id}" data-url="${ep.stream_link}" data-title="${ep.title}">
                    <div class="episode-thumbnail" style="background-image: url('${ep.info.cover_big}');" ${currentClass}></div>
                    <div class="episode-details">
                        <h4>E${index + 1}: ${ep.title}</h4>
                        <p>${ep.info.plot}</p>
                    </div>
                </div>
            `;
        });

        VideoPlayer.$episodeListContent.html(episodesHtml);
    },

    // --- 5. Inicialização e Event Listeners ---

    init() {
        const videoElement = VideoPlayer.$video[0];

        // --- A. Inicialização de Estado ---
        VideoPlayer.$volumeSlider.val(videoElement.volume);
        VideoPlayer.updateVolumeIcon();
        VideoPlayer.$container.addClass('controls-visible');

        // Lógica de reprodução inicial (do seu código antigo)
        if (tipoConteudo === 'series') {
            if (typeof progresso !== 'undefined' && progresso['tipo_conteudo'] === 'series') {
                VideoPlayer.reproduzirEpisodio(progresso['ep_id'], progresso['tp_id']);
            } else if (typeof episodios !== 'undefined' && episodios[1] && episodios[1][0]) {
                VideoPlayer.reproduzirEpisodio(episodios[1][0]['id'], 1);
            }
        }

        // --- B. Eventos do Player/UI ---
        VideoPlayer.$playPauseBtn.on('click', VideoPlayer.togglePlayPause);
        VideoPlayer.$video.on('click', VideoPlayer.togglePlayPause);
        VideoPlayer.$volumeIcon.on('click', VideoPlayer.toggleMute);
        VideoPlayer.$volumeSlider.on('input', function() {
            videoElement.volume = $(this).val();
            videoElement.muted = videoElement.volume === 0;
            VideoPlayer.updateVolumeIcon();
            VideoPlayer.resetControlsTimeout();
        });
        VideoPlayer.$progressBarContainer.on('click', VideoPlayer.seekVideo);
        VideoPlayer.$container.on('mousemove', VideoPlayer.resetControlsTimeout);

        // --- C. Eventos de Navegação/Modais ---

        // 1. Próximo Episódio via Prompt
        VideoPlayer.$nextEpisodePrompt.on('click', function () {
            const proximo = VideoPlayer.encontrarProximoEpisodio();
            if (proximo) {
                tempoAssistido = 0;
                VideoPlayer.reproduzirEpisodio(proximo.stream_id, proximo.season);
            }
            $(this).removeClass('show');
        });

        // O evento do seu código anterior para '.episode-item' e '.ep-list' foi unificado aqui:
        VideoPlayer.$episodeListContent.on('click', '.episode-item', function() {
            const $item = $(this);
            const idEpisodio = $item.data('id');
            const temporada = $item.data('temporada');
            tempoAssistido = 0;
            VideoPlayer.reproduzirEpisodio(idEpisodio, temporada);
        });

        // 3. Seleção de Temporada no Modal
        VideoPlayer.$seasonSelect.on('change', VideoPlayer.renderEpisodes);

        // 4. Fechar Modal com ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                VideoPlayer.closeChannelsModal();
                VideoPlayer.closeEpisodesModal();
            }
        });


        // --- D. Eventos de Vídeo (Lógica de Progresso) ---
        VideoPlayer.$video
            .on('loadedmetadata', function() {
                // 1. Duração e Tempo Inicial
                VideoPlayer.$durationTimeEl.text(VideoPlayer.formatTime(videoElement.duration));

                // 2. Restaura o Progresso ao Carregar (Lógica de Resume)
                const tempoSalvoDoBackend = typeof tempoAssistido !== 'undefined' ? tempoAssistido : 0; // Assume que 'tempoAssistido' está globalmente disponível

                if (tempoSalvoDoBackend > 0 && tempoSalvoDoBackend < this.duration) {
                    this.currentTime = tempoSalvoDoBackend;
                    VideoPlayer.ultimoTempoSalvo = tempoSalvoDoBackend;
                    console.log(`Progresso restaurado para ${tempoSalvoDoBackend.toFixed(2)} segundos.`);
                }

                // Tenta dar play automaticamente
                this.play().catch(error => {
                    console.log('Autoplay impedido. Usuário precisa interagir.');
                });
            })
            .on('play', function() {
                VideoPlayer.$playPauseIcon.removeClass('fa-play').addClass('fa-pause').trigger('change');
                VideoPlayer.resetControlsTimeout();
            })
            .on('pause', function() {
                VideoPlayer.$playPauseIcon.removeClass('fa-pause').addClass('fa-play').trigger('change');
                clearTimeout(VideoPlayer.controlsTimeout);
                VideoPlayer.$container.addClass('controls-visible');

                // Salva o progresso ao pausar
                VideoPlayer.salvarProgresso(this.currentTime);
            })
            .on('timeupdate', function() {
                const tempoAtual = this.currentTime;
                const percentage = (tempoAtual / this.duration) * 100;
                VideoPlayer.$progressBar.css('width', percentage + '%');
                VideoPlayer.$currentTimeEl.text(VideoPlayer.formatTime(tempoAtual));

                // Lógica de Salvamento Periódico
                if (tempoAtual - VideoPlayer.ultimoTempoSalvo >= VideoPlayer.intervaloSalvamento) {
                    VideoPlayer.salvarProgresso(tempoAtual);
                }

                // Lógica do Próximo Episódio
                const timeLeft = this.duration - tempoAtual;
                if (timeLeft <= VideoPlayer.END_THRESHOLD && timeLeft > 0) {
                    VideoPlayer.$nextEpisodePrompt.addClass('show');
                } else {
                    VideoPlayer.$nextEpisodePrompt.removeClass('show');
                }
            })
            .on('ended', function() {
                VideoPlayer.salvarProgresso(this.duration, true); // Salva o tempo total

                if (tipoConteudo === 'series') {
                    const proximoEpisodio = VideoPlayer.encontrarProximoEpisodio();
                    if (proximoEpisodio) {
                        tempoAssistido = 0;
                        VideoPlayer.reproduzirEpisodio(proximoEpisodio.stream_id, proximoEpisodio.season);
                    }
                }
            })
            .on('error', function() {
                // Tenta reiniciar o episódio atual (pode ser útil em caso de erro de stream)
                if (typeof progresso !== 'undefined' && progresso['ep_id'] && progresso['tp_id']) {
                    VideoPlayer.reproduzirEpisodio(progresso['ep_id'], progresso['tp_id']);
                }
            });
    }
};

// --- Inicia o Player quando o DOM estiver pronto ---
$(document).ready(function() {
    // Certifique-se de que o jQuery está carregado antes de chamar o init
    if (typeof jQuery !== 'undefined') {
        VideoPlayer.init();
    } else {
        console.error("jQuery não está carregado. O player não será inicializado.");
    }
});