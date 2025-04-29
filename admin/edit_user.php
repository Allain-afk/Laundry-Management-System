<?php
session_start();
require_once '../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: signin.php");
    exit();
}

// Get admin data from database
$stmt = $pdo->prepare("SELECT * FROM admins WHERE admin_id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

// Check if customer ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Customer ID is required";
    header("Location: customers.php");
    exit();
}

$customer_id = $_GET['id'];

// Get customer data
$stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch();

// Check if customer exists
if (!$customer) {
    $_SESSION['error'] = "Customer not found";
    header("Location: customers.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    
    $errors = [];
    
    // Check if username already exists (excluding current customer)
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE username = ? AND customer_id != ?");
    $stmt->execute([$username, $customer_id]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Username already exists";
    }
    
    // Check if email already exists (excluding current customer)
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ? AND customer_id != ?");
    $stmt->execute([$email, $customer_id]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Email already exists";
    }
    
    if (empty($errors)) {
        try {
            // Update customer data
            if (!empty($password)) {
                // Validate password
                if (strlen($password) < 6) {
                    $_SESSION['error'] = "Password must be at least 6 characters long";
                    header("Location: edit_user.php?id=" . $customer_id);
                    exit();
                }
                
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Update with new password
                $stmt = $pdo->prepare("UPDATE customers SET 
                                      username = ?, 
                                      full_name = ?, 
                                      email = ?, 
                                      phone = ?, 
                                      address = ?, 
                                      password = ?,
                                      updated_at = NOW() 
                                      WHERE customer_id = ?");
                $stmt->execute([$username, $full_name, $email, $phone, $address, $hashed_password, $customer_id]);
            } else {
                // Update without changing password
                $stmt = $pdo->prepare("UPDATE customers SET 
                                      username = ?, 
                                      full_name = ?, 
                                      email = ?, 
                                      phone = ?, 
                                      address = ?,
                                      updated_at = NOW() 
                                      WHERE customer_id = ?");
                $stmt->execute([$username, $full_name, $email, $phone, $address, $customer_id]);
            }
            
            $_SESSION['success'] = "Customer updated successfully";
            header("Location: customers.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error updating customer: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DryMe - Edit Customer</title>
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
        .input-group-text {
            background-color: #4e73df;
            color: white;
            border: 1px solid #4e73df;
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

        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-tint me-2"></i>DryMe</h3>
                </a>
                <div class="navbar-nav w-100">
                    <a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a href="customers.php" class="nav-item nav-link active"><i class="fa fa-users me-2"></i>Customers</a>
                    <a href="orders.php" class="nav-item nav-link"><i class="fa fa-shopping-cart me-2"></i>Orders</a>
                    <a href="sales.php" class="nav-item nav-link"><i class="fa fa-money-bill-alt me-2"></i>Sales</a>
                    <a href="inventory.php" class="nav-item nav-link"><i class="fa fa-boxes me-2"></i>Inventory</a>
                    <a href="profile.php" class="nav-item nav-link"><i class="fa fa-user-circle me-2"></i>Admin Profile</a>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="<?php echo isset($admin['profile_picture']) && $admin['profile_picture'] ? 'img/profile/' . $admin['profile_picture'] : 'img/user.jpg'; ?>" alt="" style="width: 40px; height: 40px; object-fit: cover;">
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

            <!-- Edit Customer Form Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="bg-light rounded h-100 p-4 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="mb-0"><i class="fa fa-user-edit me-2"></i>Edit Customer</h6>
                                <div>
                                    <a href="view_customer.php?id=<?php echo $customer_id; ?>" class="btn btn-info me-2"><i class="fa fa-eye me-2"></i>View Details</a>
                                    <a href="customers.php" class="btn btn-secondary"><i class="fa fa-arrow-left me-2"></i>Back to Customers</a>
                                </div>
                            </div>
                            
                            <form method="POST" action="" id="edit-customer-form">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                                            <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($customer['username']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-user-tag"></i></span>
                                            <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($customer['full_name']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                            <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
                                        <textarea class="form-control" name="address" rows="3" required><?php echo htmlspecialchars($customer['address']); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                        <input type="password" class="form-control" name="password" id="password">
                                    </div>
                                    <small class="text-muted">Leave blank to keep current password. New password must be at least 6 characters long.</small>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Update Customer</button>
                                    <a href="customers.php" class="btn btn-light">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit Customer Form End -->

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
        <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo addslashes($_SESSION['success']); ?>',
            icon: 'success',
            confirmButtonColor: '#4e73df',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            title: 'Error!',
            text: '<?php echo addslashes($_SESSION['error']); ?>',
            icon: 'error',
            confirmButtonColor: '#e74a3b',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        // Form validation and submission
        document.getElementById('edit-customer-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            
            // Validate password length if provided
            if (password && password.length < 6) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Password must be at least 6 characters long',
                    icon: 'error',
                    confirmButtonColor: '#e74a3b',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Show loading state and submit form
            Swal.fire({
                title: 'Updating Customer',
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