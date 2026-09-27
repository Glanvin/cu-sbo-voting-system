<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SBO Voting System - Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="../../assets/style.css">

    <!-- <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar-bg {
            background-color: #48672F !important;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .stat-card-icon {
            font-size: 3rem;
            color: rgba(72, 103, 47, 0.2);
        }
    </style> -->
</head>

<body class="vh-100 overflow-hidden d-flex flex-column flex-md-row">

    <div class="sidebar-bg text-white shadow offcanvas-md offcanvas-start d-flex flex-column flex-shrink-0" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel" style="width: 250px;">

        <div class="offcanvas-header d-md-none border-bottom border-secondary">
            <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel">Voting System</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column flex-grow-1 p-3 p-md-3">
            <a href="#" class="d-none d-md-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none px-2">
                <span class="fs-4 fw-bold">Voting System</span>
            </a>
            <hr class="d-none d-md-block">

            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="#" class="nav-link active" aria-current="page">
                        <span class="material-symbols-outlined">Home</span>
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
                    <a href="reports.php" class="nav-link">
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

    <div class="w-100 d-flex flex-column overflow-auto">

        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-3 px-md-4 shadow-sm">
            <div class="container-fluid p-0">

                <button class="navbar-toggler d-md-none border-0 px-1 me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <span class="material-symbols-outlined text-dark">menu</span>
                </button>

                <span class="navbar-brand mb-0 h1 fw-bold fs-5 fs-md-4 text-truncate">Dashboard Overview</span>

                <div class="d-flex align-items-center ms-auto">
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="topbarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="material-symbols-outlined me-1 me-md-2 text-muted">account_circle</span>
                            <strong class="d-none d-sm-inline">Admin User</strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="topbarUserDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 text-danger fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <span class="material-symbols-outlined" style="font-size: 1.2rem;">logout</span> Sign out
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <main class="container-fluid p-3 p-md-4">

            <div class="p-4 p-md-5 mb-4 bg-white rounded-3 shadow-sm border">
                <div class="container-fluid py-2 px-0">
                    <h1 class="display-6 fw-bold text-dark fs-3 fs-md-1">Welcome to the Voting system Dashboard</h1>
                    <p class="col-md-8 fs-6 fs-md-5 text-muted">Here is a quick overview of the voting system's current status including voter registrations and ongoing electoral events.</p>
                </div>

                <div class="row g-3 g-md-4 mt-2">
                    <div class="col-12 col-md-4">
                        <div class="card bg-light border-0 shadow-sm h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted text-uppercase fw-bold mb-1 fs-7">Registered Voters</h6>
                                    <h2 class="mb-0 fw-bold">150</h2>
                                </div>
                                <span class="material-symbols-outlined stat-card-icon">groups</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="card bg-light border-0 shadow-sm h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted text-uppercase fw-bold mb-1 fs-7">Current Election</h6>
                                    <h4 class="mb-0 fw-bold text-success fs-5">SBO 2026-2027</h4>
                                    <small class="text-muted">Status: Ongoing</small>
                                </div>
                                <span class="material-symbols-outlined stat-card-icon">how_to_vote</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="card bg-light border-0 shadow-sm h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted text-uppercase fw-bold mb-1 fs-7">Previous Elections</h6>
                                    <h2 class="mb-0 fw-bold">4</h2>
                                    <a href="#" class="text-decoration-none small" style="color: #48672F;">View Details &rarr;</a>
                                </div>
                                <span class="material-symbols-outlined stat-card-icon">history</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include '../components/logout_modal.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>