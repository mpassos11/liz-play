<div class="modal modal-lg" id="detalhes-stream" tabindex="-1" style="display: none">
	<div class="modal-dialog modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="titulo-modal"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="corpo-modal">
				<input type="hidden" name="conteudo" id="conteudo">
				<div class="position-relative">
					<img src="" id="imagem-stream" class="img-fluid w-100" style="max-height: 300px; object-fit: cover;">
					<div class="fade-overlay"></div>
					<div class="position-absolute bottom-0 start-0 p-3 text-white">
						<h2 class="fw-bold" id="titulo-stream"></h2>
					</div>
				</div>

				<div class="container mt-4">
					<div class="row">
						<!-- Description Section -->
						<div class="col-md-8">
							<p class="text-muted" id="descricao-stream"></p>
							<button class="btn btn-primary" id="btn-assistir">
								<i class="fa-solid fa-play"></i>
								<span id="text-btn-assistir">Assistir</span>
							</button>
							<button id="btn-concluido" href="#" class="btn btn-primary btn-sm">Marcar como Concluido</button>
						</div>

						<!-- Extra Info Section -->
						<div class="col-md-4">
							<h5 class="fw-bold">Elenco</h5>
							<p class="text-muted" id="elenco-stream"></p>

							<h5 class="fw-bold">Trailer</h5>
							<a id="trailer-stream" target="_blank" href="#" class="btn btn-primary btn-sm">Assistir Trailer</a>
						</div>
					</div>

					<!-- Episodes List -->
					<div class="mt-4" id="eps" style="display: none">
						<h4 class="fw-bold">Episódios</h4>
						<ul class="list-group list-group-flush"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="video-player" style="display: none;position: fixed;width: 100%;height: 100vh;background: black;z-index: 1100;top: 0;left:0">
	<div id="player"></div>
</div>

<?php foreach ($js as $file) : ?>
	<script src="<?= base_url("public/js/$file.js?v=" . time()) ?>"> type="text/javascript"></script>
<?php endforeach ?>
