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
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

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
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <!-- User Information -->
                <div class="col-lg-4 mb-5">
                    <div class="bg-light p-4 mb-4 shadow-sm rounded">
                        <h3 class="text-primary mb-4">Personal Information</h3>
                        <div class="d-flex align-items-center mb-4">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 60px; height: 60px;">
                                <i class="fa fa-user fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-0"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($user['username']); ?></p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <p class="mb-2"><i class="fa fa-envelope text-primary mr-2"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                            <p class="mb-2"><i class="fa fa-phone text-primary mr-2"></i> <?php echo htmlspecialchars($user['phone']); ?></p>
                            <p class="mb-0"><i class="fa fa-map-marker-alt text-primary mr-2"></i> <?php echo htmlspecialchars($user['address']); ?></p>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <a href="edit_profile.php" class="btn btn-primary"><i class="fa fa-edit mr-2"></i>Edit Profile</a>
                            <a href="place_order.php" class="btn btn-secondary"><i class="fa fa-plus mr-2"></i>New Order</a>
                        </div>
                    </div>
                </div>
                
                <!-- User Orders -->
                <div class="col-lg-8">
                    <div class="bg-light p-4 shadow-sm rounded">
                        <h3 class="text-primary mb-4">My Orders</h3>
                        
                        <?php if (count($orders) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Services</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
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
                                                    <span class="badge badge-<?php 
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
                                                    <button class="btn btn-sm btn-info view-order-btn" data-toggle="modal" data-target="#orderModal<?php echo $order['order_id']; ?>">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            
                                            <!-- Order Details Modal -->
                                            <div class="modal fade" id="orderModal<?php echo $order['order_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel<?php echo $order['order_id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title" id="orderModalLabel<?php echo $order['order_id']; ?>">Order #<?php echo $order['order_id']; ?> Details</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h5 class="text-primary">Order Information</h5>
                                                                    <p><strong>Order ID:</strong> #<?php echo $order['order_id']; ?></p>
                                                                    <p><strong>Date:</strong> <?php echo date('F d, Y h:i A', strtotime($order['created_at'])); ?></p>
                                                                    <p><strong>Status:</strong> 
                                                                        <span class="badge badge-<?php 
                                                                            switch($order['status']) {
                                                                                case 'pending': echo 'warning'; break;
                                                                                case 'processing': echo 'info'; break;
                                                                                case 'completed': echo 'success'; break;
                                                                                case 'cancelled': echo 'danger'; break;
                                                                                default: echo 'secondary';
                                                                            }
                                                                        ?>">
                                                                            <?php echo ucfirst($order['status']); ?>
                                                                        </span>
                                                                    </p>
                                                                    <p><strong>Priority:</strong> <?php echo ucfirst($order['priority']); ?></p>
                                                                    <p><strong>Weight:</strong> <?php echo $order['weight']; ?> kg</p>
                                                                    <p><strong>Total Amount:</strong> ₱<?php echo number_format($order['total_amount'], 2); ?></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h5 class="text-primary">Delivery Information</h5>
                                                                    <p><strong>Pickup:</strong> <?php echo $order['pickup'] ? 'Yes' : 'No'; ?></p>
                                                                    <p><strong>Pickup Date:</strong> <?php echo date('F d, Y', strtotime($order['pickup_date'])); ?></p>
                                                                    <p><strong>Delivery:</strong> <?php echo $order['delivery'] ? 'Yes' : 'No'; ?></p>
                                                                    <p><strong>Delivery Date:</strong> <?php echo date('F d, Y', strtotime($order['delivery_date'])); ?></p>
                                                                    <?php if (!empty($order['special_instructions'])): ?>
                                                                    <p><strong>Special Instructions:</strong> <?php echo htmlspecialchars($order['special_instructions']); ?></p>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            
                                                            <hr>
                                                            
                                                            <h5 class="text-primary">Services</h5>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                            <th>Service</th>
                                                                            <th>Quantity</th>
                                                                            <th>Price</th>
                                                                            <th>Subtotal</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php 
                                                                        // Get order details
                                                                        $stmt = $pdo->prepare("SELECT od.*, s.service_name 
                                                                                              FROM order_details od
                                                                                              JOIN services s ON od.service_id = s.service_id
                                                                                              WHERE od.order_id = ?");
                                                                        $stmt->execute([$order['order_id']]);
                                                                        $orderDetails = $stmt->fetchAll();
                                                                        
                                                                        foreach ($orderDetails as $detail): 
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo htmlspecialchars($detail['service_name']); ?></td>
                                                                            <td><?php echo $detail['quantity']; ?> kg</td>
                                                                            <td>₱<?php echo number_format($detail['price'], 2); ?></td>
                                                                            <td>₱<?php echo number_format($detail['subtotal'], 2); ?></td>
                                                                        </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle mr-2"></i> You don't have any orders yet. 
                                <a href="place_order.php" class="alert-link">Place your first order now!</a>
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
                <p><i class="fa fa-map-marker-alt mr-2"></i>123 Street, New York, USA</p>
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
</body>

</html>