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

// Get dashboard statistics
try {
    // First verify database connection
    if (!$pdo) {
        throw new PDOException("Database connection failed");
    }

    // Total customers - count all customers
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM customers");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_customers = $result['total'] ?? 0;

    // Total transactions - count all orders
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_transactions = $result['total'] ?? 0;

    // Monthly sales - sum of completed orders for current month using sales_records table
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(sr.amount_paid), 0) as total 
                          FROM sales_records sr
                          INNER JOIN orders o ON sr.order_id = o.order_id
                          WHERE MONTH(sr.payment_date) = MONTH(CURRENT_DATE()) 
                          AND YEAR(sr.payment_date) = YEAR(CURRENT_DATE())
                          AND o.status = 'completed'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $monthly_sales = $result['total'] ?? 0;

    // Total revenue - sum of all completed orders (lifetime sales)
    $stmt = $pdo->query("SELECT COALESCE(SUM(sr.amount_paid), 0) as total 
                        FROM sales_records sr
                        INNER JOIN orders o ON sr.order_id = o.order_id
                        WHERE o.status = 'completed'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_revenue = $result['total'] ?? 0;

    // Debug output (remove in production)
    error_log("Customers: " . $total_customers);
    error_log("Transactions: " . $total_transactions);
    error_log("Monthly Sales: " . $monthly_sales);
    error_log("Total Revenue: " . $total_revenue);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    // Set default values if queries fail
    $total_customers = 0;
    $total_transactions = 0;
    $monthly_sales = 0;
    $total_revenue = 0;
}

$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';
$admin_fullname = isset($_SESSION['admin_fullname']) ? $_SESSION['admin_fullname'] : $admin_name;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DASH - DryMe</title>
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
                    <a href="index.php" class="nav-item nav-link active"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a href="customers.php" class="nav-item nav-link"><i class="fa fa-users me-2"></i>Customers</a>
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
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link" data-bs-toggle="dropdown">
                            <i class="fa fa-envelope me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Message</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <div class="ms-2">
                                        <span class="fw-normal mb-0">No messages yet</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link" data-bs-toggle="dropdown">
                            <i class="fa fa-bell me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Notifications</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">
                                <span class="fw-normal mb-0">No notifications yet</span>
                            </a>
                        </div>
                    </div>
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


            <!-- Sale & Revenue Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-users fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Total Customers</p>
                                <h6 class="mb-0"><?php echo number_format($total_customers); ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-shopping-cart fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Total Transactions</p>
                                <h6 class="mb-0"><?php echo number_format($total_transactions); ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-money-bill-alt fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Monthly Sales</p>
                                <h6 class="mb-0">₱<?php echo number_format($monthly_sales, 2); ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-pie fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Total Revenue</p>
                                <h6 class="mb-0">₱<?php echo number_format($total_revenue, 2); ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sale & Revenue End -->

            <!-- Recent Sales Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Recent Sales</h6>
                        <a href="sales.php">Show All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-dark">
                                    <th scope="col">Date</th>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch recent sales (last 5 completed orders)
                                try {
                                    $stmt = $pdo->prepare("SELECT o.order_id, o.created_at, o.total_amount, o.status, 
                                                          c.full_name as customer_name
                                                          FROM orders o
                                                          INNER JOIN customers c ON o.customer_id = c.customer_id
                                                          WHERE o.status = 'completed'
                                                          ORDER BY o.created_at DESC LIMIT 5");
                                    $stmt->execute();
                                    $recent_sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if (empty($recent_sales)) {
                                        echo '<tr><td colspan="6" class="text-center">No recent sales found</td></tr>';
                                    } else {
                                        foreach ($recent_sales as $sale) {
                                            echo '<tr>';
                                            echo '<td>' . date('d M Y', strtotime($sale['created_at'])) . '</td>';
                                            echo '<td>#' . $sale['order_id'] . '</td>';
                                            echo '<td>' . htmlspecialchars($sale['customer_name']) . '</td>';
                                            echo '<td>₱' . number_format($sale['total_amount'], 2) . '</td>';
                                            echo '<td><span class="badge bg-success">' . ucfirst($sale['status']) . '</span></td>';
                                            echo '<td><a class="btn btn-sm btn-primary" href="orders.php?view=' . $sale['order_id'] . '">Detail</a></td>';
                                            echo '</tr>';
                                        }
                                    }
                                } catch (PDOException $e) {
                                    echo '<tr><td colspan="6" class="text-center">Error loading recent sales</td></tr>';
                                    error_log("Recent sales query error: " . $e->getMessage());
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Recent Sales End -->

            <!-- Widgets Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    
                    <div class="col-sm-12 col-md-6 col-xl-6">
                        <div class="h-100 bg-light rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Pending Orders</h6>
                                <a href="orders.php?status=pending">Show All</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch pending orders
                                        try {
                                            $stmt = $pdo->prepare("SELECT o.order_id, o.status, o.priority, o.created_at, 
                                                                  c.full_name as customer_name
                                                                  FROM orders o
                                                                  INNER JOIN customers c ON o.customer_id = c.customer_id
                                                                  WHERE o.status IN ('pending', 'processing')
                                                                  ORDER BY 
                                                                    CASE 
                                                                        WHEN o.priority = 'high' THEN 1
                                                                        WHEN o.priority = 'medium' THEN 2
                                                                        ELSE 3
                                                                    END,
                                                                    o.created_at ASC
                                                                  LIMIT 5");
                                            $stmt->execute();
                                            $pending_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            
                                            if (empty($pending_orders)) {
                                                echo '<tr><td colspan="5" class="text-center">No pending orders</td></tr>';
                                            } else {
                                                foreach ($pending_orders as $order) {
                                                    $priority_badge = 'bg-info';
                                                    if ($order['priority'] == 'high') {
                                                        $priority_badge = 'bg-danger';
                                                    } else if ($order['priority'] == 'medium') {
                                                        $priority_badge = 'bg-warning';
                                                    }
                                                    
                                                    $status_badge = 'bg-warning';
                                                    if ($order['status'] == 'processing') {
                                                        $status_badge = 'bg-primary';
                                                    }
                                                    
                                                    echo '<tr>';
                                                    echo '<td>#' . $order['order_id'] . '</td>';
                                                    echo '<td>' . htmlspecialchars($order['customer_name']) . '</td>';
                                                    echo '<td><span class="badge ' . $status_badge . '">' . ucfirst($order['status']) . '</span></td>';
                                                    echo '<td><span class="badge ' . $priority_badge . '">' . ucfirst($order['priority']) . '</span></td>';
                                                    echo '<td><a class="btn btn-sm btn-outline-primary" href="orders.php?view=' . $order['order_id'] . '">View</a></td>';
                                                    echo '</tr>';
                                                }
                                            }
                                        } catch (PDOException $e) {
                                            echo '<tr><td colspan="5" class="text-center">Error loading pending orders</td></tr>';
                                            error_log("Pending orders query error: " . $e->getMessage());
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-12 col-md-6 col-xl-6">
                        <div class="h-100 bg-light rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Low Stock Alerts</h6>
                                <a href="inventory.php">Manage Inventory</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Category</th>
                                            <th>Current Stock</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch low stock items
                                        try {
                                            $stmt = $pdo->prepare("SELECT item_id, name, category, quantity, minimum_stock 
                                                                  FROM inventory 
                                                                  WHERE quantity <= minimum_stock 
                                                                  ORDER BY (quantity/minimum_stock) ASC 
                                                                  LIMIT 5");
                                            $stmt->execute();
                                            $low_stock = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            
                                            if (empty($low_stock)) {
                                                echo '<tr><td colspan="4" class="text-center">No low stock items</td></tr>';
                                            } else {
                                                foreach ($low_stock as $item) {
                                                    $stock_ratio = $item['quantity'] / max(1, $item['minimum_stock']);
                                                    $status_badge = 'bg-warning';
                                                    $status_text = 'Low Stock';
                                                    
                                                    if ($stock_ratio <= 0.25) {
                                                        $status_badge = 'bg-danger';
                                                        $status_text = 'Critical';
                                                    } else if ($stock_ratio > 0.75) {
                                                        $status_badge = 'bg-info';
                                                        $status_text = 'Reorder Soon';
                                                    }
                                                    
                                                    echo '<tr>';
                                                    echo '<td>' . htmlspecialchars($item['name']) . '</td>';
                                                    echo '<td>' . ucfirst($item['category']) . '</td>';
                                                    echo '<td>' . $item['quantity'] . '</td>';
                                                    echo '<td><span class="badge ' . $status_badge . '">' . $status_text . '</span></td>';
                                                    echo '</tr>';
                                                }
                                            }
                                        } catch (PDOException $e) {
                                            echo '<tr><td colspan="4" class="text-center">Error loading inventory data</td></tr>';
                                            error_log("Low stock query error: " . $e->getMessage());
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Widgets End -->


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
</body>

</html>