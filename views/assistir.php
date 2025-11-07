<div id="player" style="width:100%; height:calc(100vh - 56px - 20px)"></div>
<script>
    const video = <?= json_encode($stream) ?: '{}' ?>;
    const urlDeSalvamento = "<?= base_url('/api/salvar-progresso') ?>";
    const tipoConteudo = "<?= $stream['tipo'] ?>";
    const episodios = <?= json_encode($episodios) ?: '{}' ?>;
    const progresso = <?= json_encode($progresso) ?: '{}' ?>;
    var tempoAssistido = progresso['ultimo_tempo_assistido'] || 0;
    var ultimoTempoSalvo = 0;
</script>