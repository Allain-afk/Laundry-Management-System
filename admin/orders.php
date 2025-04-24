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

// Fetch all customers for the dropdown
$stmt = $pdo->query("SELECT * FROM customers");
$customers = $stmt->fetchAll();

// Add this new query to fetch services
$stmt = $pdo->query("SELECT * FROM services");
$services = $stmt->fetchAll();

// Add query to fetch detergents
$stmt = $pdo->query("SELECT * FROM inventory WHERE category = 'supply' AND name LIKE '%detergent%' AND status = 'active'");
$detergents = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Check detergent inventory if selected
        if (!empty($_POST['detergent_id']) && !empty($_POST['detergent_qty'])) {
            $stmt = $pdo->prepare("SELECT quantity FROM inventory WHERE item_id = ? AND status = 'active' FOR UPDATE");
            $stmt->execute([$_POST['detergent_id']]);
            $inventory = $stmt->fetch();

            if (!$inventory || $inventory['quantity'] < $_POST['detergent_qty']) {
                throw new Exception("Insufficient detergent inventory");
            }

            // Update inventory
            $stmt = $pdo->prepare("UPDATE inventory SET quantity = quantity - ? WHERE item_id = ?");
            $stmt->execute([$_POST['detergent_qty'], $_POST['detergent_id']]);
        }

        // Insert into orders table
        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status, pickup_date, delivery_date, delivery, pickup, priority, weight, special_instructions, detergent_id, detergent_qty, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $_POST['customer_id'],
            $_POST['total_amount'],
            $_POST['status'],
            $_POST['pickup_date'],
            $_POST['delivery_date'],
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
                    $quantity = $_POST['weight'];
                    $price = $service['price'];
                    $subtotal = $quantity * $price;
                    
                    $stmt->execute([
                        $order_id,  // Use the obtained order_id
                        $service_id,
                        $quantity,
                        $price,
                        $subtotal
                    ]);
                }
            }
        }

        $pdo->commit();
        $_SESSION['success'] = "Order created successfully!";
        header("Location: orders.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error creating order: " . $e->getMessage();
    }
}

// Fetch existing orders for display
$stmt = $pdo->query("SELECT o.*, c.full_name as customer_name 
                     FROM orders o 
                     JOIN customers c ON o.customer_id = c.customer_id 
                     ORDER BY o.created_at DESC");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DryMe - Orders</title>
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
                    <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>DryMe</h3>
                </a>
                <div class="navbar-nav w-100">
                    <a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a href="customers.php" class="nav-item nav-link"><i class="fa fa-users me-2"></i>Customers</a>
                    <a href="orders.php" class="nav-item nav-link active"><i class="fa fa-shopping-cart me-2"></i>Orders</a>
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

            <!-- Orders Form Start -->
            <div class="container-fluid pt-4 px-4">
                <!-- Success/Error messages will be handled by SweetAlert2 -->
                <div id="alert-container"></div>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                title: 'Success!',
                                text: '<?php echo addslashes($_SESSION['success']); ?>',
                                icon: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                title: 'Error!',
                                text: '<?php echo addslashes($_SESSION['error']); ?>',
                                icon: 'error',
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="row g-4">
                    <div class="col-sm-12">
                        <div class="bg-light rounded h-100 p-4">
                            <h6 class="mb-4">Create New Order</h6>
                            <form method="POST" action="">
                                <input type="hidden" name="total_amount" id="total_amount" value="0">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Customer Name</label>
                                        <select class="form-select mb-3" name="customer_id" required>
                                            <option value="">Select Customer</option>
                                            <?php foreach ($customers as $customer): ?>
                                                <option value="<?php echo $customer['customer_id']; ?>">
                                                    <?php echo htmlspecialchars($customer['full_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Order Date</label>
                                        <input type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Service Type</label>
                                        <div class="row g-2">
                                            <?php foreach ($services as $service): ?>
                                                <?php if ($service['service_name'] !== 'Delivery Service' && $service['service_name'] !== 'Home Pickup'): ?>
                                                <div class="col-6">
                                                    <div class="border rounded p-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input service-check" 
                                                                   type="checkbox" 
                                                                   name="services[]" 
                                                                   value="<?php echo $service['service_id']; ?>"
                                                                   data-price="<?php echo $service['price']; ?>">
                                                            <label class="form-check-label">
                                                                <?php echo htmlspecialchars($service['service_name']); ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>

                                        <div class="mt-3">
                                            <label class="form-label">Weight (kg)</label>
                                            <input type="number" name="weight" class="form-control" step="0.1" min="0" required>
                                            <small class="form-text text-muted">Standard load is 7kg (₱120.00). Additional kilos are ₱5.00 each.</small>
                                        </div>

                                        <!-- Move Detergent Selection here -->
                                        <div class="mt-3">
                                            <div class="border rounded p-3">
                                                <label class="form-label">Detergent Selection</label>
                                                <div class="mb-3">
                                                    <select class="form-select" name="detergent_id" id="detergentSelect">
                                                        <option value="">No detergent needed</option>
                                                        <?php foreach ($detergents as $detergent): ?>
                                                            <option value="<?php echo $detergent['item_id']; ?>" 
                                                                    data-quantity="<?php echo $detergent['quantity']; ?>">
                                                                <?php echo htmlspecialchars($detergent['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div id="detergentQtyDiv" style="display: none;">
                                                    <label class="form-label">Number of Sachets:</label>
                                                    <input type="number" name="detergent_qty" class="form-control" min="1" value="1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Priority</label>
                                        <div class="border rounded p-3 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="priority" value="normal" checked>
                                                <label class="form-check-label">Normal (2-3 days)</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="priority" value="express">
                                                <label class="form-check-label">Express (24 hours)</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="priority" value="rush">
                                                <label class="form-check-label">Rush (~3 hours)</label>
                                            </div>
                                        </div>

                                        <label class="form-label">Additional Services</label>
                                        <div class="border rounded p-3">
                                            <div class="form-check">
                                                <input class="form-check-input delivery-check" type="checkbox" name="delivery" value="1">
                                                <label class="form-check-label">Delivery Service (₱25)</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input pickup-check" type="checkbox" name="pickup" value="1">
                                                <label class="form-check-label">Pickup Service (₱25)</label>
                                            </div>
                                        </div>
                                        <div class="border rounded p-3">
                                                    <label class="form-label">Special Instructions</label>
                                                    <textarea class="form-control" name="special_instructions" rows="2" placeholder="Enter any special instructions or notes"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 pickup-date-div">
                                        <label class="form-label">Pickup Date</label>
                                        <input type="date" name="pickup_date" class="form-control">
                                    </div>
                                    <div class="col-md-6 delivery-date-div">
                                        <label class="form-label">Delivery Date</label>
                                        <input type="date" name="delivery_date" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Payment Method</label>
                                        <select class="form-select">
                                            <option selected>Select Payment Method</option>
                                            <option value="cash">Cash</option>
                                            <option value="card">Credit/Debit Card</option>
                                            <option value="ewallet">E-Wallet</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status" required>
                                            <option value="pending" selected>Pending</option>
                                            <option value="processing">Processing</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Create Order</button>
                                        <button type="reset" class="btn btn-secondary">Reset Form</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="bg-light rounded h-100 p-4">
                            <h6 class="mb-4">Recent Orders</h6>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Order ID</th>
                                            <th scope="col">Customer</th>
                                            <th scope="col">Total Amount</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Pickup Date</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['order_id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                            <td><?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo match($order['status']) {
                                                        'pending' => 'warning',
                                                        'processing' => 'primary',
                                                        'completed' => 'success',
                                                        'cancelled' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $pickup_date = $order['pickup_date'];
                                                if (empty($pickup_date) || $pickup_date === '0000-00-00') {
                                                    echo 'N/A';
                                                } else {
                                                    echo date('M d, Y', strtotime($pickup_date));
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-info" onclick="showOrderDetails(<?php echo $order['order_id']; ?>)" title="View Details">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, '<?php echo $order['status']; ?>')" title="Update Status">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary" onclick="printReceipt(<?php echo $order['order_id']; ?>)" title="Print Receipt">
                                                        <i class="fa fa-print"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="confirmDeleteOrder(<?php echo $order['order_id']; ?>)" title="Delete Order">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Orders Form End and before Footer Start -->
            <!-- Add this before the Footer Start -->
                        <!-- Status Update Modal -->
                        <div class="modal fade" id="updateStatusModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Update Order Status</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="updateStatusForm">
                                            <input type="hidden" id="updateOrderId" name="order_id">
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" id="updateOrderStatus" name="status" required>
                                                    <option value="pending">Pending</option>
                                                    <option value="processing">Processing</option>
                                                    <option value="completed">Completed</option>
                                                    <option value="cancelled">Cancelled</option>
                                                </select>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" onclick="saveOrderStatus()">Save Changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
            <!-- Order Details Modal Start -->
            <!-- Order Details Modal -->
            <div class="modal fade" id="orderDetailsModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Order Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Customer Information</h6>
                                    <p><strong>Name:</strong> <span id="customerName"></span></p>
                                    <p><strong>Phone:</strong> <span id="customerPhone"></span></p>
                                    <p><strong>Email:</strong> <span id="customerEmail"></span></p>
                                    <p><strong>Address:</strong> <span id="customerAddress"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Order Information</h6>
                                    <div id="orderDetails"></div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6>Services</h6>
                                    <table class="table" id="servicesTable">
                                        <thead>
                                            <tr>
                                                <th>Service</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            <!-- Order Details Modal End -->
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
</body>

</html>
<script>
function showOrderDetails(orderId) {
    // Show loading indicator
    Swal.fire({
        title: 'Loading...',
        text: 'Fetching order details',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: 'helpers/get_order_details.php',
        type: 'GET',
        data: { order_id: orderId },
        dataType: 'json',
        success: function(response) {
            Swal.close();
            if (response.error) {
                Swal.fire({
                    title: 'Error!',
                    text: response.error,
                    icon: 'error'
                });
                return;
            }
            
            // Fill customer information
            $('#customerName').text(response.customer.full_name);
            $('#customerPhone').text(response.customer.phone);
            $('#customerEmail').text(response.customer.email || 'N/A');
            $('#customerAddress').text(response.customer.address);
            
            // Fill order details
            var orderDetailsHtml = '';
            orderDetailsHtml += '<p><strong>Order ID:</strong> #' + response.order.order_id + '</p>';
            orderDetailsHtml += '<p><strong>Date:</strong> ' + formatDate(response.order.created_at) + '</p>';
            orderDetailsHtml += '<p><strong>Status:</strong> ' + capitalizeFirstLetter(response.order.status) + '</p>';
            orderDetailsHtml += '<p><strong>Priority:</strong> ' + capitalizeFirstLetter(response.order.priority) + '</p>';
            orderDetailsHtml += '<p><strong>Weight:</strong> ' + response.order.weight_formatted + ' kg</p>';
            
            // Use the calculated total for consistency
            orderDetailsHtml += '<p><strong>Total Amount:</strong> ₱' + response.order.calculated_total_formatted + '</p>';
            
            // Add pickup and delivery dates
            var pickupDate = response.order.pickup_date;
            var deliveryDate = response.order.delivery_date;
            
            orderDetailsHtml += '<p><strong>Pickup Date:</strong> ' + (pickupDate && pickupDate !== '0000-00-00' ? formatDate(pickupDate) : 'N/A') + '</p>';
            orderDetailsHtml += '<p><strong>Delivery Date:</strong> ' + (deliveryDate && deliveryDate !== '0000-00-00' ? formatDate(deliveryDate) : 'N/A') + '</p>';
            
            // Add special instructions
            var specialInstructions = response.order.special_instructions;
            orderDetailsHtml += '<p><strong>Special Instructions:</strong> ' + (specialInstructions ? specialInstructions : 'None') + '</p>';
            
            $('#orderDetails').html(orderDetailsHtml);
            
            // Fill services table
            var servicesHtml = '';
            
            // Regular services
            response.services.forEach(function(service) {
                servicesHtml += '<tr>';
                servicesHtml += '<td>' + service.service_name + '</td>';
                servicesHtml += '<td>N/A</td>';
                servicesHtml += '<td>N/A</td>';
                servicesHtml += '</tr>';
            });
            
            // Additional services
            if (response.additional_services && response.additional_services.length > 0) {
                response.additional_services.forEach(function(service) {
                    servicesHtml += '<tr>';
                    servicesHtml += '<td>' + service.name + '</td>';
                    servicesHtml += '<td>' + service.quantity_formatted + '</td>';
                    servicesHtml += '<td>₱' + service.price_formatted + '</td>';
                    servicesHtml += '</tr>';
                });
            }
            
            $('#servicesTable tbody').html(servicesHtml);
            
            // Show the modal
            $('#orderDetailsModal').modal('show');
        },
        error: function() {
            alert('Error fetching order details. Please try again.');
        }
    });
}

function updateOrderStatus(orderId, currentStatus) {
    Swal.fire({
        title: 'Update Order Status',
        html: `
            <select id="swal-status-select" class="form-select mb-3">
                <option value="pending" ${currentStatus === 'pending' ? 'selected' : ''}>Pending</option>
                <option value="processing" ${currentStatus === 'processing' ? 'selected' : ''}>Processing</option>
                <option value="completed" ${currentStatus === 'completed' ? 'selected' : ''}>Completed</option>
                <option value="cancelled" ${currentStatus === 'cancelled' ? 'selected' : ''}>Cancelled</option>
            </select>
        `,
        showCancelButton: true,
        confirmButtonText: 'Update',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        preConfirm: () => {
            return document.getElementById('swal-status-select').value;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const newStatus = result.value;
            
            // Show loading state
            Swal.fire({
                title: 'Updating...',
                text: 'Updating order status',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: 'helpers/update_order_status.php',
                type: 'POST',
                data: { order_id: orderId, status: newStatus },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Order status updated successfully',
                            icon: 'success',
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.error || 'An error occurred',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while updating the order status.',
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
    });
}

function saveOrderStatus() {
    var orderId = $('#updateOrderId').val();
    var status = $('#updateOrderStatus').val();
    
    $.ajax({
        url: 'helpers/update_order_status.php',
        type: 'POST',
        data: { 
            order_id: orderId,
            status: status
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                $('#updateStatusModal').modal('hide');
                // Reload the page to show updated status
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('Error updating order status. Please try again.');
        }
    });
}

function printReceipt(orderId) {
    window.open('helpers/print_receipt.php?order_id=' + orderId, '_blank', 'width=800,height=600');
}

// Helper function to format date
function formatDate(dateString) {
    var date = new Date(dateString);
    var options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Helper function to capitalize first letter
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Initialize the page
$(document).ready(function() {
    // Show/hide detergent quantity field based on selection
    $('#detergentSelect').change(function() {
        if ($(this).val()) {
            $('#detergentQtyDiv').show();
        } else {
            $('#detergentQtyDiv').hide();
        }
    });
    
    // Show/hide pickup date field based on checkbox
    $('.pickup-check').change(function() {
        if ($(this).is(':checked')) {
            $('.pickup-date-div').show();
        } else {
            $('.pickup-date-div').hide();
        }
    });
    
    // Show/hide delivery date field based on checkbox
    $('.delivery-check').change(function() {
        if ($(this).is(':checked')) {
            $('.delivery-date-div').show();
        } else {
            $('.delivery-date-div').hide();
        }
    });
    
    // Hide pickup and delivery date fields initially
    $('.pickup-date-div, .delivery-date-div').hide();
    
    // Calculate total amount based on selected services and weight
    $('input[name="weight"], .service-check, .delivery-check, .pickup-check').on('change', function() {
        calculateTotal();
    });
    
    function calculateTotal() {
        var total = 0;
        var weight = parseFloat($('input[name="weight"]').val()) || 0;
        
        // Add service costs
        $('.service-check:checked').each(function() {
            var price = parseFloat($(this).data('price'));
            total += price * weight;
        });
        
        // Add delivery cost if selected
        if ($('.delivery-check').is(':checked')) {
            total += 25;
        }
        
        // Add pickup cost if selected
        if ($('.pickup-check').is(':checked')) {
            total += 25;
        }
        
        // Update hidden total amount field
        $('#total_amount').val(total.toFixed(2));
    }
});
</script>

<script>
function printReceipt(orderId) {
    Swal.fire({
        title: 'Preparing Receipt',
        text: 'Opening print preview...',
        icon: 'info',
        timer: 1500,
        showConfirmButton: false
    }).then(() => {
        var receiptWindow = window.open('helpers/print_receipt.php?order_id=' + orderId, '_blank', 'width=400,height=600');
        receiptWindow.focus();
    });
}

// Function to confirm order deletion
function confirmDeleteOrder(orderId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                text: 'Removing the order',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Redirect to delete handler
            window.location.href = 'helpers/delete_order.php?order_id=' + orderId;
        }
    });
}

// Also update the print button in the order details modal
$(document).ready(function() {
    $('#printReceipt').click(function() {
        const orderId = $('#updateOrderId').val();
        printReceipt(orderId);
    });
    
    // Form submission with validation
    $('form').on('submit', function(e) {
        const customerId = $('select[name="customer_id"]').val();
        const weight = $('input[name="weight"]').val();
        const servicesChecked = $('.service-check:checked').length;
        
        if (!customerId) {
            e.preventDefault();
            Swal.fire({
                title: 'Missing Information',
                text: 'Please select a customer',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
            return false;
        }
        
        if (!weight || weight <= 0) {
            e.preventDefault();
            Swal.fire({
                title: 'Invalid Weight',
                text: 'Please enter a valid weight',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
            return false;
        }
        
        if (servicesChecked === 0) {
            e.preventDefault();
            Swal.fire({
                title: 'No Services Selected',
                text: 'Please select at least one service',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
            return false;
        }
        
        // If all validations pass, show loading state
        Swal.fire({
            title: 'Processing',
            text: 'Creating your order...',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        return true;
    });
});
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Check detergent inventory if selected
        if (!empty($_POST['detergent_id']) && !empty($_POST['detergent_qty'])) {
            $stmt = $pdo->prepare("SELECT quantity FROM inventory WHERE item_id = ? AND status = 'active' FOR UPDATE");
            $stmt->execute([$_POST['detergent_id']]);
            $inventory = $stmt->fetch();

            if (!$inventory || $inventory['quantity'] < $_POST['detergent_qty']) {
                throw new Exception("Insufficient detergent inventory");
            }

            // Update inventory
            $stmt = $pdo->prepare("UPDATE inventory SET quantity = quantity - ? WHERE item_id = ?");
            $stmt->execute([$_POST['detergent_qty'], $_POST['detergent_id']]);
        }

        // Insert into orders table
        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status, pickup_date, delivery_date, delivery, pickup, priority, weight, special_instructions, detergent_id, detergent_qty, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $_POST['customer_id'],
            $_POST['total_amount'],
            $_POST['status'],
            $_POST['pickup_date'],
            $_POST['delivery_date'],
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
                    $quantity = $_POST['weight'];
                    $price = $service['price'];
                    $subtotal = $quantity * $price;
                    
                    $stmt->execute([
                        $order_id,  // Use the obtained order_id
                        $service_id,
                        $quantity,
                        $price,
                        $subtotal
                    ]);
                }
            }
        }

        $pdo->commit();
        $_SESSION['success'] = "Order created successfully!";
        header("Location: orders.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error creating order: " . $e->getMessage();
    }
}
?>

<script>
// Add these constants at the top of your script section
const BASE_PRICE_PER_KG = 30;
const DELIVERY_FEE = 25;
const PICKUP_FEE = 25;
const DETERGENT_PRICE = 10;

function calculateTotal() {
    let total = 0;
    const weight = parseFloat($('input[name="weight"]').val()) || 0;
    
    // Calculate base load price
    const baseLoadPrice = weight * BASE_PRICE_PER_KG;
    total = baseLoadPrice;
    
    // Add priority charges
    const priority = $('input[name="priority"]:checked').val();
    if (priority === 'express') {
        total += (baseLoadPrice * 0.25); // 25% extra for express
    } else if (priority === 'rush') {
        total += (baseLoadPrice * 0.5); // 50% extra for rush
    }
    
    // Add delivery/pickup fees
    if ($('.delivery-check').is(':checked')) {
        total += DELIVERY_FEE;
    }
    if ($('.pickup-check').is(':checked')) {
        total += PICKUP_FEE;
    }
    
    // Add detergent cost
    const detergentId = $('#detergentSelect').val();
    if (detergentId) {
        const detergentQty = parseInt($('input[name="detergent_qty"]').val()) || 0;
        total += (detergentQty * DETERGENT_PRICE);
    }
    
    $('#total_amount').val(total.toFixed(2));
    return total;
}

// Update event handlers to use the new calculateTotal function
$(document).ready(function() {
    // Show/hide detergent quantity field based on selection
    $('#detergentSelect').change(function() {
        if ($(this).val()) {
            $('#detergentQtyDiv').show();
        } else {
            $('#detergentQtyDiv').hide();
        }
        calculateTotal();
    });
    
    // Calculate total when these inputs change
    $('input[name="weight"], .service-check, .delivery-check, .pickup-check, input[name="priority"], input[name="detergent_qty"]').on('change', function() {
        calculateTotal();
    });
    
    // Initial calculation
    calculateTotal();
});
</script>
