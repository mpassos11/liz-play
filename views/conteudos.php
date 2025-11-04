<?php
$tituloDisplay = $tituloPagina ?? 'Catálogo';
?>

<div class="container">
    <h1 class="mb-4 text-white border-bottom pb-2"><?= htmlspecialchars($tituloDisplay) ?></h1>

    <?php if (empty($conteudo)): ?>
        <div class="alert alert-warning" role="alert">
            Nenhum conteúdo encontrado nesta categoria no momento.
        </div>
    <?php else: ?>

        <div class="row">

            <?php foreach ($conteudo as $categoria => $item): ?>
                <?php echo Navegacao::renderizarConteudo($categoria, $item); ?>
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