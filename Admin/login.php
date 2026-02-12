<?php



session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);


$error_message = '';
$success_message = '';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$login_attempts = isset($_SESSION['login_attempts']) ? (int)$_SESSION['login_attempts'] : 0;
$last_attempt_time = isset($_SESSION['last_attempt_time']) ? (int)$_SESSION['last_attempt_time'] : 0;
$max_attempts = 5;
$lockout_time = 300; 


if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

if ($login_attempts >= $max_attempts) {
    $time_remaining = $lockout_time - (time() - $last_attempt_time);
    if ($time_remaining > 0) {
        $minutes = ceil($time_remaining / 60);
        $error_message = "❌ تم قفل الحساب مؤقتاً. حاول مرة أخرى بعد " . $minutes . " دقيقة.";
    } else {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = 0;
        $login_attempts = 0; 
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login']) && empty($error_message)) {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $csrf_token) {
        $error_message = "❌ خطأ أمني: محاولة تزوير طلب عبر المواقع (CSRF).";
    } else {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        $validation_errors = array();

        if (empty($email)) {
            $validation_errors[] = "البريد الإلكتروني مطلوب.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $validation_errors[] = "صيغة البريد الإلكتروني غير صحيحة.";
        }

        if (empty($password)) {
            $validation_errors[] = "كلمة المرور مطلوبة.";
        }

        if (!empty($validation_errors)) {
            $error_message = "❌ " . implode(" | ", $validation_errors);
        } else {
            if ($email === $admin_email && $password === $admin_password) {

                $_SESSION['admin'] = 'Administrator';
                $_SESSION['admin_email'] = $email;
                $_SESSION['login_time'] = time();
                $_SESSION['login_attempts'] = 0;
                $_SESSION['last_attempt_time'] = 0;

                session_regenerate_id(true);

                header("Location: dashboard.php");
                exit;
            } else {
                $_SESSION['login_attempts'] = $login_attempts + 1;
                $_SESSION['last_attempt_time'] = time();
                $error_message = "❌ بيانات الدخول غير صحيحة.";

                $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
                $csrf_token = $_SESSION['csrf_token'];
            }
        }
    }
}


?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>تسجيل دخول الأدمن - لوحة التحكم</title>

    <!-- الخطوط -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            font-family: 'Cairo', sans-serif;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* خلفية الجسيمات */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
        }

        /* حاوية تسجيل الدخول */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 60px 45px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25), 0 0 40px rgba(102, 126, 234, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.6s ease-out;
            width: 100%;
            max-width: 420px;
        }

        /* الرأس */
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .lock-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #0066ff, #00d4ff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 25px rgba(0, 102, 255, 0.3);
            animation: bounce 2s infinite;
        }

        .lock-icon i {
            color: white;
            font-size: 35px;
        }

        .login-header h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }

        /* رسائل التنبيه */
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.4s ease-out;
        }

        .alert-error {
            background-color: #ffe5e5;
            color: #ff4d4d;
            border: 1px solid #ff4d4d;
        }

        .alert-success {
            background-color: #e5ffe5;
            color: #4dff4d;
            border: 1px solid #4dff4d;
        }

        /* مجموعة الإدخال */
        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        /* نزيد المساحة على اليمين لحقل الباسورد ليحتوي على أيقونتين */
        .input-group.password-group .input-wrapper input {
            /* padding: top right bottom left -> نزيد الـ right */
            padding: 14px 80px 14px 16px;
            /* يمكنك تقليل/زيادة 80px لو تحب */
        }

        /* نحرك أيقونة القفل داخل حقل الباسورد لتكون أيسر من العين */
        .input-group.password-group .input-wrapper i.fa-lock {
            position: absolute;
            top: 50%;
            right: 52px;
            /* المسافة من جانب الحقل — تحكم فيها حسب الذوق */
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
            pointer-events: none;
            /* لتجنب اعتراض النقر على زر العين */
            transition: color 0.2s ease;
        }

        /* نضبط زر العين (منطقة الضغط) ليكون مربع دائري ويغطي الأيقونة تمامًا */
        .input-group.password-group .password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            /* يضع العين قريب من حافة الحقل، عدّل القيمة لو حبيت */
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: transparent;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            z-index: 30;
            /* لضمان أنه فوق أي عناصر أخرى */
        }

        /* تكبير الايقونة داخل الزر بحيث تُرى واضحة ومتوسطة */
        .input-group.password-group .password-toggle i {
            font-size: 18px;
            color: #999;
            transition: color 0.2s ease;
        }

        .input-group.password-group .password-toggle:hover i {
            color: #0066ff;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 16px;
            padding-right: 45px;
            /* مسافة للأيقونة */
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            color: #333;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-family: 'Cairo', sans-serif;
            background-color: #f9f9f9;
        }

        .input-wrapper input:focus {
            border-color: #0066ff;
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
            outline: none;
            background-color: #fff;
        }

        .input-wrapper i {
            position: absolute;
            top: 50%;
            right: 16px;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .input-wrapper input:focus+i {
            color: #0066ff;
        }

        .input-wrapper input.input-error {
            border-color: #ff4d4d;
            box-shadow: 0 0 0 3px rgba(255, 77, 77, 0.1);
        }

        .error-text {
            color: #ff4d4d;
            font-size: 13px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        /* زر الدخول */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(to right, #0066ff, #00d4ff);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s, transform 0.1s, opacity 0.3s;
            box-shadow: 0 10px 20px rgba(0, 102, 255, 0.2);
            margin-top: 20px;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            background: linear-gradient(to right, #0056e6, #00b8e6);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(0, 102, 255, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 5px 15px rgba(0, 102, 255, 0.2);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* حالة التحميل */
        .btn-login.loading {
            color: transparent !important;
            pointer-events: none;
        }

        .btn-login.loading::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin-top: -10px;
            margin-left: -10px;
            border: 3px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        /* تذكرني */
        .remember-me {
            display: flex;
            align-items: center;
            margin-top: 15px;
            font-size: 14px;
            color: #666;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            /* إخفاء مربع الاختيار الأصلي */
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .remember-me label {
            position: relative;
            padding-right: 25px;
            /* مسافة للمربع المخصص */
            cursor: pointer;
            user-select: none;
            margin-bottom: 0;
            font-weight: 400;
        }

        .remember-me label::before {
            content: '';
            position: absolute;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            height: 16px;
            width: 16px;
            background-color: #eee;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .remember-me input[type="checkbox"]:checked+label::before {
            background-color: #0066ff;
            border-color: #0066ff;
        }

        .remember-me label::after {
            content: '\f00c';
            /* رمز علامة الصح من Font Awesome */
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 50%;
            right: 0;
            transform: translateY(-50%) scale(0);
            color: white;
            font-size: 10px;
            transition: transform 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 16px;
            height: 16px;
        }

        .remember-me input[type="checkbox"]:checked+label::after {
            transform: translateY(-50%) scale(1);
        }

        /* الرسوم المتحركة */
        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            60% {
                transform: translateY(-5px);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-10px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(10px);
            }
        }

        .login-container.shake {
            animation: shake 0.5s;
        }

        /* استجابة التصميم */
        @media (max-width: 500px) {
            .login-container {
                padding: 40px 30px;
                margin: 20px;
            }

            .login-header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <!-- خلفية الجسيمات -->
    <div id="particles-js"></div>

    <!-- حاوية تسجيل الدخول -->
    <div class="login-wrapper">
        <div class="login-container">
            <!-- الرأس -->
            <div class="login-header">
                <div class="lock-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h1>لوحة التحكم</h1>
                <p>يرجى تسجيل الدخول للمتابعة</p>
            </div>

            <!-- الرسائل -->
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo htmlspecialchars($success_message); ?></span>
                </div>
            <?php endif; ?>

            <!-- نموذج تسجيل الدخول -->
            <form method="POST" id="loginForm" novalidate>
                <!-- حقل CSRF Token المخفي -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <!-- حقل البريد الإلكتروني -->
                <div class="input-group">
                    <label for="email">البريد الإلكتروني</label>
                    <div class="input-wrapper">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="أدخل بريدك الإلكتروني"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            required>
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="error-text" id="emailError"></div>
                </div>

                <!-- حقل كلمة المرور -->
                <div class="input-group password-group">
                    <label for="password">كلمة المرور</label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="أدخل كلمة المرور"
                            required>
                        <i class="fas fa-lock"></i>
                        <button type="button" class="password-toggle" id="passwordToggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-text" id="passwordError"></div>
                </div>

                <!-- تذكرني (اختياري) -->
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">تذكرني</label>
                </div>

                <!-- زر الدخول -->
                <button type="submit" class="btn-login" name="login">
                    تسجيل الدخول
                </button>

                <p style="text-align: center; margin-top: 20px; color: #999; font-size: 13px;">
                    <!-- (البريد: asker@gmail.com | كلمة المرور: asker818) -->
                </p>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // particles.js configuration
        particlesJS('particles-js', {
            "particles": {
                "number": {
                    "value": 60,
                    "density": {
                        "enable": true,
                        "value_area": 800
                    }
                },
                "color": {
                    "value": "#ffffff"
                },
                "shape": {
                    "type": "circle",
                    "stroke": {
                        "width": 0,
                        "color": "#000000"
                    }
                },
                "opacity": {
                    "value": 0.5,
                    "random": true
                },
                "size": {
                    "value": 3,
                    "random": true
                },
                "line_linked": {
                    "enable": true,
                    "distance": 150,
                    "color": "#ffffff",
                    "opacity": 0.2,
                    "width": 1
                },
                "move": {
                    "enable": true,
                    "speed": 2,
                    "direction": "none",
                    "random": true,
                    "straight": false,
                    "out_mode": "out"
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": {
                        "enable": true,
                        "mode": "grab"
                    },
                    "onclick": {
                        "enable": true,
                        "mode": "push"
                    }
                },
                "modes": {
                    "grab": {
                        "distance": 140,
                        "line_linked": {
                            "opacity": 0.3
                        }
                    },
                    "push": {
                        "particles_nb": 4
                    }
                }
            },
            "retina_detect": true
        });

        // JavaScript for form validation and password toggle
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');
            const passwordToggle = document.getElementById('passwordToggle');
            const loginContainer = document.querySelector('.login-container');

            // Password Toggle
            passwordToggle.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });

            // Client-side Validation
            form.addEventListener('submit', function(e) {
                let isValid = true;

                // Reset errors
                emailError.textContent = '';
                passwordError.textContent = '';
                emailInput.classList.remove('input-error');
                passwordInput.classList.remove('input-error');
                loginContainer.classList.remove('shake');

                // Email validation
                if (emailInput.value.trim() === '') {
                    emailError.textContent = 'البريد الإلكتروني مطلوب.';
                    emailInput.classList.add('input-error');
                    isValid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
                    emailError.textContent = 'صيغة البريد الإلكتروني غير صحيحة.';
                    emailInput.classList.add('input-error');
                    isValid = false;
                }

                // Password validation
                if (passwordInput.value === '') {
                    passwordError.textContent = 'كلمة المرور مطلوبة.';
                    passwordInput.classList.add('input-error');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    loginContainer.classList.add('shake');
                }
            });
        });
    </script>
</body>

</html>