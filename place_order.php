<?php
session_start();
require_once 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login to place an order";
    header("Location: login.php");
    exit();
}

// Get services from database
$stmt = $pdo->query("SELECT * FROM services WHERE status = 'active'");
$services = $stmt->fetchAll();

// Get detergents from inventory
$stmt = $pdo->query("SELECT * FROM inventory WHERE category = 'supply' AND name LIKE '%detergent%' AND status = 'active'");
$detergents = $stmt->fetchAll();

// Get priority from URL parameter if available
$selected_priority = isset($_GET['priority']) ? $_GET['priority'] : 'normal';

// Variable to store order success message
$order_success = false;
$order_id = null;
$error_message = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Initialize total amount
        $total_amount = 0;
        $weight = $_POST['weight'];
        $priority = $_POST['priority'];
        
        // Apply minimum weight of 7kg for pricing
        $billing_weight = ($weight < 7) ? 7 : $weight;
        
        // Calculate service costs first
        if (isset($_POST['services'])) {
            // Get the service prices
            $serviceStmt = $pdo->prepare("SELECT service_id, price FROM services WHERE service_id = ?");
            
            foreach ($_POST['services'] as $service_id) {
                // Get the service price
                $serviceStmt->execute([$service_id]);
                $service = $serviceStmt->fetch();
                
                if ($service) {
                    $price = $service['price'];
                    $subtotal = $billing_weight * $price;
                    $total_amount += $subtotal;
                }
            }
        }
        
        // Apply priority multiplier to the total
        if ($priority === 'express') {
            $total_amount *= 1.25; // 25% extra for express
        } elseif ($priority === 'rush') {
            $total_amount *= 1.5; // 50% extra for rush
        }
        
        // Handle optional pickup and delivery dates
        $pickup_date = null;
        if (isset($_POST['pickup']) && isset($_POST['specify_pickup_date']) && !empty($_POST['pickup_date'])) {
            $pickup_date = $_POST['pickup_date'];
        }
        
        $delivery_date = null;
        if (isset($_POST['delivery']) && isset($_POST['specify_delivery_date']) && !empty($_POST['delivery_date'])) {
            $delivery_date = $_POST['delivery_date'];
        }
        
        // Insert into orders table
        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status, pickup_date, delivery_date, delivery, pickup, priority, weight, special_instructions, detergent_id, detergent_qty, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $_SESSION['user_id'],
            $total_amount,
            'pending', // Default status for new orders
            $pickup_date,
            $delivery_date,
            isset($_POST['delivery']) ? 1 : 0,
            isset($_POST['pickup']) ? 1 : 0,
            $_POST['priority'],
            $_POST['weight'],
            $_POST['special_instructions'] ?? null,
            $_POST['detergent_id'] ?: null,
            $_POST['detergent_qty'] ?: 0
        ]);

        // Get the last inserted order ID
        $order_id = $pdo->lastInsertId();

        // Insert into order_details table for each selected service
        if (isset($_POST['services'])) {
            $stmt = $pdo->prepare("INSERT INTO order_details (order_id, service_id, quantity, price, subtotal) 
                                  VALUES (?, ?, ?, ?, ?)");
            
            // First get the service prices
            $serviceStmt = $pdo->prepare("SELECT service_id, price FROM services WHERE service_id = ?");
            
            foreach ($_POST['services'] as $service_id) {
                // Get the service price
                $serviceStmt->execute([$service_id]);
                $service = $serviceStmt->fetch();
                
                if ($service) {
                    // Use billing weight for calculations
                    $quantity = ($weight < 7) ? 7 : $weight; // Apply minimum weight of 7kg
                    $price = $service['price'];
                    $subtotal = $quantity * $price;
                    
                    $stmt->execute([
                        $order_id,
                        $service_id,
                        $quantity,
                        $price,
                        $subtotal
                    ]);
                }
            }
        }

        $pdo->commit();
        // Set success flag and order ID for SweetAlert
        $order_success = true;
        // Store success message in session for redirect case
        $_SESSION['success'] = "Order placed successfully! Our team will contact you soon.";
        
    } catch (Exception $e) {
        // Rollback the transaction
        $pdo->rollBack();
        // Set error message for SweetAlert
        $error_message = $e->getMessage();
    }
}

// Get user data
$stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DRYME - Place Order</title>
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
    
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
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
                                    <a href="profile.php" class="dropdown-item">My Profile</a>
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
                    <h1 class="mb-4 mb-md-0 text-white">Place Your Order</h1>
                </div>
                <div class="col-md-6 text-center text-md-right">
                    <div class="d-inline-flex align-items-center">
                        <a class="btn text-white" href="index.php">Home</a>
                        <i class="fas fa-angle-right text-white"></i>
                        <a class="btn text-white" href="pricing.php">Pricing</a>
                        <i class="fas fa-angle-right text-white"></i>
                        <a class="btn text-white disabled" href="">Place Order</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->


    <!-- Order Form Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="bg-light p-5 rounded shadow">
                        <h2 class="text-primary text-center mb-4">Laundry Order Form</h2>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php 
                                echo $_SESSION['error']; 
                                unset($_SESSION['error']);
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <?php 
                                echo $_SESSION['success']; 
                                unset($_SESSION['success']);
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="" method="POST">
                            <div class="row">
                                <!-- Customer Information -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Full Name</label>
                                        <input type="text" class="form-control" id="name" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address" value="<?php echo htmlspecialchars($user['address']); ?>" readonly>
                                    </div>
                                </div>
                                
                                <!-- Service Priority -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Service Priority</label>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="normal" name="priority" value="normal" class="custom-control-input" <?php echo ($selected_priority === 'normal') ? 'checked' : ''; ?>>
                                            <label class="custom-control-label" for="normal">Normal (Standard Price)</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="express" name="priority" value="express" class="custom-control-input" <?php echo ($selected_priority === 'express') ? 'checked' : ''; ?>>
                                            <label class="custom-control-label" for="express">Express (+25% - Faster Service)</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="rush" name="priority" value="rush" class="custom-control-input" <?php echo ($selected_priority === 'rush') ? 'checked' : ''; ?>>
                                            <label class="custom-control-label" for="rush">Rush (+50% - Same Day Service)</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Laundry Services -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Laundry Services</label>
                                        <?php foreach ($services as $service): ?>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="service<?php echo $service['service_id']; ?>" name="services[]" value="<?php echo $service['service_id']; ?>">
                                                <label class="custom-control-label" for="service<?php echo $service['service_id']; ?>">
                                                    <?php echo htmlspecialchars($service['service_name']); ?> - ₱<?php echo number_format($service['price'], 2); ?> per kg
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <!-- Laundry Weight -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="weight">Estimated Weight (kg)</label>
                                        <input type="number" class="form-control" id="weight" name="weight" min="1" max="20" value="1" required>
                                        <small class="form-text text-muted">Minimum Load: 7kg, Maximum: 20kg</small>
                                        <small class="form-text text-muted font-weight-bold text-danger">Note: Orders less than 7kg will be charged at the 7kg rate (standard minimum)</small>
                                    </div>
                                </div>
                                
                                <!-- Detergent Preference -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="detergent">Choose a Detergent (Optional)</label>
                                        <select class="form-control" id="detergent" name="detergent_id">
                                            <option value="">I have detergent already.</option>
                                            <?php foreach ($detergents as $detergent): ?>
                                                <option value="<?php echo $detergent['item_id']; ?>">
                                                    <?php echo htmlspecialchars($detergent['name']); ?>
                                                    <?php if (isset($detergent['cost_per_unit']) && $detergent['cost_per_unit'] !== null): ?>
                                                        (₱<?php echo number_format($detergent['cost_per_unit'], 2); ?>)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6" id="detergentQtyContainer" style="display: none;">
                                    <div class="form-group">
                                        <label for="detergent_qty">Detergent Quantity</label>
                                        <input type="number" class="form-control" id="detergent_qty" name="detergent_qty" min="1" value="1">
                                    </div>
                                </div>
                                
                                <!-- Pickup & Delivery Options -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pickup & Delivery Options</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="pickup" name="pickup" value="1">
                                            <label class="custom-control-label" for="pickup">Request Pickup</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="delivery" name="delivery" value="1">
                                            <label class="custom-control-label" for="delivery">Request Delivery</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pickup Date (Optional) -->
                                <div class="col-md-6" id="pickupDateContainer" style="display: none;">
                                    <div class="form-group">
                                        <label for="pickup_date">Preferred Pickup Date</label>
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input" id="specify_pickup_date" name="specify_pickup_date" value="1">
                                            <label class="custom-control-label" for="specify_pickup_date">Specify a preferred date</label>
                                        </div>
                                        <input type="text" class="form-control datepicker" id="pickup_date" name="pickup_date" placeholder="Select date" disabled>
                                    </div>
                                </div>
                                
                                <!-- Delivery Date (Optional) -->
                                <div class="col-md-6" id="deliveryDateContainer" style="display: none;">
                                    <div class="form-group">
                                        <label for="delivery_date">Preferred Delivery Date</label>
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input" id="specify_delivery_date" name="specify_delivery_date" value="1">
                                            <label class="custom-control-label" for="specify_delivery_date">Specify a preferred date</label>
                                        </div>
                                        <input type="text" class="form-control datepicker" id="delivery_date" name="delivery_date" placeholder="Select date" disabled>
                                    </div>
                                </div>
                                
                                <!-- Special Instructions -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="special_instructions">Special Instructions</label>
                                        <textarea class="form-control" id="special_instructions" name="special_instructions" rows="3" placeholder="Any special instructions for your laundry..."></textarea>
                                    </div>
                                </div>
                                
                                <!-- Estimated Total -->
                                <div class="col-md-12 mb-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Estimated Total</h5>
                                            <p class="card-text h3 text-primary" id="estimatedTotal">₱0.00</p>
                                            <p class="small text-muted">Note: Orders less than 7kg will be charged at the 7kg rate (standard minimum)</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 mt-3">Place Order</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Order Form End -->


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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize flatpickr for date pickers
            $(".datepicker").flatpickr({
                minDate: "today",
                dateFormat: "Y-m-d",
                disableMobile: "true"
            });
            
            // Show/hide detergent quantity field
            $("#detergent").change(function() {
                if ($(this).val() !== "") {
                    $("#detergentQtyContainer").show();
                } else {
                    $("#detergentQtyContainer").hide();
                }
            });
            
            // Show/hide pickup date field
            $("#pickup").change(function() {
                if ($(this).is(":checked")) {
                    $("#pickupDateContainer").show();
                } else {
                    $("#pickupDateContainer").hide();
                    $("#pickup_date").val("");
                    $("#specify_pickup_date").prop("checked", false);
                    $("#pickup_date").prop("disabled", true);
                }
            });
            
            // Show/hide delivery date field
            $("#delivery").change(function() {
                if ($(this).is(":checked")) {
                    $("#deliveryDateContainer").show();
                } else {
                    $("#deliveryDateContainer").hide();
                    $("#delivery_date").val("");
                    $("#specify_delivery_date").prop("checked", false);
                    $("#delivery_date").prop("disabled", true);
                }
            });
            
            // Enable/disable pickup date field
            $("#specify_pickup_date").change(function() {
                if ($(this).is(":checked")) {
                    $("#pickup_date").prop("disabled", false);
                } else {
                    $("#pickup_date").prop("disabled", true);
                    $("#pickup_date").val("");
                }
            });
            
            // Enable/disable delivery date field
            $("#specify_delivery_date").change(function() {
                if ($(this).is(":checked")) {
                    $("#delivery_date").prop("disabled", false);
                } else {
                    $("#delivery_date").prop("disabled", true);
                    $("#delivery_date").val("");
                }
            });
            
            // Calculate estimated total
            function calculateTotal() {
                var weight = parseFloat($("#weight").val()) || 0;
                var priority = $("input[name='priority']:checked").val();
                var total = 0;
                
                // Apply minimum weight of 7kg for pricing
                var billingWeight = (weight < 7) ? 7 : weight;
                
                // Calculate service costs
                $("input[name='services[]']:checked").each(function() {
                    var serviceId = $(this).val();
                    var priceText = $(this).siblings("label").text().split("₱")[1];
                    var price = parseFloat(priceText);
                    if (!isNaN(price)) {
                        total += billingWeight * price;
                    }
                });
                
                // Apply priority multiplier
                if (priority === "express") {
                    total *= 1.25;
                } else if (priority === "rush") {
                    total *= 1.5;
                }
                
                // Update the estimated total
                $("#estimatedTotal").text("₱" + total.toFixed(2));
            }
            
            // Recalculate when inputs change
            $("#weight").change(calculateTotal);
            $("input[name='priority']").change(calculateTotal);
            $("input[name='services[]']").change(calculateTotal);
            
            // Initial calculation
            calculateTotal();
        });
    </script>
</body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($order_success): ?>
        Swal.fire({
            title: "Order Placed Successfully!",
            html: "Your order #<?php echo $order_id; ?> has been placed successfully.<br><small>Note: Orders less than 7kg are charged at the standard minimum rate (7kg).</small>",
            icon: "success",
            confirmButtonText: "View My Orders",
            confirmButtonColor: "#0038A1"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "profile.php";
            }
        });
        <?php endif; ?>
        
        <?php if ($error_message): ?>
        Swal.fire({
            title: "Error!",
            text: "There was an error placing your order: <?php echo $error_message; ?>",
            icon: "error",
            confirmButtonText: "Try Again",
            confirmButtonColor: "#0038A1"
        });
        <?php endif; ?>
    });
</script>
</body>
</html>
