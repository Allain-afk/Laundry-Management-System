<?php
session_start();
require_once '../../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die('Unauthorized access');
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    die('Order ID is required');
}

try {
    // Get order details with services
    $query = "SELECT o.*, c.full_name as customer_name, 
              GROUP_CONCAT(s.service_name SEPARATOR ', ') as services,
              GROUP_CONCAT(os.quantity SEPARATOR ', ') as quantities,
              GROUP_CONCAT(os.price SEPARATOR ', ') as prices
              FROM orders o
              INNER JOIN customers c ON o.customer_id = c.customer_id
              LEFT JOIN order_services os ON o.order_id = os.order_id
              LEFT JOIN services s ON os.service_id = s.service_id
              WHERE o.order_id = ?
              GROUP BY o.order_id";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_GET['order_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die('Order not found');
    }

    // Format the output
    ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
                <th width="30%">Order ID</th>
                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
            </tr>
            <tr>
                <th>Customer Name</th>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
            </tr>
            <tr>
                <th>Order Date</th>
                <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : 'primary'; ?>">
                    <?php echo ucfirst($order['status']); ?>
                </span></td>
            </tr>
            <tr>
                <th>Services</th>
                <td>
                    <?php 
                    if ($order['services']) {
                        $services = explode(', ', $order['services']);
                        $quantities = explode(', ', $order['quantities']);
                        $prices = explode(', ', $order['prices']);
                        
                        echo '<ul class="list-unstyled mb-0">';
                        for ($i = 0; $i < count($services); $i++) {
                            echo '<li>' . htmlspecialchars($services[$i]) . 
                                 ' (x' . htmlspecialchars($quantities[$i]) . ') - ₱' . 
                                 number_format($prices[$i], 2) . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo 'No services';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Total Amount</th>
                <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
            </tr>
            <tr>
                <th>Notes</th>
                <td><?php echo htmlspecialchars($order['notes'] ?? 'No notes'); ?></td>
            </tr>
        </table>
    </div>
    <?php

} catch (PDOException $e) {
    error_log("Error fetching order details: " . $e->getMessage());
    die('Error fetching order details');
}
?>