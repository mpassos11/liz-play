<html lang="pt-br" data-bs-theme="dark">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#dc3545">
	<title><?= $titulo ?: 'IPTV' ?></title>
	<?php foreach ($css as $file) : ?>
		<link rel="stylesheet" href="<?= base_url("public/css/$file.css?v=" . time()) ?>">
	<?php endforeach ?>
</head>
<body>
	<div id="loading" style="display: none;"><img src="<?= base_url('imagens/loading.gif') ?>" alt="Loading..." /></div>
	<nav id="navbar" class="navbar navbar-expand-lg bg-body-tertiary" style="margin-bottom: 1%">
		<div class="container">
			<a class="navbar-brand" href="<?= base_url('') ?>">Liz Play</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item">
						<a class="nav-link" href="<?= base_url('conteudos/series') ?>">Séries</a>
					</li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('conteudos/filmes') ?>">Filmes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('conteudos/tv') ?>">Ao Vivo</a>
                    </li>
				</ul>
				<div class="d-flex">
					<a class="btn btn-default" href="<?= base_url('configuracao') ?>">
						<i class="fa-solid fa-gear"></i>
					</a>
                    <a class="btn btn-default" href="<?= base_url('sair') ?>">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
				</div>
			</div>
		</div>
	</nav>
	<div style="padding: 0 1%">
		<script>const COMMON_URL = '<?= COMMON_URL ?>';</script>
		