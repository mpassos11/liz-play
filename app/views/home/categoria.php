<?php
/**
 * Variáveis injetadas pelo ControladorHome->categoria():
 * $tituloPagina (Ex: Filmes)
 * $conteudo (Array de itens da categoria)
 * $tipo (O nome da categoria no singular, ex: 'filme', 'serie')
 */

$tituloDisplay = $tituloPagina ?? 'Catálogo';

?>

<div class="container-fluid">
    <h1 class="mb-4 text-white border-bottom pb-2"><?= htmlspecialchars($tituloDisplay) ?></h1>

    <?php if (empty($conteudo)): ?>
        <div class="alert alert-warning" role="alert">
            Nenhum conteúdo encontrado nesta categoria no momento.
        </div>
    <?php else: ?>

        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3">

            <?php foreach ($conteudo as $item): ?>
                <div class="col">
                    <div class="card bg-dark text-white h-100 shadow-sm border-0">

                        <img src="<?= $item['stream_icon'] ?>"
                             class="card-img-top"
                             alt="<?= htmlspecialchars($item['title'] ?? 'Sem Título') ?>"
                             style="object-fit: cover; height: 250px;">

                        <div class="card-body p-3">
                            <h6 class="card-title text-truncate"
                                title="<?= htmlspecialchars($item['title'] ?? 'Sem Título') ?>">
                                <?= htmlspecialchars($item['title'] ?? 'Sem Título') ?>
                            </h6>

                            <?php if (isset($item['year'])): ?>
                                <p class="card-text text-muted small mb-1">
                                    Ano: <?= htmlspecialchars($item['year']) ?></p>
                            <?php endif; ?>

                            <a href="<?= htmlspecialchars($caminhoBase) ?>assistir/<?= htmlspecialchars($tipo) ?>/<?= htmlspecialchars($item['id']) ?>"
                               class="btn btn-sm btn-danger w-100 mt-2">
                                Assistir Agora
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>
</div>