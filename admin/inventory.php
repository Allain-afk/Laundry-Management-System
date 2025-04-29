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
                <a href="index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-tint me-2"></i>DryMe</h3>
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
                                                    <button class="btn btn-sm btn-primary" onclick="editItem(<?php echo $supply['item_id']; ?>, 'supply')" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-info" onclick="viewHistory(<?php echo $supply['item_id']; ?>)" title="View History">
                                                        <i class="fa fa-history"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-success" onclick="restockItem(<?php echo $supply['item_id']; ?>, 'supply')" title="Restock">
                                                        <i class="fa fa-plus-circle"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteItem(<?php echo $supply['item_id']; ?>, 'supply', '<?php echo addslashes($supply['name']); ?>')" title="Delete">
                                                        <i class="fa fa-trash"></i>
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
                                                    <button class="btn btn-sm btn-primary" onclick="editItem(<?php echo $item['item_id']; ?>, 'equipment')" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" onclick="scheduleMaintenance(<?php echo $item['item_id']; ?>)" title="Schedule Maintenance">
                                                        <i class="fa fa-tools"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-success" onclick="restockItem(<?php echo $item['item_id']; ?>, 'equipment')" title="Restock">
                                                        <i class="fa fa-plus-circle"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteItem(<?php echo $item['item_id']; ?>, 'equipment', '<?php echo addslashes($item['name']); ?>')" title="Delete">
                                                        <i class="fa fa-trash"></i>
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
    
    <!-- Edit Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-edit me-2"></i>Edit Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editItemForm">
                    <div class="modal-body">
                        <div class="bg-light rounded h-100 p-4">
                            <div class="mb-3">
                                <label class="form-label">Item Name</label>
                                <input type="text" class="form-control" name="name" id="editItemName" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" name="quantity" id="editItemQuantity" min="0" step="0.01" required>
                            </div>
                            <div id="editSupplyFields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Unit</label>
                                    <select class="form-select" name="unit" id="editItemUnit">
                                        <option value="pieces">Pieces</option>
                                        <option value="liters">Liters</option>
                                        <option value="kg">Kilograms</option>
                                        <option value="boxes">Boxes</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Minimum Stock Level</label>
                                    <input type="number" class="form-control" name="minimum_stock" id="editMinimumStock" min="0" step="0.01">
                                </div>
                            </div>
                            <div id="editEquipmentFields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status" id="editItemStatus">
                                        <option value="active">Active</option>
                                        <option value="maintenance">Under Maintenance</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Next Maintenance Date</label>
                                    <input type="date" class="form-control" name="next_maintenance_date" id="editNextMaintenanceDate">
                                </div>
                            </div>
                            <input type="hidden" name="item_id" id="editItemId">
                            <input type="hidden" name="category" id="editItemCategory">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fa fa-trash me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <p class="mb-0">Are you sure you want to delete <strong id="deleteItemName"></strong>?</p>
                    <p class="text-danger mt-2"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Restock Modal -->
    <div class="modal fade" id="restockModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa fa-plus-circle me-2"></i>Restock Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="restockForm">
                    <div class="modal-body">
                        <div class="bg-light rounded h-100 p-4">
                            <div class="mb-3">
                                <label class="form-label">Item Name</label>
                                <input type="text" class="form-control" id="restockItemName" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Current Quantity</label>
                                <input type="text" class="form-control" id="currentQuantity" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Add Quantity</label>
                                <input type="number" class="form-control" name="add_quantity" min="1" required>
                            </div>
                            <input type="hidden" name="item_id" id="restockItemId">
                            <input type="hidden" name="category" id="restockCategory">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Restock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize modals (keeping only the ones we still need)
        const deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        const restockModal = new bootstrap.Modal(document.getElementById('restockModal'));

        // Function to show success message with SweetAlert2
        function showSuccess(message) {
            Swal.fire({
                title: 'Success!',
                text: message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
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
                        Swal.fire('Error!', data.message, 'error');
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
                        Swal.fire('Error!', data.message, 'error');
                    }
                });
        });

        // Delete item function with SweetAlert2
        function deleteItem(itemId, category, itemName) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete ${itemName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'delete_item');
                    formData.append('item_id', itemId);
                    formData.append('category', category);

                    fetch('helpers/inventory_handler.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showSuccess(itemName + ' deleted successfully!');
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        });
                }
            });
        }

        // Restock item function with SweetAlert2
        function restockItem(itemId, category) {
            const formData = new FormData();
            formData.append('action', 'get_item');
            formData.append('item_id', itemId);
            formData.append('category', category);

            fetch('helpers/inventory_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = data.item;
                        const currentQty = item.quantity + ' ' + (item.unit || '');
                        
                        Swal.fire({
                            title: 'Restock ' + item.name,
                            html: `
                                <div class="mb-3">
                                    <label class="form-label">Current Quantity</label>
                                    <input type="text" class="form-control" id="swal-current-quantity" value="${currentQty}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Add Quantity</label>
                                    <input type="number" class="form-control" id="swal-add-quantity" min="0.01" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" id="swal-restock-notes" rows="2"></textarea>
                                </div>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Restock',
                            focusConfirm: false,
                            preConfirm: () => {
                                const addQuantity = document.getElementById('swal-add-quantity').value;
                                const notes = document.getElementById('swal-restock-notes').value;
                                
                                if (!addQuantity) {
                                    Swal.showValidationMessage('Please enter quantity');
                                    return false;
                                }
                                
                                return { addQuantity, notes };
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const formData = new FormData();
                                formData.append('action', 'restock_item');
                                formData.append('item_id', itemId);
                                formData.append('category', category);
                                formData.append('quantity', result.value.addQuantity);
                                formData.append('notes', result.value.notes);
                                
                                fetch('helpers/inventory_handler.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        showSuccess('Item restocked successfully!');
                                    } else {
                                        Swal.fire('Error!', data.message, 'error');
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                });
        }

        // We no longer need the restock form submission handler as it's handled in the restockItem function

        // Set minimum date for maintenance date input
        document.querySelector('input[name="next_maintenance_date"]').min = new Date().toISOString().split('T')[0];
        
        // Edit item function with SweetAlert2
        function editItem(itemId, category) {
            const formData = new FormData();
            formData.append('action', 'get_item');
            formData.append('item_id', itemId);
            formData.append('category', category);

            fetch('helpers/inventory_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = data.item;
                        
                        let htmlContent = '';
                        if (category === 'supply') {
                            htmlContent = `
                                <input type="hidden" id="swal-item-id" value="${item.item_id}">
                                <input type="hidden" id="swal-item-category" value="${category}">
                                <div class="mb-3">
                                    <label class="form-label">Supply Name</label>
                                    <input type="text" class="form-control" id="swal-item-name" value="${item.name}" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" class="form-control" id="swal-item-quantity" value="${item.quantity}" min="0" step="0.01" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Unit</label>
                                        <select class="form-select" id="swal-item-unit" required>
                                            <option value="pieces" ${item.unit === 'pieces' ? 'selected' : ''}>Pieces</option>
                                            <option value="liters" ${item.unit === 'liters' ? 'selected' : ''}>Liters</option>
                                            <option value="kg" ${item.unit === 'kg' ? 'selected' : ''}>Kilograms</option>
                                            <option value="boxes" ${item.unit === 'boxes' ? 'selected' : ''}>Boxes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Minimum Stock Level</label>
                                    <input type="number" class="form-control" id="swal-minimum-stock" value="${item.minimum_stock}" min="0" step="0.01" required>
                                </div>
                            `;
                        } else {
                            htmlContent = `
                                <input type="hidden" id="swal-item-id" value="${item.item_id}">
                                <input type="hidden" id="swal-item-category" value="${category}">
                                <div class="mb-3">
                                    <label class="form-label">Equipment Name</label>
                                    <input type="text" class="form-control" id="swal-item-name" value="${item.name}" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" class="form-control" id="swal-item-quantity" value="${item.quantity}" min="1" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" id="swal-item-status" required>
                                            <option value="active" ${item.status === 'active' ? 'selected' : ''}>Active</option>
                                            <option value="maintenance" ${item.status === 'maintenance' ? 'selected' : ''}>Under Maintenance</option>
                                            <option value="inactive" ${item.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Next Maintenance Date</label>
                                    <input type="date" class="form-control" id="swal-next-maintenance-date" value="${item.next_maintenance_date}" required>
                                </div>
                            `;
                        }
                        
                        Swal.fire({
                            title: 'Edit ' + (category === 'supply' ? 'Supply' : 'Equipment'),
                            html: htmlContent,
                            showCancelButton: true,
                            confirmButtonText: 'Save Changes',
                            focusConfirm: false,
                            preConfirm: () => {
                                const itemData = {
                                    item_id: document.getElementById('swal-item-id').value,
                                    category: document.getElementById('swal-item-category').value,
                                    name: document.getElementById('swal-item-name').value,
                                    quantity: document.getElementById('swal-item-quantity').value
                                };
                                
                                if (category === 'supply') {
                                    itemData.unit = document.getElementById('swal-item-unit').value;
                                    itemData.minimum_stock = document.getElementById('swal-minimum-stock').value;
                                } else {
                                    itemData.status = document.getElementById('swal-item-status').value;
                                    itemData.next_maintenance_date = document.getElementById('swal-next-maintenance-date').value;
                                }
                                
                                if (!itemData.name || !itemData.quantity) {
                                    Swal.showValidationMessage('Please fill all required fields');
                                    return false;
                                }
                                
                                return itemData;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const formData = new FormData();
                                formData.append('action', 'update_item');
                                
                                // Add all form data
                                Object.keys(result.value).forEach(key => {
                                    formData.append(key, result.value[key]);
                                });
                                
                                fetch('helpers/inventory_handler.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        showSuccess('Item updated successfully!');
                                    } else {
                                        Swal.fire('Error!', data.message, 'error');
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                });
        }
        
        // We no longer need the edit form submission handler as it's handled in the editItem function
        
        // Function to view item history with SweetAlert2
        function viewHistory(itemId) {
            const formData = new FormData();
            formData.append('action', 'get_history');
            formData.append('item_id', itemId);

            fetch('helpers/inventory_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Implementation for viewing history can be added here
                        Swal.fire({
                            title: 'Item History',
                            text: 'History feature will be implemented soon!',
                            icon: 'info'
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                });
        }
        
        // Function to schedule maintenance with SweetAlert2
        function scheduleMaintenance(itemId) {
            // Implementation for scheduling maintenance can be added here
            Swal.fire({
                title: 'Schedule Maintenance',
                text: 'Maintenance scheduling feature will be implemented soon!',
                icon: 'info'
            });
        }
    </script>
</body>

</html>