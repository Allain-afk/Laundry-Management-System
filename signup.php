<?php
require_once 'includes/db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match. Please try again.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO customers (username, email, password, full_name, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $password, $full_name, $phone, $address]);

            // Set success flag instead of immediate redirect
            $registration_success = true;
        } catch (PDOException $e) {
            $error_message = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DRYME</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        .success-animation {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .success-message {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
        }
        
        .redirect-message {
            font-size: 16px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <!-- Success Animation Container -->
    <?php if (isset($registration_success) && $registration_success): ?>
    <div id="successAnimation" class="success-animation">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="success-message">Registration Successful!</div>
        <div class="redirect-message">Redirecting to login page...</div>
    </div>
    <?php endif; ?>

    <div class="container-fluid position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Sign Up Start -->
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="bg-light rounded p-4 p-sm-5 my-4 mx-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <a href="index.html" class="">
                                <h3 class="text-primary"></i>DRY ME</h3>
                            </a>
                            <h3>Sign Up</h3>
                        </div>
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingText" name="username" placeholder="Username" required>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingName" name="full_name" placeholder="Full Name" required>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="floatingInput" name="email" placeholder="Email Address" required>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="tel" class="form-control" id="floatingPhone" name="phone" placeholder="Phone Number">
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingAddress" name="address" placeholder="Address">
                            </div>
                            
                            <div class="position-relative mb-4">
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required>
                                </div>
                                <div style="position: absolute; right: 10px; top: 7px; cursor: pointer;" onclick="togglePassword('floatingPassword', 'toggleIcon')">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </div>
                            </div>
                            <div class="position-relative mb-4">
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="floatingConfirmPassword" name="confirm_password" placeholder="Confirm Password" required onkeyup="checkPasswordMatch()">
                                </div>
                                <div style="position: absolute; right: 10px; top: 7px; cursor: pointer;" onclick="togglePassword('floatingConfirmPassword', 'toggleIconConfirm')">
                                    <i class="fas fa-eye" id="toggleIconConfirm"></i>
                                </div>
                                <div id="password-match-feedback" class="invalid-feedback" style="display: none;">
                                    Passwords do not match
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary py-3 w-100 mb-4">Sign Up</button>
                            <p class="text-center mb-0">Already have an Account? <a href="login.php">Sign In</a></p>
                        </form>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sign Up End -->
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        function checkPasswordMatch() {
            const password = document.getElementById('floatingPassword').value;
            const confirmPassword = document.getElementById('floatingConfirmPassword').value;
            const confirmPasswordInput = document.getElementById('floatingConfirmPassword');
            const feedbackElement = document.getElementById('password-match-feedback');
            
            if (confirmPassword === '') {
                // If confirm password is empty, don't show error yet
                confirmPasswordInput.classList.remove('is-invalid');
                feedbackElement.style.display = 'none';
            } else if (password !== confirmPassword) {
                // Passwords don't match - show error
                confirmPasswordInput.classList.add('is-invalid');
                feedbackElement.style.display = 'block';
            } else {
                // Passwords match - remove error
                confirmPasswordInput.classList.remove('is-invalid');
                feedbackElement.style.display = 'none';
            }
        }

        // Add form validation for password matching
        document.querySelector('form').addEventListener('submit', function(event) {
            const password = document.getElementById('floatingPassword').value;
            const confirmPassword = document.getElementById('floatingConfirmPassword').value;
            
            if (password !== confirmPassword) {
                event.preventDefault();
                document.getElementById('floatingConfirmPassword').classList.add('is-invalid');
                document.getElementById('password-match-feedback').style.display = 'block';
            }
        });

        // Handle success animation and redirect
        <?php if (isset($registration_success) && $registration_success): ?>
        document.addEventListener('DOMContentLoaded', function() {
            // Show success animation
            const successAnimation = document.getElementById('successAnimation');
            successAnimation.style.display = 'flex';
            
            // Fade in animation
            setTimeout(function() {
                successAnimation.style.opacity = '1';
            }, 100);
            
            // Redirect after delay
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 3000); // 3 seconds delay before redirect
        });
        <?php endif; ?>
    </script>
</body>

</html>