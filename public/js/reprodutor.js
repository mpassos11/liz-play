var player = null;
$(document).ready(function () {
    var config = {
        id:"player",
        file: video.stream_link,
        poster: video.stream_icon,
        title: video.title,
        autoplay: 1,
        start: tempoAssistido,
        vars: {
            content_id: video.stream_id,
        }
    };

    switch (tipoConteudo) {
        case 'series':
            config.file = montarPlaylist();
            if (progresso.hasOwnProperty('ep_id') && progresso.ep_id) {
                config.plstart = progresso.ep_id;
            }
            break;
        case 'tv':
            config.live = true;
            break;
    }

    player = new Playerjs(config);
    definirEventos();

    if (ehCelular()) {
        player.api('fullscreen');
    }
});

function ehCelular() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/.test(navigator.userAgent);
}

function definirEventos() {
    $('#player')
        .on('pause', function () {
            salvarProgresso(player.api('time'));
        })
        .on('time', function () {
            const tempo = player.api('time');
            if ((tempo - ultimoTempoSalvo) >= 10) {
                salvarProgresso(player.api('time'));
            }
        });
}

function montarPlaylist() {
    let playlist = [];
    $.each(episodios, function (i, temporada) {
        let epsTemporada = [];
        $.each(temporada, function (j, episodio) {
            if (!epsTemporada.some(ep => ep.title === episodio.title)) {
                epsTemporada.push({
                    id: episodio.stream_id,
                    title: episodio.title,
                    file: episodio.stream_link,
                    poster: episodio.stream_icon,
                });
            }
        });

        playlist.push({
            title: `Temporada ${i}`,
            folder: epsTemporada
        });
    });

    return playlist;
}
function salvarProgresso(tempoAtual) {
    if (Math.abs(tempoAtual - ultimoTempoSalvo) < 5 || tipoConteudo === 'tv') {
        return;
    }

    $.ajax({
        url: urlDeSalvamento,
        method: 'POST',
        data: {
            tipo: tipoConteudo,
            content_id: player.api('vars').content_id,
            tempo: tempoAtual,
            completo: 0,
            ep_id: player.api('playlist_id') || '',
        }
    });

    ultimoTempoSalvo = tempoAtual;
}