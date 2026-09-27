<?php require '../../viewmodels/student/view_receipt_viewmodel.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vote Receipt — <?php echo htmlspecialchars($election['name']); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="../../assets/style.css">

    <style>
        /* Receipt card — the element captured by html2canvas */
        #receipt-card {
            background: #fff;
            font-family: 'Segoe UI', system-ui, sans-serif;
            max-width: 560px;
        }

        .receipt-header {
            background-color: #48672F;
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .receipt-header .system-title {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            margin-bottom: 0.1rem;
        }

        .receipt-header .election-name {
            font-size: 0.85rem;
            opacity: 0.85;
        }

        .receipt-stamp {
            display: inline-block;
            border: 3px solid #28a745;
            color: #28a745;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            padding: 3px 10px;
            transform: rotate(-4deg);
            opacity: 0.85;
        }

        .receipt-meta {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .vote-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 1rem;
            padding: 0.55rem 0;
            border-bottom: 1px dashed #dee2e6;
            font-size: 0.875rem;
        }

        .vote-row:last-child {
            border-bottom: none;
        }

        .vote-position {
            color: #6c757d;
            font-weight: 600;
            flex-shrink: 0;
        }

        .vote-choice {
            font-weight: 700;
            text-align: right;
        }

        .vote-choice.abstain {
            color: #6c757d;
            font-style: italic;
            font-weight: 400;
        }

        .receipt-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
            font-size: 0.75rem;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>

<body class="bg-dashboard min-vh-100 d-flex flex-column">

    <!-- Topbar -->
    <nav class="navbar navbar-light bg-white border-bottom px-3 px-md-4 shadow-sm">
        <div class="container-fluid p-0">
            <a href="dashboard.php" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                <span class="material-symbols-outlined" style="font-size: 1rem;">arrow_back</span>
                Back to Elections
            </a>
            <span class="navbar-brand mb-0 h1 fw-bold fs-5 ms-3">Vote Receipt</span>
            <div class="ms-auto">
                <button class="btn btn-sm text-white d-flex align-items-center gap-1"
                    style="background-color: #48672F;"
                    onclick="downloadReceipt()">
                    <span class="material-symbols-outlined" style="font-size: 1rem;">download</span>
                    Download as Image
                </button>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 d-flex flex-column align-items-center justify-content-start py-4 px-3">

        <?php if ($isFresh): ?>
            <!-- Confirmation banner shown only on fresh submissions -->
            <div class="alert border-0 mb-4 d-flex align-items-center gap-2 w-100" style="max-width: 560px; background-color: rgba(72, 103, 47, 0.1); color: #3a5525;">
                <span class="material-symbols-outlined">check_circle</span>
                <span><strong>Your vote has been recorded!</strong> Keep this receipt for your reference.</span>
            </div>
        <?php endif; ?>

        <!--  RECEIPT CARD (captured as image)  -->
        <div id="receipt-card" class="shadow-lg rounded-3 overflow-hidden w-100" style="max-width: 560px;">

            <!-- Header -->
            <div class="receipt-header">
                <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                    <span class="material-symbols-outlined" style="font-size: 2rem; opacity: 0.9;">how_to_vote</span>
                </div>
                <div class="system-title">SBO Voting System</div>
                <div class="election-name"><?php echo htmlspecialchars($election['name']); ?></div>
            </div>

            <!-- Body -->
            <div class="p-4">

                <!-- Verified stamp + summary line -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="fw-bold fs-6 mb-1">Official Ballot Receipt</div>
                        <div class="receipt-meta">
                            Voted: <strong><?php echo $votedCount; ?></strong> position<?php echo $votedCount !== 1 ? 's' : ''; ?> &nbsp;|&nbsp;
                            Abstained: <strong><?php echo $abstainCount; ?></strong>
                        </div>
                    </div>
                    <div class="receipt-stamp">SUBMITTED</div>
                </div>

                <hr class="my-3">

                <!-- Voter details -->
                <div class="receipt-meta mb-3">
                    <div class="row g-1">
                        <div class="col-5 text-muted">Voter Name</div>
                        <div class="col-7 fw-semibold text-dark">
                            <?php echo htmlspecialchars($studentFirstname . ' ' . $studentLastname); ?>
                        </div>
                        <div class="col-5 text-muted">School ID</div>
                        <div class="col-7 fw-semibold text-dark"><?php echo htmlspecialchars($schoolId); ?></div>
                        <div class="col-5 text-muted">Department</div>
                        <div class="col-7 fw-semibold text-dark"><?php echo htmlspecialchars($studentDept); ?></div>
                        <div class="col-5 text-muted">Submitted On</div>
                        <div class="col-7 fw-semibold text-dark"><?php echo $submittedAt; ?></div>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Vote breakdown per position -->
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleChoicesBtn" onclick="toggleChoices()">
                        <span class="material-symbols-outlined align-middle" style="font-size: 1.1rem;">visibility</span>
                        Show My Choices
                    </button>
                </div>

                <div id="choicesContainer" class="d-none">
                    <div class="fw-semibold small text-uppercase text-muted mb-2" style="letter-spacing: 0.06em;">
                        Your Choices
                    </div>

                    <div>
                        <?php if (empty($voterVotes)): ?>
                            <p class="text-muted small">No vote records found.</p>
                        <?php else: ?>
                            <?php foreach ($voterVotes as $v): ?>
                                <div class="vote-row">
                                    <span class="vote-position"><?php echo htmlspecialchars($v['position']); ?></span>
                                    <?php if ($v['candidate_id'] === null): ?>
                                        <span class="vote-choice abstain">Abstained</span>
                                    <?php else: ?>
                                        <span class="vote-choice text-dark"><?php echo htmlspecialchars($v['candidate_name']); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="receipt-footer">
                This receipt confirms your participation in the
                <?php echo htmlspecialchars($election['name']); ?> election.
                Keep this document for your personal records.
                &nbsp;·&nbsp; Generated by SBO Voting System
            </div>

        </div>
        <!--  END RECEIPT CARD  -->

        <!-- Download button below card for convenience -->
        <div class="mt-4 d-flex gap-3 flex-wrap justify-content-center">
            <button class="btn btn-lg text-white px-5"
                style="background-color: #48672F; border-radius: 50px;"
                onclick="downloadReceipt()">
                <span class="material-symbols-outlined align-middle me-1">download</span>
                Download Receipt (PNG)
            </button>
            <a href="dashboard.php" class="btn btn-lg btn-outline-secondary px-5"
                style="border-radius: 50px;">
                Back to Elections
            </a>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- html2canvas for receipt-to-image export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        async function downloadReceipt() {
            const el = document.getElementById('receipt-card');
            const btn = document.querySelectorAll('[onclick="downloadReceipt()"]');

            // Temporarily hide buttons inside the card if any (none in this design)
            btn.forEach(b => b.disabled = true);

            try {
                const canvas = await html2canvas(el, {
                    scale: 2, // 2× resolution for sharp PNG
                    useCORS: true,
                    backgroundColor: '#ffffff'
                });

                const link = document.createElement('a');
                const voter = '<?php echo addslashes(trim($studentFirstname . '_' . $studentLastname)); ?>';
                const election = '<?php echo addslashes(preg_replace('/[^a-zA-Z0-9]/', '_', $election['name'])); ?>';
                link.download = `receipt_${voter}_${election}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            } catch (err) {
                alert('Could not generate image. Please try screenshotting the receipt manually.');
                console.error(err);
            }

            btn.forEach(b => b.disabled = false);
        }

        function toggleChoices() {
            const container = document.getElementById('choicesContainer');
            const btn = document.getElementById('toggleChoicesBtn');

            if (container.classList.contains('d-none')) {
                // Show choices
                container.classList.remove('d-none');
                btn.innerHTML = '<span class="material-symbols-outlined align-middle" style="font-size: 1.1rem;">visibility_off</span> Hide My Choices';
            } else {
                // Hide choices
                container.classList.add('d-none');
                btn.innerHTML = '<span class="material-symbols-outlined align-middle" style="font-size: 1.1rem;">visibility</span> Show My Choices';
            }
        }
    </script>

</body>

</html>