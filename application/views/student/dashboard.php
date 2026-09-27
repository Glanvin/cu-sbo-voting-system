<?php $this->load->view('components/notification'); ?>

<div class="p-4 p-md-5 mb-4 bg-white rounded-3 shadow-sm border">
    <h1 class="h3 fw-bold mb-2">Welcome, <?php echo html_escape($user_name); ?></h1>
    <p class="col-md-8 fs-6 text-muted mb-0">
        These are the elections you are currently eligible to vote in. Choose one to open its ballot.
    </p>
</div>

<?php if (empty($elections)): ?>

    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
            <i class="bi bi-calendar-x fs-1 text-muted"></i>
            <h2 class="h5 mt-3">No open elections right now</h2>
            <p class="text-muted mb-0">
                When an election opens for <?php echo html_escape(department_label($this->session->userdata('department'))); ?>
                it will appear here automatically.
            </p>
        </div>
    </div>

<?php else: ?>

    <div class="row g-4">
        <?php foreach ($elections as $election): ?>
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column">

                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <h2 class="h5 fw-bold mb-0"><?php echo html_escape($election['name']); ?></h2>
                            <span class="badge text-bg-<?php echo status_badge_class($election['status']); ?> text-nowrap">
                                <i class="bi bi-<?php echo status_badge_icon($election['status']); ?> me-1"></i>
                                <?php echo html_escape(ucfirst($election['status'])); ?>
                            </span>
                        </div>

                        <p class="text-muted small mb-3">
                            <i class="bi bi-building me-1"></i><?php echo html_escape(department_label($election['department'])); ?>
                        </p>

                        <dl class="row small mb-3">
                            <dt class="col-5 text-muted fw-normal">Opens</dt>
                            <dd class="col-7"><?php echo date('M d, Y g:i A', strtotime($election['open_at'])); ?></dd>

                            <dt class="col-5 text-muted fw-normal">Closes</dt>
                            <dd class="col-7"><?php echo date('M d, Y g:i A', strtotime($election['close_at'])); ?></dd>

                            <dt class="col-5 text-muted fw-normal">Positions</dt>
                            <dd class="col-7"><?php echo number_format($election['positions']); ?> candidates running</dd>
                        </dl>

                        <?php if ($election['has_voted']): ?>
                            <a href="<?php echo site_url('student/receipt/' . (int) $election['id']); ?>"
                               class="btn btn-outline-primary mt-auto">
 View your receipt
                            </a>
                        <?php else: ?>
                            <a href="<?php echo site_url('student/ballot/' . (int) $election['id']); ?>"
                               class="btn btn-primary mt-auto">
 Cast your vote
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>
