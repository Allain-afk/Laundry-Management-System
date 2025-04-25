<?php
require_once 'includes/db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['text'];  // Changed from email to username
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['customer_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        
        // Set login success flag instead of immediate redirect
        $login_success = true;
    } else {
        $error_message = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DRYME - Login</title>
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
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        /* Custom SweetAlert styles to match your color scheme */
        .swal2-icon.swal2-success {
            color: rgb(0, 56, 161) !important;
            border-color: rgb(0, 56, 161) !important;
        }
        
        .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgb(0, 56, 161) !important;
        }
        
        .swal2-icon.swal2-success [class^=swal2-success-line] {
            background-color: rgb(0, 56, 161) !important;
        }
        
        .swal2-title {
            color: rgb(0, 14, 204) !important;
        }
        
        .swal2-html-container {
            color: #6c757d !important;
        }
        
        /* Enhanced Login Form Styles */
        .auth-card {
            border-radius: 15px;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            border: none;
        }
        
        .auth-card:hover {
            box-shadow: 0 0.75rem 2rem rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
        }
        
        .auth-logo {
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .form-floating > label {
            padding: 12px 15px;
        }
        
        .btn-auth {
            border-radius: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .btn-auth:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 56, 161, 0.3);
        }
        
        .btn-auth:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s;
            z-index: -1;
        }
        
        .btn-auth:hover:before {
            left: 100%;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 20px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: #4e73df;
        }
        
        .auth-divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }
        
        .auth-divider:before,
        .auth-divider:after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .auth-divider span {
            padding: 0 10px;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .auth-link {
            color: #4e73df;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .auth-link:hover {
            color: #2e59d9;
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
            border-left: 4px solid;
        }
        
        .alert-danger {
            border-left-color: #e74a3b;
            background-color: rgba(231, 74, 59, 0.1);
        }
        
        /* Animation for form elements */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 20px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        
        .form-floating, .form-check, .btn-auth, .auth-divider, .text-center {
            animation: fadeInUp 0.5s ease-out forwards;
        }
        
        .form-floating:nth-child(1) { animation-delay: 0.1s; }
        .form-floating:nth-child(2) { animation-delay: 0.2s; }
        .form-check { animation-delay: 0.3s; }
        .btn-auth { animation-delay: 0.4s; }
        .auth-divider { animation-delay: 0.5s; }
        .text-center { animation-delay: 0.6s; }
    </style>
</head>

<body>
    <div class="container-fluid position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Sign In Start -->
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                    <div class="bg-light auth-card p-4 p-sm-5 my-4 mx-3">
                        <div class="text-center mb-4">
                            <a href="index.php">
                                <h2 class="text-primary auth-logo mb-3"><i class="fa fa-tint me-2"></i>DRY ME</h2>
                            </a>
                            <p class="text-muted">Welcome back! Please sign in to continue</p>
                        </div>
                        
                        <?php if(isset($error_message)): ?>
                            <div class="alert alert-danger mb-4">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="loginForm">
                            <div class="form-floating mb-4">
                                <input type="text" class="form-control" id="floatingInput" name="text" placeholder="Username" required>
                                <label for="floatingInput"><i class="fas fa-user me-2"></i> Username</label>
                                <div class="invalid-feedback">Please enter your username</div>
                            </div>
                            
                            <div class="form-floating mb-4 position-relative">
                                <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required>
                                <label for="floatingPassword"><i class="fas fa-lock me-2"></i> Password</label>
                                <div class="password-toggle" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </div>
                                <div class="invalid-feedback">Please enter your password</div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                                    <label class="form-check-label" for="rememberMe">Remember me</label>
                                </div>
                                <a href="#" class="auth-link">Forgot Password?</a>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-auth py-3 w-100 mb-4">
                                <i class="fas fa-sign-in-alt me-2"></i>  Sign In
                            </button>
                            
                            <div class="auth-divider">
                                <span>OR</span>
                            </div>
                            
                            <p class="text-center mt-4 mb-0">Don't have an account? <a href="signup.php" class="auth-link">Sign Up</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sign In End -->
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
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Main Javascript -->
    <script src="js/main.js"></script>
    <script>
        // Hide spinner after page loads
        window.addEventListener('load', function() {
            const spinner = document.getElementById('spinner');
            if (spinner) {
                spinner.classList.remove('show');
            }
        });
        
        // Password visibility toggle
        function togglePassword() {
            const passwordInput = document.getElementById('floatingPassword');
            const toggleIcon = document.getElementById('toggleIcon');
            
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
        
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            
            if (loginForm) {
                loginForm.addEventListener('submit', function(event) {
                    let isValid = true;
                    const username = document.getElementById('floatingInput');
                    const password = document.getElementById('floatingPassword');
                    
                    // Validate username
                    if (!username.value.trim()) {
                        username.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        username.classList.remove('is-invalid');
                        username.classList.add('is-valid');
                    }
                    
                    // Validate password
                    if (!password.value.trim()) {
                        password.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        password.classList.remove('is-invalid');
                        password.classList.add('is-valid');
                    }
                    
                    if (!isValid) {
                        event.preventDefault();
                        // Shake animation for invalid form
                        loginForm.classList.add('animate__animated', 'animate__shakeX');
                        setTimeout(() => {
                            loginForm.classList.remove('animate__animated', 'animate__shakeX');
                        }, 1000);
                    } else {
                        // Show loading state
                        const submitBtn = loginForm.querySelector('button[type="submit"]');
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Signing in...';
                        submitBtn.disabled = true;
                    }
                });
                
                // Real-time validation feedback
                const inputs = loginForm.querySelectorAll('input');
                inputs.forEach(input => {
                    input.addEventListener('input', function() {
                        if (this.value.trim()) {
                            this.classList.remove('is-invalid');
                        }
                    });
                    
                    // Add floating label animation
                    input.addEventListener('focus', function() {
                        this.parentElement.classList.add('focused');
                    });
                    
                    input.addEventListener('blur', function() {
                        if (!this.value.trim()) {
                            this.parentElement.classList.remove('focused');
                        }
                    });
                });
            }
        });
        
        // Handle success animation and redirect with SweetAlert
        <?php if (isset($login_success) && $login_success): ?>
        document.addEventListener('DOMContentLoaded', function() {
            // Show SweetAlert success message
            Swal.fire({
                icon: 'success',
                title: 'Welcome Back!',
                text: 'Signed in successfully. Redirecting you to our home page...',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                window.location.href = 'index.php';
            });
        });
        <?php endif; ?>
    </script>
</body>

</html>