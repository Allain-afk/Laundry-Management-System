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

// Get customer data
$customer_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch();

// Check if customer exists
if (!$customer) {
    $_SESSION['error'] = "Customer not found";
    header("Location: customers.php");
    exit();
}

// Get customer orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DryMe - Customer Details</title>
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

            <!-- Customer Details Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="bg-light rounded h-100 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="mb-0">Customer Details</h6>
                                <a href="customers.php" class="btn btn-secondary"><i class="fa fa-arrow-left me-2"></i>Back to Customers</a>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fa fa-user me-2"></i>Personal Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3 row">
                                                <label class="col-sm-4 col-form-label fw-bold">Customer ID:</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($customer['customer_id']); ?></p>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label class="col-sm-4 col-form-label fw-bold">Full Name:</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($customer['full_name']); ?></p>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label class="col-sm-4 col-form-label fw-bold">Username:</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($customer['username']); ?></p>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label class="col-sm-4 col-form-label fw-bold">Email:</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($customer['email']); ?></p>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label class="col-sm-4 col-form-label fw-bold">Phone:</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($customer['phone']); ?></p>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label class="col-sm-4 col-form-label fw-bold">Address:</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($customer['address']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fa fa-info-circle me-2"></i>Account Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3 row">
                                                <label class="col-sm-4 col-form-label fw-bold">Role:</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($customer['role']); ?></p>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label class="col-sm-4 col-form-label fw-bold">Created At:</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext"><?php echo date('F d, Y h:i A', strtotime($customer['created_at'])); ?></p>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label class="col-sm-4 col-form-label fw-bold">Updated At:</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext"><?php echo date('F d, Y h:i A', strtotime($customer['updated_at'])); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="edit_user.php?id=<?php echo $customer['customer_id']; ?>" class="btn btn-primary"><i class="fa fa-edit me-2"></i>Edit Customer</a>
                                        <button type="button" class="btn btn-danger" id="deleteCustomerBtn"><i class="fa fa-trash me-2"></i>Delete Customer</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Customer Orders -->
                            <div class="mt-4">
                                <h6 class="mb-3">Customer Orders</h6>
                                <?php if (count($orders) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Date</th>
                                                <th>Services</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                <td>
                                                    <?php 
                                                    // Get order services
                                                    $stmt = $pdo->prepare("SELECT s.service_name FROM order_details od 
                                                                          JOIN services s ON od.service_id = s.service_id 
                                                                          WHERE od.order_id = ?");
                                                    $stmt->execute([$order['order_id']]);
                                                    $services = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                                    echo implode(", ", array_map('htmlspecialchars', $services));
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        switch($order['status']) {
                                                            case 'pending': echo 'warning'; break;
                                                            case 'processing': echo 'info'; break;
                                                            case 'completed': echo 'success'; break;
                                                            case 'cancelled': echo 'danger'; break;
                                                            default: echo 'secondary';
                                                        }
                                                    ?>">
                                                        <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                                                    </span>
                                                </td>
                                                <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info view-order-btn" data-id="<?php echo $order['order_id']; ?>"><i class="fa fa-eye"></i></button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle me-2"></i> This customer has no orders yet.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Customer Details End -->

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
        // Delete customer confirmation
        document.getElementById('deleteCustomerBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this customer. This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_user.php?id=<?php echo $customer_id; ?>';
                }
            });
        });
        
        // View order details
        document.querySelectorAll('.view-order-btn').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                
                // Show loading state
                Swal.fire({
                    title: 'Loading order details...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Fetch order details via AJAX
                fetch(`helpers/get_order_details.php?order_id=${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.error
                            });
                            return;
                        }
                        
                        // Create services HTML
                        let servicesHtml = '<div class="table-responsive"><table class="table table-sm table-bordered">';
                        servicesHtml += '<thead><tr><th>Service</th><th>Quantity</th><th>Price</th></tr></thead><tbody>';
                        
                        // Add regular services (without prices)
                        data.services.forEach(service => {
                            servicesHtml += `<tr><td>${service.service_name}</td><td>N/A</td><td>N/A</td></tr>`;
                        });
                        
                        // Add additional services (with prices)
                        data.additional_services.forEach(service => {
                            servicesHtml += `<tr><td>${service.name}</td><td>${service.quantity_formatted || 'N/A'}</td><td>₱${service.price_formatted}</td></tr>`;
                        });
                        
                        servicesHtml += '</tbody></table></div>';
                        
                        // Create order details HTML
                        let detailsHtml = `
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Order ID:</strong> ${data.order.order_id}</p>
                                    <p><strong>Customer:</strong> ${data.customer.full_name}</p>
                                    <p><strong>Date:</strong> ${new Date(data.order.created_at).toLocaleString()}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(data.order.status)}">${capitalizeFirstLetter(data.order.status)}</span></p>
                                    <p><strong>Priority:</strong> ${capitalizeFirstLetter(data.order.priority)}</p>
                                    <p><strong>Total Amount:</strong> ₱${data.order.total_amount_formatted}</p>
                                </div>
                            </div>
                            <h6 class="mb-3">Services</h6>
                            ${servicesHtml}
                        `;
                        
                        // Show order details in SweetAlert2
                        Swal.fire({
                            title: 'Order Details',
                            html: detailsHtml,
                            width: '800px',
                            confirmButtonText: 'Close',
                            confirmButtonColor: '#3085d6'
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching order details:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load order details. Please try again.'
                        });
                    });
            });
        });
        
        // Helper functions
        function getStatusColor(status) {
            switch(status) {
                case 'pending': return 'warning';
                case 'processing': return 'info';
                case 'completed': return 'success';
                case 'cancelled': return 'danger';
                default: return 'secondary';
            }
        }
        
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    </script>
</body>

</html>