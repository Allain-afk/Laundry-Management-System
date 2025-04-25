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
    <title>DRYME - Sign Up</title>
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
    
    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        /* Enhanced Auth Form Styles */
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
        
        /* Password strength indicator */
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .strength-weak {
            background-color: #e74a3b;
            width: 30%;
        }
        
        .strength-medium {
            background-color: #f6c23e;
            width: 60%;
        }
        
        .strength-strong {
            background-color: #1cc88a;
            width: 100%;
        }
        
        /* Form section styles */
        .form-section {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .form-section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #4e73df;
            margin-bottom: 15px;
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
        .form-floating:nth-child(2) { animation-delay: 0.15s; }
        .form-floating:nth-child(3) { animation-delay: 0.2s; }
        .form-floating:nth-child(4) { animation-delay: 0.25s; }
        .form-floating:nth-child(5) { animation-delay: 0.3s; }
        .form-floating:nth-child(6) { animation-delay: 0.35s; }
        .form-floating:nth-child(7) { animation-delay: 0.4s; }
        .btn-auth { animation-delay: 0.45s; }
        .auth-divider { animation-delay: 0.5s; }
        .text-center { animation-delay: 0.55s; }
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

        <!-- Sign Up Start -->
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                    <div class="bg-light auth-card p-4 p-sm-5 my-4 mx-3">
                        <div class="text-center mb-4">
                            <a href="index.php">
                                <h2 class="text-primary auth-logo mb-3"><i class="fa fa-tint me-2"></i>DRY ME</h2>
                            </a>
                            <p class="text-muted">Create your account to get started</p>
                        </div>
                        
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger mb-4">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="signupForm">
                            <!-- Account Information Section -->
                            <div class="form-section">
                                <h5 class="form-section-title"><i class="fas fa-user-circle me-2"></i> Account Information</h5>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingText" name="username" placeholder="Username" required>
                                    <label for="floatingText"><i class="fas fa-user me-2"></i> Username</label>
                                    <div class="invalid-feedback">Please choose a username</div>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="floatingInput" name="email" placeholder="Email Address" required>
                                    <label for="floatingInput"><i class="fas fa-envelope me-2"></i> Email Address</label>
                                    <div class="invalid-feedback">Please enter a valid email address</div>
                                </div>
                            </div>
                            
                            <!-- Personal Information Section -->
                            <div class="form-section">
                                <h5 class="form-section-title"><i class="fas fa-id-card me-2"></i> Personal Information</h5>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingName" name="full_name" placeholder="Full Name" required>
                                    <label for="floatingName"><i class="fas fa-user-tag me-2"></i> Full Name</label>
                                    <div class="invalid-feedback">Please enter your full name</div>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="tel" class="form-control" id="floatingPhone" name="phone" placeholder="Phone Number">
                                    <label for="floatingPhone"><i class="fas fa-phone me-2"></i> Phone Number</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingAddress" name="address" placeholder="Address">
                                    <label for="floatingAddress"><i class="fas fa-map-marker-alt me-2"></i> Address</label>
                                </div>
                            </div>
                            
                            <!-- Security Section -->
                            <div class="form-section">
                                <h5 class="form-section-title"><i class="fas fa-shield-alt me-2"></i> Security</h5>
                                <div class="form-floating mb-3 position-relative">
                                    <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required onkeyup="checkPasswordStrength()">
                                    <label for="floatingPassword"><i class="fas fa-lock me-2"></i> Password</label>
                                    <div class="password-toggle" onclick="togglePassword('floatingPassword', 'toggleIcon')">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </div>
                                    <div class="invalid-feedback">Password must be at least 6 characters</div>
                                    <div class="password-strength" id="passwordStrength"></div>
                                    <small class="text-muted" id="passwordFeedback">Password must be at least 6 characters</small>
                                </div>
                                <div class="form-floating mb-4 position-relative">
                                    <input type="password" class="form-control" id="floatingConfirmPassword" name="confirm_password" placeholder="Confirm Password" required onkeyup="checkPasswordMatch()">
                                    <label for="floatingConfirmPassword"><i class="fas fa-lock me-2"></i> Confirm Password</label>
                                    <div class="password-toggle" onclick="togglePassword('floatingConfirmPassword', 'toggleIconConfirm')">
                                        <i class="fas fa-eye" id="toggleIconConfirm"></i>
                                    </div>
                                    <div class="invalid-feedback" id="password-match-feedback">Passwords do not match</div>
                                </div>
                            </div>
                            
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                <label class="form-check-label" for="termsCheck">
                                    I agree to the <a href="#" class="auth-link">Terms of Service</a> and <a href="#" class="auth-link">Privacy Policy</a>
                                </label>
                                <div class="invalid-feedback">You must agree before submitting</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-auth py-3 w-100 mb-4">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                            
                            <div class="auth-divider">
                                <span>OR</span>
                            </div>
                            
                            <p class="text-center mt-4 mb-0">Already have an account? <a href="login.php" class="auth-link">Sign In</a></p>
                        </form>
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
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Template Javascript -->
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
        
        // Check password strength
        function checkPasswordStrength() {
            const password = document.getElementById('floatingPassword').value;
            const strengthBar = document.getElementById('passwordStrength');
            const feedback = document.getElementById('passwordFeedback');
            
            // Remove all classes
            strengthBar.className = 'password-strength';
            
            // Check password strength
            if (password.length === 0) {
                strengthBar.style.width = '0';
                feedback.textContent = 'Password must be at least 6 characters';
                return;
            }
            
            let strength = 0;
            
            // Length check
            if (password.length >= 6) strength += 1;
            if (password.length >= 10) strength += 1;
            
            // Character type checks
            if (/[A-Z]/.test(password)) strength += 1; // Has uppercase
            if (/[a-z]/.test(password)) strength += 1; // Has lowercase
            if (/[0-9]/.test(password)) strength += 1; // Has number
            if (/[^A-Za-z0-9]/.test(password)) strength += 1; // Has special char
            
            // Update UI based on strength
            if (password.length < 6) {
                strengthBar.classList.add('strength-weak');
                feedback.textContent = 'Password is too short';
            } else if (strength < 3) {
                strengthBar.classList.add('strength-weak');
                feedback.textContent = 'Password is weak';
            } else if (strength < 5) {
                strengthBar.classList.add('strength-medium');
                feedback.textContent = 'Password is medium strength';
            } else {
                strengthBar.classList.add('strength-strong');
                feedback.textContent = 'Password is strong';
            }
            
            // Also check password match if confirm password has a value
            if (document.getElementById('floatingConfirmPassword').value) {
                checkPasswordMatch();
            }
        }

        // Check if passwords match
        function checkPasswordMatch() {
            const password = document.getElementById('floatingPassword').value;
            const confirmPassword = document.getElementById('floatingConfirmPassword').value;
            const confirmPasswordInput = document.getElementById('floatingConfirmPassword');
            
            if (confirmPassword === '') {
                // If confirm password is empty, don't show error yet
                confirmPasswordInput.classList.remove('is-invalid');
            } else if (password !== confirmPassword) {
                // Passwords don't match - show error
                confirmPasswordInput.classList.add('is-invalid');
            } else {
                // Passwords match - show success
                confirmPasswordInput.classList.remove('is-invalid');
                confirmPasswordInput.classList.add('is-valid');
            }
        }

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const signupForm = document.getElementById('signupForm');
            
            if (signupForm) {
                // Form submission validation
                signupForm.addEventListener('submit', function(event) {
                    let isValid = true;
                    const requiredInputs = signupForm.querySelectorAll('input[required]');
                    
                    // Check all required fields
                    requiredInputs.forEach(input => {
                        if (!input.value.trim()) {
                            input.classList.add('is-invalid');
                            isValid = false;
                        }
                    });
                    
                    // Check password length
                    const password = document.getElementById('floatingPassword');
                    if (password.value.length < 6) {
                        password.classList.add('is-invalid');
                        isValid = false;
                    }
                    
                    // Check password match
                    const confirmPassword = document.getElementById('floatingConfirmPassword');
                    if (password.value !== confirmPassword.value) {
                        confirmPassword.classList.add('is-invalid');
                        isValid = false;
                    }
                    
                    // Check terms agreement
                    const termsCheck = document.getElementById('termsCheck');
                    if (!termsCheck.checked) {
                        termsCheck.classList.add('is-invalid');
                        isValid = false;
                    }
                    
                    if (!isValid) {
                        event.preventDefault();
                        // Shake animation for invalid form
                        signupForm.classList.add('animate__animated', 'animate__shakeX');
                        setTimeout(() => {
                            signupForm.classList.remove('animate__animated', 'animate__shakeX');
                        }, 1000);
                        
                        // Scroll to first error
                        const firstError = signupForm.querySelector('.is-invalid');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstError.focus();
                        }
                    } else {
                        // Show loading state
                        const submitBtn = signupForm.querySelector('button[type="submit"]');
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Creating account...';
                        submitBtn.disabled = true;
                    }
                });
                
                // Real-time validation feedback
                const inputs = signupForm.querySelectorAll('input');
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
                
                // Terms checkbox validation
                const termsCheck = document.getElementById('termsCheck');
                termsCheck.addEventListener('change', function() {
                    if (this.checked) {
                        this.classList.remove('is-invalid');
                    }
                });
            }
        });

        // Handle success animation and redirect with SweetAlert
        <?php if (isset($registration_success) && $registration_success): ?>
        document.addEventListener('DOMContentLoaded', function() {
            // Show SweetAlert success message
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful!',
                text: 'Your account has been created. Redirecting to login page...',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                window.location.href = 'login.php';
            });
        });
        <?php endif; ?>
    </script>
</body>

</html>