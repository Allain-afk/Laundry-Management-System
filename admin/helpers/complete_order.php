<?php
session_start();
require_once '../../includes/db_connect.php';

if (!isset($_SESSION['admin_id']) || !isset($_POST['order_id'])) {
    exit('Unauthorized access');
}

try {
    $pdo->beginTransaction();

    // Update order status
    $stmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE order_id = ?");
    $stmt->execute([$_POST['order_id']]);

    // Get order details for sales record
    $stmt = $pdo->prepare("SELECT customer_id, total_amount FROM orders WHERE order_id = ?");
    $stmt->execute([$_POST['order_id']]);
    $order = $stmt->fetch();

    // Insert sales record
    $stmt = $pdo->prepare("INSERT INTO sales_records (order_id, admin_id, customer_id, amount_paid) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['order_id'],
        $_SESSION['admin_id'],
        $order['customer_id'],
        $order['total_amount']
    ]);

    $pdo->commit();
    echo 'success';
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Error completing order: " . $e->getMessage());
    echo 'error';
}
?>