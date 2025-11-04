$(document).ready(function () {
    const $video = $('#video'); // Use um ID ÚNICO para o conteúdo (não o ID do elemento)
    const intervaloSalvamento = 10; // Salvar a cada 10 segundos
    var ultimoTempoSalvo = 0;

    // Função de Salvamento (Chama seu Backend)
    function salvarProgresso(tempoAtual, videoTerminou = false) {
        if (tempoAtual <= 0 || tipoConteudo === 'tv') return; // Não salvar tempo 0

        // Verifica se houve uma diferença significativa no tempo desde o último salvamento
        if (Math.abs(tempoAtual - ultimoTempoSalvo) < 5 && !videoTerminou) {
            return; // Evita salvamentos repetitivos
        }

        // Requisição AJAX para o backend
        $.ajax({
            url: urlDeSalvamento,
            method: 'POST',
            data: {
                tipo: tipoConteudo,
                content_id: videoId,
                tempo: tempoAtual, // Em segundos
                completo: videoTerminou ? 1 : 0
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
});