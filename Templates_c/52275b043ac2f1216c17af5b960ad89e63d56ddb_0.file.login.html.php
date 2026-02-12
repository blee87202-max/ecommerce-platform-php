<?php
/* Smarty version 4.3.4, created on 2026-01-20 13:49:16
  from 'C:\wamp64\www\ecommerce_project\Views\login.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.4',
  'unifunc' => 'content_696f87dc7803e9_70081692',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '52275b043ac2f1216c17af5b960ad89e63d56ddb' => 
    array (
      0 => 'C:\\wamp64\\www\\ecommerce_project\\Views\\login.html',
      1 => 1768846954,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_696f87dc7803e9_70081692 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔐 تسجيل الدخول - النظام المتقدم</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Particles.js -->
    <?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"><?php echo '</script'; ?>
>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../Assets/css/login.css">
</head>
<body>
    <!-- Particles Background -->
    <div id="particles-js"></div>
    
    <!-- Main Wrapper -->
    <div class="login-wrapper">
        <div class="login-container" id="login-container">
            <!-- Logo -->
            <div class="logo">
                <h1>🔐 مدخل آمن</h1>
                <p>مرحباً بعودتك! سجل دخولك للوصول إلى حسابك</p>
            </div>
            
            <!-- Mode Tabs -->
            <div class="mode-tabs">
                <button class="mode-tab active" data-mode="login">
                    <i class="fas fa-sign-in-alt"></i>
                    تسجيل الدخول
                </button>
                <button class="mode-tab" id="go-to-register">
                    <i class="fas fa-user-plus"></i>
                    إنشاء حساب جديد
                </button>
                <button class="mode-tab" data-mode="forgot">
                    <i class="fas fa-key"></i>
                    نسيت كلمة المرور؟
                </button>
            </div>
            
            <!-- Login Form -->
            <form class="login-form active" id="login-form">
                <div class="input-group">
                    <label for="login-email">البريد الإلكتروني</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="login-email" name="email" placeholder="example@domain.com" required autocomplete="email">
                    </div>
                    <div class="error-message"></div>
                </div>
                
                <div class="input-group">
                    <label for="login-password">كلمة المرور</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="login-password" name="password" class="password-field" placeholder="أدخل كلمة المرور" required autocomplete="current-password">
                        <button type="button" class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message"></div>
                </div>
                
                <div class="remember-forgot">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" id="remember-me" hidden>
                        <span class="custom-checkbox"></span>
                        <span>تذكرني لمدة 30 يوم</span>
                    </label>
                    <a href="#" class="forgot-link" data-mode="forgot">
                        نسيت كلمة المرور؟
                    </a>
                </div>
                
                <button type="submit" class="submit-btn" data-original-text="تسجيل الدخول">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="btn-text">تسجيل الدخول</span>
                </button>
                
                <!-- Social Login -->
                <div class="social-login">
                    <div class="social-buttons">
                        <button type="button" class="social-login-btn google" data-provider="google">
                            <i class="fab fa-google"></i>
                            Google
                        </button>
                        <button type="button" class="social-login-btn facebook" data-provider="facebook">
                            <i class="fab fa-facebook"></i>
                            Facebook
                        </button>
                    </div>
                </div>
                
                <!-- Guest Login -->
                <div class="guest-login">
                    <button type="button" class="guest-btn" id="guest-login">
                        <i class="fas fa-user-clock"></i>
                        الدخول كضيف
                    </button>
                </div>
                
                <!-- Register Link -->
                <div class="register-link">
                    <p>ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a></p>
                </div>
            </form>
            
            <!-- Forgot Password Form -->
            <form class="login-form" id="forgot-form">
                <div class="input-group">
                    <label for="forgot-email">البريد الإلكتروني</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="forgot-email" name="email" placeholder="أدخل بريدك الإلكتروني" required autocomplete="email">
                    </div>
                    <div class="error-message"></div>
                </div>
                
                <button type="submit" class="submit-btn" data-original-text="إرسال رمز التحقق">
                    <i class="fas fa-paper-plane"></i>
                    <span class="btn-text">إرسال رمز التحقق</span>
                </button>
                
                <div class="register-link">
                    <p>تذكرت كلمة المرور؟ <a href="#" data-mode="login">سجل دخولك</a></p>
                </div>
            </form>
            
            <!-- OTP Verification Form -->
            <form class="otp-form" id="otp-form">
                <div class="input-group">
                    <p class="email-display" style="text-align: center; margin-bottom: 20px; color: rgba(255,255,255,0.8);">
                        تم إرسال رمز التحقق إلى: <strong id="otp-email"></strong>
                    </p>
                    <input type="hidden" name="user_id" id="otp-user-id">
                    
                    <label>رمز التحقق المكون من 6 أرقام</label>
                    <div class="otp-inputs">
                        <input type="text" maxlength="1" class="otp-input" data-index="1" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input" data-index="2" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input" data-index="3" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input" data-index="4" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input" data-index="5" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input" data-index="6" autocomplete="off">
                    </div>
                    <input type="hidden" name="otp" id="otp-code">
                    <div class="error-message"></div>
                </div>
                
                <div class="otp-timer">05:00</div>
                
                <button type="submit" class="submit-btn" data-original-text="تحقق">
                    <i class="fas fa-check-circle"></i>
                    <span class="btn-text">تحقق</span>
                </button>
                
                <button type="button" class="resend-otp disabled" disabled>
                    إعادة إرسال الرمز
                </button>
            </form>
            
            <!-- Reset Password Form -->
            <form class="reset-form" id="reset-form">
                <input type="hidden" name="user_id" id="reset-user-id">
                <input type="hidden" name="otp" id="reset-otp">
                
                <div class="input-group">
                    <label for="new-password">كلمة المرور الجديدة</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="new-password" name="new_password" class="password-field" placeholder="كلمة مرور جديدة" required autocomplete="new-password">
                        <button type="button" class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message"></div>
                </div>
                
                <div class="input-group">
                    <label for="confirm-new-password">تأكيد كلمة المرور الجديدة</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="confirm-new-password" name="confirm_password" class="password-field" placeholder="تأكيد كلمة المرور الجديدة" required autocomplete="new-password">
                        <button type="button" class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message"></div>
                </div>
                
                <button type="submit" class="submit-btn" data-original-text="تحديث كلمة المرور">
                    <i class="fas fa-sync-alt"></i>
                    <span class="btn-text">تحديث كلمة المرور</span>
                </button>
            </form>
            
            <!-- Error/Success Containers -->
            <div class="error-container"></div>
            <div class="success-container"></div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <?php echo '<script'; ?>
 src="../Assets/js/login.js"><?php echo '</script'; ?>
>
</body>
</html><?php }
}
