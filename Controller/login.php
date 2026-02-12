<?php
// login.php - Main entry point
session_start();
require_once '../Vendor/autoload.php'; // Assuming Smarty is loaded via Composer
require_once '../Smarty/libs/Smarty.class.php'; // Or direct Smarty inclusion
require_once '../Model/db.php'; // Database connection

// Check if user is already logged in via session
if (isset($_SESSION['user_id'])) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'Home.php';
    header('Location: ' . $redirect);
    exit;
}

// Check remember token auto-login
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    
    $query = "SELECT u.id, u.name, u.email, u.avatar 
              FROM users u
              JOIN remember_tokens rt ON u.id = rt.user_id
              WHERE rt.token = ? AND rt.expires_at > NOW() 
              LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Regenerate session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_avatar'] = $row['avatar'] ?: 'default_avatar.png';
        
        // Update last login
        $updateQuery = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param('i', $row['id']);
        $updateStmt->execute();
        
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'Home.php';
        header('Location: ' . $redirect);
        exit;
    } else {
        // Invalid token, remove cookie
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
}

// Initialize Smarty
$smarty = new Smarty();
$smarty->setTemplateDir('../Views/');
$smarty->setCompileDir('../Templates_c/');
$smarty->setCacheDir('../Cache/');

// Display the template
$smarty->display('login.html');
?>