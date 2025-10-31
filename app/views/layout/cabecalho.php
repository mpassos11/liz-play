<?php
// Garante que a variável $tituloPagina foi definida pelo Controller
$titulo = $tituloPagina ?? 'Seu Sistema IPTV';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests; block-all-mixed-content;">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests;">
    <title><?= htmlspecialchars($titulo) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">

    <style>
        /* Estilos básicos para o tema escuro de streaming */
        body {
            background-color: #1a1a1a;
            color: #f0f0f0;
        }

        .navbar {
            background-color: #0d0d0d !important;
        }

        .card-body {
            background-color: #2b2b2b;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $_ENV['URL_BASE'] ?>">LIZ PLAY</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= baseUrl('/filmes') ?>">Filmes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= baseUrl('/series') ?>">Séries</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= baseUrl('/tv') ?>">TV Ao Vivo</a>
                </li>
            </ul>
            <span class="navbar-text text-light">
                Olá, <?= $_SESSION['nome_usuario'] ?> |
                <?php if (class_exists('ModeloAuth') && ModeloAuth::estaAutenticado()): ?>
                    <a href="<?= baseUrl('/config') ?>">Configurações</a>
                    <a href="<?= baseUrl('/trocar-perfil') ?>">Perfil</a>
                    <a href="<?= baseUrl('/sair') ?>">Sair</a>
                <?php endif; ?>
            </span>
        </div>
    </div>
</nav>

<main class="container-fluid py-4">