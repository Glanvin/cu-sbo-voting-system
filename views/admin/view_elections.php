<?php
require '../../viewmodels/admin/view_elections_viewmodel.php';
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

    <!-- Sidebar -->
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
                    <a href="../shared/dashboard.php" class="nav-link">
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
                    <a href="#" class="nav-link active" aria-current="page">
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

    <!-- Main Area -->
    <div class="w-100 d-flex flex-column overflow-auto">

        <!-- Topbar -->
        <nav class="navbar navbar-light bg-white border-bottom px-3 px-md-4 shadow-sm">
            <div class="container-fluid p-0">
                <button class="navbar-toggler d-md-none border-0 px-1 me-2" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <span class="material-symbols-outlined text-dark">menu</span>
                </button>

                <span class="navbar-brand mb-0 h1 fw-bold fs-5 fs-md-4 text-truncate">Election Management</span>

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

        <!-- Content -->
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

            <?php elseif (!$hasElections): ?>

                <div class="d-flex flex-column justify-content-center align-items-center bg-white border rounded shadow-sm p-4 p-md-5 mt-4 text-center" style="min-height: 60vh;">
                    <span class="material-symbols-outlined text-muted mb-3" style="font-size: 5rem;">how_to_vote</span>
                    <h2 class="display-6 fw-bold text-dark mb-2 fs-3 fs-md-2">No elections found</h2>
                    <p class="text-muted mb-5 fs-6 fs-md-5">No elections have been created yet. Create the first one to get started.</p>
                    <button class="btn btn-lg text-white d-flex align-items-center gap-2 px-4 px-md-5 py-2 py-md-3 shadow"
                        data-bs-toggle="modal" data-bs-target="#createElectionModal"
                        style="background-color: #48672F; border-radius: 50px;">
                        <span class="material-symbols-outlined fs-3">add_circle</span>
                        <span class="fs-5 fs-md-4">Create First Election</span>
                    </button>
                </div>

            <?php else: ?>

                <div class="bg-white rounded-3 shadow-sm border p-3 p-md-4">

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
                        <div class="btn-group w-100 w-md-auto" role="group" aria-label="Filter Elections">
                            <button type="button" class="btn btn-sm btn-outline-theme active">All</button>
                            <button type="button" class="btn btn-sm btn-outline-theme">Ongoing</button>
                            <button type="button" class="btn btn-sm btn-outline-theme">Upcoming</button>
                            <button type="button" class="btn btn-sm btn-outline-theme">Closed</button>
                        </div>

                        <button class="btn text-white d-flex align-items-center gap-2 w-100 w-md-auto justify-content-center"
                            data-bs-toggle="modal" data-bs-target="#createElectionModal"
                            style="background-color: #48672F;">
                            <span class="material-symbols-outlined">add_circle</span>
                            Create Election
                        </button>
                    </div>

                    <div class="alert alert-warning py-2 small d-flex align-items-center gap-2 border-0 mb-3" role="alert"
                        style="background-color: rgba(255, 193, 7, 0.1); color: #856404;">
                        <span class="material-symbols-outlined fs-5">warning</span>
                        Elections <strong>cannot be edited or deleted</strong> while ongoing. Close an election first before it can be removed. Closed elections cannot be edited.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Election Name</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">Opens</th>
                                    <th scope="col">Closes</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allElections as $index => $election): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>

                                        <td class="fw-bold"><?php echo htmlspecialchars($election['name']); ?></td>

                                        <td><?php echo htmlspecialchars($election['department']); ?></td>

                                        <td><?php echo date('M d, Y h:i A', strtotime($election['open_at'])); ?></td>

                                        <td><?php echo date('M d, Y h:i A', strtotime($election['close_at'])); ?></td>

                                        <td>
                                            <?php if ($election['status'] === 'ongoing'): ?>
                                                <span class="badge bg-success">Ongoing</span>
                                            <?php elseif ($election['status'] === 'upcoming'): ?>
                                                <span class="badge bg-primary">Upcoming</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Closed</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Actions 
                                         Ongoing  : Close Early button + Delete (disabled)
                                         Upcoming : Edit button + Delete (disabled)
                                         Closed   : Delete (enabled) only — no edit, no close
                                        -->
                                        <td class="text-end">

                                            <?php if ($election['status'] === 'ongoing'): ?>

                                                <!-- Close Early — stops voting immediately -->
                                                <button class="btn btn-sm btn-outline-warning me-1"
                                                    data-bs-toggle="modal" data-bs-target="#closeElectionModal"
                                                    data-id="<?php echo $election['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($election['name']); ?>">
                                                    Close Early
                                                </button>

                                                <!-- Cannot delete an active election -->
                                                <button class="btn btn-sm btn-outline-danger" disabled
                                                    title="Cannot delete an ongoing election">
                                                    Delete
                                                </button>

                                            <?php elseif ($election['status'] === 'upcoming'): ?>

                                                <!-- Edit is only allowed before the election starts -->
                                                <button class="btn btn-sm btn-outline-primary me-1"
                                                    data-bs-toggle="modal" data-bs-target="#editElectionModal"
                                                    data-id="<?php echo $election['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($election['name']); ?>"
                                                    data-department="<?php echo htmlspecialchars($election['department']); ?>"
                                                    data-open-at="<?php echo date('Y-m-d\TH:i', strtotime($election['open_at'])); ?>"
                                                    data-close-at="<?php echo date('Y-m-d\TH:i', strtotime($election['close_at'])); ?>">
                                                    Edit
                                                </button>

                                                <!-- Cannot delete until closed -->
                                                <button class="btn btn-sm btn-outline-danger" disabled
                                                    title="Cannot delete until the election is closed">
                                                    Delete
                                                </button>

                                            <?php else: ?>

                                                <!-- Closed elections can only be deleted -->
                                                <button class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal" data-bs-target="#deleteElectionModal"
                                                    data-id="<?php echo $election['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($election['name']); ?>">
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

            <?php endif; ?>

        </main>
    </div>


    <!-- Create Election Modal  -->
    <div class="modal fade" id="createElectionModal" tabindex="-1" aria-labelledby="createElectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header" style="background-color: #48672F;">
                    <h5 class="modal-title text-white fw-bold d-flex align-items-center gap-2" id="createElectionModalLabel">
                        Create New Election
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" action="view_elections.php">
                    <div class="modal-body p-4">

                        <div class="alert alert-warning py-2 small d-flex align-items-start gap-2 border-0"
                            style="background-color: rgba(255, 193, 7, 0.12); color: #856404;">
                            <span>Once created, an election <strong>cannot be deleted</strong> until it has been closed.</span>
                        </div>

                        <div class="mb-3">
                            <label for="electionName" class="form-label fw-semibold">Election Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="electionName" name="election_name"
                                placeholder="e.g. SBO Election 2026–2027" required />
                        </div>

                        <div class="mb-3">
                            <label for="electionDepartment" class="form-label fw-semibold">Department / College <span class="text-danger">*</span></label>
                            <select class="form-select" id="electionDepartment" name="department" required>
                                <option value="" disabled selected>Select a college</option>
                                <option value="University Wide">University Wide</option>
                                <option value="CCS">College of Computer Studies (CCS)</option>
                                <option value="COE">College of Engineering (COE)</option>
                                <option value="CBA">College of Business Administration (CBA)</option>
                                <option value="CAS">College of Arts and Sciences (CAS)</option>
                                <option value="CED">College of Education (CED)</option>
                                <option value="CRIM">College of Criminology (CRIM)</option>
                                <option value="COHS">College of Health and Sciences (COHS)</option>
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label for="openAt" class="form-label fw-semibold">Opening Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="openAt" name="open_at" required />
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="closeAt" class="form-label fw-semibold">Closing Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="closeAt" name="close_at" required />
                            </div>
                        </div>
                        <div class="form-text mt-1">Closing time must be after the opening time.</div>

                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="create_election" class="btn text-white" style="background-color: #48672F;">
                            Create Election
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Election Modal    -->
    <!-- Only shown for upcoming elections — populated via JS data attributes -->
    <div class="modal fade" id="editElectionModal" tabindex="-1" aria-labelledby="editElectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="editElectionModalLabel">
                        <span class="material-symbols-outlined">edit_calendar</span>
                        Edit Election
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" action="view_elections.php">
                    <input type="hidden" name="election_id" id="editElectionId" />
                    <div class="modal-body p-4">

                        <div class="mb-3">
                            <label for="editElectionName" class="form-label fw-semibold">Election Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editElectionName" name="election_name" required />
                        </div>

                        <div class="mb-3">
                            <label for="editElectionDepartment" class="form-label fw-semibold">Department / College <span class="text-danger">*</span></label>
                            <select class="form-select" id="editElectionDepartment" name="department" required>
                                <option value="University Wide">University Wide</option>
                                <option value="CCS">College of Computer Studies (CCS)</option>
                                <option value="COE">College of Engineering (COE)</option>
                                <option value="CBA">College of Business Administration (CBA)</option>
                                <option value="CAS">College of Arts and Sciences (CAS)</option>
                                <option value="CED">College of Education (CED)</option>
                                <option value="CRIM">College of Criminology (CRIM)</option>
                                <option value="COHS">College of Health and Sciences (COHS)</option>
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label for="editOpenAt" class="form-label fw-semibold">Opening Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="editOpenAt" name="open_at" required />
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="editCloseAt" class="form-label fw-semibold">Closing Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="editCloseAt" name="close_at" required />
                            </div>
                        </div>
                        <div class="form-text mt-1">Closing time must be after the opening time.</div>

                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_election" class="btn btn-primary fw-bold">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Close Early Modal  -->
    <div class="modal fade" id="closeElectionModal" tabindex="-1" aria-labelledby="closeElectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2 text-dark" id="closeElectionModalLabel">
                        Close Election Early
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" action="view_elections.php">
                    <input type="hidden" name="election_id" id="closeElectionId" />
                    <div class="modal-body p-4">
                        <p class="mb-0">
                            Are you sure you want to <strong>close</strong> the election
                            <strong id="closeElectionName" class="text-theme"></strong> early?
                        </p>
                        <p class="text-muted small mt-2 mb-0">This will immediately stop all voting and cannot be undone.</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="close_election" class="btn btn-warning fw-bold">
                            Yes, Close Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Delete Election Modal -->
    <div class="modal fade" id="deleteElectionModal" tabindex="-1" aria-labelledby="deleteElectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="deleteElectionModalLabel">
                        Delete Election
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" action="view_elections.php">
                    <input type="hidden" name="election_id" id="deleteElectionId" />
                    <div class="modal-body p-4">
                        <p class="mb-0">
                            Are you sure you want to permanently delete
                            <strong id="deleteElectionName" class="text-danger"></strong>?
                        </p>
                        <p class="text-muted small mt-2 mb-0">This action cannot be undone and all related data will be removed.</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_election" class="btn btn-danger fw-bold">
                            Yes, Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../components/notification.php'; ?>
    <?php include '../components/logout_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit Election Modal — populate fields from the row's data attributes
        const editElectionModal = document.getElementById('editElectionModal');
        editElectionModal.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            document.getElementById('editElectionId').value = btn.dataset.id;
            document.getElementById('editElectionName').value = btn.dataset.name;
            document.getElementById('editElectionDepartment').value = btn.dataset.department;
            document.getElementById('editOpenAt').value = btn.dataset.openAt;
            document.getElementById('editCloseAt').value = btn.dataset.closeAt;
        });

        // Close Early Modal — populate election ID and name  
        const closeElectionModal = document.getElementById('closeElectionModal');
        closeElectionModal.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            document.getElementById('closeElectionId').value = btn.dataset.id;
            document.getElementById('closeElectionName').textContent = btn.dataset.name;
        });

        // Delete Modal — populate election ID and name
        const deleteElectionModal = document.getElementById('deleteElectionModal');
        deleteElectionModal.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            document.getElementById('deleteElectionId').value = btn.dataset.id;
            document.getElementById('deleteElectionName').textContent = btn.dataset.name;
        });

        // Create form validation — closing time must be after opening time
        const openAt = document.getElementById('openAt');
        const closeAt = document.getElementById('closeAt');

        function validateDates(a, b) {
            if (a.value && b.value && b.value <= a.value) {
                b.setCustomValidity('Closing time must be after opening time.');
            } else {
                b.setCustomValidity('');
            }
        }
        openAt.addEventListener('change', () => validateDates(openAt, closeAt));
        closeAt.addEventListener('change', () => validateDates(openAt, closeAt));

        // ── Edit form validation — same rule  ─────────────────
        const editOpenAt = document.getElementById('editOpenAt');
        const editCloseAt = document.getElementById('editCloseAt');
        editOpenAt.addEventListener('change', () => validateDates(editOpenAt, editCloseAt));
        editCloseAt.addEventListener('change', () => validateDates(editOpenAt, editCloseAt));
    </script>
</body>

</html>