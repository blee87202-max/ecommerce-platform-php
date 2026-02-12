<?php
/* Smarty version 4.3.4, created on 2026-01-19 15:24:04
  from 'C:\wamp64\www\ecommerce_project\Views\cart.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.4',
  'unifunc' => 'content_696e4c946fd804_03144766',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '434153d39b0ca2016c8e36ec53a3c6ad63e8183b' => 
    array (
      0 => 'C:\\wamp64\\www\\ecommerce_project\\Views\\cart.html',
      1 => 1768835577,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_696e4c946fd804_03144766 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛒 سلة المشتريات</title>
    <link rel="stylesheet" href="../Assets/css/cart.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap">
</head>
<body>
    <div class="cart-wrapper">
        <!-- Header -->
        <header class="cart-header">
            <h1><span class="cart-icon">🛒</span> سلة المشتريات</h1>
            <div class="user-info" id="user-info">
                <span class="loading-text">جاري تحميل...</span>
            </div>
        </header>

        <!-- Main Container -->
        <main class="cart-main-container">
            <!-- Loading State -->
            <div class="loading-section" id="loading-section">
                <div class="spinner-container">
                    <div class="spinner"></div>
                    <p>جاري تحميل سلة المشتريات...</p>
                </div>
            </div>

            <!-- Error State -->
            <div class="error-section" id="error-section" style="display: none;">
                <div class="error-content">
                    <span class="error-icon">⚠️</span>
                    <h3>حدث خطأ</h3>
                    <p id="error-message"></p>
                    <button class="retry-btn" onclick="CartUI.retryLoading()">إعادة المحاولة</button>
                </div>
            </div>

            <!-- Empty Cart -->
            <div class="empty-cart-section" id="empty-cart-section" style="display: none;">
                <div class="empty-cart-content">
                    <span class="empty-icon">🛒</span>
                    <h2>سلة المشتريات فارغة</h2>
                    <p>لم تقم بإضافة أي منتجات إلى السلة بعد</p>
                    <a href="Home.php" class="browse-products-btn">تصفح المنتجات</a>
                </div>
            </div>

            <!-- Cart Content -->
            <div class="cart-content-section" id="cart-content-section" style="display: none;">
                <!-- Cart Items -->
                <section class="cart-items-section" aria-label="عناصر السلة">
                    <div class="cart-items-header">
                        <h2>المنتجات في سلة المشتريات</h2>
                        <div class="items-count" id="items-count"></div>
                    </div>
                    <div class="cart-items-container" id="cart-items-container"></div>
                </section>

                <!-- Cart Summary -->
                <aside class="cart-summary-section" aria-label="ملخص الطلب">
                    <div class="summary-card">
                        <h3>ملخص الطلب</h3>
                        <div class="summary-details">
                            <div class="summary-row">
                                <span>عدد المنتجات:</span>
                                <span id="summary-products-count">0</span>
                            </div>
                            <div class="summary-row">
                                <span>المجموع الجزئي:</span>
                                <span id="summary-subtotal">0.00 ج.م</span>
                            </div>
                            <div class="summary-row">
                                <span>التوصيل:</span>
                                <span id="summary-shipping">0.00 ج.م</span>
                            </div>
                            <div class="summary-row total-row">
                                <span>الإجمالي الكلي:</span>
                                <span id="summary-total">0.00 ج.م</span>
                            </div>
                        </div>
                        <div class="summary-actions">
                            <button class="checkout-btn" id="checkout-btn" disabled>
                                💳 إتمام الشراء
                            </button>
                            <div class="secure-payment-note">
                                <span>🔒</span> دفع آمن ومشفّر
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </main>

        <!-- Cart Actions -->
        <footer class="cart-footer" id="cart-footer" style="display: none;">
            <div class="cart-actions">
                <button class="action-btn clear-all-btn" id="clear-all-btn">
                    <span class="btn-icon">🗑️</span>
                    تفريغ السلة
                </button>
                <button class="action-btn continue-shopping-btn" onclick="window.location.href='Home.php'">
                    <span class="btn-icon">🛍️</span>
                    متابعة التسوق
                </button>
            </div>
        </footer>

        <!-- Modal for Confirmation -->
        <div class="modal-overlay" id="confirmation-modal" style="display: none;">
            <div class="modal-content">
                <h3 id="modal-title"></h3>
                <p id="modal-message"></p>
                <div class="modal-actions">
                    <button class="modal-btn cancel-btn" id="modal-cancel">إلغاء</button>
                    <button class="modal-btn confirm-btn" id="modal-confirm">تأكيد</button>
                </div>
            </div>
        </div>

        <!-- Notification Toast -->
        <div class="toast-container" id="toast-container"></div>
    </div>

    <!-- JavaScript Files -->
    <?php echo '<script'; ?>
 src="https://code.jquery.com/jquery-3.7.0.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="../Assets/js/cart.js"><?php echo '</script'; ?>
>
</body>
</html><?php }
}
