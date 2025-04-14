<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    try {
        $pdo->beginTransaction();

        // Calculate total amount based on weight and services
        $weight = $_POST['weight'];
        $base_price = 120; // Base price for first 7kg
        $extra_price = 5;  // Price per kg after 7kg
        $priority_multiplier = 1;

        // Set priority multiplier
        switch ($_POST['priority']) {
            case 'normal':
                $priority_multiplier = 1;
                break;
            case 'express':
                $priority_multiplier = 1.25; // 25% increase
                break;
            case 'extra_rush':
                $priority_multiplier = 1.5;   // 50% increase
                break;
        }

        // Calculate base amount
        if ($weight <= 7) {
            $total_amount = $base_price;
        } else {
            $extra_weight = $weight - 7;
            $total_amount = $base_price + ($extra_weight * $extra_price);
        }

        // Apply priority multiplier
        $total_amount *= $priority_multiplier;

        // Add delivery and pickup costs if selected
        if (isset($_POST['delivery'])) $total_amount += 25;
        if (isset($_POST['pickup'])) $total_amount += 25;

        // Insert into orders table
        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status, pickup_date, delivery, pickup, priority, weight, created_at) 
                              VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, NOW())");

        $stmt->execute([
            $_SESSION['user_id'],
            $total_amount,
            $_POST['pickup_date'],
            isset($_POST['delivery']) ? 1 : 0,
            isset($_POST['pickup']) ? 1 : 0,
            $_POST['priority'],
            $weight  // Add weight to be stored
        ]);

        $pdo->commit();
        $_SESSION['success'] = "Order placed successfully!";
        header("Location: ../pricing.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error creating order: " . $e->getMessage();
        header("Location: ../pricing.php");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>