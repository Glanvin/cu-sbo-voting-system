<?php
require '../../viewmodels/admin/view_users_viewmodel.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SBO Voting System - Manage Users</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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

        .btn-outline-theme {
            color: #48672F;
            border-color: #48672F;
        }

        .btn-outline-theme:hover,
        .btn-outline-theme.active {
            background-color: #48672F;
            color: white;
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
                    <a href="../shared/dashboard.php" class="nav-link">
                        <span class="material-symbols-outlined">Home</span>
                        Home
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link active" aria-current="page">
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
                    <a href="../shared/reports.php" class="nav-link">
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

                <span class="navbar-brand mb-0 h1 fw-bold fs-5 fs-md-4 text-truncate">User Management</span>

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

            <?php if (!empty($actionMessage)): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <?php echo htmlspecialchars($actionMessage); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!$dbConnected): ?>

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

            <?php elseif (!$hasUsers): ?>

                <div class="d-flex flex-column justify-content-center align-items-center bg-white border rounded shadow-sm p-4 p-md-5 mt-4 text-center" style="min-height: 60vh;">
                    <span class="material-symbols-outlined text-muted mb-3" style="font-size: 5rem;">group_off</span>
                    <h2 class="display-6 fw-bold text-dark mb-2 fs-3 fs-md-2">No users found</h2>
                    <p class="text-muted mb-5 fs-6 fs-md-5">The database is connected, but there are currently no users registered in the system.</p>

                    <button class="btn btn-lg text-white d-flex align-items-center gap-2 px-4 px-md-5 py-2 py-md-3 shadow" data-bs-toggle="modal" data-bs-target="#addUserModal" style="background-color: #48672F; border-radius: 50px;">
                        <span class="material-symbols-outlined fs-3">person_add</span>
                        <span class="fs-5 fs-md-4">Add First User</span>
                    </button>
                </div>

            <?php else: ?>

                <div class="bg-white rounded-3 shadow-sm border p-3 p-md-4">

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">

                        <div class="btn-group w-100 w-md-auto" role="group" aria-label="Filter Users">
                            <button type="button" class="btn btn-sm btn-md btn-outline-theme active">All</button>
                            <button type="button" class="btn btn-sm btn-md btn-outline-theme">Admin</button>
                            <button type="button" class="btn btn-sm btn-md btn-outline-theme">Moderator</button>
                            <button type="button" class="btn btn-sm btn-md btn-outline-theme">Student</button>
                        </div>

                        <button class="btn text-white d-flex align-items-center gap-2 w-100 w-md-auto justify-content-center" data-bs-toggle="modal" data-bs-target="#addUserModal" style="background-color: #48672F;">
                            <span class="material-symbols-outlined">person_add</span>
                            Add User
                        </button>
                    </div>

                    <div class="alert alert-info py-2 small d-flex align-items-center gap-2 border-0" style="background-color: rgba(13, 202, 240, 0.1); color: #087990;" role="alert">
                        <span class="material-symbols-outlined fs-5">info</span>
                        For system integrity, your currently active account is excluded from this list.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Account Status</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allUsers as $index => $user): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>

                                        <td class="fw-bold"><?php echo htmlspecialchars($user['email']); ?></td>

                                        <td>
                                            <?php if ($user['role'] == 'admin'): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php elseif ($user['role'] == 'moderator'): ?>
                                                <span class="badge bg-warning text-dark">Moderator</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark">Student</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if ($user['is_online'] == 1): ?>
                                                <span class="badge bg-success">Online</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Offline</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary me-1"
                                                data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                data-id="<?php echo $user['id']; ?>"
                                                data-school-id="<?php echo htmlspecialchars($user['school_id']); ?>"
                                                data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                data-role="<?php echo htmlspecialchars($user['role']); ?>">
                                                Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                                                data-id="<?php echo $user['id']; ?>"
                                                data-email="<?php echo htmlspecialchars($user['email']); ?>">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <?php include '../components/actions_modal.php'; ?>
    <?php include '../components/logout_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>