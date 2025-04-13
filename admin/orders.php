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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Insert into orders table
        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status, pickup_date, delivery_date, delivery, pickup, priority, created_at) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $_POST['customer_id'],
            $_POST['total_amount'],
            $_POST['status'],
            $_POST['pickup_date'],
            $_POST['delivery_date'],
            isset($_POST['delivery']) ? 1 : 0,
            isset($_POST['pickup']) ? 1 : 0,
            $_POST['priority']
        ]);
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
                <a href="index.php" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="<?php echo isset($admin['profile_picture']) ? 'img/profile/' . $admin['profile_picture'] : 'img/user.jpg'; ?>" alt="" style="width: 40px; height: 40px;">
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
                <!-- Display success/error messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
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
                                                <input class="form-check-input" type="radio" name="priority" value="express">
                                                <label class="form-check-label">Extra Rush (~3 hours)</label>
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

                                <div class="mb-3">
                                    <label class="form-label">Special Instructions</label>
                                    <textarea class="form-control" rows="3" placeholder="Enter any special instructions or notes"></textarea>
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
                                            <td><?php echo date('M d, Y', strtotime($order['pickup_date'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-info" onclick="showOrderDetails(<?php echo $order['order_id']; ?>)">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, '<?php echo $order['status']; ?>')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary" onclick="printReceipt(<?php echo $order['order_id']; ?>)">
                                                        <i class="fa fa-print"></i>
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
            <div class="modal fade" id="orderDetailsModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Order Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <!-- Customer Details -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="border-bottom pb-2">Customer Details</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Name:</strong> <span id="customerName"></span></p>
                                                <p><strong>Phone:</strong> <span id="customerPhone"></span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Email:</strong> <span id="customerEmail"></span></p>
                                                <p><strong>Address:</strong> <span id="customerAddress"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Laundry Notes -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="border-bottom pb-2">Laundry Notes</h6>
                                        <div class="alert alert-info" id="laundryNotes">
                                            <!-- Notes will be inserted here -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Item List -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="border-bottom pb-2">Item List</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th>Quantity</th>
                                                        <th>Service</th>
                                                        <th>Notes</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="itemList">
                                                    <!-- Items will be inserted here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <!-- Action Buttons -->
                            <button type="button" class="btn btn-warning" id="updateStatus">
                                <i class="fa fa-refresh me-2"></i>Update Status
                            </button>
                            <button type="button" class="btn btn-info" id="printReceipt">
                                <i class="fa fa-print me-2"></i>Print Receipt
                            </button>
                            <button type="button" class="btn btn-success" id="notifyCustomer">
                                <i class="fa fa-bell me-2"></i>Notify Customer
                            </button>
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
    // Fetch order details using AJAX
    $.ajax({
        url: 'helpers/get_order_details.php',
        type: 'GET',
        data: { order_id: orderId },
        success: function(response) {
            const data = JSON.parse(response);
            
            // Populate modal with order details
            $('#customerName').text(data.customer.full_name);
            $('#customerPhone').text(data.customer.phone);
            $('#customerEmail').text(data.customer.email);
            $('#customerAddress').text(data.customer.address);
            $('#laundryNotes').html(data.order.special_instructions || 'No special instructions');
            
            // Populate items table
            let itemsHtml = '';
            data.items.forEach(item => {
                itemsHtml += `<tr>
                    <td>${item.service_name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.price}</td>
                    <td>${item.notes || '-'}</td>
                </tr>`;
            });
            $('#itemList').html(itemsHtml);
            
            // Show modal
            $('#orderDetailsModal').modal('show');
        },
        error: function() {
            alert('Error fetching order details');
        }
    });
}
</script>

<script>
function updateOrderStatus(orderId, currentStatus) {
    $('#updateOrderId').val(orderId);
    $('#updateOrderStatus').val(currentStatus);
    $('#updateStatusModal').modal('show');
}

function saveOrderStatus() {
    const orderId = $('#updateOrderId').val();
    const status = $('#updateOrderStatus').val();

    $.ajax({
        url: 'helpers/update_order_status.php',
        type: 'POST',
        data: {
            order_id: orderId,
            status: status
        },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                $('#updateStatusModal').modal('hide');
                // Reload the page to show updated status
                location.reload();
            } else {
                alert('Error updating status: ' + data.message);
            }
        },
        error: function() {
            alert('Error updating order status');
        }
    });
}
</script>

<script>
$(document).ready(function() {
    const LOAD_WEIGHT = 7; // Default load weight in kilos
    const BASE_LOAD_PRICE = 120; // Price per load
    const EXTRA_KILO_PRICE = 5; // Additional price per extra kilo
    const DELIVERY_FEE = 25;
    const PICKUP_FEE = 25;

    function calculateTotal() {
        let total = 0;
        const weight = parseFloat($('input[name="weight"]').val()) || 0;
        
        // Calculate base load price only once
        let loadPrice = BASE_LOAD_PRICE;
        if (weight > LOAD_WEIGHT) {
            const extraKilos = weight - LOAD_WEIGHT;
            loadPrice += (extraKilos * EXTRA_KILO_PRICE);
        }

        // Add load price only once
        total += loadPrice;

        // Add service prices separately
        $('.service-check:checked').each(function() {
            const servicePrice = parseFloat($(this).data('price'));
            total += servicePrice; // Only add the service price, not the load price
        });

        // Add delivery/pickup fees
        if ($('input[name="delivery"]').is(':checked')) {
            total += DELIVERY_FEE;
        }
        if ($('input[name="pickup"]').is(':checked')) {
            total += PICKUP_FEE;
        }

        // Add priority charges
        const priority = $('input[name="priority"]:checked').val();
        if (priority === 'express') {
            total *= 1.5; // 50% extra for express
        } else if (priority === 'rush') {
            total *= 2; // 100% extra for rush
        }

        $('#total_amount').val(total.toFixed(2));
        return total;
    }

    // Handle pickup/delivery date requirements
    function toggleDateFields() {
        const pickupChecked = $('.pickup-check').is(':checked');
        const deliveryChecked = $('.delivery-check').is(':checked');

        $('.pickup-date-div input').prop('required', pickupChecked);
        $('.delivery-date-div input').prop('required', deliveryChecked);

        $('.pickup-date-div').toggle(pickupChecked);
        $('.delivery-date-div').toggle(deliveryChecked);
    }

    // Initial state
    toggleDateFields();

    // Event listeners
    $('.service-check, input[name="weight"], input[name="priority"], .delivery-check, .pickup-check').on('change', calculateTotal);
    $('.delivery-check, .pickup-check').on('change', toggleDateFields);

    // Add helper text for weight input
    $('input[name="weight"]').after('<small class="form-text text-muted">Standard load is 7kg (₱120.00). Additional kilos are ₱5.00 each.</small>');
});
</script>

<script>
function printReceipt(orderId) {
    var receiptWindow = window.open('helpers/print_receipt.php?order_id=' + orderId, '_blank', 'width=400,height=600');
    receiptWindow.focus();
}

// Also update the print button in the order details modal
$(document).ready(function() {
    $('#printReceipt').click(function() {
        const orderId = $('#updateOrderId').val();
        printReceipt(orderId);
    });
});
</script>
