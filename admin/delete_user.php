<?php
session_start();
require_once '../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: signin.php");
    exit();
}

// Check if customer ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Customer ID is required";
    header("Location: customers.php");
    exit();
}

$customer_id = $_GET['id'];

// Check if customer exists
$stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch();

if (!$customer) {
    $_SESSION['error'] = "Customer not found";
    header("Location: customers.php");
    exit();
}

// Check if customer has orders
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$order_count = $stmt->fetchColumn();

if ($order_count > 0) {
    $_SESSION['error'] = "Cannot delete customer with existing orders. Please delete or reassign the orders first.";
    header("Location: customers.php");
    exit();
}

try {
    // Delete customer
    $stmt = $pdo->prepare("DELETE FROM customers WHERE customer_id = ?");
    $stmt->execute([$customer_id]);
    
    $_SESSION['success'] = "Customer deleted successfully";
    header("Location: customers.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting customer: " . $e->getMessage();
    header("Location: customers.php");
    exit();
}