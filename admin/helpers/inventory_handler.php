<?php
session_start();
require_once '../../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_supply':
            try {
                $stmt = $pdo->prepare("INSERT INTO inventory (name, category, quantity, unit, minimum_stock) VALUES (?, 'supply', ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['quantity'],
                    $_POST['unit'],
                    $_POST['minimum_stock']
                ]);
                
                // Log the transaction
                $item_id = $pdo->lastInsertId();
                $stmt = $pdo->prepare("INSERT INTO inventory_transactions (item_id, transaction_type, quantity, created_by) VALUES (?, 'purchase', ?, ?)");
                $stmt->execute([$item_id, $_POST['quantity'], $_SESSION['admin_id']]);
                
                $response = ['success' => true, 'message' => 'Supply added successfully'];
            } catch (PDOException $e) {
                $response = ['success' => false, 'message' => 'Error adding supply'];
            }
            break;

        case 'add_equipment':
            try {
                $stmt = $pdo->prepare("INSERT INTO inventory (name, category, quantity, status, next_maintenance_date) VALUES (?, 'equipment', ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['quantity'],
                    $_POST['status'],
                    $_POST['next_maintenance_date']
                ]);
                
                $response = ['success' => true, 'message' => 'Equipment added successfully'];
            } catch (PDOException $e) {
                $response = ['success' => false, 'message' => 'Error adding equipment'];
            }
            break;

        case 'update_item':
            try {
                $stmt = $pdo->prepare("UPDATE inventory SET quantity = ? WHERE item_id = ?");
                $stmt->execute([$_POST['quantity'], $_POST['item_id']]);
                
                // Log the transaction
                $stmt = $pdo->prepare("INSERT INTO inventory_transactions (item_id, transaction_type, quantity, notes, created_by) VALUES (?, 'adjustment', ?, 'Manual adjustment', ?)");
                $stmt->execute([$_POST['item_id'], $_POST['quantity'], $_SESSION['admin_id']]);
                
                $response = ['success' => true, 'message' => 'Item updated successfully'];
            } catch (PDOException $e) {
                $response = ['success' => false, 'message' => 'Error updating item'];
            }
            break;

        case 'delete_item':
            try {
                // Get item details before deletion for logging
                $stmt = $pdo->prepare("SELECT * FROM inventory WHERE item_id = ?");
                $stmt->execute([$_POST['item_id']]);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$item) {
                    $response = ['success' => false, 'message' => 'Item not found'];
                    break;
                }
                
                // Delete the item
                $stmt = $pdo->prepare("DELETE FROM inventory WHERE item_id = ?");
                $stmt->execute([$_POST['item_id']]);
                
                // Log the transaction
                $stmt = $pdo->prepare("INSERT INTO inventory_transactions (item_id, transaction_type, quantity, notes, created_by) VALUES (?, 'deletion', ?, 'Item deleted', ?)");
                $stmt->execute([$_POST['item_id'], $item['quantity'], $_SESSION['admin_id']]);
                
                $response = ['success' => true, 'message' => 'Item deleted successfully'];
            } catch (PDOException $e) {
                $response = ['success' => false, 'message' => 'Error deleting item: ' . $e->getMessage()];
            }
            break;

        case 'get_item':
            try {
                $stmt = $pdo->prepare("SELECT * FROM inventory WHERE item_id = ?");
                $stmt->execute([$_POST['item_id']]);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$item) {
                    $response = ['success' => false, 'message' => 'Item not found'];
                } else {
                    $response = ['success' => true, 'item' => $item];
                }
            } catch (PDOException $e) {
                $response = ['success' => false, 'message' => 'Error fetching item: ' . $e->getMessage()];
            }
            break;

        case 'restock_item':
            try {
                // Get current item details
                $stmt = $pdo->prepare("SELECT * FROM inventory WHERE item_id = ?");
                $stmt->execute([$_POST['item_id']]);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$item) {
                    $response = ['success' => false, 'message' => 'Item not found'];
                    break;
                }
                
                // Update quantity
                $new_quantity = $item['quantity'] + $_POST['add_quantity'];
                $stmt = $pdo->prepare("UPDATE inventory SET quantity = ? WHERE item_id = ?");
                $stmt->execute([$new_quantity, $_POST['item_id']]);
                
                // Log the transaction
                $stmt = $pdo->prepare("INSERT INTO inventory_transactions (item_id, transaction_type, quantity, notes, created_by) VALUES (?, 'restock', ?, 'Item restocked', ?)");
                $stmt->execute([$_POST['item_id'], $_POST['add_quantity'], $_SESSION['admin_id']]);
                
                $response = ['success' => true, 'message' => 'Item restocked successfully'];
            } catch (PDOException $e) {
                $response = ['success' => false, 'message' => 'Error restocking item: ' . $e->getMessage()];
            }
            break;

        case 'schedule_maintenance':
            try {
                $stmt = $pdo->prepare("UPDATE inventory SET next_maintenance_date = ?, last_maintenance_date = CURRENT_DATE, status = 'maintenance' WHERE item_id = ?");
                $stmt->execute([$_POST['maintenance_date'], $_POST['item_id']]);
                
                // Log the maintenance
                $stmt = $pdo->prepare("INSERT INTO inventory_transactions (item_id, transaction_type, quantity, notes, created_by) VALUES (?, 'maintenance', 0, 'Scheduled maintenance', ?)");
                $stmt->execute([$_POST['item_id'], $_SESSION['admin_id']]);
                
                $response = ['success' => true, 'message' => 'Maintenance scheduled successfully'];
            } catch (PDOException $e) {
                $response = ['success' => false, 'message' => 'Error scheduling maintenance'];
            }
            break;

        case 'get_history':
            try {
                $stmt = $pdo->prepare("
                    SELECT t.*, a.username as admin_name 
                    FROM inventory_transactions t
                    LEFT JOIN admins a ON t.created_by = a.admin_id
                    WHERE t.item_id = ?
                    ORDER BY t.transaction_date DESC
                ");
                $stmt->execute([$_POST['item_id']]);
                $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $response = ['success' => true, 'data' => $history];
            } catch (PDOException $e) {
                $response = ['success' => false, 'message' => 'Error fetching history'];
            }
            break;
    }
}

echo json_encode($response);
?>