<div class="card mb-3" style="">
	<div class="row g-0">
		<div class="col-md-4">
			<img src="<?= $serie['background'] ?>" class="img-fluid rounded-start" alt="<?= $serie['nome'] ?>">
		</div>
		<div class="col-md-8">
			<div class="card-body">
				<h5 class="card-title"><?= $serie['nome'] ?></h5>
				<p class="card-text"><?= utf8_decode($serie['descricao']) ?></p>
				<p class="card-text"><small class="text-body-secondary"><?= $serie['elenco'] ?></small></p>
				<iframe src="https://www.youtube.com/embed/<?= $serie['youtube_trailer'] ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
			</div>
		</div>
	</div>
</div>
<h2 class="text-center">Episodios</h2>
<?php

$newEpisodios = [];

$temporada = 1;
foreach ($episodios as $ep) {
	$newEpisodios[$ep['temporada']][] = $ep;
}

?>
<div class="row">
	<?php foreach ($newEpisodios as $temporada => $episodios): ?>
		<h3>Temporada <?= $temporada ?></h3>
		<?php foreach ($episodios as $ep): ?>
			<div class="col-md-3 items" style="margin-bottom: 1%">
				<a class="btn btn-primary" style="width: 100%" href="<?= base_url("assistir/series/{$ep['stream_id']}") ?>">
					<?= $ep['titulo'] ?>
				</a>
			</div>
		<?php endforeach; ?>
	<?php endforeach; ?>
</div>
