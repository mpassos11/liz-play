$(document).ready(function () {
    const $video = $('#video'); // Use um ID ÚNICO para o conteúdo (não o ID do elemento)
    const intervaloSalvamento = 10; // Salvar a cada 10 segundos
    var ultimoTempoSalvo = 0;

    if (tipoConteudo === 'series') {
        if (typeof progresso != 'undefined' && progresso['tipo_conteudo'] === 'series') {
            reproduzirEpisodio(progresso['ep_id'], progresso['tp_id']);
        } else {
            reproduzirEpisodio(episodios[1][0]['id'], 1);
        }
    }

    // Função de Salvamento (Chama seu Backend)
    function salvarProgresso(tempoAtual, videoTerminou = false) {
        if (tempoAtual <= 0 || tipoConteudo === 'tv') return; // Não salvar tempo 0

        // Verifica se houve uma diferença significativa no tempo desde o último salvamento
        if (Math.abs(tempoAtual - ultimoTempoSalvo) < 5 && !videoTerminou) {
            return; // Evita salvamentos repetitivos
        }

        var epId = $('#ep_id').val() || false;
        var tpId = $('#tp_id').val() || false;

        // Requisição AJAX para o backend
        $.ajax({
            url: urlDeSalvamento,
            method: 'POST',
            data: {
                tipo: tipoConteudo,
                content_id: videoId,
                tempo: tempoAtual, // Em segundos
                completo: videoTerminou ? 1 : 0,
                ep_id: epId,
                tp_id: tpId
            }
            // Não precisa de .done() aqui, pois o salvamento é assíncrono e não bloqueia a reprodução
        });

        ultimoTempoSalvo = tempoAtual;
    }

    // --- EVENTOS DO VÍDEO ---

    // 1. timeupdate (Otimizado com Throttle)
    $video.on('timeupdate', function () {
        const tempoAtual = this.currentTime;
        const tempoDecorridoDesdeUltimoSalvamento = tempoAtual - ultimoTempoSalvo;

        // Se o vídeo já passou o tempo de salvamento (ex: 10 segundos)
        if (tempoDecorridoDesdeUltimoSalvamento >= intervaloSalvamento) {
            salvarProgresso(tempoAtual);
        }
    });

    // 2. pause (Prioridade alta para salvar)
    $video.on('pause', function () {
        salvarProgresso(this.currentTime);
    });

    // 3. ended (Vídeo concluído)
    $video.on('ended', function () {
        salvarProgresso(this.duration, true); // Salva o tempo total (duration) e marca como completo

        if (tipoConteudo === 'series') {
            // se for serie, vamos tentar pegar o proximo episodio e reproduzir ele
            const proximoEpisodio = encontrarProximoEpisodio();
            if (proximoEpisodio) {
                reproduzirEpisodio(proximoEpisodio.stream_id, proximoEpisodio.season);
            }
        }
    });

    // 4. loadedmetadata (Restaura o Progresso ao Carregar)
    // Você precisa carregar o tempo salvo do backend e atribuir ao 'tempoSalvo'
    $video.on('loadedmetadata', function () {
        // Exemplo: Simule que você carregou o tempo de 120.5 segundos do banco de dados
        const tempoSalvoDoBackend = tempoAssistido; // <--- SUBSTITUA PELA LÓGICA DE CARREGAMENTO DO SEU BACKEND

        if (tempoSalvoDoBackend > 0 && tempoSalvoDoBackend < this.duration) {
            this.currentTime = tempoSalvoDoBackend;
            // Opcional: Mostrar uma mensagem "Continuar de 2:00"
            console.log(`Progresso restaurado para ${tempoSalvoDoBackend.toFixed(2)} segundos.`);
        }

        this.play();
    });

    $('.ep-list').on('click', function () {
        const idEpisodio = $(this).data('id');
        const temporada = $(this).data('temporada');
        reproduzirEpisodio(idEpisodio, temporada);
    })

    function reproduzirEpisodio(idEpisodio, temporada) {
        temps = episodios[temporada];
        if (typeof temps == 'undefined') {
            return;
        }

        // percorrer os eps da temporada
        var episodio = null;
        for (const ep in temps) {
            if (temps[ep]['id'] == idEpisodio) {
                episodio = temps[ep];
                break;
            }
        }

        if (episodio === null) {
            return;
        }

        $video
            .attr('poster', episodio['info']['movie_image'])
            .attr('src', episodio['stream_link'])
            .trigger('change');

        $('#videoTitle').text(episodio['title']);

        $('#ep_id').val(idEpisodio);
        $('#tp_id').val(temporada);

        $(`#accordionTemporada${temporada}`).collapse('hide');
    }
});

function encontrarProximoEpisodio() {
    // 1. Obter a Posição Atual do HTML
    // NOTA: É crucial que episode_num seja um número para o cálculo, então usamos parseInt()
    const currentSeasonStr = document.getElementById('tp_id').value; // Ex: "1"
    const currentEpNum = parseInt(document.getElementById('ep_id').value); // Ex: 5 (se for o 5º episódio da lista)

    // Conversão para números inteiros para facilitar a navegação pelas chaves
    const currentSeasonNum = parseInt(currentSeasonStr);
    const proximaEpNum = currentEpNum + 1;

    // Se as IDs não existirem ou os valores não forem válidos, retorna null
    if (isNaN(currentSeasonNum) || isNaN(currentEpNum)) {
        console.error("IDs de Temporada/Episódio não encontrados ou são inválidos.");
        return null;
    }

    // 2. Tentar encontrar o próximo episódio na TEMPORADA ATUAL
    const episodiosNaTemporada = episodios[currentSeasonStr];

    // Filtramos o array de episódios da temporada atual para encontrar o próximo (pelo número do episódio)
    const proximoEpisodioNaTemporada = episodiosNaTemporada.find(ep =>
        parseInt(ep.episode_num) === proximaEpNum
    );

    if (proximoEpisodioNaTemporada) {
        // Encontrou o próximo episódio na mesma temporada
        console.log("Próximo Ep encontrado:", proximoEpisodioNaTemporada.title);
        return proximoEpisodioNaTemporada;
    }

    // 3. Se não encontrou, procurar o próximo episódio na PRÓXIMA TEMPORADA

    // Obter todas as chaves de temporada e ordená-las numericamente
    const temporadasOrdenadas = Object.keys(episodios)
        .map(key => parseInt(key))
        .sort((a, b) => a - b);

    const indexTemporadaAtual = temporadasOrdenadas.indexOf(currentSeasonNum);

    // Verifica se existe uma próxima temporada
    if (indexTemporadaAtual !== -1 && indexTemporadaAtual < temporadasOrdenadas.length - 1) {
        const proximaTemporadaNum = temporadasOrdenadas[indexTemporadaAtual + 1];
        const proximaTemporadaStr = String(proximaTemporadaNum);

        const epsProximaTemporada = episodios[proximaTemporadaStr];

        // O primeiro episódio da próxima temporada
        if (epsProximaTemporada && epsProximaTemporada.length > 0) {
            const primeiroEpProximaTemporada = epsProximaTemporada[0];
            console.log("Próxima Temporada encontrada:", primeiroEpProximaTemporada.title);
            return primeiroEpProximaTemporada;
        }
    }

    // Se chegou aqui, significa que era o último episódio da última temporada
    console.log("Não há mais episódios disponíveis.");
    return null;
}