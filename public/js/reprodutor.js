// -------------------------------------
// JAVASCRIPT / LÓGICA DO PLAYER
// -------------------------------------
const video = document.getElementById('video');
const container = document.getElementById('videoContainer');
const playPauseBtn = document.getElementById('playPauseBtn');
const progressBar = document.getElementById('progressBar');
const progressBarContainer = document.getElementById('progressBarContainer');
const currentTimeEl = document.getElementById('currentTime');
const durationTimeEl = document.getElementById('durationTime');
const volumeIcon = document.getElementById('volumeIcon'); // Novo: Ícone de Volume
const volumeSlider = document.getElementById('volumeSlider'); // Novo: Slider

let controlsTimeout; // Variável para o timer de ocultar os controles

// --- Funções de Ajuda ---

// Função para formatar o tempo (segundos para MM:SS)
function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.floor(seconds % 60);
    const paddedSeconds = remainingSeconds < 10 ? '0' + remainingSeconds : remainingSeconds;
    return `${minutes}:${paddedSeconds}`;
}

// Função para reiniciar o timer de ocultar controles
function resetControlsTimeout() {
    clearTimeout(controlsTimeout);
    container.classList.add('controls-visible');

    // Define um novo timer para ocultar os controles após 3 segundos
    controlsTimeout = setTimeout(() => {
        if (!video.paused) {
            container.classList.remove('controls-visible');
        }
    }, 3000);
}

// --- Lógica Principal do Player ---

function togglePlayPause() {
    if (video.paused || video.ended) {
        video.play();
    } else {
        video.pause();
    }
    resetControlsTimeout(); // Mantém os controles visíveis após a interação
}

// Atualiza o ícone do botão quando o estado do vídeo muda
// Atualiza o ícone do botão (Font Awesome) quando o estado do vídeo muda
video.addEventListener('play', () => {
    // Troca para o ícone de Pause
    playPauseIcon.classList.remove('fa-play');
    playPauseIcon.classList.add('fa-pause');
    resetControlsTimeout();
});

video.addEventListener('pause', () => {
    // Troca para o ícone de Play
    playPauseIcon.classList.remove('fa-pause');
    playPauseIcon.classList.add('fa-play');
    clearTimeout(controlsTimeout);
    container.classList.add('controls-visible');
});

// Lógica de Mute/Unmute
function toggleMute() {
    video.muted = !video.muted; // Alterna o estado mute
    updateVolumeIcon();
    // Se desmutar, ajusta o slider para o último volume conhecido
    if (!video.muted && video.volume === 0) {
        video.volume = 0.5; // Valor padrão se estava em 0 e desmutou
        volumeSlider.value = 0.5;
    }
    resetControlsTimeout();
}

// Lógica para sincronizar o slider com o volume do vídeo
volumeSlider.addEventListener('input', () => {
    video.volume = volumeSlider.value;
    video.muted = video.volume === 0; // Se o slider for zero, silencia
    updateVolumeIcon();
    resetControlsTimeout();
});

// Atualiza o ícone de volume (Font Awesome)
function updateVolumeIcon() {
    volumeIcon.classList.remove('fa-volume-up', 'fa-volume-down', 'fa-volume-mute');

    if (video.muted || video.volume === 0) {
        volumeIcon.classList.add('fa-volume-mute'); // Silenciado
    } else if (video.volume < 0.5) {
        volumeIcon.classList.add('fa-volume-down'); // Volume baixo
    } else {
        volumeIcon.classList.add('fa-volume-up'); // Volume alto
    }
}

// Evento de "metadata" para carregar a duração inicial
video.addEventListener('loadedmetadata', () => {
    durationTimeEl.textContent = formatTime(video.duration);
});

// Evento de atualização de tempo para a barra de progresso
video.addEventListener('timeupdate', () => {
    const percentage = (video.currentTime / video.duration) * 100;
    progressBar.style.width = percentage + '%';
    currentTimeEl.textContent = formatTime(video.currentTime);
});

// Lógica de "seek" (arrastar/clicar na barra de progresso)
progressBarContainer.addEventListener('click', (e) => {
    const clickPosition = e.offsetX; // Posição do clique em pixels
    const totalWidth = progressBarContainer.clientWidth; // Largura total da barra
    const clickPercentage = clickPosition / totalWidth;

    video.currentTime = clickPercentage * video.duration;
    resetControlsTimeout();
});

// Tela Cheia
function toggleFullScreen() {
    if (!document.fullscreenElement) {
        container.requestFullscreen().catch(err => {
            alert(`Erro ao tentar modo tela cheia: ${err.message}`);
        });
    } else {
        document.exitFullscreen();
    }
}

// --- Controles de Visibilidade na Inatividade (Estilo Netflix) ---
container.addEventListener('mousemove', resetControlsTimeout);

// Inicialização:
container.classList.add('controls-visible');

// Garante que o slider comece sincronizado com o volume (útil se o HTML tiver valor diferente de 1)
volumeSlider.value = video.volume;
updateVolumeIcon(); // Define o ícone de volume inicial