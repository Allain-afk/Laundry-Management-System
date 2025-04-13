<?php
session_start();
require_once '../../includes/db_connect.php';

if (!isset($_GET['order_id'])) {
    die('Order ID is required');
}

$order_id = $_GET['order_id'];

// Get order details
$stmt = $pdo->prepare("
    SELECT o.*, o.delivery, o.pickup, o.priority, c.full_name, c.phone, c.email, c.address 
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    WHERE o.order_id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

// Get order items
$stmt = $pdo->prepare("
    SELECT od.*, s.service_name, s.price
    FROM order_details od
    JOIN services s ON od.service_id = s.service_id
    WHERE od.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Receipt #<?php echo $order_id; ?></title>
    <style>
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 10px;
            }
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            color: #191C24;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            color: #009CFF;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .receipt-info {
            margin-bottom: 20px;
        }
        .customer-info {
            margin-bottom: 20px;
        }
        .items {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .items th, .items td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #757575;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="company-name">DryMe</h1>
        <p>Laundry Services</p>
    </div>

    <div class="receipt-info">
        <p><strong>Receipt #:</strong> <?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></p>
        <p><strong>Date:</strong> <?php echo date('M d, Y h:i A'); ?></p>
    </div>

    <div class="customer-info">
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Service</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['service_name']); ?></td>
                <td>₱<?php echo number_format($item['price'], 2); ?></td>
                <td>₱<?php echo number_format($item['subtotal'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin: 20px 0; border-top: 1px solid #ddd; padding-top: 10px;">
        <?php
        $LOAD_WEIGHT = 7;
        $BASE_LOAD_PRICE = 120;
        $EXTRA_KILO_PRICE = 5;
        $DELIVERY_FEE = 25;
        $PICKUP_FEE = 25;
        
        $weight = $items[0]['quantity'];
        $loadPrice = $BASE_LOAD_PRICE;
        if ($weight > $LOAD_WEIGHT) {
            $extraKilos = $weight - $LOAD_WEIGHT;
            $loadPrice += ($extraKilos * $EXTRA_KILO_PRICE);
        }
        ?>
        <p><strong>Total Load:</strong> <?php echo number_format($weight, 1); ?> kg (₱<?php echo number_format($loadPrice, 2); ?>)</p>
        <p style="font-size: 12px; color: #666; margin-left: 20px;">
            Base price for <?php echo $LOAD_WEIGHT; ?>kg: ₱<?php echo number_format($BASE_LOAD_PRICE, 2); ?><br>
            <?php if ($weight > $LOAD_WEIGHT): ?>
            Additional <?php echo number_format($extraKilos, 1); ?>kg × ₱<?php echo $EXTRA_KILO_PRICE; ?>: ₱<?php echo number_format($extraKilos * $EXTRA_KILO_PRICE, 2); ?>
            <?php endif; ?>
        </p>

        <?php if ($order['delivery'] || $order['pickup']): ?>
            <p><strong>Additional Services:</strong></p>
            <p style="font-size: 12px; color: #666; margin-left: 20px;">
                <?php if ($order['delivery']): ?>
                    Delivery Service: ₱<?php echo number_format($DELIVERY_FEE, 2); ?><br>
                <?php endif; ?>
                <?php if ($order['pickup']): ?>
                    Pickup Service: ₱<?php echo number_format($PICKUP_FEE, 2); ?><br>
                <?php endif; ?>
            </p>
        <?php endif; ?>
        
        <?php if ($order['priority'] !== 'normal'): ?>
            <p><strong>Priority Service (<?php 
                echo $order['priority'] === 'express' ? 'Express - 50%' : 'Rush - 100%'; 
            ?>):</strong> ₱<?php 
                $baseAmount = $order['total_amount'] / ($order['priority'] === 'express' ? 1.5 : 2);
                $priorityFee = $order['total_amount'] - $baseAmount;
                echo number_format($priorityFee, 2); 
            ?></p>
        <?php endif; ?>
    </div>

    <div class="total">
        <p>Total Amount: ₱<?php echo number_format($order['total_amount'], 2); ?></p>
    </div>

    <div class="footer">
        <p>Thank you for choosing DryMe!</p>
        <p>For inquiries, please call: (123) 456-7890</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print Receipt</button>
        <button onclick="window.close()">Close</button>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>