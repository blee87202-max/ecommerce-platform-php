<?php
/* Smarty version 4.3.4, created on 2026-01-19 19:21:00
  from 'C:\wamp64\www\ecommerce_project\Views\profile.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.4',
  'unifunc' => 'content_696e841c2c67f7_53352431',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6a04b074b9426b0400b36f105d89188c3553b6a8' => 
    array (
      0 => 'C:\\wamp64\\www\\ecommerce_project\\Views\\profile.html',
      1 => 1768839286,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_696e841c2c67f7_53352431 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👤 الملف الشخصي</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap">
    <link rel="stylesheet" href="../Assets/css/checkout.css">
    <link rel="stylesheet" href="../Assets/css/profile.css">
    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
</head>
<body>
    <!-- Container -->
    <div class="profile-wrapper">
        <!-- Header -->
        <header class="profile-header">
            <h1>👤 الملف الشخصي</h1>
            <div class="header-actions">
                <button class="back-btn" onclick="window.location.href='Home.php'">
                    🏠 الرئيسية
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="profile-main">
            <!-- Loading State -->
            <div class="loading-section" id="loading-section">
                <div class="spinner-container">
                    <div class="spinner"></div>
                    <p>جاري تحميل البيانات...</p>
                </div>
            </div>

            <!-- Error State -->
            <div class="error-section" id="error-section" style="display: none;">
                <div class="error-content">
                    <span class="error-icon">⚠️</span>
                    <h3>حدث خطأ</h3>
                    <p id="error-message"></p>
                    <button class="retry-btn" onclick="ProfileSystem.retryLoading()">إعادة المحاولة</button>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="profile-content" id="profile-content" style="display: none;">
                <!-- Profile Header -->
                <section class="profile-header-section">
                    <div class="avatar-container">
                        <img id="user-avatar" 
                             src="" 
                             alt="الصورة الشخصية" 
                             class="profile-avatar"
                             onerror="if(typeof ProfileSystem !== 'undefined') ProfileSystem.handleAvatarError(this)">
                        <div id="avatar-fallback" class="avatar-fallback"></div>
                        <div class="avatar-overlay">
                            <label for="avatar-input" class="avatar-upload-btn">
                                📷 تغيير الصورة
                            </label>
                            <input type="file" id="avatar-input" accept="image/*" style="display: none;">
                        </div>
                    </div>
                    <div class="profile-basic-info">
                        <h2 id="user-name"></h2>
                        <p class="user-email" id="user-email"></p>
                        <p class="member-since" id="member-since"></p>
                    </div>
                </section>

                <!-- User Stats -->
                <section class="stats-section">
                    <h3>📊 إحصائيات</h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">🛒</div>
                            <div class="stat-info">
                                <div class="stat-value" id="total-orders">0</div>
                                <div class="stat-label">الطلبات</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">💰</div>
                            <div class="stat-info">
                                <div class="stat-value" id="total-spent">0 ج.م</div>
                                <div class="stat-label">إجمالي المشتريات</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">⭐</div>
                            <div class="stat-info">
                                <div class="stat-value">عضو</div>
                                <div class="stat-label" id="member-status">منذ 0 يوم</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Edit Forms -->
                <div class="forms-container">
                    <!-- Edit Profile Form -->
                    <section class="form-section">
                        <h3>✏️ تعديل المعلومات الشخصية</h3>
                        <form id="edit-profile-form" class="profile-form">
                            <div class="form-group">
                                <label for="edit-name">الاسم الكامل:</label>
                                <input type="text" id="edit-name" name="name" required>
                                <div class="error-message" id="name-error"></div>
                            </div>

                            <div class="form-group">
                                <label for="edit-email">البريد الإلكتروني:</label>
                                <input type="email" id="edit-email" name="email" required>
                                <div class="error-message" id="email-error"></div>
                            </div>

                            <div class="form-group">
                                <label for="edit-phone">رقم الهاتف:</label>
                                <input type="tel" id="edit-phone" name="phone">
                                <div class="error-message" id="phone-error"></div>
                            </div>

                            <button type="submit" class="submit-btn" id="save-profile-btn">
                                💾 حفظ التغييرات
                            </button>
                        </form>
                    </section>

                    <!-- Change Password Form -->
                    <section class="form-section">
                        <h3>🔒 تغيير كلمة المرور</h3>
                        <form id="change-password-form" class="profile-form">
                            <div class="form-group">
                                <label for="current-password">كلمة المرور الحالية:</label>
                                <input type="password" id="current-password" name="current_password" required>
                                <div class="error-message" id="current-password-error"></div>
                            </div>

                            <div class="form-group">
                                <label for="new-password">كلمة المرور الجديدة:</label>
                                <input type="password" id="new-password" name="new_password" required>
                                <div class="error-message" id="new-password-error"></div>
                            </div>

                            <div class="form-group">
                                <label for="confirm-password">تأكيد كلمة المرور:</label>
                                <input type="password" id="confirm-password" name="confirm_password" required>
                                <div class="error-message" id="confirm-password-error"></div>
                            </div>

                            <button type="submit" class="submit-btn" id="change-password-btn">
                                🔑 تغيير كلمة المرور
                            </button>
                        </form>
                    </section>
                </div>

                <!-- Danger Zone -->
                <section class="danger-zone-section">
                    <h3>⚠️ منطقة الخطر</h3>
                    <div class="danger-zone-content">
                        <div class="warning-message">
                            <span class="warning-icon">🚨</span>
                            <div class="warning-text">
                                <strong>حذف الحساب نهائيًا</strong>
                                <p>سيتم حذف جميع بياناتك وطلباتك ولا يمكن استرجاعها بعد الحذف</p>
                            </div>
                        </div>
                        
                        <button class="delete-account-btn" onclick="ProfileSystem.showDeleteModal()">
                            🗑️ حذف الحساب
                        </button>
                    </div>
                </section>
            </div>
        </main>

        <!-- Footer -->
        <footer class="profile-footer">
            <p>🔒 بياناتك محمية ومشفرة</p>
            <p class="help-text">للاستفسارات: <a href="tel:0123456789">0123456789</a></p>
        </footer>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal-overlay" id="delete-modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>تأكيد حذف الحساب</h3>
                <button class="modal-close" onclick="ProfileSystem.hideDeleteModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="delete-confirmation">
                    <div class="warning-icon-large">⚠️</div>
                    <h4>هل أنت متأكد من حذف حسابك؟</h4>
                    <p class="warning-text">هذا الإجراء لا يمكن التراجع عنه. سيتم حذف جميع بياناتك وطلباتك نهائيًا.</p>
                    
                    <div class="form-group">
                        <label for="delete-password">أدخل كلمة المرور للتأكيد:</label>
                        <input type="password" id="delete-password" placeholder="كلمة المرور الحالية">
                        <div class="error-message" id="delete-password-error"></div>
                    </div>
                    
                    <div class="modal-actions">
                        <button class="cancel-btn" onclick="ProfileSystem.hideDeleteModal()">إلغاء</button>
                        <button class="delete-btn" id="confirm-delete-btn">حذف الحساب نهائيًا</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Cropper Modal -->
    <div class="modal-overlay" id="cropper-modal" style="display: none;">
        <div class="modal-content cropper-modal-content">
            <div class="modal-header">
                <h3>✂️ تحديد تمركز الصورة</h3>
                <button class="modal-close" onclick="ProfileSystem.hideCropperModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="cropper-container">
                    <img id="cropper-image" src="" alt="صورة للقص">
                </div>
                <div class="cropper-instructions">
                    <p>قم بتحريك المربع لتحديد الجزء الذي تريد إظهاره في البروفايل.</p>
                </div>
                <div class="modal-actions">
                    <button class="cancel-btn" onclick="ProfileSystem.hideCropperModal()">إلغاء</button>
                    <button class="submit-btn" id="crop-save-btn">✅ اعتماد الصورة</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <?php echo '<script'; ?>
 src="https://code.jquery.com/jquery-3.7.0.min.js"><?php echo '</script'; ?>
>
    <!-- Cropper.js JS -->
    <?php echo '<script'; ?>
 src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="../Assets/js/profile.js"><?php echo '</script'; ?>
>

</body>
</html><?php }
}
