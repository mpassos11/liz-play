<?php

require_once __DIR__ . '/core/FuncoesConfig.php';

carregarEnv(__DIR__ . '/.env');

header("Location: {$_ENV['URL_BASE']}/home");
exit();
