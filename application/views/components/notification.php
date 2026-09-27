<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
// Flash message banner, included once per page.
// Rationale: see README.md "Conversion notes and gotchas".
if (empty($message)) {
    return;
}
?>
<div class="alert alert-dismissible fade show no-print" role="alert" data-flash-message>
    <?php // Anything in a flash is controller-authored prose, but it is
          // escaped anyway so a name containing markup cannot break out. ?>
    <i class="bi bi-info-circle me-2"></i><?php echo html_escape($message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
