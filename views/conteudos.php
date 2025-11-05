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

        <div class="row" id="conteudo">
            <?php
            $html = [];
            foreach ($conteudo as $categoria => $item) {
                $append = Navegacao::renderizarConteudo($categoria, $item);
                if (is_string($categoria)) {
                    $append .= "<hr style='margin-bottom: 2rem'>";
                }

                $html[] = $append;
            }

            echo implode('', $html);
            ?>
        </div>
    <?php endif; ?>
</div>