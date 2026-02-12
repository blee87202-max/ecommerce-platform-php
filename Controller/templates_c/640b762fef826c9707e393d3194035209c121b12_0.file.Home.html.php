<?php
/* Smarty version 4.3.4, created on 2026-01-20 12:21:07
  from 'C:\wamp64\www\ecommerce_project\Views\Home.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.4',
  'unifunc' => 'content_696f73333d1427_84446384',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '640b762fef826c9707e393d3194035209c121b12' => 
    array (
      0 => 'C:\\wamp64\\www\\ecommerce_project\\Views\\Home.html',
      1 => 1768911643,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../Api/mobile_splash.php' => 1,
  ),
),false)) {
function content_696f73333d1427_84446384 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\wamp64\\www\\ecommerce_project\\Smarty\\libs\\plugins\\modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" class="preload">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes" />
    <meta name="description" content="متجر فاخر يقدم أفضل المنتجات والخدمات المتميزة بأسعار تنافسية." />
    <title>✨ المتجر البسيط | تجربة تسوق استثنائية</title>
    <link rel="icon" type="image/png" href="../Assets/images/favicon.png" />
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="../Controller/Home.php" />
    <meta property="og:title" content="✨ المتجر البسيط | تجربة تسوق استثنائية" />
    <meta property="og:description" content="اكتشف تشكيلتنا الحصرية من المنتجات البسيطة بأفضل الأسعار." />
    <meta property="og:image" content="../Assets/images/share.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" />
    <link rel="preconnect" href="https://unpkg.com" />
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" as="style" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <noscript><link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet" /></noscript>
    
    <!-- Icons & Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'" />
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" media="print" onload="this.media='all'" />
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" media="print" onload="this.media='all'" />
    
    <!-- Custom Styles -->
    <style>
      /* Critical CSS for fast first paint */
      :root { --primary: #C5A059; --secondary: #1A1A1A; --white: #FFFFFF; }
      body { font-family: 'Cairo', sans-serif; margin: 0; padding: 0; overflow-x: hidden; }
      #preloader { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #fff; z-index: 9999; display: flex; align-items: center; justify-content: center; }
      header { height: 70px; background: #fff; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
      .container { width: 100%; max-width: 1400px; margin: 0 auto; padding: 0 20px; }
    </style>
    <link rel="stylesheet" href="../Assets/css/Home.css" media="print" onload="this.media='all'" />
    <?php echo '<script'; ?>
 src="../Assets/js/Home.js" defer><?php echo '</script'; ?>
>
    
    <!-- PWA -->
    <link rel="manifest" href="../Assets/Services/manifest.json" />
    <meta name="theme-color" content="#C5A059" />

    
    <?php echo '<script'; ?>
 type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "المتجر البسيط",
        "url": "../Controller/Home.php",
        "potentialAction": {
          "@type": "SearchAction",
          "target": "../Controller/Home.php?q={search_term_string}",
          "query-input": "required name=search_term_string"
        }
      }
    <?php echo '</script'; ?>
>
    
  </head>
  <body>
    <!-- Preloader & Audio Activation -->
    <div id="preloader">
        <div class="loader-content">
            <span class="loader-logo">✨</span>
            <div class="loader-bar"></div>
            <button id="start-experience" class="btn-start-exp" style="display: none;">
                <i class="fas fa-play"></i> ابدأ التجربة البسيطة
            </button>
        </div>
    </div>

    <div class="app-wrapper">
      <!-- Top Bar -->
      <div class="top-bar">
          <div class="container">
              <div class="top-bar-content">
                  <div class="contact-info">
                      <span><i class="fas fa-phone"></i> +20 1026103523</span>
                      <span><i class="fas fa-envelope"></i> support@luxury-store.com</span>
                  </div>
                  <div class="top-links">
                      <a href="track-order.php"><i class="fas fa-truck"></i> تتبع طلبك</a>
                      <a href="help.php"><i class="fas fa-question-circle"></i> المساعدة</a>
                  </div>
              </div>
          </div>
      </div>

      <!-- Compare Bar -->
      <div id="compare-bar" class="compare-bar-premium">
        <div class="container">
            <div class="compare-content">
                <div class="compare-info">
                    <div class="compare-icon-wrapper">
                        <i class="fas fa-exchange-alt"></i>
                        <span class="compare-badge" id="compare-count-text"><?php echo (($tmp = $_smarty_tpl->tpl_vars['counts']->value['compare'] ?? null)===null||$tmp==='' ? 0 ?? null : $tmp);?>
</span>
                    </div>
                    <span class="compare-text">منتجات مضافة للمقارنة</span>
                </div>
                <div class="compare-actions">
                    <a href="../Controller/compare.php" class="btn-compare-view">
                        <i class="fas fa-list-ul"></i>
                        <span>عرض قائمة المقارنة</span>
                    </a>
                    <button id="clear-compare" class="btn-compare-clear" title="مسح الكل">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
      </div>

      <!-- Toast Notifications -->
      <div id="toast-region" role="status" aria-live="polite"></div>

      <!-- Header -->
      <header id="main-header">
        <div class="container header-container">
            <div class="header-top-row">
                <div class="header-left">
                    <button class="mobile-menu-toggle" aria-label="القائمة">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a href="../Controller/Home.php" class="logo">
                        <span class="logo-icon">✨</span>
                        <span class="logo-text">المتجر<span class="highlight">البسيط</span></span>
                    </a>
                </div>

                <div class="header-actions">
                    <a href="https://wa.me/201026103523" target="_blank" class="action-btn whatsapp-header" title="تواصل معنا عبر واتساب">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <button class="search-trigger">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="action-btn notification-trigger" id="notification-bell" style="display: none;">
                        <i class="fas fa-bell"></i>
                        <span class="badge-count" id="notification-count">0</span>
                    </button>
                    <a href="../Controller/wishlist.php" class="action-btn wishlist-trigger">
                        <i class="fas fa-heart"></i>
                        <span class="badge-count" id="wishlist-count"><?php echo (($tmp = $_smarty_tpl->tpl_vars['counts']->value['wishlist'] ?? null)===null||$tmp==='' ? 0 ?? null : $tmp);?>
</span>
                    </a>
                    <a href="../Controller/cart.php" class="action-btn cart-trigger">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="badge-count" id="cart-count"><?php echo (($tmp = $_smarty_tpl->tpl_vars['counts']->value['cart'] ?? null)===null||$tmp==='' ? 0 ?? null : $tmp);?>
</span>
                    </a>
                    <div class="user-menu">
                        <?php if ($_smarty_tpl->tpl_vars['logged_in']->value) {?>
                            <div class="user-avatar" id="user-avatar-trigger">
                                <img src="<?php if ($_smarty_tpl->tpl_vars['user']->value['avatar'] && $_smarty_tpl->tpl_vars['user']->value['avatar'] != 'default_avatar.png') {?>../Assets/uploads/avatars/<?php echo $_smarty_tpl->tpl_vars['user']->value['avatar'];
} else { ?>../Assets/uploads/avatars/default_avatar.png<?php }?>" 
                                     alt="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['user']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
" />
                                <div class="user-dropdown" id="user-dropdown-menu">
                                    <a href="../Controller/profile.php"><i class="fas fa-user"></i> الملف الشخصي</a>
                                    <a href="../Controller/my_orders.php"><i class="fas fa-box"></i> طلباتي</a>
                                    <a href="../Controller/logout_user.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <a href="../Controller/login.php" class="action-btn login-btn">
                                <i class="fas fa-user"></i>
                            </a>
                        <?php }?>
                    </div>
                    <button class="theme-toggle" id="theme-switcher" aria-label="تبديل الوضع">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="search-wrapper">
                <form id="search-form" action="../Controller/Home.php" method="GET" class="search-form">
                    <input type="text" id="search-input" name="q" class="search-input" placeholder="ابحث عن منتج، فئة، ماركة..." 
                           value="<?php echo htmlspecialchars((string)(($tmp = $_GET['q'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp), ENT_QUOTES, 'UTF-8', true);?>
" autocomplete="off" />
                    <button type="submit" aria-label="بحث" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <div id="search-suggestions" class="search-suggestions search-suggestions-premium"></div>
            </div>
        </div>

        <!-- Full Screen Search -->
        <div class="search-overlay" id="search-overlay">
            <button class="search-close"><i class="fas fa-times"></i></button>
            <div class="search-container">
                <form action="../Controller/Home.php" method="GET" class="search-form-full" id="search-form-full">
                    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" />
                    <input type="text" name="q" id="search-input-full" placeholder="ما الذي تبحث عنه اليوم؟" autocomplete="off" 
                           value="<?php echo htmlspecialchars((string)(($tmp = $_GET['q'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp), ENT_QUOTES, 'UTF-8', true);?>
" />
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
                <div id="search-suggestions-full" class="search-suggestions-grid"></div>
            </div>
        </div>
      </header>

	      <!-- Cinematic Hero Section (Optimized) -->
		      <section class="cinematic-hero" id="hero-story">
		        <div class="hero-layers">
	            <div class="layer layer-bg" style="background-image: url('../Assets/images/video-poster.jpg');"></div>
	            <div class="layer layer-overlay"></div>
	            <div class="layer layer-content">
		                <div class="hero-glass-container neon-board">
		                    <div class="hero-statement">
		                        <div class="impact-wrapper">
			                            <div class="impact-text-container" data-text="BEYOND">
	                                        <h1 class="statement-title impact-text" dir="ltr">
	                                            <span class="char" data-char="B">B<span class="reflection">B</span></span>
	                                            <span class="char" data-char="E">E<span class="reflection">E</span></span>
	                                            <span class="char" data-char="Y">Y<span class="reflection">Y</span></span>
	                                            <span class="char" data-char="O">O<span class="reflection">O</span></span>
	                                            <span class="char" data-char="N">N<span class="reflection">N</span></span>
	                                            <span class="char loose-d" data-char="D">D<span class="reflection">D</span></span>
	                                        </h1>
                                            <div class="golden-underline"></div>
	                                    </div>
			                            <!-- Spark and Dust Containers -->
			                            <div class="spark-container"></div>
			                            <div class="dust-container"></div>
		                        </div>
	                        <div class="hero-actions">
	                            <a href="#products-container" class="btn-hero-explore">
	                                <span>اكتشف المنتجات</span>
	                                <i class="fas fa-chevron-down"></i>
	                            </a>
	                        </div>
		                    </div>
		                </div>
	            </div>
        </div>
      </section>

      <!-- Infinite Trust Slider -->
      <section class="trust-slider-section">
          <div class="trust-slider-track">
              <div class="trust-item"><i class="fas fa-shipping-fast"></i> شحن سريع عالمي</div>
              <div class="trust-item"><i class="fas fa-undo"></i> ضمان استرجاع 30 يوم</div>
              <div class="trust-item"><i class="fas fa-hand-holding-usd"></i> دفع عند الاستلام</div>
              <div class="trust-item"><i class="fas fa-check-circle"></i> منتجات أصلية 100%</div>
              <div class="trust-item"><i class="fas fa-headset"></i> دعم فني 24/7</div>
              <!-- Duplicate for infinite effect -->
              <div class="trust-item"><i class="fas fa-shipping-fast"></i> شحن سريع عالمي</div>
              <div class="trust-item"><i class="fas fa-undo"></i> ضمان استرجاع 30 يوم</div>
              <div class="trust-item"><i class="fas fa-hand-holding-usd"></i> دفع عند الاستلام</div>
              <div class="trust-item"><i class="fas fa-check-circle"></i> منتجات أصلية 100%</div>
              <div class="trust-item"><i class="fas fa-headset"></i> دعم فني 24/7</div>
          </div>
      </section>

      <!-- Experience Story Section - REMOVED FOR CLEANER DESIGN -->

      <!-- Categories Slider -->
      <section class="category-slider-section">
        <div class="container">
            <div class="section-header">
	                <h2 class="section-title" id="cat-title"><i class="fas fa-tags"></i> تسوق حسب الفئة</h2>
            </div>
            <div class="category-slider-container">
                <div class="swiper category-swiper">
<div class="swiper-wrapper" id="category-slider-container">
	                        <?php if ((isset($_smarty_tpl->tpl_vars['categories']->value)) && count($_smarty_tpl->tpl_vars['categories']->value) > 0) {?>
	                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['categories']->value, 'cat');
$_smarty_tpl->tpl_vars['cat']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['cat']->value) {
$_smarty_tpl->tpl_vars['cat']->do_else = false;
?>
	                            <div class="swiper-slide">
	                                <div class="category-slide-card skeleton-loading" data-category-id="<?php echo $_smarty_tpl->tpl_vars['cat']->value['id'];?>
" onclick="window.HomeApp.filterAndScroll('<?php echo $_smarty_tpl->tpl_vars['cat']->value['id'];?>
')">
	                                    <img src="../Controller/image.php?src=<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'urlencode' ][ 0 ], array( $_smarty_tpl->tpl_vars['cat']->value['image'] ));?>
&w=300&h=300&q=80" 
	                                         alt="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['cat']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
" 
	                                         loading="lazy" 
	                                         onload="this.parentElement.classList.remove('skeleton-loading')"
	                                         onerror="this.src='../Assets/images/default-category.jpg'; this.parentElement.classList.remove('skeleton-loading')" />
	                                    <div class="category-slide-content">
	                                        <h3><?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['cat']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</h3>
	                                        <p><?php echo (($tmp = $_smarty_tpl->tpl_vars['cat']->value['count'] ?? null)===null||$tmp==='' ? 0 ?? null : $tmp);?>
 منتج</p>
	                                    </div>
	                                </div>
	                            </div>
	                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        <?php } else { ?>
                            <div class="no-categories">
                                <i class="fas fa-folder-open fa-3x"></i>
                                <h3>لا توجد فئات متاحة حالياً</h3>
                            </div>
                        <?php }?>
                    </div>
                    <!-- أزرار التنقل -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
      </section>

      <!-- Random Products Slides (Updated to Featured Style) -->
      <section class="random-products-section">
          <div class="container">
              <div class="section-header">
	                  <h2 class="section-title" id="random-offer-title"><i class="fas fa-star"></i> العروض المميزة</h2>
              </div>
              <div class="swiper random-products-slider">
                  <div class="swiper-wrapper" id="random-products-container">
                      <!-- سيتم تحميلها بواسطة JavaScript -->
                  </div>
                  <div class="swiper-pagination"></div>
                  <div class="swiper-button-next"></div>
                  <div class="swiper-button-prev"></div>
              </div>
          </div>
      </section>

      <!-- Main Shopping Area -->
      <main class="main-shopping-area">
          <div class="container main-layout">
              <!-- Products Grid -->
              <div class="shop-main">
                  <div class="shop-toolbar">
                      <div class="results-count">
                          <i class="fas fa-box-open"></i>
                          <span id="total-results"><?php echo (($tmp = $_smarty_tpl->tpl_vars['total_products']->value ?? null)===null||$tmp==='' ? 0 ?? null : $tmp);?>
</span> منتج
                          <?php if ((isset($_GET['q'])) && $_GET['q']) {?>
                          لـ "<span id="search-term"><?php echo htmlspecialchars((string)$_GET['q'], ENT_QUOTES, 'UTF-8', true);?>
</span>"
                          <?php }?>
                      </div>
                      <div class="toolbar-actions">
                          <button id="toggle-filters" class="btn-filter-mobile">
                              <i class="fas fa-sliders-h"></i> تصفية النتائج
                          </button>
                          <div class="sort-wrapper">
                              <select id="sort-products" name="sort">
                                  <option value="">الترتيب الافتراضي</option>
                                  <option value="newest" <?php if ((isset($_GET['sort'])) && $_GET['sort'] == 'newest') {?>selected<?php }?>>الأحدث أولاً</option>
                                  <option value="price-asc" <?php if ((isset($_GET['sort'])) && $_GET['sort'] == 'price-asc') {?>selected<?php }?>>السعر: من الأقل للأعلى</option>
                                  <option value="price-desc" <?php if ((isset($_GET['sort'])) && $_GET['sort'] == 'price-desc') {?>selected<?php }?>>السعر: من الأعلى للأقل</option>
                                  <option value="popular" <?php if ((isset($_GET['sort'])) && $_GET['sort'] == 'popular') {?>selected<?php }?>>الأكثر مبيعاً</option>
                              </select>
                              <i class="fas fa-chevron-down"></i>
                          </div>
                      </div>
                  </div>

                  <section id="products-container" class="products-grid-v2">
                      <?php if ((isset($_smarty_tpl->tpl_vars['products']->value)) && count($_smarty_tpl->tpl_vars['products']->value) > 0) {?>
                          <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['products']->value, 'prod');
$_smarty_tpl->tpl_vars['prod']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['prod']->value) {
$_smarty_tpl->tpl_vars['prod']->do_else = false;
?>
                          <article class="product-card-v2" data-id="<?php echo $_smarty_tpl->tpl_vars['prod']->value['id'];?>
" data-stock="<?php echo (($tmp = $_smarty_tpl->tpl_vars['prod']->value['stock'] ?? null)===null||$tmp==='' ? 0 ?? null : $tmp);?>
" data-aos="fade-up">
                              <div class="product-image-v2">
                                  <div class="product-badges">
                                      <?php if ($_smarty_tpl->tpl_vars['prod']->value['is_new']) {?><span class="badge-v2 badge-new"><i class="fas fa-star"></i> جديد</span><?php }?>
                                      <?php if ($_smarty_tpl->tpl_vars['prod']->value['discount'] > 0) {?><span class="badge-v2 badge-discount">-<?php echo $_smarty_tpl->tpl_vars['prod']->value['discount'];?>
%</span><?php }?>
                                      <?php if ($_smarty_tpl->tpl_vars['prod']->value['stock'] <= 0) {?><span class="badge-v2 badge-out"><i class="fas fa-ban"></i> نفذ</span><?php }?>
                                  </div>
                                  <img src="../Controller/image.php?src=<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'urlencode' ][ 0 ], array( $_smarty_tpl->tpl_vars['prod']->value['image'] ));?>
&w=400&h=400&q=90" 
                                       alt="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['prod']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
" 
                                       loading="lazy"
                                       onerror="this.src='../Assets/images/default-product.png'" />
                                  <div class="product-actions-v2">
                                      <button class="btn-action-v2 add-cart" data-id="<?php echo $_smarty_tpl->tpl_vars['prod']->value['id'];?>
" title="أضف للسلة">
                                          <i class="fas fa-shopping-cart"></i>
                                      </button>
                                      <button class="btn-action-v2 toggle-wishlist <?php if ($_smarty_tpl->tpl_vars['prod']->value['in_wishlist']) {?>active<?php }?>" 
                                              data-id="<?php echo $_smarty_tpl->tpl_vars['prod']->value['id'];?>
" title="<?php if ($_smarty_tpl->tpl_vars['prod']->value['in_wishlist']) {?>إزالة من المفضلة<?php } else { ?>أضف للمفضلة<?php }?>">
                                          <i class="fas fa-heart"></i>
                                      </button>
                                      <button class="btn-action-v2 quick-view" data-id="<?php echo $_smarty_tpl->tpl_vars['prod']->value['id'];?>
" title="عرض سريع">
                                          <i class="fas fa-eye"></i>
                                      </button>
                                  </div>
                              </div>
                              <div class="product-info-v2">
                                  <h3 class="product-title-v2">
                                      <a href="../Controller/product.php?id=<?php echo $_smarty_tpl->tpl_vars['prod']->value['id'];?>
"><?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['prod']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</a>
                                  </h3>
                                  <div class="product-price-v2">
                                      <span class="current-price"><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'number_format' ][ 0 ], array( $_smarty_tpl->tpl_vars['prod']->value['price'],2 ));?>
 ج.م</span>
                                      <?php if ($_smarty_tpl->tpl_vars['prod']->value['old_price'] > $_smarty_tpl->tpl_vars['prod']->value['price']) {?>
                                          <span class="old-price"><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'number_format' ][ 0 ], array( $_smarty_tpl->tpl_vars['prod']->value['old_price'],2 ));?>
 ج.م</span>
                                      <?php }?>
                                  </div>
                                  <?php if ($_smarty_tpl->tpl_vars['prod']->value['stock'] > 0 && $_smarty_tpl->tpl_vars['prod']->value['stock'] <= 3) {?>
                                  <div class="product-stock-info low-stock">باقي <?php echo $_smarty_tpl->tpl_vars['prod']->value['stock'];?>
 فقط!</div>
                                  <?php } elseif ($_smarty_tpl->tpl_vars['prod']->value['stock'] > 3) {?>
                                  <div class="product-stock-info in-stock">متوفر في المخزون</div>
                                  <?php }?>
                                  <?php if ($_smarty_tpl->tpl_vars['prod']->value['rating'] > 0) {?>
                                  <div class="product-rating-v2">
                                      <div class="stars">
                                          <?php
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable(null, $_smarty_tpl->isRenderingCache);$_smarty_tpl->tpl_vars['i']->step = 1;$_smarty_tpl->tpl_vars['i']->total = (int) ceil(($_smarty_tpl->tpl_vars['i']->step > 0 ? 5+1 - (1) : 1-(5)+1)/abs($_smarty_tpl->tpl_vars['i']->step));
if ($_smarty_tpl->tpl_vars['i']->total > 0) {
for ($_smarty_tpl->tpl_vars['i']->value = 1, $_smarty_tpl->tpl_vars['i']->iteration = 1;$_smarty_tpl->tpl_vars['i']->iteration <= $_smarty_tpl->tpl_vars['i']->total;$_smarty_tpl->tpl_vars['i']->value += $_smarty_tpl->tpl_vars['i']->step, $_smarty_tpl->tpl_vars['i']->iteration++) {
$_smarty_tpl->tpl_vars['i']->first = $_smarty_tpl->tpl_vars['i']->iteration === 1;$_smarty_tpl->tpl_vars['i']->last = $_smarty_tpl->tpl_vars['i']->iteration === $_smarty_tpl->tpl_vars['i']->total;?>
                                              <?php if ($_smarty_tpl->tpl_vars['i']->value <= $_smarty_tpl->tpl_vars['prod']->value['rating']) {?>
                                                  <i class="fas fa-star"></i>
                                              <?php } elseif ($_smarty_tpl->tpl_vars['i']->value == ceil($_smarty_tpl->tpl_vars['prod']->value['rating']) && ($_smarty_tpl->tpl_vars['prod']->value['rating']-floor($_smarty_tpl->tpl_vars['prod']->value['rating'])) >= 0.5) {?>
                                                  <i class="fas fa-star-half-alt"></i>
                                              <?php } else { ?>
                                                  <i class="far fa-star"></i>
                                              <?php }?>
                                          <?php }
}
?>
                                          <span class="rating-count">(<?php echo (($tmp = $_smarty_tpl->tpl_vars['prod']->value['rating_count'] ?? null)===null||$tmp==='' ? 0 ?? null : $tmp);?>
)</span>
                                      </div>
                                  </div>
                                  <?php }?>
                              </div>
                          </article>
                          <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                      <?php } else { ?>
                          <div class="no-products">
                              <i class="fas fa-box-open fa-3x"></i>
                              <h3>لا توجد منتجات حالياً</h3>
                              <p>جرّب تغيير فلاتر البحث أو تصفح فئات أخرى</p>
                              <a href="../Controller/Home.php" class="btn-primary">عرض جميع المنتجات</a>
                          </div>
                      <?php }?>
                  </section>

                  <!-- Loading Indicators -->
                  <div id="infinite-scroll-trigger" class="scroll-trigger">
                      <div class="loading-spinner"></div>
                      <p>جاري تحميل المزيد من المنتجات...</p>
                  </div>
                  
                  <button id="load-more-btn" class="load-more-btn" style="display: none;">
                      <i class="fas fa-sync-alt"></i> تحميل المزيد من المنتجات
                  </button>
              </div>

              <!-- Foldable Filters Sidebar -->
              <aside class="shop-sidebar" id="filter-sidebar">
                  <div class="sidebar-header">
                      <button class="toggle-fold-btn" id="toggle-fold-sidebar" title="طي/فتح التصفية">
                          <i class="fas fa-chevron-left"></i>
                      </button>
                      <h3><i class="fas fa-filter"></i> تصفية المنتجات</h3>
<!-- Close Button Removed -->
                  </div>
                  <div class="sidebar-content">
                      <form id="filter-form">
                          <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" />
                          
                          <div class="filter-block">
                              <h4 class="filter-title"><i class="fas fa-th-large"></i> الفئات</h4>
                              <div class="filter-options">
                                  <label class="custom-radio">
                                      <input type="radio" name="category" value="" checked onclick="window.HomeApp.filterAndScroll('')">
                                      <span class="radio-mark"></span> جميع الفئات
                                  </label>
                                  <?php if ((isset($_smarty_tpl->tpl_vars['categories']->value))) {?>
                                      <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['categories']->value, 'c');
$_smarty_tpl->tpl_vars['c']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['c']->value) {
$_smarty_tpl->tpl_vars['c']->do_else = false;
?>
                                      <label class="custom-radio">
                                          <input type="radio" name="category" value="<?php echo $_smarty_tpl->tpl_vars['c']->value['id'];?>
" 
                                                 <?php if ((isset($_GET['category'])) && $_GET['category'] == $_smarty_tpl->tpl_vars['c']->value['id']) {?>checked<?php }?>
                                                 onclick="window.HomeApp.filterAndScroll('<?php echo $_smarty_tpl->tpl_vars['c']->value['id'];?>
')">
                                          <span class="radio-mark"></span> <?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['c']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
 (<?php echo (($tmp = $_smarty_tpl->tpl_vars['c']->value['count'] ?? null)===null||$tmp==='' ? 0 ?? null : $tmp);?>
)
                                      </label>
                                      <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                  <?php }?>
                              </div>
                          </div>

                          <div class="filter-block">
                              <h4 class="filter-title"><i class="fas fa-money-bill-wave"></i> نطاق السعر</h4>
                              <div class="price-range">
                                  <div class="price-range-inputs">
                                      <div class="input-group">
                                          <span class="input-label">من</span>
                                          <input type="number" id="min-price" name="min" placeholder="0" 
                                                 value="<?php echo (($tmp = $_GET['min'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" min="0" max="100000" />
                                      </div>
                                      <div class="input-group">
                                          <span class="input-label">إلى</span>
                                          <input type="number" id="max-price" name="max" placeholder="10000" 
                                                 value="<?php echo (($tmp = $_GET['max'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" min="0" max="100000" />
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="filter-actions">
                              <button type="button" id="apply-filters" class="btn-apply">
                                  <i class="fas fa-check"></i> تطبيق الفلاتر
                              </button>
                              <button type="button" id="clear-filters" class="btn-reset">
                                  <i class="fas fa-redo"></i> إعادة ضبط
                              </button>
                          </div>
                      </form>
                  </div>
              </aside>
          </div>
      </main>

      <!-- Footer -->
      <footer class="main-footer">
          <div class="container">
              <div class="footer-top">
                  <div class="footer-brand">
                      <a href="../Controller/Home.php" class="logo">
                          <span class="logo-icon">✨</span>
                          <span class="logo-text">المتجر<span class="highlight">البسيط</span></span>
                      </a>
                      <p>وجهتك الأولى للأناقة والفخامة. نقدم لك أفضل المنتجات بأعلى جودة وأفضل الأسعار مع خدمة عملاء متميزة على مدار الساعة.</p>
                      <div class="social-links">
                          <a href="#" title="فيسبوك"><i class="fab fa-facebook-f"></i></a>
                          <a href="#" title="تويتر"><i class="fab fa-twitter"></i></a>
                          <a href="#" title="انستجرام"><i class="fab fa-instagram"></i></a>
                          <a href="#" title="يوتيوب"><i class="fab fa-youtube"></i></a>
                          <a href="#" title="واتساب"><i class="fab fa-whatsapp"></i></a>
                      </div>
                  </div>
                  <div class="footer-links">
                      <h4><i class="fas fa-link"></i> روابط سريعة</h4>
                      <ul>
                          <li><a href="../Controller/Home.php"><i class="fas fa-home"></i> الرئيسية</a></li>
                          <li><a href="shop.php"><i class="fas fa-store"></i> المتجر</a></li>
                          <li><a href="categories.php"><i class="fas fa-th-large"></i> الفئات</a></li>
                          <li><a href="offers.php"><i class="fas fa-tag"></i> العروض</a></li>
                          <li><a href="contact.php"><i class="fas fa-phone"></i> اتصل بنا</a></li>
                      </ul>
                  </div>
                  <div class="footer-links">
                      <h4><i class="fas fa-headset"></i> خدمة العملاء</h4>
                      <ul>
                          <li><a href="faq.php"><i class="fas fa-question-circle"></i> الأسئلة الشائعة</a></li>
                          <li><a href="shipping.php"><i class="fas fa-shipping-fast"></i> سياسة الشحن</a></li>
                          <li><a href="returns.php"><i class="fas fa-undo"></i> سياسة الإرجاع</a></li>
                          <li><a href="privacy.php"><i class="fas fa-shield-alt"></i> سياسة الخصوصية</a></li>
                          <li><a href="terms.php"><i class="fas fa-file-contract"></i> الشروط والأحكام</a></li>
                      </ul>
                  </div>
                  <div class="footer-newsletter">
                      <h4><i class="fas fa-newspaper"></i> النشرة البريدية</h4>
                      <p>اشترك في نشرتنا البريدية للحصول على أحدث العروض والخصومات الحصرية مباشرة في بريدك.</p>
                      <form class="newsletter-form" id="newsletter-form">
                          <input type="email" placeholder="بريدك الإلكتروني" required />
                          <button type="submit"><i class="fas fa-paper-plane"></i> اشتراك</button>
                      </form>
                  </div>
              </div>
              <div class="footer-bottom">
                  <div class="footer-bottom-left">
                      <p>&copy; <span id="year"><?php echo smarty_modifier_date_format(time(),"%Y");?>
</span> المتجر البسيط. جميع الحقوق محفوظة.</p>
                      <div class="footer-links-bottom">
                          <a href="privacy.php">الخصوصية</a> | 
                          <a href="terms.php">الشروط</a> | 
                          <a href="sitemap.php">خريطة الموقع</a>
                      </div>
                  </div>
                  <div class="payment-methods">
                      <span>طرق الدفع المقبولة:</span>
                      <i class="fab fa-cc-visa" title="فيزا"></i>
                      <i class="fab fa-cc-mastercard" title="ماستركارد"></i>
                      <i class="fab fa-cc-paypal" title="بايبال"></i>
                      <i class="fab fa-cc-amex" title="أمريكان إكسبريس"></i>
                      <i class="fas fa-money-bill-wave" title="الدفع عند الاستلام"></i>
                  </div>
              </div>
          </div>
      </footer>
    </div>

    <!-- Quick View Modal -->
    <div id="quick-view-modal" class="modal" style="display: none;">
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <div class="modal-header">
                <h3>عرض سريع</h3>
                <button class="modal-close" aria-label="إغلاق"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" id="quick-view-body">
                <!-- سيتم تحميل محتوى العرض السريع هنا -->
            </div>
        </div>
    </div>

    <!-- Mobile Splash Screen -->
    <?php if ($_smarty_tpl->tpl_vars['show_mobile_splash']->value) {?>
        <?php $_smarty_tpl->_subTemplateRender('file:../Api/mobile_splash.php', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    <?php }?>

    <!-- Scripts -->
    <?php echo '<script'; ?>
 id="php-vars" type="application/json">
    {
        "page": <?php if ((isset($_smarty_tpl->tpl_vars['current_page']->value))) {
echo $_smarty_tpl->tpl_vars['current_page']->value;
} else { ?>1<?php }?>,
        "hasNextPage": <?php if ((isset($_smarty_tpl->tpl_vars['has_next_page']->value)) && $_smarty_tpl->tpl_vars['has_next_page']->value) {?>true<?php } else { ?>false<?php }?>,
        "total": <?php if ((isset($_smarty_tpl->tpl_vars['total_products']->value))) {
echo $_smarty_tpl->tpl_vars['total_products']->value;
} else { ?>0<?php }?>,
        "searchQuery": "<?php if ((isset($_GET['q']))) {
echo strtr((string)$_GET['q'], array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", 
                       "\n" => "\\n", "</" => "<\/", "<!--" => "<\!--", "<s" => "<\s", "<S" => "<\S",
                       "`" => "\\`", "\${" => "\\\$\{"));
}?>",
        "category": "<?php if ((isset($_GET['category']))) {
echo strtr((string)$_GET['category'], array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", 
                       "\n" => "\\n", "</" => "<\/", "<!--" => "<\!--", "<s" => "<\s", "<S" => "<\S",
                       "`" => "\\`", "\${" => "\\\$\{"));
}?>",
        "min": "<?php if ((isset($_GET['min']))) {
echo strtr((string)$_GET['min'], array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", 
                       "\n" => "\\n", "</" => "<\/", "<!--" => "<\!--", "<s" => "<\s", "<S" => "<\S",
                       "`" => "\\`", "\${" => "\\\$\{"));
}?>",
        "max": "<?php if ((isset($_GET['max']))) {
echo strtr((string)$_GET['max'], array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", 
                       "\n" => "\\n", "</" => "<\/", "<!--" => "<\!--", "<s" => "<\s", "<S" => "<\S",
                       "`" => "\\`", "\${" => "\\\$\{"));
}?>",
        "sort": "<?php if ((isset($_GET['sort']))) {
echo strtr((string)$_GET['sort'], array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", 
                       "\n" => "\\n", "</" => "<\/", "<!--" => "<\!--", "<s" => "<\s", "<S" => "<\S",
                       "`" => "\\`", "\${" => "\\\$\{"));
}?>",
        "logged_in": <?php if ($_smarty_tpl->tpl_vars['logged_in']->value) {?>true<?php } else { ?>false<?php }?>
    }
    <?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="https://unpkg.com/swiper/swiper-bundle.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="https://unpkg.com/aos@2.3.4/dist/aos.js"><?php echo '</script'; ?>
>

    </div>

    <!-- WhatsApp Elegant Float Button -->
<!-- WhatsApp Float Removed -->
  </body>
</html><?php }
}
