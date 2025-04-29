<?php
session_start();
require_once 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DRYME - My Profile</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <!-- Custom Styles for Orders Table -->
    <style>
        /* Enhanced Table Styles */
        .order-table-container {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }
        
        #ordersTable {
            margin-bottom: 0;
        }
        
        .thead-primary {
            background-color: #0038A1;
            color: white;
        }
        
        .thead-primary th {
            border: none;
            padding: 15px 10px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        #ordersTable tbody tr {
            transition: all 0.3s ease;
        }
        
        #ordersTable tbody tr:hover {
            background-color: rgba(0, 56, 161, 0.05);
            transform: translateY(-2px);
        }
        
        #ordersTable td {
            vertical-align: middle;
            padding: 15px 10px;
            border-color: #f0f0f0;
        }
        
        /* Status Badge Enhancements */
        .badge-pill {
            font-weight: 500;
            font-size: 0.8rem;
            padding: 8px 12px;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 30px;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #d1d1d1;
            margin-bottom: 15px;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 767.98px) {
            #ordersTable td, #ordersTable th {
                padding: 10px 5px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <!-- Topbar Start -->
    <div class="container-fluid bg-primary py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-lg-left mb-2 mb-lg-0">
                    <div class="d-inline-flex align-items-center">
                        <a class="text-white pr-3" href="">FAQs</a>
                        <span class="text-white">|</span>
                        <a class="text-white px-3" href="">Help</a>
                        <span class="text-white">|</span>
                        <a class="text-white pl-3" href="">Support</a>
                    </div>
                </div>
                <div class="col-md-6 text-center text-lg-right">
                    <div class="d-inline-flex align-items-center">
                        <a class="text-white px-3" href="">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a class="text-white px-3" href="">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a class="text-white px-3" href="">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a class="text-white px-3" href="">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a class="text-white pl-3" href="">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <div class="container-fluid position-relative nav-bar p-0">
        <div class="container-lg position-relative p-0 px-lg-3" style="z-index: 9;">
            <nav class="navbar navbar-expand-lg bg-white navbar-light py-3 py-lg-0 pl-3 pl-lg-5">
                <a href="" class="navbar-brand">
                    <h1 class="m-0 text-secondary"><span class="text-primary">DRY</span>ME</h1>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                    <div class="navbar-nav ml-auto py-0">
                        <a href="index.php" class="nav-item nav-link">Home</a>
                        <a href="about.php" class="nav-item nav-link">About</a>
                        <a href="service.php" class="nav-item nav-link">Services</a>
                        <a href="pricing.php" class="nav-item nav-link">Pricing</a>
                        <a href="contact.php" class="nav-item nav-link">Contact</a>

                        <!-- Add User Profile Dropdown -->
                        <?php
                        if (isset($_SESSION['user_id'])) { ?>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle active" data-toggle="dropdown">
                                    <i class="fa fa-user"></i> <?php echo $_SESSION['username']; ?>
                                </a>
                                <div class="dropdown-menu border-0 rounded-0 m-0">
                                    <div class="px-3 py-2">
                                        <p class="mb-1"><strong>Username:</strong> <?php echo $_SESSION['username']; ?></p>
                                        <p class="mb-1"><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>
                                        <?php if (isset($_SESSION['full_name'])): ?>
                                            <p class="mb-1"><strong>Full Name:</strong> <?php echo $_SESSION['full_name']; ?></p>
                                        <?php endif; ?>
                                        <?php if (isset($_SESSION['phone'])): ?>
                                            <p class="mb-1"><strong>Phone:</strong> <?php echo $_SESSION['phone']; ?></p>
                                        <?php endif; ?>
                                        <?php if (isset($_SESSION['address'])): ?>
                                            <p class="mb-1"><strong>Address:</strong> <?php echo $_SESSION['address']; ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a href="profile.php" class="dropdown-item active">My Profile</a>
                                    <a href="helpers/logout.php" class="dropdown-item">Logout</a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <a href="login.php" class="nav-item nav-link">Login</a>
                        <?php } ?>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->


    <!-- Page Header Start -->
    <div class="page-header container-fluid bg-secondary pt-2 pt-lg-5 pb-2 mb-5">
        <div class="container py-5">
            <div class="row align-items-center py-4">
                <div class="col-md-6 text-center text-md-left">
                    <h1 class="mb-4 mb-md-0 text-white">My Profile</h1>
                </div>
                <div class="col-md-6 text-center text-md-right">
                    <div class="d-inline-flex align-items-center">
                        <a class="btn text-white" href="index.php">Home</a>
                        <i class="fas fa-angle-right text-white"></i>
                        <a class="btn text-white disabled" href="">My Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->


    <!-- Profile Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="bg-light p-4 rounded shadow-sm">
                        <h3 class="mb-4">Personal Information</h3>
                        <div class="text-center mb-4">
                            <!-- Replace image with Font Awesome icon -->
                            <div class="mb-3">
                                <i class="fa fa-user-circle text-primary" style="font-size: 80px;"></i>
                            </div>
                            <h5 class="mb-0">
                                <?php 
                                $fullName = '';
                                if (isset($user['first_name']) && isset($user['last_name'])) {
                                    $fullName = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
                                } elseif (isset($user['username'])) {
                                    $fullName = htmlspecialchars($user['username']);
                                } else {
                                    $fullName = 'User';
                                }
                                echo $fullName;
                                ?>
                            </h5>
                            <p class="text-muted">
                                <?php echo isset($user['username']) ? htmlspecialchars($user['username']) : ''; ?>
                            </p>
                        </div>
                        <div class="mb-3">
                            <i class="fa fa-envelope text-primary mr-2"></i>
                            <?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>
                        </div>
                        <div class="mb-3">
                            <i class="fa fa-phone text-primary mr-2"></i>
                            <?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>
                        </div>
                        <div class="mb-4">
                            <i class="fa fa-map-marker-alt text-primary mr-2"></i>
                            <?php echo isset($user['address']) ? htmlspecialchars($user['address']) : ''; ?>
                        </div>
                        <a href="edit_profile.php" class="btn btn-primary btn-block">
                            <i class="fa fa-edit mr-2"></i> Edit Profile
                        </a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="bg-light p-4 rounded shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="mb-0">My Orders</h3>
                            <a href="place_order.php" class="btn btn-primary">
                                <i class="fa fa-plus mr-2"></i> New Order
                            </a>
                        </div>
                        
                        <?php if (count($orders) > 0): ?>
                            <div class="table-responsive order-table-container">
                                <table class="table table-hover" id="ordersTable">
                                    <thead class="thead-primary">
                                        <tr>
                                            <th>ORDER ID</th>
                                            <th>DATE</th>
                                            <th>STATUS</th>
                                            <th>AMOUNT PAID</th>
                                            <th>ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td>#<?php echo $order['order_id']; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                <td>
                                                    <?php
                                                    $status_class = '';
                                                    switch ($order['status']) {
                                                        case 'pending':
                                                            $status_class = 'warning';
                                                            break;
                                                        case 'processing':
                                                            $status_class = 'info';
                                                            break;
                                                        case 'completed':
                                                            $status_class = 'success';
                                                            break;
                                                        case 'cancelled':
                                                            $status_class = 'danger';
                                                            break;
                                                        default:
                                                            $status_class = 'secondary';
                                                    }
                                                    ?>
                                                    <span class="badge badge-<?php echo $status_class; ?> badge-pill">
                                                        <?php echo ucfirst($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info view-order-btn" data-order-id="<?php echo $order['order_id']; ?>">
                                                        <i class="fa fa-eye"></i> View
                                                    </button>
                                                    <?php if ($order['status'] == 'completed'): ?>
                                                    <button class="btn btn-sm btn-primary reorder-btn" data-order-id="<?php echo $order['order_id']; ?>">
                                                        <i class="fa fa-redo"></i> Reorder
                                                    </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Order Details Modal -->
                            <div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" id="orderDetailsContent">
                                            <div class="text-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" id="reorderBtn">Reorder</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fa fa-shopping-basket"></i>
                                <h5 class="text-muted">No Orders Found</h5>
                                <p class="text-muted mb-4">You don't have any laundry orders yet.</p>
                                <a href="place_order.php" class="btn btn-primary">
                                    <i class="fa fa-plus mr-2"></i> Place Your First Order
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Profile End -->


    <!-- Footer Start -->
    <div class="container-fluid bg-primary text-white mt-5 pt-5 px-sm-3 px-md-5">
        <div class="row pt-5">
            <div class="col-lg-3 col-md-6 mb-5">
                <a href=""><h1 class="text-secondary mb-3"><span class="text-white">DRY</span>ME</h1></a>
                <p>We provide the best laundry services in town. Quality, efficiency, and customer satisfaction are our top priorities.</p>
                <div class="d-flex justify-content-start mt-4">
                    <a class="btn btn-outline-light rounded-circle text-center mr-2 px-0" style="width: 38px; height: 38px;" href="#"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-outline-light rounded-circle text-center mr-2 px-0" style="width: 38px; height: 38px;" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-outline-light rounded-circle text-center mr-2 px-0" style="width: 38px; height: 38px;" href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-outline-light rounded-circle text-center mr-2 px-0" style="width: 38px; height: 38px;" href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-white mb-4">Get In Touch</h4>
                <p>Contact us for any inquiries or to schedule a pickup.</p>
                <p><i class="fa fa-map-marker-alt mr-2"></i>Cebu City, Philippines</p>
                <p><i class="fa fa-phone-alt mr-2"></i>+012 345 67890</p>
                <p><i class="fa fa-envelope mr-2"></i>info@example.com</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-white mb-4">Quick Links</h4>
                <div class="d-flex flex-column justify-content-start">
                    <a class="text-white mb-2" href="index.php"><i class="fa fa-angle-right mr-2"></i>Home</a>
                    <a class="text-white mb-2" href="about.php"><i class="fa fa-angle-right mr-2"></i>About Us</a>
                    <a class="text-white mb-2" href="service.php"><i class="fa fa-angle-right mr-2"></i>Services</a>
                    <a class="text-white mb-2" href="pricing.php"><i class="fa fa-angle-right mr-2"></i>Pricing</a>
                    <a class="text-white" href="contact.php"><i class="fa fa-angle-right mr-2"></i>Contact Us</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-white mb-4">Newsletter</h4>
                <form action="">
                    <div class="form-group">
                        <input type="text" class="form-control border-0" placeholder="Your Name" required="required" />
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control border-0" placeholder="Your Email" required="required" />
                    </div>
                    <div>
                        <button class="btn btn-lg btn-secondary btn-block border-0" type="submit">Submit Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="container-fluid bg-dark text-white py-4 px-sm-3 px-md-5">
        <p class="m-0 text-center text-white">
            &copy; <a class="text-white font-weight-medium" href="#">DRYME</a>. All Rights Reserved.
        </p>
    </div>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    
    <!-- Orders Table JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const ordersTable = document.getElementById('ordersTable');
            if (!ordersTable) return;
            
            const orderRows = document.querySelectorAll('#ordersTable tbody tr');
            
            // Add hover effect for better user experience
            orderRows.forEach(row => {
                row.addEventListener('mouseover', function() {
                    this.style.backgroundColor = 'rgba(0, 56, 161, 0.05)';
                    this.style.transform = 'translateY(-2px)';
                });
                
                row.addEventListener('mouseout', function() {
                    this.style.backgroundColor = '';
                    this.style.transform = '';
                });
            });
            
            // Add click effect for view buttons
            const viewButtons = document.querySelectorAll('.view-order-btn');
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // The modal is handled by Bootstrap's data attributes
                    // This is just for any additional functionality if needed
                });
            });
        });
    </script>

<!-- Add SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // View Order Button
    $('.view-order-btn').click(function() {
        const orderId = $(this).data('order-id');
        
        // Show loading in modal
        $('#orderDetailsModal').modal('show');
        $('#orderDetailsContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        
        // Fetch order details
        $.ajax({
            url: 'get_order_details.php',
            type: 'GET',
            data: { order_id: orderId },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    $('#orderDetailsContent').html('<div class="alert alert-danger">' + response.error + '</div>');
                    return;
                }
                
                const order = response.order;
                const items = response.items;
                
                // Build HTML content
                let html = `
                    <div class="order-details">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Order Information</h5>
                                <p><strong>Order ID:</strong> #${order.order_id}</p>
                                <p><strong>Date:</strong> ${order.created_at_formatted}</p>
                                <p><strong>Status:</strong> <span class="badge badge-${order.status_class} badge-pill">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span></p>
                                <p><strong>Priority:</strong> ${order.priority.charAt(0).toUpperCase() + order.priority.slice(1)}</p>
                                <p><strong>Weight:</strong> ${order.weight} kg</p>
                            </div>
                            <div class="col-md-6">
                                <h5>Customer Information</h5>
                                <p><strong>Name:</strong> ${order.full_name}</p>
                                <p><strong>Phone:</strong> ${order.phone}</p>
                                <p><strong>Address:</strong> ${order.address}</p>
                                <p><strong>Pickup:</strong> ${order.pickup ? 'Yes' : 'No'}</p>
                                <p><strong>Delivery:</strong> ${order.delivery ? 'Yes' : 'No'}</p>
                            </div>
                        </div>
                        
                        <h5>Order Items</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Service</th>
                                        <th>Quantity (kg)</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                
                items.forEach(item => {
                    html += `
                        <tr>
                            <td>${item.service_name}</td>
                            <td>${item.quantity}</td>
                            <td>₱${parseFloat(item.price).toFixed(2)}</td>
                            <td>₱${parseFloat(item.subtotal).toFixed(2)}</td>
                        </tr>`;
                });
                
                html += `
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-right">Total:</th>
                                        <th>₱${parseFloat(order.total_amount).toFixed(2)}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>`;
                
                if (order.special_instructions) {
                    html += `
                        <div class="mt-3">
                            <h5>Special Instructions</h5>
                            <p>${order.special_instructions}</p>
                        </div>`;
                }
                
                // Update modal content
                $('#orderDetailsContent').html(html);
                
                // Show/hide reorder button based on status
                if (order.status === 'completed') {
                    $('#reorderBtn').show();
                    $('#reorderBtn').data('order-id', order.order_id);
                } else {
                    $('#reorderBtn').hide();
                }
            },
            error: function() {
                $('#orderDetailsContent').html('<div class="alert alert-danger">Error loading order details. Please try again.</div>');
            }
        });
    });
    
    // Reorder Button (in modal)
    $('#reorderBtn').click(function() {
        $('#orderDetailsModal').modal('hide');
        
        // Show SweetAlert
        Swal.fire({
            title: 'Coming Soon!',
            text: 'The reorder feature will be available soon. Thank you for your patience!',
            icon: 'info',
            confirmButtonText: 'OK',
            confirmButtonColor: '#0038A1'
        });
    });
    
    // Reorder Button (in table)
    $('.reorder-btn').click(function() {
        // Show SweetAlert
        Swal.fire({
            title: 'Coming Soon!',
            text: 'The reorder feature will be available soon. Thank you for your patience!',
            icon: 'info',
            confirmButtonText: 'OK',
            confirmButtonColor: '#0038A1'
        });
    });
});
</script>
</body>
</html>