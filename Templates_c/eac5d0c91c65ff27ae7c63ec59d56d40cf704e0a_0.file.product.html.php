<?php
/* Smarty version 4.3.4, created on 2026-01-20 11:34:22
  from 'C:\wamp64\www\ecommerce_project\Views\product.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.4',
  'unifunc' => 'content_696f683e434a21_91079536',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'eac5d0c91c65ff27ae7c63ec59d56d40cf704e0a' => 
    array (
      0 => 'C:\\wamp64\\www\\ecommerce_project\\Views\\product.html',
      1 => 1768846743,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_696f683e434a21_91079536 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="صفحة عرض المنتج - متجرنا الإلكتروني">
    <meta name="theme-color" content="#B8860B">
    <title>عرض المنتج - متجرنا</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Assets/css/product.css">
    <style>
        /* Skeleton Loading Effect */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: 8px;
        }
        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .skeleton-text { height: 20px; margin-bottom: 10px; width: 100%; }
        .skeleton-title { height: 40px; width: 70%; margin-bottom: 20px; }
        .skeleton-img { height: 400px; width: 100%; }
    </style>
</head>
<body>
    <!-- Container -->
    <div class="product-wrapper">
        <!-- Header -->
        <header class="product-header">
            <h1><i class="fas fa-store"></i> متجرنا</h1>
            <div class="header-actions">
                <a href="../Controller/Home.php" class="home-btn">
                    <i class="fas fa-home"></i> الرئيسية
                </a>
                <a href="../Controller/cart.php" class="cart-btn">
                    <i class="fas fa-shopping-cart"></i> السلة
                    <span class="cart-count" id="cart-count">0</span>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="product-main">
            <!-- Loading State (Skeleton) -->
            <div class="loading-section" id="loading-section">
                <div class="product-details-container">
                    <div class="product-images">
                        <div class="gallery-layout">
                            <div class="thumbnails-sidebar">
                                <div class="skeleton" style="width:70px; height:70px; margin-bottom:10px;"></div>
                                <div class="skeleton" style="width:70px; height:70px; margin-bottom:10px;"></div>
                                <div class="skeleton" style="width:70px; height:70px;"></div>
                            </div>
                            <div class="skeleton skeleton-img"></div>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="skeleton skeleton-title"></div>
                        <div class="skeleton skeleton-text" style="width:40%"></div>
                        <div class="skeleton skeleton-text" style="height:100px; margin-top:30px;"></div>
                        <div class="skeleton skeleton-text" style="height:50px; margin-top:30px;"></div>
                    </div>
                </div>
            </div>

            <!-- Error State -->
            <div class="error-section" id="error-section" style="display: none;">
                <div class="error-content">
                    <span class="error-icon"><i class="fas fa-exclamation-triangle"></i></span>
                    <h3>حدث خطأ</h3>
                    <p id="error-message"></p>
                    <button class="retry-btn" onclick="ProductUI.retryLoading()">
                        <i class="fas fa-redo"></i> إعادة المحاولة
                    </button>
                </div>
            </div>

            <!-- Product Content -->
            <div class="product-content" id="product-content" style="display: none;">
                <!-- Product Gallery & Details -->
                <div class="product-details-container">
	                    <!-- Product Images -->
	                    <div class="product-images">
	                        <div class="gallery-layout">
	                            <!-- Thumbnails Sidebar -->
	                            <div class="thumbnails-sidebar" id="product-thumbnails">
	                                <!-- سيتم تعبئته بواسطة JS -->
	                            </div>
	                            
	                            <div class="main-image-container">
	                                <div class="main-image">
	                                    <img id="product-main-image" src="" alt="صورة المنتج">
	                                </div>
                            <!-- نافذة التلميح -->
                            <div class="zoom-hint" id="zoom-hint" style="display: none;">
                                <i class="fas fa-search-plus"></i> حرك الماوس فوق الصورة للتكبير
                            </div>
                            <!-- أزرار التحكم بالزوم -->
                            <div class="zoom-controls">
                                <button class="zoom-btn zoom-in" title="تكبير">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                                <button class="zoom-btn zoom-out" title="تصغير">
                                    <i class="fas fa-search-minus"></i>
                                </button>
                                <button class="zoom-btn zoom-reset" title="إعادة التعيين">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                            <!-- شريط مؤشر الزوم -->
                            <div class="zoom-slider-container">
                                <span class="zoom-label">التكبير:</span>
                                <input type="range" id="zoom-slider" min="100" max="400" value="100" step="25">
                                <span class="zoom-percent" id="zoom-percent">100%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Product Information -->
                    <div class="product-info">
                        <!-- Product Title -->
                        <h2 id="product-name"></h2>

                        <!-- Price Section -->
                        <div class="price-section">
                            <div class="current-price" id="current-price"></div>
                            <div class="old-price" id="old-price" style="display: none;"></div>
                        </div>

                        <!-- Stock Status -->
                        <div class="stock-status" id="stock-status">
                            <i class="fas fa-box"></i>
                            <span id="stock-text"></span>
                        </div>

                        <!-- Product Description -->
                        <div class="product-description">
                            <h3><i class="fas fa-align-right"></i> وصف المنتج</h3>
                            <p id="product-description-text"></p>
                        </div>

                        <!-- Quantity Selector -->
                        <div class="quantity-section">
                            <label for="quantity">الكمية:</label>
                            <div class="quantity-controls">
                                <button class="quantity-btn minus">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" max="10">
                                <button class="quantity-btn plus">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="product-actions">
                            <button class="add-to-cart-btn" id="add-to-cart-btn">
                                <i class="fas fa-cart-plus"></i> أضف إلى السلة
                            </button>
                            <button class="buy-now-btn" id="buy-now-btn">
                                <i class="fas fa-bolt"></i> شراء الآن
                            </button>
                        </div>

                        <!-- Additional Info -->
                        <div class="additional-info">
                            <div class="info-item">
                                <i class="fas fa-shipping-fast"></i>
                                <span>شحن مجاني للطلبات فوق 500 ج.م</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-undo-alt"></i>
                                <span>إمكانية الإرجاع خلال 14 يوم</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>دفع آمن 100%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Similar Products -->
                <section class="similar-products">
                    <h3><i class="fas fa-random"></i> منتجات ذات صلة</h3>
                    <div class="similar-products-grid" id="similar-products-grid">
                        <!-- سيتم تعبئته بواسطة JavaScript -->
                    </div>
                </section>
            </div>
        </main>

        <!-- Footer -->
        <footer class="product-footer">
            <p><i class="fas fa-shield-alt"></i> 100% دفع آمن وضمان استعادة الأموال</p>
            <p class="help-text">للاستفسارات: <a href="tel:01026103523"><i class="fas fa-phone"></i> 01026103523</a></p>
        </footer>
    </div>

    <!-- Toast Notifications -->
    <div class="toast-container" id="toast-container"></div>

    <!-- JavaScript Files -->
    <?php echo '<script'; ?>
 src="https://code.jquery.com/jquery-3.7.0.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="../Assets/js/product.js"><?php echo '</script'; ?>
>
</body>
</html><?php }
}
