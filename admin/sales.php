<?php
session_start();
require_once '../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: signin.php");
    exit();
}

// Get admin data
$stmt = $pdo->prepare("SELECT * FROM admins WHERE admin_id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

// Set default filter values
$period = isset($_GET['period']) ? $_GET['period'] : 'daily';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Prepare the date condition based on the selected period
switch ($period) {
    case 'daily':
        $date_condition = "DATE(sr.payment_date) = CURDATE()";
        break;
    case 'monthly':
        $date_condition = "MONTH(sr.payment_date) = MONTH(CURDATE()) AND YEAR(sr.payment_date) = YEAR(CURDATE())";
        break;
    case 'yearly':
        $date_condition = "YEAR(sr.payment_date) = YEAR(CURDATE())";
        break;
    case 'lifetime':
        $date_condition = "1=1"; // No date restriction
        break;
    case 'custom':
        $date_condition = "DATE(sr.payment_date) BETWEEN :start_date AND :end_date";
        break;
    default:
        $date_condition = "DATE(sr.payment_date) = CURDATE()";
}

// Fetch sales data
try {
    $query = "SELECT 
              o.order_id,
              o.total_amount,
              o.status as order_status,
              o.created_at,
              c.full_name as customer_name,
              sr.sale_id,
              sr.amount_paid,
              sr.payment_date,
              a.full_name as admin_name
              FROM orders o
              INNER JOIN customers c ON o.customer_id = c.customer_id
              LEFT JOIN sales_records sr ON o.order_id = sr.order_id
              LEFT JOIN admins a ON sr.admin_id = a.admin_id
              WHERE o.status = 'completed' 
              AND (sr.payment_date IS NULL OR $date_condition)
              ORDER BY o.created_at DESC";

    $stmt = $pdo->prepare($query);
    
    if ($period === 'custom') {
        $stmt->execute([
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);
    } else {
        $stmt->execute();
    }
    
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Update total sales query to use sales_records table
    $total_query = "SELECT COALESCE(SUM(sr.amount_paid), 0) as total 
                    FROM sales_records sr
                    INNER JOIN orders o ON sr.order_id = o.order_id
                    WHERE o.status = 'completed'
                    AND (sr.payment_date IS NULL OR $date_condition)";

    if ($period === 'custom') {
        $stmt = $pdo->prepare($total_query);
        $stmt->execute([
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);
    } else {
        $stmt = $pdo->query($total_query);
    }

    $total_sales = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    error_log("Sales query error: " . $e->getMessage());
    $sales = [];
    $total_sales = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sales - DryMe</title>
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
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>DryMe</h3>
                </a>
                <div class="navbar-nav w-100">
                    <a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a href="customers.php" class="nav-item nav-link"><i class="fa fa-users me-2"></i>Customers</a>
                    <a href="orders.php" class="nav-item nav-link"><i class="fa fa-shopping-cart me-2"></i>Orders</a>
                    <a href="sales.php" class="nav-item nav-link active"><i class="fa fa-money-bill-alt me-2"></i>Sales</a>
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
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
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

            <!-- Sales Filter Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light rounded h-100 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Sales Records</h6>
                        <div>
                            <select id="period" class="form-select form-select-sm d-inline-block w-auto me-2">
                                <option value="daily" <?php echo $period === 'daily' ? 'selected' : ''; ?>>Daily</option>
                                <option value="monthly" <?php echo $period === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                                <option value="yearly" <?php echo $period === 'yearly' ? 'selected' : ''; ?>>Yearly</option>
                                <option value="lifetime" <?php echo $period === 'lifetime' ? 'selected' : ''; ?>>Lifetime</option>
                                <option value="custom" <?php echo $period === 'custom' ? 'selected' : ''; ?>>Custom Range</option>
                            </select>
                            <div id="dateRange" class="d-none d-inline-block">
                                <input type="date" id="start_date" class="form-control form-control-sm d-inline-block w-auto" value="<?php echo $start_date; ?>">
                                <span class="mx-2">to</span>
                                <input type="date" id="end_date" class="form-control form-control-sm d-inline-block w-auto" value="<?php echo $end_date; ?>">
                            </div>
                            <button id="applyFilter" class="btn btn-sm btn-primary">Apply</button>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="text-primary mb-0">Total Sales: ₱<?php echo number_format($total_sales, 2); ?></h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Sale ID</th>
                                    <th scope="col">Cashier</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Amount Paid</th>
                                    <th scope="col">Payment Date</th>
                                    <th scope="col">Order Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($sales)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No completed orders found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($sales as $sale): ?>
                                        <tr>
                                            <td><?php echo isset($sale['sale_id']) ? $sale['sale_id'] : 'N/A'; ?></td>
                                            <td><?php echo isset($sale['admin_name']) ? htmlspecialchars($sale['admin_name']) : 'N/A'; ?></td>
                                            <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                                            <td>₱<?php echo isset($sale['amount_paid']) ? number_format($sale['amount_paid'], 2) : '0.00'; ?></td>
                                            <td><?php echo isset($sale['payment_date']) ? date('M d, Y', strtotime($sale['payment_date'])) : 'N/A'; ?></td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <?php echo ucfirst($sale['order_status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info view-details" data-order-id="<?php echo $sale['order_id']; ?>">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Sales End -->

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
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Services</h6>
                                    <table class="table table-bordered" id="servicesTable">
                                        <thead>
                                            <tr>
                                                <th>Service</th>
                                                <th>Qty</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Services will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-end">
                                    <button class="btn btn-primary" id="printReceipt">Print Receipt</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">DryMe</a>, All Rights Reserved.
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

    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function() {
            // Initialize with current period selection
            if ($('#period').val() === 'custom') {
                $('#dateRange').removeClass('d-none');
            }

            $('#period').change(function() {
                if ($(this).val() === 'custom') {
                    $('#dateRange').removeClass('d-none');
                } else {
                    $('#dateRange').addClass('d-none');
                }
            });

            // Apply filter
            $('#applyFilter').click(function() {
                let period = $('#period').val();
                let url = 'sales.php?period=' + period;

                if (period === 'custom') {
                    url += '&start_date=' + $('#start_date').val() + '&end_date=' + $('#end_date').val();
                }

                window.location.href = url;
            });

            // View order details
            $('.view-details').click(function() {
                let orderId = $(this).data('order-id');
                showOrderDetails(orderId);
            });

            // Print receipt
            $('#printReceipt').click(function() {
                const orderId = $('#currentOrderId').val();
                printReceipt(orderId);
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        function showOrderDetails(orderId) {
            $.ajax({
                url: 'helpers/get_order_details.php',
                type: 'GET',
                data: {
                    order_id: orderId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        alert(response.error);
                        return;
                    }

                    // Store the order ID for printing
                    if (!$('#currentOrderId').length) {
                        $('body').append('<input type="hidden" id="currentOrderId" value="' + orderId + '">');
                    } else {
                        $('#currentOrderId').val(orderId);
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

        function printReceipt(orderId) {
            var receiptWindow = window.open('helpers/print_receipt.php?order_id=' + orderId, '_blank', 'width=400,height=600');
            receiptWindow.focus();
        }

        // Helper function to format date
        function formatDate(dateString) {
            var date = new Date(dateString);
            var options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            return date.toLocaleDateString('en-US', options);
        }

        // Helper function to capitalize first letter
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    </script>
</body>

</html>