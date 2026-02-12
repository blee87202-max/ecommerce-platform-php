<?php
// checkout.php - Main entry point
session_start();

// 1. Smarty Setup and Path Fix
require_once '../Smarty/libs/Smarty.class.php';
require_once '../Model/db.php';

// Initialize Smarty
$smarty = new Smarty();
$smarty->setTemplateDir('../Views/');
$smarty->setCompileDir('../Templates_c/');
$smarty->setCacheDir('../Cache/');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout');
    exit;
}

// --- Business Logic for Checkout Page (Add your logic here) ---
// Example: Fetch user's cart items, shipping address, and calculate total
$user_id = $_SESSION['user_id'];
$cart_items = []; // Fetch cart items from DB
$total_amount = 0; // Calculate total

// 2. Assign data to Smarty
$smarty->assign('cart_items', $cart_items);
$smarty->assign('total_amount', $total_amount);

// 3. Display the template
$smarty->display('checkout.html');
?>