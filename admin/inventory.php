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

// Fetch supplies inventory
$stmt = $pdo->prepare("SELECT * FROM inventory WHERE category = 'supply' ORDER BY name");
$stmt->execute();
$supplies = $stmt->fetchAll();

// Fetch equipment inventory
$stmt = $pdo->prepare("SELECT * FROM inventory WHERE category = 'equipment' ORDER BY name");
$stmt->execute();
$equipment = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Inventory - DryMe</title>
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
                    <a href="sales.php" class="nav-item nav-link"><i class="fa fa-money-bill-alt me-2"></i>Sales</a>
                    <a href="inventory.php" class="nav-item nav-link active"><i class="fa fa-boxes me-2"></i>Inventory</a>
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

            <!-- Supplies Table Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="bg-light rounded h-100 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="mb-0">Laundry Supplies</h6>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplyModal">
                                    <i class="fa fa-plus me-2"></i>Add New Supply
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Unit</th>
                                            <th scope="col">Minimum Stock</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($supplies as $supply): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($supply['name']); ?></td>
                                                <td><?php echo htmlspecialchars($supply['quantity']); ?></td>
                                                <td><?php echo htmlspecialchars($supply['unit']); ?></td>
                                                <td><?php echo htmlspecialchars($supply['minimum_stock']); ?></td>
                                                <td>
                                                    <?php if ($supply['quantity'] <= $supply['minimum_stock']): ?>
                                                        <span class="badge bg-danger">Low Stock</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">In Stock</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="editItem(<?php echo $supply['item_id']; ?>, 'supply')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-info" onclick="viewHistory(<?php echo $supply['item_id']; ?>)">
                                                        <i class="fa fa-history"></i>
                                                    </button>
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
            <!-- Supplies Table End -->

            <!-- Equipment Table Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="bg-light rounded h-100 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="mb-0">Equipment Inventory</h6>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                                    <i class="fa fa-plus me-2"></i>Add New Equipment
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Last Maintenance</th>
                                            <th scope="col">Next Maintenance</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($equipment as $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = [
                                                        'active' => 'bg-success',
                                                        'maintenance' => 'bg-warning',
                                                        'inactive' => 'bg-danger'
                                                    ][$item['status']] ?? 'bg-secondary';
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($item['status']); ?></span>
                                                </td>
                                                <td><?php echo htmlspecialchars($item['last_maintenance_date']); ?></td>
                                                <td><?php echo htmlspecialchars($item['next_maintenance_date']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="editItem(<?php echo $item['item_id']; ?>, 'equipment')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" onclick="scheduleMaintenance(<?php echo $item['item_id']; ?>)">
                                                        <i class="fa fa-tools"></i>
                                                    </button>
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
            <!-- Equipment Table End -->

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

    <!-- Add Supply Modal -->
    <div class="modal fade" id="addSupplyModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="fa fa-plus me-2"></i>Add New Supply</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addSupplyForm">
                    <div class="modal-body">
                        <div class="bg-light rounded h-100 p-4">
                            <div class="mb-3">
                                <label class="form-label">Supply Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" class="form-control" name="quantity" min="0" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Unit</label>
                                    <select class="form-select" name="unit" required>
                                        <option value="">Select Unit</option>
                                        <option value="pieces">Pieces</option>
                                        <option value="liters">Liters</option>
                                        <option value="kg">Kilograms</option>
                                        <option value="boxes">Boxes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Minimum Stock Level</label>
                                <input type="number" class="form-control" name="minimum_stock" min="0" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Supply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Equipment Modal -->
    <div class="modal fade" id="addEquipmentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="fa fa-plus me-2"></i>Add New Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addEquipmentForm">
                    <div class="modal-body">
                        <div class="bg-light rounded h-100 p-4">
                            <div class="mb-3">
                                <label class="form-label">Equipment Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" class="form-control" name="quantity" min="1" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="maintenance">Under Maintenance</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Next Maintenance Date</label>
                                <input type="date" class="form-control" name="next_maintenance_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Equipment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa fa-check-circle me-2"></i>Success</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <p class="mb-0" id="successMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
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
    <script>
        // Initialize all modals
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));

        function showSuccess(message) {
            document.getElementById('successMessage').textContent = message;
            successModal.show();
            setTimeout(() => {
                location.reload();
            }, 1500);
        }

        document.getElementById('addSupplyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_supply');

            fetch('helpers/inventory_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('addSupplyModal')).hide();
                        showSuccess('Supply added successfully!');
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
        });

        document.getElementById('addEquipmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_equipment');

            fetch('helpers/inventory_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('addEquipmentModal')).hide();
                        showSuccess('Equipment added successfully!');
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
        });

        // Set minimum date for maintenance date input
        document.querySelector('input[name="next_maintenance_date"]').min = new Date().toISOString().split('T')[0];
    </script>
</body>

</html>