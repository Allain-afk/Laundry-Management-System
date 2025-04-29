<?php
require_once 'includes/db_connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    // Check if password is being updated
    $password_update = "";
    $params = [$full_name, $email, $phone, $address];
    
    if (!empty($_POST['password']) && !empty($_POST['confirm_password'])) {
        if ($_POST['password'] === $_POST['confirm_password']) {
            $password_update = ", password = ?";
            $params[] = $_POST['password'];
        } else {
            $error_message = "Passwords do not match. Please try again.";
        }
    }
    
    if (!isset($error_message)) {
        try {
            $params[] = $user_id;
            $stmt = $pdo->prepare("UPDATE customers SET full_name = ?, email = ?, phone = ?, address = ?" . $password_update . " WHERE customer_id = ?");
            $stmt->execute($params);
            
            // Set success flag
            $update_success = true;
            
            // Update session data
            $_SESSION['email'] = $email;
        } catch (PDOException $e) {
            $error_message = "Update failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DRYME - Edit Profile</title>
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
    
    <!-- Custom Styles -->
    <style>
        /* SweetAlert custom styles to match your color scheme */
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
        
        .profile-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .section-header {
            color: rgb(0, 56, 161);
            border-bottom: 2px solid rgb(0, 56, 161);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .btn-primary {
            background-color: rgb(0, 56, 161);
            border-color: rgb(0, 56, 161);
        }
        
        .btn-primary:hover {
            background-color: rgb(0, 14, 204);
            border-color: rgb(0, 14, 204);
        }
        
        /* Additional styles for independent page */
        .navbar {
            background-color: #194376;
            padding: 15px 0;
        }
        
        .navbar-brand {
            color: #fff;
            font-size: 24px;
            font-weight: bold;
        }
        
        .navbar-brand:hover {
            color: #fff;
        }
        
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .navbar-nav .nav-link:hover {
            color: #fff;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <!-- Remove the old success animation container -->

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">DRYME</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($user['username']); ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="orders.php"><i class="fas fa-list me-2"></i> My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h3 class="section-header mb-4"><i class="fas fa-user-edit me-2"></i> Edit Profile</h3>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <!-- Account Information Section -->
                        <div class="profile-section">
                            <h5 class="mb-3"><i class="fas fa-id-card me-2"></i> Account Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                    <small class="text-muted">Username cannot be changed</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                        
                        <!-- Contact Information Section -->
                        <div class="profile-section">
                            <h5 class="mb-3"><i class="fas fa-phone-alt me-2"></i> Contact Information</h5>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                            </div>
                        </div>
                        
                        <!-- Security Section -->
                        <div class="profile-section">
                            <h5 class="mb-3"><i class="fas fa-lock me-2"></i> Security</h5>
                            <p class="text-muted mb-3">Leave password fields blank if you don't want to change your password</p>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'toggleIcon')">
                                            <i class="fas fa-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" onkeyup="checkPasswordMatch()">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password', 'toggleIconConfirm')">
                                            <i class="fas fa-eye" id="toggleIconConfirm"></i>
                                        </button>
                                    </div>
                                    <div id="password-match-feedback" class="invalid-feedback">
                                        Passwords do not match
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="profile.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Go Back  
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> DRYME. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const confirmPasswordInput = document.getElementById('confirm_password');
            const feedbackElement = document.getElementById('password-match-feedback');
            
            if (confirmPassword === '' || password === '') {
                // If either password field is empty, don't show error
                confirmPasswordInput.classList.remove('is-invalid');
            } else if (password !== confirmPassword) {
                // Passwords don't match - show error
                confirmPasswordInput.classList.add('is-invalid');
            } else {
                // Passwords match - remove error
                confirmPasswordInput.classList.remove('is-invalid');
            }
        }
        
        // Handle form validation
        document.querySelector('form').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== '' && confirmPassword !== '' && password !== confirmPassword) {
                event.preventDefault();
                document.getElementById('confirm_password').classList.add('is-invalid');
            }
        });
        
        // Handle success animation with SweetAlert
        <?php if (isset($update_success) && $update_success): ?>
        document.addEventListener('DOMContentLoaded', function() {
            // Show SweetAlert success message
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated!',
                text: 'Your profile has been successfully updated.',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        });
        <?php endif; ?>
    </script>
</body>

</html>