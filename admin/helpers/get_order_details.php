<?php
require_once '../../includes/db_connect.php';

if (!isset($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID is required']);
    exit;
}

try {
    $order_id = $_GET['order_id'];
    
    // Get order and customer details
    $stmt = $pdo->prepare("
        SELECT o.*, c.*
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        WHERE o.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get order items
    $stmt = $pdo->prepare("
        SELECT od.*, s.service_name
        FROM order_details od
        JOIN services s ON od.service_id = s.service_id
        WHERE od.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'customer' => [
            'full_name' => $order['full_name'],
            'phone' => $order['phone'],
            'email' => $order['email'],
            'address' => $order['address']
        ],
        'order' => [
            'special_instructions' => $order['special_instructions'],
            'status' => $order['status'],
            'total_amount' => $order['total_amount']
        ],
        'items' => $items
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error fetching order details']);
}
?>