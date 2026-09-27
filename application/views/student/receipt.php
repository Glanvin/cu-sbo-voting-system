<?php $this->load->view('components/notification'); ?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4 no-print">
    <div>
        <h1 class="h4 fw-bold mb-1">Ballot Receipt</h1>
        <p class="text-muted mb-0"><?php echo html_escape($election['name']); ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo site_url('student'); ?>" class="btn btn-outline-secondary">
 My Elections
        </a>
        <button type="button" class="btn btn-primary" onclick="window.print()">
 Print
        </button>
    </div>
</div>

<?php if ($fresh): ?>
    <div class="alert alert-success d-flex align-items-center no-print" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <div>Your ballot has been recorded. This receipt confirms your submission.</div>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 pb-3 mb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <img src="<?php echo base_url('assets/img/cu-logo.png'); ?>" alt="Capitol University" style="width: 44px;">
                    <div>
                        <strong class="d-block">Capitol University</strong>
                        <small class="text-muted">Student Election Services</small>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <span class="badge text-bg-<?php echo status_badge_class($election['status']); ?> fs-6">
                    <i class="bi bi-<?php echo status_badge_icon($election['status']); ?> me-1"></i>
                    <?php echo html_escape(ucfirst($election['status'])); ?>
                </span>
            </div>
        </div>

        <dl class="row mb-4">
            <dt class="col-sm-3 text-muted fw-normal">Voter</dt>
            <dd class="col-sm-9"><?php echo html_escape($user_name); ?></dd>

            <dt class="col-sm-3 text-muted fw-normal">School ID</dt>
            <dd class="col-sm-9"><?php echo html_escape($school_id); ?></dd>

            <dt class="col-sm-3 text-muted fw-normal">Department</dt>
            <dd class="col-sm-9"><?php echo html_escape(department_label($department)); ?></dd>

            <dt class="col-sm-3 text-muted fw-normal">Election</dt>
            <dd class="col-sm-9"><?php echo html_escape($election['name']); ?></dd>

            <dt class="col-sm-3 text-muted fw-normal">Submitted at</dt>
            <dd class="col-sm-9">
                <?php
                // The legacy page fell back to "now" when the submission row
                // was missing, which printed a plausible looking timestamp
                // for a ballot that may never have been cast.
                echo $submitted_at
                    ? date('F d, Y \a\t g:i A', strtotime($submitted_at))
                    : '<span class="text-muted">Unknown</span>';
                ?>
            </dd>
        </dl>

        <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Recorded selections</h2>

        <table class="table table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 40%;">Position</th>
                    <th scope="col">Your choice</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ballot)): ?>
                    <tr>
                        <td colspan="2" class="text-center text-muted py-3">No selections were recorded.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($ballot as $row): ?>
                    <tr>
                        <td><?php echo html_escape($row['position']); ?></td>
                        <td>
                            <?php if ($row['candidate_id'] === NULL): ?>
                                <span class="text-muted">
                                    <i class="bi bi-dash-circle me-1"></i><em>Abstained</em>
                                </span>
                            <?php elseif ($row['candidate_name'] === NULL): ?>
                                <?php /* Only reachable if the candidate was deleted
                                       after the vote: the FK is ON DELETE SET NULL,
                                       so the vote survives without a name. */ ?>
                                <span class="text-muted"><em>Candidate no longer listed</em></span>
                            <?php else: ?>
                                <?php echo html_escape($row['candidate_name']); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th scope="row" class="small fw-normal">Summary</th>
                    <td class="small">
                        <?php echo number_format($voted); ?> of <?php echo number_format($total); ?>
                        positions voted
                        <?php if ($abstained > 0): ?>
                            &middot; <?php echo number_format($abstained); ?> abstained
                        <?php endif; ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="alert alert-secondary small no-print" role="alert">
    <i class="bi bi-shield-lock me-2"></i>
    This receipt is visible only to you. It confirms that your ballot was submitted and
    counted; it is not a public record of how you voted.
</div>
