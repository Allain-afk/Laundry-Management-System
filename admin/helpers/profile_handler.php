<?php
session_start();
require_once 'C:/xampp/htdocs/Laundry/includes/db_connect.php';  // Changed to absolute path

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../signin.php");  // Changed path
    exit();
}

// Get admin data from database
$stmt = $pdo->prepare("SELECT * FROM admins WHERE admin_id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        
        $stmt = $pdo->prepare("UPDATE admins SET full_name = ?, email = ?, username = ? WHERE admin_id = ?");
        $stmt->execute([$fullname, $email, $username, $_SESSION['admin_id']]);
        
        // Update session variables
        $_SESSION['admin_name'] = $username;
        $_SESSION['admin_fullname'] = $fullname;
        $_SESSION['admin_email'] = $email;
        
        $_SESSION['success_msg'] = "Profile updated successfully!";
        header("Location: /Laundry/admin/profile.php");
        exit();
    }
    
    if (isset($_FILES['profile_picture'])) {
        $file = $_FILES['profile_picture'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (in_array($file['type'], $allowed_types)) {
            $upload_dir = dirname(__DIR__) . '/img/profile/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $filename = 'admin_' . $_SESSION['admin_id'] . '_' . time() . '.jpg';
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                try {
                    $pdo->exec("ALTER TABLE admins ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255)");
                    
                    // Delete old profile picture if exists
                    if (!empty($admin['profile_picture'])) {
                        $old_file = $upload_dir . $admin['profile_picture'];
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                    
                    $stmt = $pdo->prepare("UPDATE admins SET profile_picture = ? WHERE admin_id = ?");
                    $stmt->execute([$filename, $_SESSION['admin_id']]);
                    
                    // Update session variable for profile picture
                    $_SESSION['admin_profile_picture'] = $filename;
                    
                    $_SESSION['success_msg'] = "Profile picture updated successfully!";
                } catch(PDOException $e) {
                    $_SESSION['error_msg'] = "Database error occurred.";
                }
            } else {
                $_SESSION['error_msg'] = "Failed to upload file. Please try again.";
            }
        } else {
            $_SESSION['error_msg'] = "Invalid file type! Please upload JPG, PNG or GIF.";
        }
        header("Location: /Laundry/admin/profile.php");
        exit();
    }
}
?>