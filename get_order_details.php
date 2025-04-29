<?php
session_start();
require_once 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    echo json_encode(['error' => 'No order ID provided']);
    exit();
}

$order_id = $_GET['order_id'];

// Get order details
$stmt = $pdo->prepare("
    SELECT o.*, c.full_name, c.phone, c.address 
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    WHERE o.order_id = ? AND o.customer_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode(['error' => 'Order not found or access denied']);
    exit();
}

// Get order items
$stmt = $pdo->prepare("
    SELECT od.*, s.service_name
    FROM order_details od
    JOIN services s ON od.service_id = s.service_id
    WHERE od.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format dates
$order['created_at_formatted'] = date('F d, Y', strtotime($order['created_at']));
$order['pickup_date_formatted'] = $order['pickup_date'] ? date('F d, Y', strtotime($order['pickup_date'])) : 'Not specified';
$order['delivery_date_formatted'] = $order['delivery_date'] ? date('F d, Y', strtotime($order['delivery_date'])) : 'Not specified';

// Get status class
$status_class = '';
switch ($order['status']) {
    case 'pending':
        $status_class = 'warning';
        break;
    case 'processing':
        $status_class = 'info';
        break;
    case 'completed':
        $status_class = 'success';
        break;
    case 'cancelled':
        $status_class = 'danger';
        break;
    default:
        $status_class = 'secondary';
}

$order['status_class'] = $status_class;

// Return data
echo json_encode([
    'order' => $order,
    'items' => $items
]);