<?php
// register.php - Main entry point
session_start();
require_once '../Vendor/autoload.php'; // Assuming Smarty is loaded via Composer
require_once '../Smarty/libs/Smarty.class.php'; // Or direct Smarty inclusion
require_once '../Model/db.php'; // Database connection

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: Home.php');
    exit;
}

// Initialize Smarty
$smarty = new Smarty();
$smarty->setTemplateDir('../Views/');
$smarty->setCompileDir('../Templates_c/');
$smarty->setCacheDir('../Cache/');

// Display the template
$smarty->display('register.html');
?>