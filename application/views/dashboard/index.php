<?php $this->load->view('components/notification'); ?>

<div class="p-4 p-md-5 mb-4 bg-white rounded-3 shadow-sm border">
    <h1 class="h3 fw-bold mb-2">Welcome to the Voting System Dashboard</h1>
    <p class="col-md-8 fs-6 text-muted mb-0">
        An overview of voter registration and the current state of every electoral event.
    </p>

    <div class="row g-3 g-md-4 mt-2">

        <div class="col-12 col-md-4">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1 small">Registered Voters</h6>
                        <h2 class="mb-0 fw-bold"><?php echo number_format($total_voters); ?></h2>
                    </div>
                    <i class="bi bi-people stat-card-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1 small">Current Election</h6>
                        <?php if ($current): ?>
                            <h4 class="mb-0 fw-bold fs-6 text-truncate" title="<?php echo html_escape($current['name']); ?>">
                                <?php echo html_escape($current['name']); ?>
                            </h4>
                            <span class="badge text-bg-<?php echo $current['status'] === 'ongoing' ? 'success' : 'warning'; ?> mt-1">
                                <?php echo html_escape(ucfirst($current['status'])); ?>
                            </span>
                        <?php else: ?>
                            <?php /* The legacy tile always showed "SBO 2026-2027 /
                                     Ongoing" whether or not such an election existed. */ ?>
                            <h4 class="mb-0 fw-bold fs-6 text-muted">No active election</h4>
                            <small class="text-muted"><?php echo number_format($upcoming); ?> upcoming</small>
                        <?php endif; ?>
                    </div>
                    <i class="bi bi-check2-square stat-card-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1 small">Previous Elections</h6>
                        <h2 class="mb-0 fw-bold"><?php echo number_format($closed); ?></h2>
                        <a href="<?php echo site_url('elections'); ?>" class="text-decoration-none small text-theme">
                            View Details <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <i class="bi bi-clock-history stat-card-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 g-md-4">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-list-check me-2 text-theme"></i>Recent Elections
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Election</th>
                            <th scope="col">Department</th>
                            <th scope="col">Closes</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($elections)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No elections have been created yet.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($elections as $election): ?>
                            <tr>
                                <td><?php echo html_escape($election['name']); ?></td>
                                <td class="text-muted"><?php echo html_escape($election['department']); ?></td>
                                <td class="text-nowrap"><?php echo date('M d, Y g:i A', strtotime($election['close_at'])); ?></td>
                                <td>
                                    <span class="badge text-bg-<?php echo status_badge_class($election['status']); ?>">
                                        <?php echo html_escape(ucfirst($election['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-bar-chart me-2 text-theme"></i>At a Glance
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-people me-2 text-muted"></i>Registered voters</span>
                    <strong><?php echo number_format($total_voters); ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-play-circle me-2 text-muted"></i>Ongoing elections</span>
                    <strong><?php echo number_format($ongoing); ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-calendar-event me-2 text-muted"></i>Upcoming elections</span>
                    <strong><?php echo number_format($upcoming); ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-lock me-2 text-muted"></i>Closed elections</span>
                    <strong><?php echo number_format($closed); ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-person-badge me-2 text-muted"></i>Total candidates</span>
                    <strong><?php echo number_format($total_candidates); ?></strong>
                </li>
            </ul>
        </div>
    </div>
</div>
