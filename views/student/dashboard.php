<?php
require '../../viewmodels/student/view_student_viewmodel.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SBO Voting System - Elections</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="../../assets/style.css">
</head>

<body class="bg-dashboard vh-100 overflow-hidden d-flex flex-column flex-md-row">

    <div class="sidebar-bg text-white shadow offcanvas-md offcanvas-start d-flex flex-column flex-shrink-0"
         tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel" style="width: 250px;">

        <div class="offcanvas-header d-md-none border-bottom border-secondary">
            <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel">Voting System</h5>
            <button type="button" class="btn-close btn-close-white"
                    data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column flex-grow-1 p-3">
            <a href="#" class="d-none d-md-flex align-items-center me-md-auto text-white text-decoration-none px-2">
                <span class="fs-4 fw-bold">Voting System</span>
            </a>
            <hr class="d-none d-md-block">

            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link active" aria-current="page">
                        <span class="material-symbols-outlined">how_to_vote</span>
                        Elections
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

                <span class="navbar-brand mb-0 h1 fw-bold fs-5 fs-md-4 text-truncate">My Elections</span>

                <div class="d-flex align-items-center ms-auto">
                    <div class="dropdown">
                        <!-- Display the logged-in student's full name from session -->
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                           id="topbarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="material-symbols-outlined me-1 me-md-2 text-muted">account_circle</span>
                            <strong class="d-none d-sm-inline">
                                <?php echo htmlspecialchars($studentFirstname . ' ' . $studentLastname); ?>
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

        <!-- Content -->
        <main class="container-fluid p-3 p-md-4">

            <?php if (!$dbConnected): ?>

                <!-- Database connection error state -->
                <div class="d-flex flex-column justify-content-center align-items-center bg-white border rounded shadow-sm p-4 p-md-5 mt-4 text-center" style="min-height: 60vh;">
                    <span class="material-symbols-outlined text-danger mb-3" style="font-size: 5rem;">database_off</span>
                    <h2 class="display-6 fw-bold text-dark mb-2 fs-3 fs-md-2">Unable to connect to database</h2>
                    <p class="text-danger small mb-5 bg-light p-3 border rounded w-100" style="max-width: 600px; word-wrap: break-word;">
                        <strong>System Error:</strong> <?php echo htmlspecialchars($dbError); ?>
                    </p>
                    <button onclick="window.location.reload();" class="btn btn-lg text-white d-flex align-items-center gap-2 px-4 px-md-5 py-2 py-md-3 shadow" style="background-color: #6c757d; border-radius: 50px;">
                        <span class="material-symbols-outlined fs-3">refresh</span>
                        <span class="fs-5 fs-md-4">Reload Page</span>
                    </button>
                </div>

            <?php elseif (!$hasElections): ?>

                <!-- No active elections for this student's department -->
                <div class="d-flex flex-column justify-content-center align-items-center bg-white border rounded shadow-sm p-4 p-md-5 mt-4 text-center" style="min-height: 60vh;">
                    <span class="material-symbols-outlined text-muted mb-3" style="font-size: 5rem;">how_to_vote</span>
                    <h2 class="display-6 fw-bold text-dark mb-2 fs-3 fs-md-2">No Active Elections</h2>
                    <p class="text-muted mb-0 fs-6 fs-md-5">
                        There are no ongoing elections for
                        <strong><?php echo htmlspecialchars($studentDepartment); ?></strong>
                        at the moment.
                    </p>
                    <p class="text-muted small mt-1">Check back later or contact your department SBO Office</p>
                </div>

            <?php else: ?>

                <!-- Welcome header -->
                <div class="bg-white rounded-3 shadow-sm border p-3 p-md-4 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <span class="material-symbols-outlined text-theme" style="font-size: 2.5rem;">how_to_vote</span>
                        <div>
                            <h5 class="fw-bold mb-0">
                                Welcome, <?php echo htmlspecialchars($studentFirstname); ?>!
                            </h5>
                            <p class="text-muted small mb-0">
                                The following
                                <?php echo count($ongoingElections) === 1 ? 'election is' : 'elections are'; ?>
                                currently open for <strong><?php echo htmlspecialchars($studentDepartment); ?></strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Election cards -->
                <div class="row g-3">
                    <?php foreach ($ongoingElections as $election): ?>
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card border shadow-sm h-100">

                            <!-- Card header with election name -->
                            <div class="card-header d-flex align-items-center justify-content-between border-bottom py-3"
                                 style="background-color: rgba(72, 103, 47, 0.06);">
                                <span class="fw-bold text-dark text-truncate pe-2">
                                    <?php echo htmlspecialchars($election['name']); ?>
                                </span>
                                <span class="badge bg-success flex-shrink-0">Ongoing</span>
                            </div>

                            <div class="card-body">

                                <!-- Department badge -->
                                <p class="mb-3">
                                    <span class="material-symbols-outlined align-middle text-muted" style="font-size: 1rem;">school</span>
                                    <span class="text-muted small ms-1"><?php echo htmlspecialchars($election['department']); ?></span>
                                </p>

                                <!-- Open / close times -->
                                <div class="d-flex flex-column gap-1 mb-4">
                                    <div class="d-flex align-items-center gap-2 small">
                                        <span class="material-symbols-outlined text-success" style="font-size: 1rem;">lock_open</span>
                                        <span class="text-muted">Opened:</span>
                                        <span class="fw-semibold"><?php echo date('M d, Y h:i A', strtotime($election['open_at'])); ?></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 small">
                                        <span class="material-symbols-outlined text-danger" style="font-size: 1rem;">lock</span>
                                        <span class="text-muted">Closes:</span>
                                        <span class="fw-semibold"><?php echo date('M d, Y h:i A', strtotime($election['close_at'])); ?></span>
                                    </div>
                                </div>

                                <!-- Vote button — links to the ballot page for this election -->
                                <a href="../student/ballot.php?election_id=<?php echo $election['id']; ?>"
                                   class="btn w-100 text-white fw-semibold d-flex align-items-center justify-content-center gap-2"
                                   style="background-color: #48672F;">
                                    <span class="material-symbols-outlined" style="font-size: 1.1rem;">how_to_vote</span>
                                    Cast Your Vote
                                </a>

                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        </main>
    </div>

    <?php include '../components/logout_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>