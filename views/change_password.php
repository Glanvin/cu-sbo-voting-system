<?php
include '../viewmodels/change_password_viewmodel.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
        body {
            /* Kept your exact background styling */
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0), white 100%), url("/assets/cu-logo.png");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>

<body class="vh-100 d-flex justify-content-center align-items-center">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">

                <div class="card shadow-lg" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">

                        <div class="d-flex justify-content-center mb-3" style="gap: 5px;">
                            <img src="../assets/cu-logo.png" style="height: 1in;" alt="Logo">
                            <img src="../assets/ccs-logo.png" style="height: 1in;" alt="CCS Logo">
                        </div>

                        <h3 class="mb-2">Secure Account</h3>
                        <p class="text-muted small mb-4">Please change your default password to continue to the SBO Voting System.</p>

                        <?php if (!empty($message)): ?>
                            <div class="alert alert-warning" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-4">
                                <input type="password" name="new_password" id="new_password" class="form-control form-control-lg" placeholder="New Password" required>
                            </div>

                            <div class="mb-4">
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control form-control-lg" placeholder="Confirm New Password" required>
                            </div>

                            <button type="submit" name="change_password" id="change_password" class="btn btn-lg w-100 mt-2" style="background-color: #48672F; border-color: #48672F; color: white;">Update Password</button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>