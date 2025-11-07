<?php if (ehCelular()): ?>
    <nav class="navbar navbar-expand-lg fixed-bottom bg-dark d-lg-none" data-bs-theme="dark">
        <div class="container-fluid justify-content-around">
            <ul class="navbar-nav flex-row">
                <li class="nav-item mx-2">
                    <a class="nav-link" href="<?= base_url('') ?>">Inicio</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link" href="<?= base_url('conteudos/series') ?>">Séries</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link" href="<?= base_url('conteudos/filmes') ?>">Filmes</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link" href="<?= base_url('conteudos/tv') ?>">Ao Vivo</a>
                </li>
            </ul>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav" aria-controls="offcanvasNav">
                <i class="bi bi-list"></i> Menu
            </button>
        </div>
    </nav>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavLabel">Opções de Conta</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('configuracao') ?>">Configurações</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('sair') ?>">Trocar Perfil</a>
                </li>
            </ul>
        </div>
    </div>
<?php else: ?>
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
<?php endif; ?>
