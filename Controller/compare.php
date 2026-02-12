<?php
// compare.php - Main entry point
session_start();

// 1. Smarty Setup and Path Fix
require_once '../Smarty/libs/Smarty.class.php';
require_once '../Model/db.php';

// Initialize Smarty
$smarty = new Smarty();
$smarty->setTemplateDir('../Views/');
$smarty->setCompileDir('../Templates_c/');
$smarty->setCacheDir('../Cache/');

// --- Business Logic for Compare Page (Add your logic here) ---
// Example: Fetch products to compare from session or database
$products_to_compare = []; 
// $products_to_compare = fetch_products_for_comparison($conn, $_SESSION['compare_list'] ?? []);

// 2. Assign data to Smarty
$smarty->assign('products', $products_to_compare);

// 3. Display the template
$smarty->display('compare.html');
?>