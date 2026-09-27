<?php $this->load->view('components/notification'); ?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">Vote Reports</h1>
        <p class="text-muted mb-0">Turnout and per position results for a single election.</p>
    </div>

    <?php if ( ! empty($elections)): ?>
        <?php /* The election is a URI segment (reports/election/:id) rather
               than a query string, so the control navigates instead of
               submitting a form. A GET form would post to /reports and
               lose the id. */ ?>
        <div class="d-flex gap-2">
            <select class="form-select" id="report_election" aria-label="Select election">
                <?php foreach ($elections as $election): ?>
                    <option value="<?php echo (int) $election['id']; ?>"
                        <?php echo $selected['id'] === $election['id'] ? 'selected' : ''; ?>>
                        <?php echo html_escape($election['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <a href="<?php echo site_url('reports'); ?>" class="btn btn-outline-secondary text-nowrap">
 View reports
            </a>
        </div>
    <?php endif; ?>
</div>

<?php if (empty($elections)): ?>

    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
            <i class="bi bi-bar-chart fs-1 text-muted"></i>
            <h2 class="h5 mt-3">No elections to report on</h2>
            <p class="text-muted mb-0">Create an election and record some ballots to see tallies here.</p>
        </div>
    </div>

<?php else: ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="h5 fw-bold mb-1"><?php echo html_escape($selected['name']); ?></h2>
                    <p class="text-muted mb-0">
                        <?php echo html_escape(department_label($selected['department'])); ?>
                    </p>
                    <p class="text-muted small mb-0">
                        <?php echo date('M d, Y g:i A', strtotime($selected['open_at'])); ?>
                        &rarr;
                        <?php echo date('M d, Y g:i A', strtotime($selected['close_at'])); ?>
                    </p>
                </div>
                <span class="badge text-bg-<?php echo status_badge_class($selected['status']); ?> fs-6">
                    <i class="bi bi-<?php echo status_badge_icon($selected['status']); ?> me-1"></i>
                    <?php echo html_escape(ucfirst($selected['status'])); ?>
                </span>
            </div>
        </div>
    </div>

    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1 small">Eligible Voters</h6>
                        <h2 class="mb-0 fw-bold"><?php echo number_format($stats['eligible']); ?></h2>
                    </div>
                    <i class="bi bi-people stat-card-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1 small">Ballots Submitted</h6>
                        <h2 class="mb-0 fw-bold"><?php echo number_format($stats['submitted']); ?></h2>
                    </div>
                    <i class="bi bi-check2-all stat-card-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1 small">Participation</h6>
                        <h2 class="mb-0 fw-bold"><?php echo number_format($stats['participation'], 1); ?>%</h2>
                        <small class="text-muted"><?php echo number_format($stats['eligible'] - $stats['submitted']); ?> yet to vote</small>
                    </div>
                    <i class="bi bi-graph-up-arrow stat-card-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($positions)): ?>
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="bi bi-person-x fs-1 text-muted"></i>
                <h2 class="h5 mt-3">No candidates on this ballot</h2>
                <p class="text-muted mb-0">Add candidates to the election to see tallies.</p>
            </div>
        </div>
    <?php else: ?>

        <div class="accordion" id="tallyAccordion">
            <?php foreach ($positions as $position): ?>
                <div class="accordion-item shadow-sm border-0 mb-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button<?php echo $position['open'] ? '' : ' collapsed'; ?>"
                                type="button" data-bs-toggle="collapse"
                                data-bs-target="#position_<?php echo html_escape($position['key']); ?>">
                            <span class="me-2"><i class="bi bi-award text-theme"></i></span>
                            <strong><?php echo html_escape($position['title']); ?></strong>
                            <span class="badge text-bg-light border ms-2"><?php echo count($position['candidates']); ?> candidates</span>
                        </button>
                    </h2>

                    <div id="position_<?php echo html_escape($position['key']); ?>"
                         class="accordion-collapse collapse<?php echo $position['open'] ? ' show' : ''; ?>"
                         data-bs-parent="#tallyAccordion">
                        <div class="accordion-body">

                            <table class="table table-sm align-middle mb-3">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 2.5rem;">#</th>
                                        <th scope="col">Candidate</th>
                                        <th scope="col" class="text-end" style="width: 6rem;">Votes</th>
                                        <th scope="col" style="width: 9rem;">Share</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($position['candidates'] as $index => $candidate): ?>
                                        <tr>
                                            <td class="text-muted"><?php echo $index + 1; ?></td>
                                            <td><?php echo html_escape($candidate['name']); ?></td>
                                            <td class="text-end fw-bold"><?php echo number_format($candidate['votes']); ?></td>
                                            <td>
                                                <div class="progress" style="height: 8px;" role="progressbar"
                                                     aria-label="<?php echo html_escape($candidate['name']); ?>"
                                                     aria-valuenow="<?php echo $candidate['pct']; ?>" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-success" style="width: <?php echo $candidate['pct']; ?>%;"></div>
                                                </div>
                                                <small class="text-muted"><?php echo number_format($candidate['pct'], 1); ?>%</small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <tr class="table-light">
                                        <td></td>
                                        <td class="text-muted">
                                            <i class="bi bi-dash-circle me-1"></i>Abstained
                                        </td>
                                        <td class="text-end"><?php echo number_format($position['abstain']); ?></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>

                            <p class="text-muted small mb-0">
                                <?php echo number_format($position['cast']); ?> of
                                <?php echo number_format($position['submitted']); ?> submitted ballots
                                include a vote for this position;
                                <?php echo number_format($position['abstain']); ?> abstained.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php if ( ! empty($elections)): ?>
    <script>
        // Navigate to the segment form of the reports route when the
        // election picker changes.
        document.getElementById('report_election').addEventListener('change', function () {
            window.location = '<?php echo site_url('reports/election/'); ?>' + this.value;
        });
    </script>
<?php endif; ?>

