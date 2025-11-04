<div class="container">
	<div class="row">
		<?php if (!empty($ultimosAssistidos)): ?>
			<div class="col-md-12">
				<h3>Últimos Assistidos</h3>
				<div class="row slick-carousel">
					<?php foreach ($ultimosAssistidos['progressos'] as $stream): ?>
                        <a href="<?= base_url("assistir/{$stream['tipo']}/{$stream['stream_id']}") ?>">
                            <img src="<?= $stream['stream_icon'] ?>" class="img-fluid img-thumbnail rounded lazy" alt="<?= $stream['title'] ?>">
                        </a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
		
		<?php if (!empty($seriesAleatorias)): ?>
			<div class="col-md-12">
				<h3>Séries Recomendadas</h3>
				<div class="row slick-carousel">
					<?php foreach ($seriesAleatorias as $stream): ?>
						<div onclick="modalInfo(<?= $stream['id'] ?>)">
							<img data-src="<?= $stream['background'] ?: $stream['capa'] ?>" class="img-fluid img-thumbnail rounded lazy" alt="<?= $stream['nome'] ?: $stream['titulo'] ?>">
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
		
		<?php if (!empty($filmesAleatorios)): ?>
			<div class="col-md-12">
				<h3>Filmes Recomendados</h3>
				<div class="row slick-carousel">
					<?php foreach ($filmesAleatorios as $stream): ?>
						<div onclick="modalInfo(<?= $stream['id'] ?>)">
							<img data-src="<?= $stream['background'] ?: $stream['capa'] ?>" class="img-fluid img-thumbnail rounded lazy" alt="<?= $stream['nome'] ?: $stream['titulo'] ?>">
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
