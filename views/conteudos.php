<?php
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

                            <a href="<?= base_url("assistir/$tipo/{$item['stream_id']}") ?>"
                               class="btn btn-sm btn-danger w-100 mt-2">
                                Assistir Agora
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <!-- paginação -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item">
                    <a class="page-link" href="<?= $paginaAtual > 1 ? "?p=" . ($paginaAtual - 1) : '#' ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="<?= $paginaAtual < $totalPaginas ? "?p=" . ($paginaAtual + 1) : '#' ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>


    <?php endif; ?>
</div>