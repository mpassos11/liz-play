<?php
// Garante que a variável $tituloPagina foi definida pelo Controller
$titulo = $tituloPagina ?? 'Seu Sistema IPTV';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">

    <link rel="stylesheet" href="<?= htmlspecialchars($caminhoBase) ?>css/style.css">

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
        <a class="navbar-brand" href="<?= htmlspecialchars($caminhoBase) ?>">LIZ PLAY</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= htmlspecialchars($caminhoBase) ?>filmes">Filmes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= htmlspecialchars($caminhoBase) ?>series">Séries</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= htmlspecialchars($caminhoBase) ?>tv">TV Ao Vivo</a>
                </li>
            </ul>
            <span class="navbar-text text-light">
                Olá, Usuário <?= USUARIO_LOGADO_ID ?? 'Visitante' ?>
            </span>
        </div>
    </div>
</nav>

<main class="container-fluid py-4">