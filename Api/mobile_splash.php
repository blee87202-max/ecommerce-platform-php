<?php
/**
 * mobile_splash.php
 * 
 * Mobile Splash Screen Controller / View
 * Handles the rendering of the mobile splash/welcome overlay for mobile users
 * 
 * Architecture: MVC Pattern
 * - This file acts as the Controller when called directly, or as a View when included.
 * - Template: Views/mobile_splash.html (Smarty)
 * - Styles: Assets/css/mobile_splash.css
 * - Scripts: Assets/js/mobile_splash.js
 */

// ===== Error Handling & Configuration =====
ini_set('display_errors', 0);
error_reporting(E_ALL);

// ===== Session Management (Only if not already started) =====
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===== Include Required Files =====
require_once __DIR__ . '/../Model/db.php';

// Check if Smarty is already available in the global scope (when included via Home.php)
if (!isset($smarty) || !($smarty instanceof Smarty)) {
    require_once __DIR__ . '/../Smarty/libs/Smarty.class.php';
    $smarty = new Smarty();
    // إعداد مسارات Smarty لتشمل مجلد Views
    $smarty->setTemplateDir(__DIR__ . '/../Views/');
    $smarty->setCompileDir(__DIR__ . '/../Controller/templates_c/');
    $smarty->setCacheDir(__DIR__ . '/../Controller/cache/');
    $smarty->setConfigDir(__DIR__ . '/../Controller/configs/');
} else {
    // إذا كان Smarty موجوداً بالفعل، نتأكد من إضافة مجلد Views للمسارات
    $smarty->addTemplateDir(__DIR__ . '/../Views/');
}

// ===== Prepare Splash Screen Data =====
$splashData = [
    'title'              => 'مرحباً بك في متجرنا!',
    'subtitle'           => 'تم تحسين الموقع خصيصاً لتجربة تسوق رائعة على هاتفك',
    'features'           => [
        'واجهة محسّنة للهاتف المحمول',
        'تصفح سريع وسهل للمنتجات',
        'عملية شراء آمنة وموثوقة',
        'دعم العملاء على مدار الساعة'
    ],
    'btn_primary'        => 'ابدأ التسوق الآن',
    'btn_secondary'      => 'عرض نسخة سطح المكتب',
    'checkbox_label'     => 'لا تظهر هذه الرسالة مرة أخرى'
];

// ===== Assign Data to Template =====
$smarty->assign('splash', $splashData);

// ===== Render Logic =====
$format = isset($_GET['format']) ? $_GET['format'] : 'html';

?>

<!-- Mobile Splash Assets -->
<link rel="stylesheet" href="../Assets/css/mobile_splash.css">
<script src="../Assets/js/mobile_splash.js" defer></script>

<?php
if ($format === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'html'    => $smarty->fetch('mobile_splash.html')
    ], JSON_UNESCAPED_UNICODE);
} else {
    // Render the template from the Views directory
    $smarty->display('mobile_splash.html');
}
?>