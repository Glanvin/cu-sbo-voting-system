<div class="card shadow-sm border-0" style="border-radius: 1rem;">

    <div class="card-body p-4 p-md-5">

        <div class="text-center mb-4">
            <img src="<?php echo base_url('assets/img/cu-logo.png'); ?>" alt="Capitol University"
                 class="mb-3" style="width: 90px; height: auto;">
            <h1 class="h4 fw-bold mb-1">SBO Voting System</h1>
            <p class="text-muted mb-0">Sign in with your university email to continue</p>
        </div>

        <?php if ( ! empty($login_error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo html_escape($login_error); ?>
            </div>
        <?php endif; ?>

        <?php
        // The legacy login form posted to itself and the viewmodel did the work.
        // form_open() now posts to auth/login, and the CSRF token csrf_field() is required because csrf_protection is on.
        echo form_open('auth/login', array('class' => 'needs-validation', 'novalidate' => 'novalidate'));
        ?>

            <div class="mb-3">
                <label for="email" class="form-label">University Email</label>
                <input type="email" class="form-control<?php echo $this->form_validation->error('email') ? ' is-invalid' : ''; ?>"
                       id="email" name="email" value="<?php echo html_escape($email ?? ''); ?>"
                       placeholder="you@student.edu.ph" required autofocus>
                <div class="invalid-feedback">
                    <?php echo $this->form_validation->error('email'); ?>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control<?php echo $this->form_validation->error('password') ? ' is-invalid' : ''; ?>"
                       id="password" name="password" placeholder="Enter your password" required>
                <div class="invalid-feedback">
                    <?php echo $this->form_validation->error('password'); ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">
 Sign In
            </button>

        <?php echo form_close(); ?>

        <p class="text-center text-muted small mt-4 mb-0">
            Capitol University &middot; Student &amp; Faculty Election Services
        </p>
    </div>
</div>
