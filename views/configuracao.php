<form method="post">
	<div class="mb-3">
		<label for="IPTV_API_URL" class="form-label">URL</label>
		<input type="url" class="form-control" id="IPTV_API_URL" name="IPTV_API_URL" value="<?= $config['IPTV_API_URL'] ?: '' ?>">
	</div>
	<div class="mb-3">
		<label for="IPTV_USERNAME" class="form-label">Username</label>
		<input type="text" class="form-control" id="IPTV_USERNAME" name="IPTV_USERNAME" value="<?= $config['IPTV_USERNAME'] ?: '' ?>">
	</div>
	<div class="mb-3">
		<label for="IPTV_PASSWORD" class="form-label">Senha</label>
		<input type="password" class="form-control" id="IPTV_PASSWORD" name="IPTV_PASSWORD" value="<?= $config['IPTV_PASSWORD'] ?: '' ?>">
	</div>
	<button type="submit" class="btn btn-primary">Salvar</button>
</form>
<br>
<a target="_blank" href="<?= base_url('/atualizar-dados') ?>" class="btn btn-primary">Forçar atualização dos dados</a>