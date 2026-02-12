<?php
/**
 * set_view_preference.php
 * 
 * AJAX endpoint to set user view preferences
 * Handles setting cookies and session variables for view preferences
 */

session_start();

// Get the action from query string
$action = isset($_GET['action']) ? $_GET['action'] : '';
$view = isset($_GET['view']) ? $_GET['view'] : 'mobile';

// Set response header
header('Content-Type: application/json');

try {
    if ($action === 'hide_splash') {
        // User clicked "Don't show again"
        setcookie('hide_mobile_splash', '1', time() + (30 * 24 * 60 * 60), '/');
        $_SESSION['hide_mobile_splash'] = '1';
        
        echo json_encode(['success' => true, 'message' => 'تم حفظ التفضيل']);
    } elseif ($view === 'desktop') {
        // User chose desktop version
        setcookie('preferred_view', 'desktop', time() + (365 * 24 * 60 * 60), '/');
        $_SESSION['preferred_view'] = 'desktop';
        
        echo json_encode(['success' => true, 'message' => 'تم التبديل إلى نسخة سطح المكتب']);
    } elseif ($view === 'mobile') {
        // User chose mobile version
        setcookie('preferred_view', 'mobile', time() + (365 * 24 * 60 * 60), '/');
        $_SESSION['preferred_view'] = 'mobile';
        
        echo json_encode(['success' => true, 'message' => 'تم التبديل إلى نسخة الهاتف']);
    } else {
        echo json_encode(['success' => false, 'message' => 'إجراء غير معروف']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()]);
}
?>
