<?php $this->load->view('components/notification'); ?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">View Users</h1>
        <p class="text-muted mb-0">Register and manage accounts for voters, moderators and administrators.</p>
    </div>
    <div class="d-flex gap-3">
        <span class="badge text-bg-light border fs-6">
            <i class="bi bi-mortarboard me-1"></i><?php echo number_format($role_counts['student']); ?> students
        </span>
        <span class="badge text-bg-light border fs-6">
            <i class="bi bi-shield me-1"></i><?php echo number_format($role_counts['moderator']); ?> moderators
        </span>
        <span class="badge text-bg-light border fs-6">
            <i class="bi bi-gear me-1"></i><?php echo number_format($role_counts['admin']); ?> admins
        </span>
    </div>
</div>

<div class="row g-4">

    <!--
        One form serves both create and edit. $edit_user is NULL when adding,
        and holds either a stored row (arriving via ?edit=) or the posted
        values (arriving back from a failed validation), so the same markup
        handles prefill, repopulation and a blank slate.
    -->
    <div class="col-12 col-xl-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-<?php echo $edit_user ? 'pencil-square' : 'person-plus'; ?> me-2 text-theme"></i>
                <?php echo $edit_user ? 'Edit User' : 'Add User'; ?>
            </div>
            <div class="card-body">

                <?php
                $form = array('id' => 'user_form', 'class' => 'needs-validation', 'novalidate' => 'novalidate');
                echo $edit_user
                    ? form_open('users/save', $form, array('user_id' => $edit_user['user_id']))
                    : form_open('users/save', $form);
                ?>

                    <div class="mb-3">
                        <label for="school_id" class="form-label">School ID</label>
                        <input type="text" class="form-control<?php echo field_error_class('school_id'); ?>"
                               id="school_id" name="school_id" maxlength="50" required
                               value="<?php echo html_escape($edit_user['school_id'] ?? ''); ?>">
                        <div class="invalid-feedback"><?php echo $this->form_validation->error('school_id'); ?></div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="firstname" class="form-label">First name</label>
                            <input type="text" class="form-control<?php echo field_error_class('firstname'); ?>"
                                   id="firstname" name="firstname" maxlength="50" required
                                   value="<?php echo html_escape($edit_user['firstname'] ?? ''); ?>">
                            <div class="invalid-feedback"><?php echo $this->form_validation->error('firstname'); ?></div>
                        </div>
                        <div class="col-6">
                            <label for="lastname" class="form-label">Last name</label>
                            <input type="text" class="form-control<?php echo field_error_class('lastname'); ?>"
                                   id="lastname" name="lastname" maxlength="50" required
                                   value="<?php echo html_escape($edit_user['lastname'] ?? ''); ?>">
                            <div class="invalid-feedback"><?php echo $this->form_validation->error('lastname'); ?></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control<?php echo field_error_class('email'); ?>"
                               id="email" name="email" maxlength="100" required
                               value="<?php echo html_escape($edit_user['email'] ?? ''); ?>">
                        <div class="invalid-feedback"><?php echo $this->form_validation->error('email'); ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control<?php echo field_error_class('password'); ?>"
                               id="password" name="password" minlength="6" maxlength="255"
                               <?php /* Blank on edit means "keep the existing
                                       password": User_model::update() omits
                                       the column when nothing is posted. */ ?>
                               <?php echo $edit_user ? '' : 'required'; ?>>
                        <div class="form-text">
                            <?php echo $edit_user ? 'Leave blank to keep the current password.' : 'At least 6 characters.'; ?>
                        </div>
                        <div class="invalid-feedback"><?php echo $this->form_validation->error('password'); ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select<?php echo field_error_class('role'); ?>" id="role" name="role" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo html_escape($role); ?>"
                                    <?php echo ($edit_user['role'] ?? '') === $role ? 'selected' : ''; ?>>
                                    <?php echo html_escape(ucfirst($role)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?php echo $this->form_validation->error('role'); ?></div>
                    </div>

                    <div class="mb-4">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-select<?php echo field_error_class('department'); ?>" id="department" name="department" required>
                            <option value="" disabled <?php echo isset($edit_user['department']) ? '' : 'selected'; ?>>Select a college</option>
                            <?php foreach ($departments as $value => $label): ?>
                                <?php /* The option value is the full department
                                       name, which is what the column stores and
                                       what the election eligibility check
                                       compares against. The abbreviation is
                                       display only. */ ?>
                                <option value="<?php echo html_escape($value); ?>"
                                    <?php echo ($edit_user['department'] ?? '') === $value ? 'selected' : ''; ?>>
                                    <?php echo html_escape($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?php echo $this->form_validation->error('department'); ?></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
 <?php echo $edit_user ? 'Save Changes' : 'Add User'; ?>
                    </button>

                    <?php if ($edit_user): ?>
                        <a href="<?php echo site_url('users'); ?>" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                    <?php endif; ?>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-people me-2 text-theme"></i>Registered Accounts
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">School ID</th>
                            <th scope="col">Email</th>
                            <th scope="col">Department</th>
                            <th scope="col">Role</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No other accounts are registered.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <strong><?php echo html_escape($user['firstname'] . ' ' . $user['lastname']); ?></strong>
                                    <?php if ((int) $user['is_new'] === 1): ?>
                                        <span class="badge text-bg-warning ms-1" title="Must change password on first sign in">New</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap"><?php echo html_escape($user['school_id']); ?></td>
                                <td class="text-break"><?php echo html_escape($user['email']); ?></td>
                                <td class="text-muted small"><?php echo html_escape(department_label($user['department'])); ?></td>
                                <td>
                                    <span class="badge text-bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'moderator' ? 'info' : 'secondary'); ?>">
                                        <?php echo html_escape(ucfirst($user['role'])); ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="<?php echo site_url('users?edit=' . (int) $user['id']); ?>"
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        Edit
                                    </a>

                                    <?php
                                    // Delete is a POST, not a link: the legacy delete form submitted from a modal, but a plain form keeps it working without JavaScript and avoids a GET that mutates.
                                    echo form_open('users/delete', array('class' => 'd-inline', 'id' => 'delete_user_' . (int) $user['id']));
                                    ?>
                                        <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                                data-confirm="Delete <?php echo html_escape($user['firstname'] . ' ' . $user['lastname']); ?> permanently? Their ballot history will be removed too.">
                                            Delete
                                        </button>
                                    <?php echo form_close(); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Confirmation on the destructive action only. Every delete form gets
    // the handler by class, so adding a row needs no extra JavaScript.
    document.querySelectorAll('button[data-confirm]').forEach(function (button) {
        button.addEventListener('click', function (event) {
            if (!window.confirm(button.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });
</script>
