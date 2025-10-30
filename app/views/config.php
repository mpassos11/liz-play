<div class="container mt-5">
    <h2 class="text-center mb-4">Configurações de IPTV</h2>

    <?php if (isset($dados['mensagem'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($dados['mensagem']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow">
                <div class="card-body">

                    <form action="/config/atualizar" method="POST">
                        <h4 class="card-title text-warning mb-3">🛠️ Configuração API (Xtream Codes)</h4>

                        <div class="mb-3">
                            <label for="iptv_api_url" class="form-label text-warning">URL da API (Ex: http://host:porta):</label>
                            <input
                                    type="url"
                                    class="form-control"
                                    id="iptv_api_url"
                                    name="iptv_api_url"
                                    value="<?= htmlspecialchars($dados['iptv_api_url'] ?? '') ?>"
                                    placeholder="Deixe em branco se usar apenas M3U"
                            >
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="iptv_username" class="form-label text-warning">Usuário:</label>
                                <input
                                        type="text"
                                        class="form-control"
                                        id="iptv_username"
                                        name="iptv_username"
                                        value="<?= htmlspecialchars($dados['iptv_username'] ?? '') ?>"
                                >
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="iptv_password" class="form-label text-warning">Nova Senha (deixe em branco para manter a atual):</label>
                                <input
                                        type="password"
                                        class="form-control"
                                        id="iptv_password"
                                        name="iptv_password"
                                        placeholder="Mudar senha"
                                >
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i> Salvar Configurações
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-body">
                    <h4 class="card-title text-warning mb-3">Cache e Reprocessamento</h4>
                    <p class="card-text text-warning">Use o botão abaixo para limpar o cache e reprocessar a lista IPTV com base nas configurações salvas.</p>
                    <div class="d-grid gap-2">
                        <button id="btn-reprocessar-iptv" class="btn btn-warning btn-lg">
                            <i class="fas fa-redo-alt me-2"></i> Reprocessar e Recarregar IPTV
                        </button>
                    </div>
                    <p id="status-cache" class="mt-3"></p>
                </div>
            </div>
        </div>
    </div>
</div>