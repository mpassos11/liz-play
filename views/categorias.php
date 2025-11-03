<div class="container">
	<div class="row">
		<?php if (!empty($categorias)): ?>
			<?php foreach ($categorias as $categoria): ?>
				<div class="col-md-12" style="margin-bottom: 5%">
					<h3><?= $categoria['nome'] ?></h3>
					<div class="row slick-carousel">
						<?php foreach ($categoria['streams'] as $stream): ?>
							<div class="card" style="width: 18rem;" onclick="modalInfo(<?= $stream['id'] ?>)">
								<img data-src="<?= base_url('imagens/tv-ao-vivo.jpg') ?>" class="card-img-top img-fluid img-thumbnail rounded lazy">
								<div class="card-body">
									<p class="card-text"><?= $stream['nome'] ?: $stream['titulo'] ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>

<script>
	var TITULOS = {};
	var CONTEUDOS = {};
	var STREAMS = <?= json_encode($conteudos) ?>
</script>
