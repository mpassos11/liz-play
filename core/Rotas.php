<?php

// 2. Instanciar o Roteador
$roteador = new Roteador();

$roteador->adicionarRota('/login', 'Login', 'index');
$roteador->adicionarRota('/logar', 'Login', 'logar', 'POST');
$roteador->adicionarRota('/sair', 'Login', 'sair');

// 3. Definir Rotas (mantidas como est�o, esperando a URI limpa)
// Rota Principal: /
$roteador->adicionarRota('', 'Login', 'index');
$roteador->adicionarRota('/home', 'Home', 'index');
$roteador->adicionarRota('/trocar-perfil', 'Home', 'trocar_perfil');
// Rota de Visualiza��o: /assistir/{tipo}/{id}
$roteador->adicionarRota('/assistir/(\w+)/([\w\d]+)', 'Conteudo', 'assistir');
// Rota AJAX para salvar o progresso: /api/salvar-progresso
$roteador->adicionarRota('/api/salvar-progresso', 'Usuario', 'salvarProgresso', 'POST');

$roteador->adicionarRota('/config', 'Config', 'index');
$roteador->adicionarRota('/config/atualizar', 'Config', 'atualizar', 'POST');

$roteador->adicionarRota('/proxy', 'Proxy', 'stream');

// Rota de Categoria: /filmes, /series, /tv
$roteador->adicionarRota('/(\w+)', 'Home', 'categoria');

// 4. Despachar a Requisi��o
// Passamos a URI limpa e tratada para o roteador.
$roteador->despachar($uriFinal);
