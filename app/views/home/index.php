<?php
/**
 * Variáveis injetadas pelo ControladorHome->index():
 * $tituloPagina
 * $filmes
 * $series
 * $tv
 * $progressoUsuario (array de itens que o usuário está assistindo)
 */
?>

<div class="container-fluid">
    <h1 class="mb-4 text-white">Bem-vindo ao Liz Play</h1>

    <?php if (!empty($progressoUsuario)): ?>
        <section class="mb-5">
            <h2 class="text-warning border-bottom border-warning pb-2">Continuar Assistindo</h2>
            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php foreach ($progressoUsuario as $item): ?>
                    <?php
                    $porcentagem = ($item['ultimo_tempo_assistido'] / max(1, $item['duracao_total'])) * 100;
                    if ($porcentagem > 5 && $porcentagem < 95): // Exibe se assistiu mais de 5% e menos de 95%
                        ?>
                        <div class="col">
                            <div class="card bg-dark text-white h-100 shadow-sm">
                                <img src="https://via.placeholder.com/300x168/222222/FFFFFF?text=<?= strtoupper($item['content_type'][0]) . $item['content_id'] ?>"
                                     class="card-img-top" alt="Capa">
                                <div class="card-body">
                                    <h5 class="card-title text-truncate"><?= htmlspecialchars("Conteúdo ID: {$item['content_id']}") ?></h5>
                                    <p class="card-text text-muted small"><?= htmlspecialchars(ucfirst($item['content_type'])) ?></p>

                                    <div class="progress mb-2" style="height: 10px;">
                                        <div class="progress-bar bg-info" role="progressbar"
                                             style="width: <?= round($porcentagem) ?>%"
                                             aria-valuenow="<?= round($porcentagem) ?>"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted">Assistido: <?= round($porcentagem) ?>%</small>

                                    <a href="<?= htmlspecialchars($caminhoBase) ?>assistir/<?= htmlspecialchars($item['content_type']) ?>/<?= htmlspecialchars($item['content_id']) ?>"
                                       class="btn btn-sm btn-outline-info mt-2 w-100">
                                        Continuar
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="mb-5">
        <h2 class="text-primary border-bottom border-primary pb-2"><a href="<?= htmlspecialchars($caminhoBase) ?>filmes"
                                                                      class="text-decoration-none text-primary">Filmes</a>
        </h2>
        <div class="row row-cols-1 row-cols-md-5 g-3">
            <?php
            // Exibir apenas os 5 primeiros filmes como destaque
            $filmesDestaque = array_slice($filmes, 0, 5);
            foreach ($filmesDestaque as $filme):
                ?>
                <div class="col">
                    <div class="card bg-dark text-white h-100 border-0">
                        <img src="https://via.placeholder.com/300x450/333333/FFFFFF?text=<?= urlencode($filme['title']) ?>"
                             class="card-img-top" alt="<?= htmlspecialchars($filme['title']) ?>">
                        <div class="card-body p-2">
                            <h6 class="card-title text-truncate"><?= htmlspecialchars($filme['title']) ?></h6>
                            <a href="<?= htmlspecialchars($caminhoBase) ?>assistir/filme/<?= htmlspecialchars($filme['id']) ?>"
                               class="btn btn-sm btn-danger w-100 mt-2">Assistir</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

</div>