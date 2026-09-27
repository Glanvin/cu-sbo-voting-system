<?php $this->load->view('components/notification'); ?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">Elections</h1>
        <p class="text-muted mb-0">Schedule electoral events, then close or remove them once finished.</p>
    </div>
    <span class="badge text-bg-light border fs-6">
        <i class="bi bi-calendar-event me-1"></i><?php echo number_format(count($elections)); ?> total
    </span>
</div>

<div class="row g-4">

    <!-- Same shared form as the user page: $edit_election is NULL for a
         new election, and holds a row or the posted values otherwise. -->
    <div class="col-12 col-xl-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-<?php echo $edit_election ? 'pencil-square' : 'plus-circle'; ?> me-2 text-theme"></i>
                <?php echo $edit_election ? 'Edit Election' : 'Create Election'; ?>
            </div>
            <div class="card-body">

                <?php
                $form = array('id' => 'election_form', 'class' => 'needs-validation', 'novalidate' => 'novalidate');
                echo $edit_election
                    ? form_open('elections/save', $form, array('election_id' => $edit_election['election_id']))
                    : form_open('elections/save', $form);
                ?>

                    <div class="mb-3">
                        <label for="election_name" class="form-label">Election name</label>
                        <input type="text" class="form-control<?php echo field_error_class('election_name'); ?>"
                               id="election_name" name="election_name" maxlength="150" required
                               placeholder="SBO Supreme Student Council Election 2026"
                               value="<?php echo html_escape($edit_election['name'] ?? ''); ?>">
                        <div class="invalid-feedback"><?php echo $this->form_validation->error('election_name'); ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-select<?php echo field_error_class('department'); ?>" id="department" name="department" required>
                            <option value="" disabled <?php echo isset($edit_election['department']) ? '' : 'selected'; ?>>Select a college</option>
                            <?php foreach ($departments as $value => $label): ?>
                                <?php /* Full name as the value so the student
                                       eligibility match in
                                       Election_model::is_eligible() compares
                                       like for like. */ ?>
                                <option value="<?php echo html_escape($value); ?>"
                                    <?php echo ($edit_election['department'] ?? '') === $value ? 'selected' : ''; ?>>
                                    <?php echo html_escape($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Choose <strong>University Wide</strong> to open an election to every college.</div>
                        <div class="invalid-feedback"><?php echo $this->form_validation->error('department'); ?></div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label for="open_at" class="form-label">Opens at</label>
                            <input type="datetime-local" class="form-control<?php echo field_error_class('open_at'); ?>"
                                   id="open_at" name="open_at" required
                                   value="<?php echo html_escape(to_datetime_local($edit_election['open_at'] ?? '')); ?>">
                            <div class="invalid-feedback"><?php echo $this->form_validation->error('open_at'); ?></div>
                        </div>
                        <div class="col-6">
                            <label for="close_at" class="form-label">Closes at</label>
                            <input type="datetime-local" class="form-control<?php echo field_error_class('close_at'); ?>"
                                   id="close_at" name="close_at" required
                                   value="<?php echo html_escape(to_datetime_local($edit_election['close_at'] ?? '')); ?>">
                            <div class="invalid-feedback"><?php echo $this->form_validation->error('close_at'); ?></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
 <?php echo $edit_election ? 'Save Changes' : 'Create Election'; ?>
                    </button>

                    <?php if ($edit_election): ?>
                        <a href="<?php echo site_url('elections'); ?>" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                    <?php endif; ?>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-list-check me-2 text-theme"></i>All Elections
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Election</th>
                            <th scope="col">Department</th>
                            <th scope="col">Window</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($elections)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No elections have been created yet.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($elections as $election): ?>
                            <tr>
                                <td>
                                    <strong><?php echo html_escape($election['name']); ?></strong><br>
                                    <a href="<?php echo site_url('candidates?election=' . (int) $election['id']); ?>"
                                       class="small text-decoration-none">
                                        <?php echo number_format($election['candidate_count']); ?>
                                        candidates
                                    </a>
                                </td>
                                <td class="text-muted small"><?php echo html_escape(department_label($election['department'])); ?></td>
                                <td class="small text-nowrap">
                                    <?php echo date('M d, Y g:i A', strtotime($election['open_at'])); ?><br>
                                    <span class="text-muted">to <?php echo date('M d, Y g:i A', strtotime($election['close_at'])); ?></span>
                                </td>
                                <td>
                                    <span class="badge text-bg-<?php echo status_badge_class($election['status']); ?>">
                                        <i class="bi bi-<?php echo status_badge_icon($election['status']); ?> me-1"></i>
                                        <?php echo html_escape(ucfirst($election['status'])); ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <?php if ($election['status'] !== 'ongoing'): ?>
                                        <a href="<?php echo site_url('elections?edit=' . (int) $election['id']); ?>"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            Edit
                                        </a>
                                    <?php else: ?>
                                        <?php /* An open election cannot be edited:
                                                 ElectionController::save() refuses it
                                                 because moving the window would
                                                 invalidate ballots in progress. */ ?>
                                        <button class="btn btn-sm btn-outline-secondary" disabled
                                                title="Close the election before editing it">
                                            Edit
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($election['status'] === 'ongoing'): ?>
                                        <?php echo form_open('elections/close', array('class' => 'd-inline')); ?>
                                            <input type="hidden" name="election_id" value="<?php echo (int) $election['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Close early"
                                                    data-confirm="Close this election now? Students will no longer be able to vote.">
                                                Close
                                            </button>
                                        <?php echo form_close(); ?>
                                    <?php endif; ?>

                                    <?php if ($election['status'] === 'closed'): ?>
                                        <?php echo form_open('elections/delete', array('class' => 'd-inline')); ?>
                                            <input type="hidden" name="election_id" value="<?php echo (int) $election['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                                    data-confirm="Delete this election permanently? Its candidates, ballots and votes will be removed with it.">
                                                Delete
                                            </button>
                                        <?php echo form_close(); ?>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary" disabled
                                                title="Only a closed election can be deleted">
                                            OK
                                        </button>
                                    <?php endif; ?>
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
