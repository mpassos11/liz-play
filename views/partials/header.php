<html lang="pt-br" data-bs-theme="dark">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#dc3545">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="<?= base_url('public/imagens/icon.ico') ?>" type="image/x-icon">
	<title><?= $titulo ? $titulo . ' - Liz Play' : 'Liz Play' ?></title>
	<?php foreach ($css as $file) : ?>
		<link rel="stylesheet" href="<?= base_url("public/css/$file.css?v=" . time()) ?>">
	<?php endforeach ?>
</head>
<body>
    <div id="loading-overlay" class="loaded">
        <div id="loading-spinner"></div>
    </div>
	<?php include_once COMMON_PATH . 'views/partials/nav.php' ?>
	<div>
		<script>const COMMON_URL = '<?= COMMON_URL ?>';</script>
		