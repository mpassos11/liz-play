<?php
// Esta variável $dados é injetada pelo Controller
$progressoUsuario = $dados['progresso'];
$filmes = $dados['filmes'];
?>

<?php include APP_ROOT . '/app/Views/layout/cabecalho.php'; ?>

    <div class="container-fluid">
        <h1 class="my-4">Página Inicial</h1>

        <section class="mb-5">
            <h2>Continuar Assistindo</h2>
            <div class="row">
                <?php foreach ($progressoUsuario['progressos'] as $item): ?>
                    <?php
                    $porcentagem = ($item['ultimo_tempo_assistido'] / $item['duracao_total']) * 100;
                    if ($porcentagem < 95): // Exibe se não estiver quase no final
                        ?>
                        <div class="col-md-3">
                            <p><?= htmlspecialchars($item['content_id']) ?> (<?= $item['content_type'] ?>)</p>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: <?= round($porcentagem) ?>%"
                                     aria-valuenow="<?= round($porcentagem) ?>" aria-valuemin="0" aria-valuemax="100">
                                    <?= round($porcentagem) ?>%
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

    </div>

<?php include APP_ROOT . '/app/Views/layout/rodape.php'; ?>