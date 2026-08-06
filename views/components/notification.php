<?php 
if (empty($message)) {
    if (isset($_GET['error_msg'])) {
        $message = sanitization($_GET['error_msg']);
    } elseif (isset($_GET['success_msg'])) {
        $message = sanitization($_GET['success_msg']);
    }
}
?>

<?php if (!empty($message)): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
        <div id="systemToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">

            <div class="toast-header" style="background-color: #48672F; color: white;">
                <img src="../../assets/cu-logo.png" class="rounded me-2" alt="CU" style="height: 20px;">
                <strong class="me-auto">System Notice</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>

            <div class="toast-body text-dark bg-white">
                <?php echo $message; ?>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            var toastElement = document.getElementById('systemToast');
            if (toastElement) {
                var toast = new bootstrap.Toast(toastElement);
                toast.show();
                
                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    url.searchParams.delete('error_msg');
                    url.searchParams.delete('success_msg');
                    window.history.replaceState(null, null, url);
                }
            }
        });
    </script>
<?php endif; ?>