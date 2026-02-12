<?php
session_start();
require_once '../Model/db.php';

// إذا كان هناك توكن تذكر، احذفه من قاعدة البيانات
if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    
    // حذف التوكن من قاعدة البيانات
    $deleteTokenQuery = "DELETE FROM remember_tokens WHERE token = ?";
    $deleteStmt = $conn->prepare($deleteTokenQuery);
    $deleteStmt->bind_param('s', $token);
    $deleteStmt->execute();
    
    // احذف الكوكي (قيمة فارغة وانتهاء في الماضي)
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    unset($_COOKIE['remember_token']);
}

// إذا كان المستخدم مسجل دخول، احذف جميع توكنات التذكر الخاصة به
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // حذف جميع توكنات المستخدم من قاعدة البيانات
    $deleteAllTokensQuery = "DELETE FROM remember_tokens WHERE user_id = ?";
    $deleteAllStmt = $conn->prepare($deleteAllTokensQuery);
    $deleteAllStmt->bind_param('i', $userId);
    $deleteAllStmt->execute();
}

// تفريغ متغيرات الجلسة المتعلقة بالمستخدم
unset($_SESSION["user_id"]);
unset($_SESSION["user_name"]);
unset($_SESSION["user_email"]);
unset($_SESSION["user_avatar"]);
unset($_SESSION["cart"]);
unset($_SESSION["wishlist"]);
unset($_SESSION["compare"]);

// تدمير الجلسة بالكامل
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
session_destroy();

// إعادة التوجيه إلى الصفحة الرئيسية
header("Location: Home.php");
exit;
?>