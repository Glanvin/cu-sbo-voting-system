<?php
require '../../viewmodels/student/view_ballot_viewmodel.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SBO Voting System - Ballot</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="../../assets/style.css">

    <style>
        /* Candidate radio card — tappable selection with visual feedback */
        .candidate-card {
            border: 2px solid #dee2e6;
            border-radius: 0.6rem;
            padding: 0.85rem 1rem;
            cursor: pointer;
            transition: border-color 0.18s, background-color 0.18s;
        }
        .candidate-card:hover {
            border-color: #48672F;
            background-color: rgba(72, 103, 47, 0.04);
        }
        .candidate-card.selected {
            border-color: #48672F;
            background-color: rgba(72, 103, 47, 0.08);
        }
        .candidate-card.abstain-card.selected {
            border-color: #6c757d;
            background-color: rgba(108, 117, 125, 0.08);
        }
        .candidate-card input[type="radio"] {
            accent-color: #48672F;
        }
        .position-counter {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.75);
        }
    </style>
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
                    <a href="dashboard.php" class="nav-link">
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

                <span class="navbar-brand mb-0 h1 fw-bold fs-5 fs-md-4 text-truncate">
                    <?php echo htmlspecialchars($election['name']); ?>
                </span>

                <div class="d-flex align-items-center ms-auto">
                    <div class="dropdown">
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

        <!-- Ballot Content -->
        <main class="container-fluid p-3 p-md-4">

            <?php if (empty($candidatesByPosition)): ?>

                <!-- Election has no candidates registered yet -->
                <div class="d-flex flex-column justify-content-center align-items-center bg-white border rounded shadow-sm p-5 mt-4 text-center" style="min-height: 60vh;">
                    <span class="material-symbols-outlined text-muted mb-3" style="font-size: 5rem;">person_off</span>
                    <h2 class="fw-bold mb-2">No Candidates Registered</h2>
                    <p class="text-muted mb-4">
                        Candidates have not been added to this election yet. Please check back later.
                    </p>
                    <a href="dashboard.php" class="btn btn-lg text-white px-5"
                       style="background-color: #48672F; border-radius: 50px;">
                        <span class="material-symbols-outlined align-middle">arrow_back</span> Back to Elections
                    </a>
                </div>

            <?php else: ?>

                <!-- Instruction banner -->
                <div class="alert border-0 mb-4 d-flex align-items-start gap-2 py-3"
                     style="background-color: rgba(72, 103, 47, 0.08); color: #3a5525;">
                    <span class="material-symbols-outlined mt-1" style="font-size: 1.2rem;">info</span>
                    <div>
                        <strong>Voting Instructions:</strong> Select exactly <strong>one candidate</strong> per position.
                        If you do not wish to vote for a position, choose <strong>Abstain</strong>.
                        Your ballot is <strong>final</strong> once submitted and cannot be changed.
                    </div>
                </div>

                <form method="POST" id="ballotForm">
                    <input type="hidden" name="submit_ballot" value="1">

                    <?php
                    $positionIndex = 0;
                    $totalPositions = count($candidatesByPosition);
                    foreach ($candidatesByPosition as $position => $candidates):
                        $positionIndex++;
                        $fieldName = 'vote[' . htmlspecialchars($position) . ']';
                    ?>

                    <!-- Position card -->
                    <div class="bg-white border rounded-3 shadow-sm mb-4 overflow-hidden">

                        <!-- Position header -->
                        <div class="px-4 py-3 d-flex justify-content-between align-items-center"
                             style="background-color: #48672F;">
                            <div>
                                <h5 class="text-white fw-bold mb-0"><?php echo htmlspecialchars($position); ?></h5>
                                <span class="position-counter"><?php echo $positionIndex; ?> of <?php echo $totalPositions; ?></span>
                            </div>
                            <span class="material-symbols-outlined text-white opacity-75" style="font-size: 2rem;">how_to_vote</span>
                        </div>

                        <div class="p-3 p-md-4">
                            <div class="row g-3">

                                <?php foreach ($candidates as $candidate): ?>
                                <div class="col-12 col-md-6 col-xl-4">
                                    <label class="candidate-card d-flex gap-3 w-100 h-100">
                                        <input type="radio"
                                               name="<?php echo $fieldName; ?>"
                                               value="<?php echo $candidate['id']; ?>"
                                               class="flex-shrink-0 mt-1"
                                               required>
                                        <div class="overflow-hidden">
                                            <div class="fw-bold text-dark text-truncate">
                                                <?php echo htmlspecialchars($candidate['fullname']); ?>
                                            </div>
                                            <div class="text-muted small mb-1"><?php echo htmlspecialchars($candidate['school_id']); ?></div>
                                            <?php if (!empty($candidate['bio'])): ?>
                                                <p class="text-muted small mb-0"
                                                   style="display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;">
                                                    <?php echo htmlspecialchars($candidate['bio']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                </div>
                                <?php endforeach; ?>

                                <!-- Abstain option — always last -->
                                <div class="col-12 col-md-6 col-xl-4">
                                    <label class="candidate-card abstain-card d-flex gap-3 w-100 h-100 align-items-center">
                                        <input type="radio"
                                               name="<?php echo $fieldName; ?>"
                                               value="abstain"
                                               class="flex-shrink-0"
                                               style="accent-color: #6c757d;">
                                        <div>
                                            <div class="fw-bold text-muted">Abstain</div>
                                            <div class="small text-muted">I choose not to vote for this position.</div>
                                        </div>
                                    </label>
                                </div>

                            </div>
                        </div>
                    </div>

                    <?php endforeach; ?>

                    <!-- Submit section -->
                    <div class="bg-white border rounded-3 shadow-sm p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <h6 class="fw-bold mb-1">Ready to submit your ballot?</h6>
                            <p class="text-muted small mb-0">Make sure all positions have a selection before submitting. This action is irreversible.</p>
                        </div>
                        <button type="button" class="btn btn-lg text-white px-5 flex-shrink-0"
                                style="background-color: #48672F;"
                                data-bs-toggle="modal" data-bs-target="#confirmSubmitModal">
                            <span class="material-symbols-outlined align-middle me-1">how_to_vote</span>
                            Submit Ballot
                        </button>
                    </div>

                </form>

            <?php endif; ?>

        </main>
    </div>


    <!-- Confirm Submit Modal -->
    <div class="modal fade" id="confirmSubmitModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Confirm Ballot Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <span class="material-symbols-outlined mb-3" style="font-size: 3.5rem; color: #48672F;">how_to_vote</span>
                    <p class="mb-1">You are about to submit your vote for</p>
                    <p class="fw-bold fs-5 mb-3"><?php echo htmlspecialchars($election['name']); ?></p>
                    <p class="text-muted small">Once submitted, your ballot <strong>cannot be changed or recalled</strong>.</p>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-center bg-light rounded-bottom">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Go Back</button>
                    <button type="button" class="btn text-white px-4 fw-bold"
                            style="background-color: #48672F;"
                            onclick="document.getElementById('ballotForm').submit();">
                        Yes, Submit My Vote
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/logout_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add .selected class to the card wrapping the chosen radio for visual feedback
        document.querySelectorAll('.candidate-card input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', () => {
                // Remove selected from all cards in the same radio group
                document.querySelectorAll(`input[name="${CSS.escape(radio.name)}"]`).forEach(r => {
                    r.closest('.candidate-card').classList.remove('selected');
                });
                radio.closest('.candidate-card').classList.add('selected');
            });
        });
    </script>

</body>
</html>