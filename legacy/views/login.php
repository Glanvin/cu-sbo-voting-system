<?php
require '../viewmodels/login_viewmodel.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
        body {
            /* Kept only the background image logic since Bootstrap doesn't have utilities for custom URL backgrounds */
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

                        <h3 class="mb-4">SBO Voting System</h3>

                        <form method="post">
                            <div class="mb-4">
                                <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="Email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                            </div>

                            <div class="mb-4">
                                <input type="password" name="password" id="pwd" class="form-control form-control-lg" placeholder="Password" required>
                            </div>

                            <button type="submit" name="login" id="login" class="btn btn-lg w-100 mb-3 " style="background-color: #48672F; border-color: #48672F; color: white;">Login</button>

                            <p class="small pb-lg-2">
                                <a class="text-dark" href="#">Forgot password?</a>
                            </p>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <?php if (!empty($message)): ?>
        <div class="position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 1050;">
            <div id="systemToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">

                <div class="toast-header" style="background-color: #48672F; color: white;">
                    <img src="../assets/cu-logo.png" class="rounded me-2" alt="CU" style="height: 20px;">
                    <strong class="me-auto">System Notice</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>

                <div class="toast-body text-dark bg-white">
                    <?php echo $message; ?>
                </div>

            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var toastElement = document.getElementById('systemToast');
                if (toastElement) {
                    var toast = new bootstrap.Toast(toastElement);
                    toast.show();
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>