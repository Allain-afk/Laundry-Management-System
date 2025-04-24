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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DryMe - Customers</title>
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
                    <a href="customers.php" class="nav-item nav-link active"><i class="fa fa-users me-2"></i>Customers</a>
                    <a href="orders.php" class="nav-item nav-link"><i class="fa fa-shopping-cart me-2"></i>Orders</a>
                    <a href="sales.php" class="nav-item nav-link"><i class="fa fa-money-bill-alt me-2"></i>Sales</a>
                    <a href="inventory.php" class="nav-item nav-link"><i class="fa fa-boxes me-2"></i>Inventory</a>
                    <a href="profile.php" class="nav-item nav-link"><i class="fa fa-user-circle me-2"></i>Admin Profile</a>
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
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <form class="d-none d-md-flex ms-4" id="searchForm">
                    <input class="form-control border-0" type="search" placeholder="Search customer..." id="searchInput">
                </form>
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


            <!-- Table Start -->
            <div class="container-fluid pt-4 px-4">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <div class="row g-4">
                    <div class="col-12">
                        <div class="bg-light rounded h-100 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="mb-0">Registered Users</h6>
                                <a href="add_customer.php" class="btn btn-primary"><i class="fa fa-plus me-2"></i>Add New Customer</a>
                            </div>
                            
                            <div class="mb-3">
                                <input type="text" class="form-control bg-white" id="customerSearch" placeholder="Search customers..." style="max-width: 300px;">
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Full Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">Address</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once '../includes/db_connect.php';
                                        
                                        try {
                                            // Pagination setup
                                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                            $records_per_page = 10;
                                            $offset = ($page - 1) * $records_per_page;
                                            
                                            // Count total records
                                            $total_records_stmt = $pdo->query("SELECT COUNT(*) FROM customers");
                                            $total_records = $total_records_stmt->fetchColumn();
                                            $total_pages = ceil($total_records / $records_per_page);
                                            
                                            // Get records with pagination
                                            $stmt = $pdo->prepare("SELECT * FROM customers ORDER BY customer_id DESC LIMIT :offset, :records_per_page");
                                            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                                            $stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
                                            $stmt->execute();
                                            
                                            if ($stmt->rowCount() > 0) {
                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<tr class='customer-row'>";
                                                    echo "<td>" . htmlspecialchars($row['customer_id']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                                                    echo "<td>" . htmlspecialchars(date('M d, Y', strtotime($row['created_at']))) . "</td>";
                                                    echo "<td>
                                                            <button class='btn btn-sm btn-info view-btn' data-id='" . $row['customer_id'] . "' data-bs-toggle='tooltip' title='View Details'><i class='fa fa-eye'></i></button>
                                                            <button class='btn btn-sm btn-primary edit-btn' data-id='" . $row['customer_id'] . "' data-bs-toggle='tooltip' title='Edit'><i class='fa fa-edit'></i></button>
                                                            <button class='btn btn-sm btn-danger delete-btn' data-id='" . $row['customer_id'] . "' data-name='" . htmlspecialchars($row['full_name']) . "' data-bs-toggle='tooltip' title='Delete'><i class='fa fa-trash'></i></button>
                                                          </td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='7' class='text-center'>No customers found</td></tr>";
                                            }
                                        } catch(PDOException $e) {
                                            echo "<tr><td colspan='7' class='text-center text-danger'>Error: " . $e->getMessage() . "</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if (isset($total_pages) && $total_pages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Table End -->


            <!-- Footer Start -->
            <div class="container-fluid pt-4 px-4" style="position: fixed; bottom: 0; left: 0; right: 0; background: white;">
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
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        
        // Customer search functionality
        document.getElementById('customerSearch').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.customer-row');

            tableRows.forEach(row => {
                let text = '';
                row.querySelectorAll('td').forEach(cell => {
                    text += cell.textContent.toLowerCase() + ' ';
                });

                if (text.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // View customer details
        document.querySelectorAll('.view-btn').forEach(button => {
            button.addEventListener('click', function() {
                const customerId = this.getAttribute('data-id');
                // Redirect to customer details page or show modal with AJAX
                window.location.href = 'view_customer.php?id=' + customerId;
            });
        });
        
        // Edit customer
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const customerId = this.getAttribute('data-id');
                window.location.href = 'edit_user.php?id=' + customerId;
            });
        });
        
        // Delete customer with SweetAlert2 confirmation
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const customerId = this.getAttribute('data-id');
                const customerName = this.getAttribute('data-name');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete customer: ${customerName}. This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'delete_user.php?id=' + customerId;
                    }
                });
            });
        });
        
        // Display SweetAlert2 for success/error messages if needed
        <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo addslashes($_SESSION['success']); ?>',
            icon: 'success',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            title: 'Error!',
            text: '<?php echo addslashes($_SESSION['error']); ?>',
            icon: 'error',
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>

</html>