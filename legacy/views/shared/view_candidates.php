<?php
require '../../viewmodels/shared/view_candidates_viewmodel.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SBO Voting System - Candidates</title>

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
                <?php if ($userRole === 'admin'): ?>
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
                <?php endif; ?>
                <li>
                    <a href="view_candidates.php" class="nav-link active" aria-current="page">
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

                <span class="navbar-brand mb-0 h1 fw-bold fs-5 fs-md-4 text-truncate">Candidate Management</span>

                <div class="d-flex align-items-center ms-auto gap-2">
                    <?php if ($userRole === 'moderator'): ?>
                        <span class="badge bg-warning text-dark d-none d-sm-inline">Moderator</span>
                    <?php endif; ?>
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

                <div class="d-flex flex-column justify-content-center align-items-center bg-white border rounded shadow-sm p-5 mt-4 text-center" style="min-height: 60vh;">
                    <span class="material-symbols-outlined text-danger mb-3" style="font-size: 5rem;">database_off</span>
                    <h2 class="fw-bold mb-2">Unable to connect to database</h2>
                    <p class="text-danger small mb-5 bg-light p-3 border rounded">
                        <strong>System Error:</strong> <?php echo htmlspecialchars($dbError); ?>
                    </p>
                    <button onclick="window.location.reload();" class="btn btn-lg text-white px-5 shadow" style="background-color: #6c757d; border-radius: 50px;">
                        <span class="material-symbols-outlined align-middle">refresh</span> Reload Page
                    </button>
                </div>

            <?php elseif (empty($allElections)): ?>

                <!-- No elections exist yet — candidates cannot be created without one -->
                <div class="d-flex flex-column justify-content-center align-items-center bg-white border rounded shadow-sm p-5 mt-4 text-center" style="min-height: 60vh;">
                    <span class="material-symbols-outlined text-muted mb-3" style="font-size: 5rem;">how_to_vote</span>
                    <h2 class="fw-bold mb-2">No Elections Found</h2>
                    <p class="text-muted mb-0">Candidates must be linked to an election. Please create an election first.</p>
                    <?php if ($userRole === 'admin'): ?>
                        <a href="view_elections.php" class="btn btn-lg text-white mt-4 px-5 shadow"
                            style="background-color: #48672F; border-radius: 50px;">
                            <span class="material-symbols-outlined align-middle">add_circle</span> Create Election
                        </a>
                    <?php endif; ?>
                </div>

            <?php elseif (!$hasCandidates): ?>

                <div class="d-flex flex-column justify-content-center align-items-center bg-white border rounded shadow-sm p-5 mt-4 text-center" style="min-height: 60vh;">
                    <span class="material-symbols-outlined text-muted mb-3" style="font-size: 5rem;">person_off</span>
                    <h2 class="fw-bold mb-2">No Candidates Yet</h2>
                    <p class="text-muted mb-5">No candidates have been registered. Add the first one to get started.</p>
                    <button class="btn btn-lg text-white px-5 shadow"
                        data-bs-toggle="modal" data-bs-target="#addCandidateModal"
                        style="background-color: #48672F; border-radius: 50px;">
                        <span class="material-symbols-outlined align-middle">person_add</span> Add First Candidate
                    </button>
                </div>

            <?php else: ?>

                <div class="bg-white rounded-3 shadow-sm border p-3 p-md-4">

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">

                        <!-- Election filter — JS powered, no reload -->
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <label class="text-muted small fw-semibold mb-0">Filter by Election:</label>
                            <select id="electionFilter" class="form-select form-select-sm" style="min-width: 220px;">
                                <option value="all">All Elections</option>
                                <?php foreach ($allElections as $el): ?>
                                    <option value="<?php echo $el['id']; ?>">
                                        <?php echo htmlspecialchars($el['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button class="btn text-white d-flex align-items-center gap-2 w-100 w-md-auto justify-content-center"
                            data-bs-toggle="modal" data-bs-target="#addCandidateModal"
                            style="background-color: #48672F;">
                            <span class="material-symbols-outlined">person_add</span>
                            Add Candidate
                        </button>
                    </div>

                    <?php if ($userRole === 'moderator'): ?>
                        <div class="alert alert-warning py-2 small d-flex align-items-center gap-2 border-0 mb-3"
                            style="background-color: rgba(255,193,7,0.12); color: #856404;">
                            <span class="material-symbols-outlined fs-5">info</span>
                            As a <strong>Moderator</strong>, you can add and edit candidates but cannot delete them.
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border text-nowrap" id="candidatesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Full Name</th>
                                    <th>Position</th>
                                    <th>Election</th>
                                    <th>School ID</th>
                                    <th>Email</th>
                                    <th>Contact No.</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allCandidates as $index => $candidate): ?>
                                    <tr data-election-id="<?php echo $candidate['election_id']; ?>">
                                        <td><?php echo $index + 1; ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($candidate['fullname']); ?></td>
                                        <td>
                                            <span class="badge rounded-pill text-bg-light border">
                                                <?php echo htmlspecialchars($candidate['position']); ?>
                                            </span>
                                        </td>
                                        <td class="text-muted small"><?php echo htmlspecialchars($candidate['election_name']); ?></td>
                                        <td><?php echo htmlspecialchars($candidate['school_id']); ?></td>
                                        <td><?php echo htmlspecialchars($candidate['email']); ?></td>
                                        <td><?php echo htmlspecialchars($candidate['contact_number']); ?></td>
                                        <td class="text-end">
                                            <!-- Edit — available to both admin and moderator -->
                                            <button class="btn btn-sm btn-outline-primary me-1"
                                                data-bs-toggle="modal" data-bs-target="#editCandidateModal"
                                                data-id="<?php echo $candidate['id']; ?>"
                                                data-election-id="<?php echo $candidate['election_id']; ?>"
                                                data-fullname="<?php echo htmlspecialchars($candidate['fullname']); ?>"
                                                data-position="<?php echo htmlspecialchars($candidate['position']); ?>"
                                                data-bio="<?php echo htmlspecialchars($candidate['bio']); ?>"
                                                data-school-id="<?php echo htmlspecialchars($candidate['school_id']); ?>"
                                                data-email="<?php echo htmlspecialchars($candidate['email']); ?>"
                                                data-contact="<?php echo htmlspecialchars($candidate['contact_number']); ?>">
                                                Edit
                                            </button>

                                            <!-- Delete — only rendered and clickable for admin -->
                                            <?php if ($userRole === 'admin'): ?>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal" data-bs-target="#deleteCandidateModal"
                                                    data-id="<?php echo $candidate['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($candidate['fullname']); ?>">
                                                    Delete
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-danger" disabled title="Moderators cannot delete candidates.">
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


    <!-- Add Candidate Modal -->
    <div class="modal fade" id="addCandidateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #48672F;">
                    <h5 class="modal-title fw-bold">Add New Candidate</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body p-4">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Election <span class="text-danger">*</span></label>
                            <select name="election_id" class="form-select" required>
                                <option value="" disabled selected>Select an election</option>
                                <?php foreach ($allElections as $el): ?>
                                    <option value="<?php echo $el['id']; ?>">
                                        <?php echo htmlspecialchars($el['name']); ?> — <?php echo htmlspecialchars($el['department']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" class="form-control" placeholder="e.g. Juan Dela Cruz" required>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-semibold">Position Running For <span class="text-danger">*</span></label>
                                <select name="position" class="form-select" required>
                                    <option value="" disabled selected>Select a position</option>
                                    <option value="President">President</option>
                                    <option value="Vice President">Vice President</option>
                                    <option value="Secretary">Secretary</option>
                                    <option value="Treasurer">Treasurer</option>
                                    <option value="Auditor">Auditor</option>
                                    <option value="Public Information Officer">Public Information Officer</option>
                                    <option value="Representative">Representative</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-semibold">School ID <span class="text-danger">*</span></label>
                                <input type="text" name="school_id" class="form-control" placeholder="209873" required>
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="@cu.edu.ph" required>
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="contact_number" class="form-control" placeholder="09XX XXX XXXX" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bio / Platform <span class="text-danger">*</span></label>
                            <textarea name="bio" class="form-control" rows="3"
                                placeholder="Brief description of the candidate's platform or background..." required></textarea>
                        </div>

                    </div>
                    <div class="modal-footer bg-light border-0 rounded-bottom">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_candidate" class="btn text-white" style="background-color: #48672F;">
                            Save Candidate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Candidate Modal -->
    <div class="modal fade" id="editCandidateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Edit Candidate</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="candidate_id" id="edit_candidate_id">
                    <div class="modal-body p-4">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Election <span class="text-danger">*</span></label>
                            <select name="election_id" id="edit_election_id" class="form-select" required>
                                <?php foreach ($allElections as $el): ?>
                                    <option value="<?php echo $el['id']; ?>">
                                        <?php echo htmlspecialchars($el['name']); ?> — <?php echo htmlspecialchars($el['department']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" id="edit_fullname" class="form-control" required>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-semibold">Position <span class="text-danger">*</span></label>
                                <select name="position" id="edit_position" class="form-select" required>
                                    <option value="" disabled>Select a position</option>
                                    <option value="President">President</option>
                                    <option value="Vice President">Vice President</option>
                                    <option value="Secretary">Secretary</option>
                                    <option value="Treasurer">Treasurer</option>
                                    <option value="Auditor">Auditor</option>
                                    <option value="Public Information Officer">Public Information Officer</option>
                                    <option value="Representative">Representative</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-semibold">School ID <span class="text-danger">*</span></label>
                                <input type="text" name="school_id" id="edit_school_id" class="form-control" required>
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="edit_email" class="form-control" required>
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="contact_number" id="edit_contact" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bio / Platform <span class="text-danger">*</span></label>
                            <textarea name="bio" id="edit_bio" class="form-control" rows="3" required></textarea>
                        </div>

                    </div>
                    <div class="modal-footer bg-light border-0 rounded-bottom">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_candidate" class="btn btn-primary">Update Candidate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Delete Candidate Modal — only reachable by admin -->
    <div class="modal fade" id="deleteCandidateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-body text-center pt-4 pb-3">
                        <span class="material-symbols-outlined text-danger mb-2" style="font-size: 3rem;">warning</span>
                        <h5 class="fw-bold mb-3">Delete Candidate?</h5>
                        <p class="text-muted small mb-0">
                            Are you sure you want to permanently delete
                            <strong id="delete_candidate_name"></strong>?
                            This action cannot be undone.
                        </p>
                        <input type="hidden" name="candidate_id" id="delete_candidate_id">
                    </div>
                    <div class="modal-footer border-0 d-flex justify-content-center bg-light rounded-bottom">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_candidate" class="btn btn-danger px-4">Yes, Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../components/logout_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit modal — populate fields from button data attributes
        const editModal = document.getElementById('editCandidateModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', (e) => {
                const btn = e.relatedTarget;
                document.getElementById('edit_candidate_id').value = btn.dataset.id;
                document.getElementById('edit_election_id').value = btn.dataset.electionId;
                document.getElementById('edit_fullname').value = btn.dataset.fullname;
                document.getElementById('edit_position').value = btn.dataset.position;
                document.getElementById('edit_bio').value = btn.dataset.bio;
                document.getElementById('edit_school_id').value = btn.dataset.schoolId;
                document.getElementById('edit_email').value = btn.dataset.email;
                document.getElementById('edit_contact').value = btn.dataset.contact;
            });
        }

        // Delete modal — populate candidate ID and name
        const deleteModal = document.getElementById('deleteCandidateModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', (e) => {
                const btn = e.relatedTarget;
                document.getElementById('delete_candidate_id').textContent = btn.dataset.id;
                document.getElementById('delete_candidate_id').value = btn.dataset.id;
                document.getElementById('delete_candidate_name').textContent = btn.dataset.name;
            });
        }

        // Election filter — hide/show rows without a page reload
        document.getElementById('electionFilter').addEventListener('change', function() {
            const selected = this.value;
            document.querySelectorAll('#candidatesTable tbody tr').forEach(row => {
                if (selected === 'all' || row.dataset.electionId === selected) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>

</body>

</html>