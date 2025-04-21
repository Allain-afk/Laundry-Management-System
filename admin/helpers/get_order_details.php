<?php
session_start();
require_once '../../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if order_id is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    echo json_encode(['error' => 'Order ID is required']);
    exit();
}

$order_id = intval($_GET['order_id']);

try {
    // Get order details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode(['error' => 'Order not found']);
        exit();
    }
    
    // Get customer details
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->execute([$order['customer_id']]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get order services
    $stmt = $pdo->prepare("
        SELECT od.*, s.service_name 
        FROM order_details od
        JOIN services s ON od.service_id = s.service_id
        WHERE od.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format prices and subtotals for display
    foreach ($services as &$service) {
        $service['price_formatted'] = 'N/A';
        $service['quantity_formatted'] = 'N/A';
    }
    
    // Get detergent details if any
    $detergent = null;
    if (!empty($order['detergent_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM inventory WHERE item_id = ?");
        $stmt->execute([$order['detergent_id']]);
        $detergent = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Format additional data for display
    $order['total_amount_formatted'] = number_format($order['total_amount'], 2);
    $order['weight_formatted'] = number_format($order['weight'], 2);
    
    // Calculate base load price
    $baseLoadPrice = $order['weight'] * 30;
    $order['load_weight_price'] = number_format($baseLoadPrice, 2);
    
    // Calculate additional services cost
    $additional_services = [];
    
    // Add load weight as a service
    $additional_services[] = [
        'name' => 'Load Weight',
        'price' => $baseLoadPrice,
        'price_formatted' => number_format($baseLoadPrice, 2),
        'quantity' => $order['weight'],
        'quantity_formatted' => number_format($order['weight'], 2) . ' kg'
    ];
    
    // Calculate and add priority fee if applicable
    $priorityFee = 0;
    if ($order['priority'] == 'express') {
        // Express is 25% of base price
        $priorityFee = $baseLoadPrice * 0.25;
        $additional_services[] = [
            'name' => 'Express Priority (25%)',
            'price' => $priorityFee,
            'price_formatted' => number_format($priorityFee, 2),
            'quantity' => 1,
            'quantity_formatted' => '1'
        ];
    } elseif ($order['priority'] == 'rush') {
        // Rush is 50% of base price
        $priorityFee = $baseLoadPrice * 0.5;
        $additional_services[] = [
            'name' => 'Rush Priority (50%)',
            'price' => $priorityFee,
            'price_formatted' => number_format($priorityFee, 2),
            'quantity' => 1,
            'quantity_formatted' => '1'
        ];
    }
    
    // Add delivery fee if applicable
    if ($order['delivery'] == 1) {
        $additional_services[] = [
            'name' => 'Delivery Service',
            'price' => 25.00,
            'price_formatted' => '25.00',
            'quantity' => 1,
            'quantity_formatted' => '1'
        ];
    }
    
    // Add pickup fee if applicable
    if ($order['pickup'] == 1) {
        $additional_services[] = [
            'name' => 'Pickup Service',
            'price' => 25.00,
            'price_formatted' => '25.00',
            'quantity' => 1,
            'quantity_formatted' => '1'
        ];
    }
    
    // Add detergent cost if applicable
    if ($detergent && $order['detergent_qty'] > 0) {
        $detergent_price = 10.00;
        $detergent_total = $detergent_price * $order['detergent_qty'];
        $additional_services[] = [
            'name' => 'Detergent: ' . $detergent['name'],
            'price' => $detergent_total,
            'price_formatted' => number_format($detergent_total, 2),
            'quantity' => $order['detergent_qty'],
            'quantity_formatted' => $order['detergent_qty']
        ];
    }

    // Calculate total from all services to verify it matches the stored total
    $calculatedTotal = $baseLoadPrice + $priorityFee;
    if ($order['delivery'] == 1) $calculatedTotal += 25.00;
    if ($order['pickup'] == 1) $calculatedTotal += 25.00;
    if ($detergent && $order['detergent_qty'] > 0) $calculatedTotal += (10.00 * $order['detergent_qty']);
    
    // Use the calculated total for consistency
    $order['calculated_total'] = $calculatedTotal;
    $order['calculated_total_formatted'] = number_format($calculatedTotal, 2);
    
    // Prepare response
    $response = [
        'order' => $order,
        'customer' => $customer,
        'services' => $services,
        'additional_services' => $additional_services,
        'detergent' => $detergent
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>