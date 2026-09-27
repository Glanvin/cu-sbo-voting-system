<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1"><?php echo html_escape($election['name']); ?></h1>
        <p class="text-muted mb-0">
            <i class="bi bi-building me-1"></i><?php echo html_escape(department_label($election['department'])); ?>
        </p>
    </div>
    <a href="<?php echo site_url('student'); ?>" class="btn btn-outline-secondary">
 Back
    </a>
</div>

<?php if (empty($grouped)): ?>

    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
            <i class="bi bi-person-x fs-1 text-muted"></i>
            <h2 class="h5 mt-3">No candidates on this ballot</h2>
            <p class="text-muted mb-0">The election commission has not published any candidates yet. Please check back later.</p>
        </div>
    </div>

<?php else: ?>

    <div class="alert alert-info d-flex align-items-start" role="alert">
        <i class="bi bi-info-circle me-2 mt-1"></i>
        <div>
            <strong>Your ballot is final once submitted.</strong>
            Choose one candidate for each position, or select <em>Abstain</em> to leave that
            position blank deliberately. You may only vote once in this election.
        </div>
    </div>

    <?php
    // election_id is a hidden field rather than a URI segment because the form posts to student/ballot/submit, which has no segment of its own.
    // StudentController::submit_ballot() re-reads it from the POST and then runs the same eligibility and not-already-voted checks the GET did.
    echo form_open('student/ballot/submit', array('id' => 'ballot_form'));
    ?>
        <input type="hidden" name="election_id" value="<?php echo (int) $election['id']; ?>">

        <?php foreach ($grouped as $position => $candidates): ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-award me-2 text-theme"></i><?php echo html_escape($position); ?></span>
                    <span class="badge text-bg-theme"><?php echo count($candidates); ?> candidates</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($candidates as $candidate): ?>
                            <div class="col-12 col-md-6">
                                <label class="candidate-card d-flex gap-3 align-items-start w-100 h-100"
                                       data-candidate-card>
                                    <input class="form-check-input mt-1 flex-shrink-0" type="radio"
                                           name="vote[<?php echo html_escape($position); ?>]"
                                           value="<?php echo (int) $candidate['id']; ?>"
                                           data-candidate-radio>
                                    <span>
                                        <strong class="d-block"><?php echo html_escape($candidate['fullname']); ?></strong>
                                        <small class="text-muted d-block mb-1"><?php echo html_escape($candidate['school_id']); ?></small>
                                        <small class="text-muted d-block"><?php echo html_escape($candidate['bio']); ?></small>
                                    </span>
                                </label>
                            </div>
                        <?php endforeach; ?>

                        <div class="col-12 col-md-6">
                            <label class="candidate-card abstain-card d-flex gap-3 align-items-start w-100 h-100"
                                   data-candidate-card>
                                <input class="form-check-input mt-1 flex-shrink-0" type="radio"
                                       name="vote[<?php echo html_escape($position); ?>]"
                                       value="abstain" data-candidate-radio>
                                <span>
                                    <strong class="d-block">Abstain</strong>
                                    <small class="text-muted d-block">I choose not to vote for this position.</small>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="text-muted small">
                    <i class="bi bi-shield-check me-1"></i>
                    Closing: <strong><?php echo date('F d, Y \a\t g:i A', strtotime($election['close_at'])); ?></strong>
                </div>
                <button type="submit" class="btn btn-primary px-4"
                        data-confirm="Submit your ballot? This cannot be undone, and you will not be able to vote again in this election.">
 Submit ballot
                </button>
            </div>
        </div>

    <?php echo form_close(); ?>

<?php endif; ?>

<script>
    // Bootstrap has no class for styling the label that wraps a checked radio, so the selected card is highlighted here.
    // Rationale: see README.md "Conversion notes and gotchas".
    document.querySelectorAll('[data-candidate-radio]').forEach(function (radio) {
        var card = radio.closest('[data-candidate-card]');

        function sync() {
            // Only the selected card in this position lights up.
            var name = radio.getAttribute('name');
            document.querySelectorAll('input[name="' + CSS.escape(name) + '"]').forEach(function (sibling) {
                sibling.closest('[data-candidate-card]').classList.toggle('selected', sibling.checked);
            });
        }

        radio.addEventListener('change', sync);
        sync();
    });

    // One guard on the final submit, rather than one per position.
    var form = document.getElementById('ballot_form');

    if (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(event.submitter && event.submitter.dataset.confirm
                ? event.submitter.dataset.confirm
                : 'Submit your ballot?')) {
                event.preventDefault();
            }
        });
    }
</script>
