<?php
session_start();
require_once '../../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access");
}

// Check if order_id is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    die("Order ID is required");
}

$order_id = intval($_GET['order_id']);

try {
    // Get order details
    $stmt = $pdo->prepare("
        SELECT o.*, c.full_name, c.phone, c.email, c.address 
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        WHERE o.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        die("Order not found");
    }
    
    // Get order services
    $stmt = $pdo->prepare("
        SELECT od.*, s.service_name 
        FROM order_details od
        JOIN services s ON od.service_id = s.service_id
        WHERE od.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get detergent details if any
    $detergent = null;
    if (!empty($order['detergent_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM inventory WHERE item_id = ?");
        $stmt->execute([$order['detergent_id']]);
        $detergent = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Calculate total
    $total = 0;
    foreach ($services as $service) {
        $total += $service['subtotal'];
    }
    
    // Add delivery and pickup fees if applicable
    if ($order['delivery'] == 1) {
        $total += 25;
    }
    if ($order['pickup'] == 1) {
        $total += 25;
    }
    
    // Add detergent cost if applicable
    if ($detergent && $order['detergent_qty'] > 0) {
        $total += 10 * $order['detergent_qty'];
    }
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Order #<?php echo $order_id; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .receipt {
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
        }
        .customer-info, .order-info {
            margin-bottom: 20px;
        }
        .customer-info h2, .order-info h2, .services h2 {
            font-size: 14px;
            margin: 0 0 10px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            text-align: left;
            padding: 5px;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>DryMe Laundry</h1>
            <p>123 Laundry Street, Clean City</p>
            <p>Phone: (123) 456-7890</p>
            <p>Email: info@drymelaundry.com</p>
        </div>
        
        <div class="order-info">
            <h2>Order Information</h2>
            <p><strong>Order #:</strong> <?php echo $order_id; ?></p>
            <p><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
            <p><strong>Priority:</strong> <?php echo ucfirst($order['priority']); ?></p>
            <p><strong>Weight:</strong> <?php echo number_format($order['weight'], 2); ?> kg</p>
        </div>
        
        <div class="customer-info">
            <h2>Customer Information</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            <?php if (!empty($order['email'])): ?>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            <?php endif; ?>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
        </div>
        
        <div class="services">
            <h2>Services</h2>
            <table>
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Qty</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                        <td>N/A</td>
                        <td>N/A</td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php 
                    // Calculate base load price
                    $baseLoadPrice = $order['weight'] * 30;
                    ?>
                    <tr>
                        <td>Load Weight</td>
                        <td><?php echo number_format($order['weight'], 2); ?> kg</td>
                        <td>₱<?php echo number_format($baseLoadPrice, 2); ?></td>
                    </tr>
                    
                    <?php 
                    // Calculate priority fee
                    $priorityFee = 0;
                    if ($order['priority'] == 'express'): 
                        $priorityFee = $baseLoadPrice * 0.25;
                    ?>
                    <tr>
                        <td>Express Priority (25%)</td>
                        <td>1</td>
                        <td>₱<?php echo number_format($priorityFee, 2); ?></td>
                    </tr>
                    <?php elseif ($order['priority'] == 'rush'): 
                        $priorityFee = $baseLoadPrice * 0.5;
                    ?>
                    <tr>
                        <td>Rush Priority (50%)</td>
                        <td>1</td>
                        <td>₱<?php echo number_format($priorityFee, 2); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if ($order['delivery'] == 1): ?>
                    <tr>
                        <td>Delivery Service</td>
                        <td>1</td>
                        <td>₱25.00</td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if ($order['pickup'] == 1): ?>
                    <tr>
                        <td>Pickup Service</td>
                        <td>1</td>
                        <td>₱25.00</td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if ($detergent && $order['detergent_qty'] > 0): 
                        $detergentTotal = 10 * $order['detergent_qty'];
                    ?>
                    <tr>
                        <td>Detergent: <?php echo htmlspecialchars($detergent['name']); ?></td>
                        <td><?php echo $order['detergent_qty']; ?></td>
                        <td>₱<?php echo number_format($detergentTotal, 2); ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <?php
            // Calculate total from all services for consistency
            $calculatedTotal = $baseLoadPrice + $priorityFee;
            if ($order['delivery'] == 1) $calculatedTotal += 25.00;
            if ($order['pickup'] == 1) $calculatedTotal += 25.00;
            if ($detergent && $order['detergent_qty'] > 0) $calculatedTotal += (10.00 * $order['detergent_qty']);
            ?>
            
            <div class="total">
                <p>Total Amount: ₱<?php echo number_format($calculatedTotal, 2); ?></p>
            </div>
        </div>
        
        <div class="footer">
            <p>Thank you for choosing DryMe Laundry!</p>
            <p>Please bring this receipt when picking up your laundry.</p>
            <?php if (!empty($order['special_instructions'])): ?>
            <p><strong>Special Instructions:</strong> <?php echo htmlspecialchars($order['special_instructions']); ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print Receipt</button>
        <button onclick="window.close()">Close</button>
    </div>
</body>
</html>