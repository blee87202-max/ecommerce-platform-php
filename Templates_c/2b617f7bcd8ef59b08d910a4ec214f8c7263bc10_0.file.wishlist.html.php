<?php
/* Smarty version 4.3.4, created on 2026-01-19 15:13:44
  from 'C:\wamp64\www\ecommerce_project\Views\wishlist.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.4',
  'unifunc' => 'content_696e4a28862e74_19147698',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2b617f7bcd8ef59b08d910a4ec214f8c7263bc10' => 
    array (
      0 => 'C:\\wamp64\\www\\ecommerce_project\\Views\\wishlist.html',
      1 => 1768835620,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_696e4a28862e74_19147698 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>❤️ قائمة الرغبات</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap">
    <link rel="stylesheet" href="../Assets/css/wishlist.css">
</head>
<body>
    <!-- Container -->
    <div class="wishlist-wrapper">
        <!-- Header -->
        <header class="wishlist-header">
            <h1>❤️ قائمة الرغبات</h1>
            <div class="user-info" id="user-info">
                <!-- سيتم تعبئته بواسطة JavaScript -->
            </div>
        </header>

        <!-- Main Content -->
        <main class="wishlist-main">
            <!-- Loading State -->
            <div class="loading-section" id="loading-section">
                <div class="spinner-container">
                    <div class="spinner"></div>
                    <p>جاري تحميل قائمة الرغبات...</p>
                </div>
            </div>

            <!-- Error State -->
            <div class="error-section" id="error-section" style="display: none;">
                <div class="error-content">
                    <span class="error-icon">⚠️</span>
                    <h3>حدث خطأ</h3>
                    <p id="error-message"></p>
                    <button class="retry-btn" onclick="WishlistUI.retryLoading()">إعادة المحاولة</button>
                </div>
            </div>

            <!-- Empty State -->
            <div class="empty-section" id="empty-section" style="display: none;">
                <div class="empty-content">
                    <span class="empty-icon">💔</span>
                    <h3>قائمة الرغبات فارغة</h3>
                    <p>لم تقم بإضافة أي منتجات إلى قائمة الرغبات بعد.</p>
                    <button class="shop-btn" onclick="window.location.href='Home.php'">🛍️ ابدأ التسوق</button>
                </div>
            </div>

            <!-- Wishlist Content -->
            <div class="wishlist-content" id="wishlist-content" style="display: none;">
                <div class="wishlist-header-info">
                    <div class="wishlist-count">
                        <span id="wishlist-count">0</span> منتج في قائمة الرغبات
                    </div>
                    <button class="clear-all-btn" id="clear-all-btn">🗑️ مسح الكل</button>
                </div>

                <div class="wishlist-items" id="wishlist-items">
                    <!-- سيتم تعبئته بواسطة JavaScript -->
                </div>

                <div class="wishlist-actions">
                    <button type="button" class="continue-shopping-btn" onclick="window.location.href='Home.php'">
                        🛍️ متابعة التسوق
                    </button>
                    <button type="button" class="view-cart-btn" onclick="window.location.href='cart.php'">
                        🛒 عرض السلة
                    </button>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="wishlist-footer">
            <p>❤️ احفظ منتجاتك المفضلة هنا للمستقبل</p>
            <p class="help-text">يمكنك إضافة المنتجات للسلة أو حذفها من القائمة</p>
        </footer>
    </div>

    <!-- JavaScript Files -->
    <?php echo '<script'; ?>
 src="https://code.jquery.com/jquery-3.7.0.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="../Assets/js/wishlist.js"><?php echo '</script'; ?>
>
</body>
</html><?php }
}
