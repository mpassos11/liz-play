<?php
$msg = '';
if ($_SESSION['msg']) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
?>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Alerta</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-body">
            <?= $msg ?? '' ?>
        </div>
    </div>
</div>

<?php foreach ($js as $file) : ?>
    <script src="<?= base_url("public/js/$file.js?v=" . time()) ?>">></script>
<?php endforeach ?>

<script>
    <?php if (!empty($msg)): ?>
    const toast = bootstrap.Toast.getOrCreateInstance($('#liveToast'), {

    });
    toast.show();
    <?php endif; ?>

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('<?= base_url('sw.js') ?>')
                .then(registration => {
                    console.log('ServiceWorker registered: ', registration);
                })
                .catch(registrationError => {
                    console.log('ServiceWorker registration failed: ', registrationError);
                });
        });
    }
</script>
