<?php
// order_success.php - Main Controller
session_start();
require_once '../Vendor/autoload.php'; // Assuming Smarty is loaded via Composer
require_once '../Smarty/libs/Smarty.class.php'; // Or direct Smarty inclusion
require_once '../Model/db.php'; // Corrected path for db.php (assuming it's in Model)

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=order_success');
    exit;
}

// Get order ID
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($order_id <= 0) {
    header('Location: my_orders.php?error=invalid_order');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Initialize OrderSuccess class
require_once '../Api/order_success_api.php'; // Corrected path for order_success_api.php (assuming it's in Api)
$orderSuccess = new OrderSuccess($conn, $user_id, $order_id);

// Get order data
$orderData = $orderSuccess->getOrderData();

// If order doesn't exist or doesn't belong to user, redirect
if (!$orderData || !$orderData['order']) {
    header('Location: my_orders.php?error=order_not_found');
    exit;
}

// Initialize Smarty
$smarty = new Smarty();
$smarty->setTemplateDir('../Views/');
$smarty->setCompileDir('../Templates_c/');
$smarty->setCacheDir('../Cache/');

// Assign data to Smarty
$smarty->assign('orderData', $orderData);

// Display the template
$smarty->display('order_success.tpl');
?>