<div class="card shadow-sm border-0" style="border-radius: 1rem;">

    <div class="card-body p-4 p-md-5">

        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-theme-soft mb-3"
                 style="width: 64px; height: 64px;">
                <i class="bi bi-shield-lock fs-2 text-theme"></i>
            </div>
            <h1 class="h4 fw-bold mb-1">Set a new password</h1>
            <?php if ($is_new): ?>
                <p class="text-muted mb-0">
                    Hello <?php echo html_escape($firstname ?: 'there'); ?>, this is your first sign in,
                    so please choose a password of your own before continuing.
                </p>
            <?php else: ?>
                <p class="text-muted mb-0">Choose a new password for your account.</p>
            <?php endif; ?>
        </div>

        <?php
        // `matches[new_password]` on the confirm field is the framework replacement for the legacy `$new_password === $confirm_password` comparison, and the error is shown against the confirm input where the user is looking for it.
        echo form_open('auth/change_password', array('class' => 'needs-validation', 'novalidate' => 'novalidate'));
        ?>

            <div class="mb-3">
                <label for="new_password" class="form-label">New password</label>
                <input type="password" class="form-control<?php echo $this->form_validation->error('new_password') ? ' is-invalid' : ''; ?>"
                       id="new_password" name="new_password" minlength="6" required autofocus>
                <div class="form-text">At least 6 characters.</div>
                <div class="invalid-feedback">
                    <?php echo $this->form_validation->error('new_password'); ?>
                </div>
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="form-label">Confirm new password</label>
                <input type="password" class="form-control<?php echo $this->form_validation->error('confirm_password') ? ' is-invalid' : ''; ?>"
                       id="confirm_password" name="confirm_password" required>
                <div class="invalid-feedback">
                    <?php echo $this->form_validation->error('confirm_password'); ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">
 Save Password
            </button>

        <?php echo form_close(); ?>
    </div>
</div>
