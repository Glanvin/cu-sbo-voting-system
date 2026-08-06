<?php
require '../../viewmodels/shared/view_reports_viewmodel.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SBO Voting System - Reports</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="../../assets/style.css">

    <style>
        .progress { height: 6px; }
        .accordion-button:not(.collapsed) {
            color: #48672F;
            background-color: rgba(72, 103, 47, 0.07);
            box-shadow: none;
        }
        .accordion-button:focus { box-shadow: none; }
    </style>
</head>

<body class="bg-dashboard vh-100 overflow-hidden d-flex flex-column flex-md-row">

    <!-- Sidebar -->
    <div class="sidebar-bg text-white shadow offcanvas-md offcanvas-start d-flex flex-column flex-shrink-0"
         tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel" style="width: 250px;">

        <div class="offcanvas-body d-flex flex-column flex-grow-1 p-3">
            <a href="#" class="d-none d-md-flex align-items-center me-md-auto text-white text-decoration-none px-2">
                <span class="fs-4 fw-bold">Voting System</span>
            </a>
            <hr class="d-none d-md-block">

            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <span class="material-symbols-outlined">home</span>
                        Home
                    </a>
                </li>
                <li>
                    <a href="../admin/view_users.php" class="nav-link">
                        <span class="material-symbols-outlined">groups</span>
                        View Users
                    </a>
                </li>
                <li>
                    <a href="../admin/view_elections.php" class="nav-link">
                        <span class="material-symbols-outlined">how_to_vote</span>
                        Elections
                    </a>
                </li>
                <li>
                    <a href="../shared/view_candidates.php" class="nav-link">
                        <span class="material-symbols-outlined">person</span>
                        Candidates
                    </a>
                </li>
                <li>
                    <a href="reports.php" class="nav-link active" aria-current="page">
                        <span class="material-symbols-outlined">analytics</span>
                        Reports
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link">
                        <span class="material-symbols-outlined">settings</span>
                        Settings
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Area -->
    <div class="w-100 d-flex flex-column overflow-auto">

        <!-- Topbar -->
        <nav class="navbar navbar-light bg-white border-bottom px-3 px-md-4 shadow-sm">
            <div class="container-fluid p-0">
                <button class="navbar-toggler d-md-none border-0 px-1 me-2" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <span class="material-symbols-outlined text-dark">menu</span>
                </button>

                <span class="navbar-brand mb-0 h1 fw-bold fs-5 fs-md-4 text-truncate">Election Reports</span>

                <div class="d-flex align-items-center ms-auto">
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                           id="topbarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="material-symbols-outlined me-1 me-md-2 text-muted">account_circle</span>
                            <strong class="d-none d-sm-inline">
                                <?php echo htmlspecialchars(($_SESSION['firstname'] ?? '') . ' ' . ($_SESSION['lastname'] ?? '')); ?>
                            </strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="topbarUserDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 text-danger fw-bold"
                                   href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <span class="material-symbols-outlined" style="font-size: 1.2rem;">logout</span>
                                    Sign out
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Reports Content -->
        <main class="container-fluid p-3 p-md-4">

            <?php if (!$dbConnected): ?>

                <div class="d-flex flex-column justify-content-center align-items-center bg-white border rounded shadow-sm p-5 mt-4 text-center" style="min-height: 60vh;">
                    <span class="material-symbols-outlined text-danger mb-3" style="font-size: 5rem;">database_off</span>
                    <h2 class="fw-bold mb-2">Unable to connect to database</h2>
                    <p class="text-danger small mb-5 bg-light p-3 border rounded">
                        <strong>System Error:</strong> <?php echo htmlspecialchars($dbError); ?>
                    </p>
                    <button onclick="window.location.reload();" class="btn btn-lg text-white px-5 shadow"
                            style="background-color: #6c757d; border-radius: 50px;">
                        <span class="material-symbols-outlined align-middle">refresh</span> Reload Page
                    </button>
                </div>

            <?php elseif (empty($allElections)): ?>

                <div class="d-flex flex-column justify-content-center align-items-center bg-white border rounded shadow-sm p-5 mt-4 text-center" style="min-height: 60vh;">
                    <span class="material-symbols-outlined text-muted mb-3" style="font-size: 5rem;">analytics</span>
                    <h2 class="fw-bold mb-2">No Elections Found</h2>
                    <p class="text-muted">Reports will appear here once elections have been created and votes are cast.</p>
                </div>

            <?php else: ?>

                <div class="bg-white rounded-3 shadow-sm border p-3 p-md-4">

                    <!-- Header row: title + election selector + export -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div>
                            <h5 class="fw-bold text-theme mb-1">Voting Tally</h5>
                            <p class="text-muted small mb-0">Live results pulled directly from the database.</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                            <!-- Election selector — reloads page with selected election -->
                            <form method="GET" action="reports.php" class="d-flex align-items-center gap-2">
                                <label class="text-muted small fw-semibold mb-0 text-nowrap">Election:</label>
                                <select name="election_id" class="form-select form-select-sm"
                                        onchange="this.form.submit()" style="min-width: 200px;">
                                    <?php foreach ($allElections as $el): ?>
                                        <option value="<?php echo $el['id']; ?>"
                                            <?php echo ($selectedElection && $el['id'] === $selectedElection['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($el['name']); ?>
                                            (<?php echo ucfirst($el['status'] ?? 'closed'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                    </div>

                    <?php if ($selectedElection): ?>

                        <!-- Selected election info badge -->
                        <div class="alert border-0 py-2 small d-flex align-items-center gap-2 mb-4"
                             style="background-color: rgba(72, 103, 47, 0.08); color: #3a5525;">
                            <span class="material-symbols-outlined fs-5">info</span>
                            Showing results for <strong><?php echo htmlspecialchars($selectedElection['name']); ?></strong>
                            &nbsp;&mdash;&nbsp; <?php echo htmlspecialchars($selectedElection['department']); ?>
                        </div>

                    <?php endif; ?>

                    <!-- Stats cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <div class="card bg-light border-0 shadow-sm h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: .72rem;">Eligible Voters</h6>
                                        <h2 class="mb-0 fw-bold"><?php echo number_format($stats['total_voters']); ?></h2>
                                    </div>
                                    <span class="material-symbols-outlined stat-card-icon">groups</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card bg-light border-0 shadow-sm h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: .72rem;">Ballots Submitted</h6>
                                        <h2 class="mb-0 fw-bold text-success"><?php echo number_format($stats['votes_cast']); ?></h2>
                                        <?php if ($stats['total_voters'] > 0): ?>
                                            <small class="text-muted">
                                                <?php echo round(($stats['votes_cast'] / $stats['total_voters']) * 100); ?>% turnout
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <span class="material-symbols-outlined stat-card-icon">how_to_vote</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card bg-light border-0 shadow-sm h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: .72rem;">Did Not Vote</h6>
                                        <h2 class="mb-0 fw-bold text-warning"><?php echo number_format($stats['abstained']); ?></h2>
                                    </div>
                                    <span class="material-symbols-outlined stat-card-icon">do_not_disturb</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($stats['total_voters'] > 0 && $hasData): ?>
                    <!-- Participation donut chart -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-5 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size: .72rem; letter-spacing: .06em;">Voter Turnout</h6>
                                    <div style="width: 180px; height: 180px;">
                                        <canvas id="turnoutChart"></canvas>
                                    </div>
                                    <div class="d-flex gap-3 mt-3 small">
                                        <span><span class="badge me-1" style="background:#48672F;">&nbsp;</span>Voted</span>
                                        <span><span class="badge me-1 bg-warning">&nbsp;</span>No Vote</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-7 col-lg-8 d-flex align-items-center">
                            <div class="w-100">
                                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size: .72rem; letter-spacing: .06em;">Ballots by Position (Abstain Rate)</h6>
                                <canvas id="abstainChart" style="max-height: 200px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tally accordion -->
                    <div class="accordion" id="tallyAccordion">

                        <?php if (!empty($tallyPositions)): ?>
                            <?php foreach ($tallyPositions as $pos):
                                usort($pos['candidates'], function ($a, $b) { return $b['votes'] - $a['votes']; });
                                $castVotes = $pos['total'] - $pos['abstain'];
                            ?>

                            <div class="accordion-item border mb-2">
                                <h2 class="accordion-header" id="heading-<?php echo $pos['id']; ?>">
                                    <button class="accordion-button fw-semibold <?php echo $pos['open'] ? '' : 'collapsed'; ?>"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse-<?php echo $pos['id']; ?>"
                                            aria-expanded="<?php echo $pos['open'] ? 'true' : 'false'; ?>">

                                        <span class="me-auto"><?php echo htmlspecialchars($pos['title']); ?></span>

                                        <span class="d-none d-sm-flex align-items-center gap-3 me-3 fw-normal small">
                                            <span class="text-success">
                                                <span class="material-symbols-outlined align-middle" style="font-size:.9rem;">check_circle</span>
                                                <?php echo number_format($castVotes); ?> voted
                                            </span>
                                            <span class="text-warning">
                                                <span class="material-symbols-outlined align-middle" style="font-size:.9rem;">do_not_disturb</span>
                                                <?php echo number_format($pos['abstain']); ?> abstained
                                            </span>
                                        </span>

                                    </button>
                                </h2>

                                <div id="collapse-<?php echo $pos['id']; ?>"
                                     class="accordion-collapse collapse <?php echo $pos['open'] ? 'show' : ''; ?>"
                                     data-bs-parent="#tallyAccordion">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="ps-4">Candidate</th>
                                                        <th class="text-center">Votes</th>
                                                        <th class="text-center">% of Cast</th>
                                                        <th style="width: 28%;">Standing</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($pos['candidates'] as $i => $c):
                                                        $pct      = $castVotes > 0 ? round(($c['votes'] / $castVotes) * 100) : 0;
                                                        $isLeader = ($i === 0);
                                                        $gap      = $pos['candidates'][0]['votes'] - $c['votes'];
                                                    ?>
                                                    <tr>
                                                        <td class="ps-4 fw-bold">
                                                            <?php echo htmlspecialchars($c['name']); ?>
                                                            <?php if ($isLeader && $c['votes'] > 0): ?>
                                                                <span class="badge bg-success ms-1">Leading</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center fw-bold"><?php echo number_format($c['votes']); ?></td>
                                                        <td class="text-center"><?php echo $pct; ?>%</td>
                                                        <td>
                                                            <div class="progress mb-1">
                                                                <div class="progress-bar <?php echo $isLeader ? 'sidebar-bg' : 'bg-secondary'; ?>"
                                                                     style="width: <?php echo $pct; ?>%"></div>
                                                            </div>
                                                            <?php if (!$isLeader): ?>
                                                                <small class="text-muted">&ndash;<?php echo number_format($gap); ?> votes behind</small>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-warning">
                                                        <td class="ps-4 text-muted fst-italic small">Abstained</td>
                                                        <td class="text-center fw-bold small"><?php echo number_format($pos['abstain']); ?></td>
                                                        <td colspan="2"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <div class="alert alert-info border-0 shadow-sm text-center py-4">
                                <span class="material-symbols-outlined fs-1 text-muted mb-2">pending_actions</span>
                                <h6 class="fw-bold text-dark">No Tally Data Yet</h6>
                                <p class="text-muted small mb-0">
                                    <?php if ($selectedElection && $selectedElection['status'] === 'upcoming'): ?>
                                        This election has not started yet. Results will appear after voting begins.
                                    <?php else: ?>
                                        No votes have been cast for this election yet.
                                    <?php endif; ?>
                                </p>
                            </div>

                        <?php endif; ?>

                    </div>
                </div>

            <?php endif; ?>

        </main>
    </div>

    <?php include '../components/logout_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <?php if ($hasData && $stats['total_voters'] > 0): ?>
    <script>
        // Turnout donut chart
        new Chart(document.getElementById('turnoutChart'), {
            type: 'doughnut',
            data: {
                labels: ['Voted', 'Did Not Vote'],
                datasets: [{
                    data: [<?php echo $stats['votes_cast']; ?>, <?php echo $stats['abstained']; ?>],
                    backgroundColor: ['#48672F', '#ffc107'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });

        <?php if (!empty($tallyPositions)): ?>
        // Abstain rate per position (horizontal bar)
        const positionLabels = [
            <?php foreach ($tallyPositions as $p): ?>
                '<?php echo addslashes($p['title']); ?>',
            <?php endforeach; ?>
        ];
        const abstainCounts = [
            <?php foreach ($tallyPositions as $p): ?>
                <?php echo $p['abstain']; ?>,
            <?php endforeach; ?>
        ];
        const votedCounts = [
            <?php foreach ($tallyPositions as $p): ?>
                <?php echo max(0, $p['total'] - $p['abstain']); ?>,
            <?php endforeach; ?>
        ];

        new Chart(document.getElementById('abstainChart'), {
            type: 'bar',
            data: {
                labels: positionLabels,
                datasets: [
                    {
                        label: 'Voted',
                        data: votedCounts,
                        backgroundColor: '#48672F',
                        borderRadius: 4
                    },
                    {
                        label: 'Abstained',
                        data: abstainCounts,
                        backgroundColor: '#ffc107',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    x: { stacked: true, grid: { display: false } },
                    y: { stacked: true }
                }
            }
        });
        <?php endif; ?>
    </script>
    <?php endif; ?>

</body>
</html>