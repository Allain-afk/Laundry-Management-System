<?php require_once 'helpers/profile_handler.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin Profile - DryMe</title>
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

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        .profile-card {
            transition: all 0.3s ease;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15);
        }
        .profile-image {
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }
        .profile-image:hover {
            transform: scale(1.05);
        }
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        .btn-update {
            transition: all 0.3s ease;
        }
        .btn-update:hover {
            transform: translateY(-2px);
        }
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

        <!-- Add this script to hide spinner after page loads -->
        <script>
            window.addEventListener('load', function() {
                var spinner = document.getElementById('spinner');
                spinner.classList.remove('show');
            });
        </script>


        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-tint me-2"></i>DryMe</h3>
                </a>
                <div class="navbar-nav w-100">
                    <a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a href="customers.php" class="nav-item nav-link"><i class="fa fa-users me-2"></i>Customers</a>
                    <a href="orders.php" class="nav-item nav-link"><i class="fa fa-shopping-cart me-2"></i>Orders</a>
                    <a href="sales.php" class="nav-item nav-link"><i class="fa fa-money-bill-alt me-2"></i>Sales</a>
                    <a href="inventory.php" class="nav-item nav-link"><i class="fa fa-boxes me-2"></i>Inventory</a>
                    <a href="profile.php" class="nav-item nav-link active"><i class="fa fa-user-circle me-2"></i>Admin Profile</a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="index.php" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-tint"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <form class="d-none d-md-flex ms-4">
                    <input class="form-control border-0" type="search" placeholder="Search">
                </form>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="<?php echo isset($admin['profile_picture']) ? 'img/profile/' . $admin['profile_picture'] : 'img/user.jpg'; ?>" alt="" style="width: 40px; height: 40px; object-fit: cover;">
                            <span class="d-none d-lg-inline-flex"><?php echo htmlspecialchars($admin['full_name']); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="profile.php" class="dropdown-item">My Profile</a>
                            <a href="helpers/logout.php" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->


            <!-- Blank Start -->
            <?php        
            // Check if admin is logged in
            if (!isset($_SESSION['admin_id'])) {
                header("Location: signin.php");
                exit();
            }
            
            // Get admin data from database
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE admin_id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch();
            
            // Handle profile updates
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['update_profile'])) {
                    $fullname = $_POST['fullname'];
                    $email = $_POST['email'];
                    $username = $_POST['username'];
                    
                    $stmt = $pdo->prepare("UPDATE admins SET full_name = ?, email = ?, username = ? WHERE admin_id = ?");
                    $stmt->execute([$fullname, $email, $username, $_SESSION['admin_id']]);
                    
                    $_SESSION['success_msg'] = "Profile updated successfully!";
                    header("Location: profile.php");
                    exit();
                }
                
                if (isset($_POST['change_password'])) {
                    $current_password = $_POST['current_password'];
                    $new_password = $_POST['new_password'];
                    $confirm_password = $_POST['confirm_password'];
                    
                    if (password_verify($current_password, $admin['password'])) {
                        if ($new_password === $confirm_password) {
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE admin_id = ?");
                            $stmt->execute([$hashed_password, $_SESSION['admin_id']]);
                            
                            $_SESSION['success_msg'] = "Password changed successfully!";
                        } else {
                            $_SESSION['error_msg'] = "New passwords do not match!";
                        }
                    } else {
                        $_SESSION['error_msg'] = "Current password is incorrect!";
                    }
                    header("Location: profile.php");
                    exit();
                }
                
                if (isset($_FILES['profile_picture'])) {
                    $file = $_FILES['profile_picture'];
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                    
                    if (in_array($file['type'], $allowed_types)) {
                        $filename = 'admin_' . $_SESSION['admin_id'] . '_' . time() . '.jpg';
                        move_uploaded_file($file['tmp_name'], 'img/profile/' . $filename);
                        
                        $stmt = $pdo->prepare("UPDATE admins SET profile_picture = ? WHERE admin_id = ?");
                        $stmt->execute([$filename, $_SESSION['admin_id']]);
                        
                        // Update the session variable for the profile picture
                        $_SESSION['admin_profile_picture'] = $filename;
                        
                        $_SESSION['success_msg'] = "Profile picture updated successfully!";
                    } else {
                        $_SESSION['error_msg'] = "Invalid file type! Please upload JPG, PNG or GIF.";
                    }
                    header("Location: profile.php");
                    exit();
                }
            }
            ?>
            <!-- Replace the Blank Start section with this -->
            <!-- Profile Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-4">
                        <div class="bg-light rounded p-4 profile-card">
                            <div class="text-center">
                                <img class="mb-4 profile-image" src="<?php echo isset($admin['profile_picture']) ? 'img/profile/' . $admin['profile_picture'] : 'img/user.jpg'; ?>" alt="" style="width: 200px; height: 200px; object-fit: cover;">
                                <h4><?php echo htmlspecialchars($admin['full_name']); ?></h4>
                                <p class="text-muted mb-4"><?php echo htmlspecialchars($admin['role']); ?></p>
                                <form action="" method="POST" enctype="multipart/form-data" id="profile-picture-form">
                                    <div class="mb-3">
                                        <input class="form-control" type="file" name="profile_picture" accept="image/*" id="profile-picture-input">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-update"><i class="fa fa-camera me-2"></i>Update Picture</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-8">
                        <div class="bg-light rounded p-4 profile-card">
                            <h5 class="mb-4"><i class="fa fa-user-edit me-2"></i>Profile Information</h5>
                            <form action="" method="POST" id="profile-form">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                                            <input type="text" class="form-control" name="fullname" value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-user-tag"></i></span>
                                            <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                    </div>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary btn-update"><i class="fa fa-save me-2"></i>Update Profile</button>
                            </form>
                            
                            <hr class="my-4">
                            
                            <h5 class="mb-4"><i class="fa fa-lock me-2"></i>Change Password</h5>
                            <form action="" method="POST" id="password-form">
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-key"></i></span>
                                        <input type="password" class="form-control" name="current_password" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">New Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                            <input type="password" class="form-control" name="new_password" id="new_password" required>
                                        </div>
                                        <small class="text-muted">Password must be at least 6 characters long</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirm New Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-primary btn-update"><i class="fa fa-key me-2"></i>Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Profile End -->
            

            <!-- Footer Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">DryMe</a>, All Right Reserved. 
                        </div>

                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        // Display SweetAlert notifications for session messages
        <?php if (isset($_SESSION['success_msg'])): ?>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo addslashes($_SESSION['success_msg']); ?>',
            icon: 'success',
            confirmButtonColor: '#4e73df',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_msg'])): ?>
        Swal.fire({
            title: 'Error!',
            text: '<?php echo addslashes($_SESSION['error_msg']); ?>',
            icon: 'error',
            confirmButtonColor: '#e74a3b',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>
        
        // Profile form validation and submission
        document.getElementById('profile-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Updating Profile',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    this.submit();
                }
            });
        });
        
        // Password form validation and submission
        document.getElementById('password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Validate password length
            if (newPassword.length < 6) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Password must be at least 6 characters long',
                    icon: 'error',
                    confirmButtonColor: '#e74a3b',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Validate password match
            if (newPassword !== confirmPassword) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Passwords do not match',
                    icon: 'error',
                    confirmButtonColor: '#e74a3b',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            Swal.fire({
                title: 'Changing Password',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    this.submit();
                }
            });
        });
        
        // Profile picture form validation and submission
        document.getElementById('profile-picture-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById('profile-picture-input');
            if (fileInput.files.length === 0) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please select an image file',
                    icon: 'error',
                    confirmButtonColor: '#e74a3b',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(fileInput.files[0].type)) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Invalid file type! Please upload JPG, PNG or GIF',
                    icon: 'error',
                    confirmButtonColor: '#e74a3b',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            Swal.fire({
                title: 'Updating Profile Picture',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    this.submit();
                }
            });
        });
    </script>
</body>

</html>