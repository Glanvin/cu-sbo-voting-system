<?php $this->load->view('components/notification'); ?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">Candidates</h1>
        <p class="text-muted mb-0">
            <?php if ($can_delete): ?>
                Add and manage the candidates competing in each election.
            <?php else: ?>
                Add and edit candidates. Deleting is reserved for administrators.
            <?php endif; ?>
        </p>
    </div>
    <span class="badge text-bg-light border fs-6">
        <i class="bi bi-person-badge me-1"></i><?php echo number_format(count($candidates)); ?> shown
    </span>
</div>

<div class="row g-4">

    <div class="col-12 col-xl-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-<?php echo $edit_candidate ? 'pencil-square' : 'person-plus'; ?> me-2 text-theme"></i>
                <?php echo $edit_candidate ? 'Edit Candidate' : 'Add Candidate'; ?>
            </div>
            <div class="card-body">

                <?php
                $form = array('id' => 'candidate_form', 'class' => 'needs-validation', 'novalidate' => 'novalidate');
                echo $edit_candidate
                    ? form_open('candidates/save', $form, array('candidate_id' => $edit_candidate['candidate_id']))
                    : form_open('candidates/save', $form);
                ?>

                    <div class="mb-3">
                        <label for="election_id" class="form-label">Election</label>
                        <select class="form-select<?php echo field_error_class('election_id'); ?>" id="election_id" name="election_id" required>
                            <option value="" disabled <?php echo isset($edit_candidate['election_id']) ? '' : 'selected'; ?>>Select an election</option>
                            <?php foreach ($elections as $election): ?>
                                <option value="<?php echo (int) $election['id']; ?>"
                                    <?php echo (int) ($edit_candidate['election_id'] ?? 0) === (int) $election['id'] ? 'selected' : ''; ?>>
                                    <?php echo html_escape($election['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?php echo $this->form_validation->error('election_id'); ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="fullname" class="form-label">Full name</label>
                        <input type="text" class="form-control<?php echo field_error_class('fullname'); ?>"
                               id="fullname" name="fullname" maxlength="150" required
                               value="<?php echo html_escape($edit_candidate['fullname'] ?? ''); ?>">
                        <div class="invalid-feedback"><?php echo $this->form_validation->error('fullname'); ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="position" class="form-label">Position</label>
                        <select class="form-select<?php echo field_error_class('position'); ?>" id="position" name="position" required>
                            <option value="" disabled <?php echo isset($edit_candidate['position']) ? '' : 'selected'; ?>>Select a position</option>
                            <?php foreach ($positions as $position): ?>
                                <?php /* The list comes from config/form_rules.php,
                                       the same array the in_list rule is built
                                       from, so the two cannot disagree. */ ?>
                                <option value="<?php echo html_escape($position); ?>"
                                    <?php echo ($edit_candidate['position'] ?? '') === $position ? 'selected' : ''; ?>>
                                    <?php echo html_escape($position); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?php echo $this->form_validation->error('position'); ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control<?php echo field_error_class('bio'); ?>" id="bio" name="bio"
                                  rows="3" maxlength="2000" required
                                  placeholder="A short statement of the candidate's platform."><?php echo html_escape($edit_candidate['bio'] ?? ''); ?></textarea>
                        <div class="invalid-feedback"><?php echo $this->form_validation->error('bio'); ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="school_id" class="form-label">School ID</label>
                        <input type="text" class="form-control<?php echo field_error_class('school_id'); ?>"
                               id="school_id" name="school_id" maxlength="50" required
                               value="<?php echo html_escape($edit_candidate['school_id'] ?? ''); ?>">
                        <div class="invalid-feedback"><?php echo $this->form_validation->error('school_id'); ?></div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-7">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control<?php echo field_error_class('email'); ?>"
                                   id="email" name="email" maxlength="100" required
                                   value="<?php echo html_escape($edit_candidate['email'] ?? ''); ?>">
                            <div class="invalid-feedback"><?php echo $this->form_validation->error('email'); ?></div>
                        </div>
                        <div class="col-5">
                            <label for="contact_number" class="form-label">Contact</label>
                            <input type="text" class="form-control<?php echo field_error_class('contact_number'); ?>"
                                   id="contact_number" name="contact_number" maxlength="11"
                                   placeholder="09171234567" required
                                   value="<?php echo html_escape($edit_candidate['contact_number'] ?? ''); ?>">
                            <div class="invalid-feedback"><?php echo $this->form_validation->error('contact_number'); ?></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
 <?php echo $edit_candidate ? 'Save Changes' : 'Add Candidate'; ?>
                    </button>

                    <?php if ($edit_candidate): ?>
                        <a href="<?php echo site_url('candidates'); ?>" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                    <?php endif; ?>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-8">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="get" action="<?php echo site_url('candidates'); ?>" class="row g-2 align-items-end">
                    <div class="col-12 col-md-5">
                        <label for="filter_election" class="form-label small text-muted mb-1">Filter by election</label>
                        <select class="form-select" id="filter_election" name="election" onchange="this.form.submit()">
                            <option value="">All Elections</option>
                            <?php foreach ($elections as $election): ?>
                                <option value="<?php echo (int) $election['id']; ?>"
                                    <?php echo $filter_election === (int) $election['id'] ? 'selected' : ''; ?>>
                                    <?php echo html_escape($election['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-5">
                        <label for="search" class="form-label small text-muted mb-1">Search</label>
                        <input type="text" class="form-control" id="search" name="q"
                               value="<?php echo html_escape($search); ?>" placeholder="Candidate or election name">
                    </div>
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
 Filter
                        </button>
                        <a href="<?php echo site_url('candidates'); ?>" class="btn btn-outline-secondary" title="Clear filters">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-list-ul me-2 text-theme"></i>Candidate List
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Candidate</th>
                            <th scope="col">Position</th>
                            <th scope="col">Election</th>
                            <th scope="col">Contact</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($candidates)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <?php echo $search !== '' || $filter_election ? 'No candidates match these filters.' : 'No candidates have been added yet.'; ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($candidates as $candidate): ?>
                            <tr>
                                <td>
                                    <strong><?php echo html_escape($candidate['fullname']); ?></strong><br>
                                    <small class="text-muted"><?php echo html_escape($candidate['school_id']); ?></small>
                                </td>
                                <td>
                                    <span class="badge text-bg-light border"><?php echo html_escape($candidate['position']); ?></span>
                                </td>
                                <td class="small text-muted"><?php echo html_escape($candidate['election_name']); ?></td>
                                <td class="small text-nowrap">
                                    <?php echo html_escape($candidate['contact_number']); ?><br>
                                    <span class="text-muted text-truncate d-inline-block" style="max-width: 180px;"
                                          title="<?php echo html_escape($candidate['email']); ?>">
                                        <?php echo html_escape($candidate['email']); ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="<?php echo site_url('candidates?edit=' . (int) $candidate['id']); ?>"
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        Edit
                                    </a>

                                    <?php if ($can_delete): ?>
                                        <?php echo form_open('candidates/delete', array('class' => 'd-inline')); ?>
                                            <input type="hidden" name="candidate_id" value="<?php echo (int) $candidate['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                                    data-confirm="Delete <?php echo html_escape($candidate['fullname']); ?> permanently? Votes already cast for them are kept but will no longer be attributed.">
                                                Delete
                                            </button>
                                        <?php echo form_close(); ?>
                                    <?php else: ?>
                                        <?php /* A moderator sees no delete control at
                                                 all, and CandidateController::delete()
                                                 refuses the POST if one is sent anyway. */ ?>
                                        <button class="btn btn-sm btn-outline-secondary" disabled
                                                title="Only administrators can delete candidates">
                                            Delete
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
