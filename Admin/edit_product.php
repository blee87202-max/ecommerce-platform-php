<?php
require __DIR__ . '/admin_init.php';
if (!is_admin()) {
    header('Location: login.php');
    exit;
}
include(__DIR__ . '/../Model/db.php');
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// ===== إعدادات ومساعدات بسيطة =====
function safe($v)
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

// توليد CSRF token متوافق مع PHP 5.5
function generate_csrf_token()
{
    if (!isset($_SESSION)) session_start();
    if (!isset($_SESSION['csrf_token'])) {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        } else {
            $_SESSION['csrf_token'] = bin2hex(md5(uniqid(mt_rand(), true)));
        }
    }
    return $_SESSION['csrf_token'];
}

function rotate_csrf_token()
{
    if (function_exists('openssl_random_pseudo_bytes')) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    } else {
        $_SESSION['csrf_token'] = bin2hex(md5(uniqid(mt_rand(), true)));
    }
    return $_SESSION['csrf_token'];
}

// حذف آمن لملف صورة
function safe_unlink($publicPath)
{
    if (!$publicPath) return;
    $publicPath = ltrim($publicPath, '/');
    $full = __DIR__ . '/assets/images/' . $publicPath;
    if (is_file($full)) {
        @unlink($full);
    }
    // حذف النسخة الأصلية في مجلد products
    $alt = __DIR__ . '/assets/images/products/' . basename($publicPath);
    if (is_file($alt)) {
        @unlink($alt);
    }
}

// دالة لإنشاء thumbnail مربع (300x300)
function create_square_thumbnail($sourceFile, $destFile, $size = 300, $quality = 85)
{
    if (!is_file($sourceFile)) return false;
    $info = @getimagesize($sourceFile);
    if ($info === false) return false;
    $mime = isset($info['mime']) ? $info['mime'] : '';
    
    switch ($mime) {
        case 'image/jpeg':
        case 'image/pjpeg':
            $src = @imagecreatefromjpeg($sourceFile);
            break;
        case 'image/png':
            $src = @imagecreatefrompng($sourceFile);
            break;
        case 'image/gif':
            $src = @imagecreatefromgif($sourceFile);
            break;
        case 'image/webp':
            if (function_exists('imagecreatefromwebp')) {
                $src = @imagecreatefromwebp($sourceFile);
            } else {
                $src = false;
            }
            break;
        default:
            $src = false;
    }
    
    if (!$src) {
        $data = @file_get_contents($sourceFile);
        if ($data !== false) $src = @imagecreatefromstring($data);
        if (!$src) return false;
    }
    
    $w = imagesx($src);
    $h = imagesy($src);
    $crop = min($w, $h);
    $x = floor(($w - $crop) / 2);
    $y = floor(($h - $crop) / 2);
    $dst = imagecreatetruecolor($size, $size);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);
    imagecopyresampled($dst, $src, 0, 0, $x, $y, $size, $size, $crop, $crop);
    $saved = imagejpeg($dst, $destFile, $quality);
    imagedestroy($src);
    imagedestroy($dst);
    return $saved;
}

// ==== بداية منطق الصفحة ====
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id <= 0) {
    echo "<script>alert('❌ معرّف المنتج غير صالح'); window.location='dashboard.php';</script>";
    exit;
}

// جلب الفئات
$categories = array();
$catRes = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
if ($catRes) {
    while ($cr = $catRes->fetch_assoc()) {
        $categories[$cr['id']] = htmlspecialchars($cr['name']);
    }
    $catRes->free();
}

// جلب بيانات المنتج
$stmt = $conn->prepare("SELECT id, name, price, image, category_id, stock, description, countdown FROM products WHERE id = ? LIMIT 1");
if (!$stmt) {
    error_log("DB prepare error (select product) in edit_product.php: " . $conn->error);
    echo "<script>alert('خطأ في السيرفر.'); window.location='dashboard.php';</script>";
    exit;
}
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    echo "<script>alert('❌ المنتج غير موجود'); window.location='dashboard.php';</script>";
    exit;
}

// ===== جلب الصور الإضافية للمنتج =====
$additional_images = [];
$imgStmt = $conn->prepare("SELECT id, image_path FROM product_images WHERE product_id = ? ORDER BY sort_order ASC");
if ($imgStmt) {
    $imgStmt->bind_param("i", $product_id);
    $imgStmt->execute();
    $imgRes = $imgStmt->get_result();
    while ($row = $imgRes->fetch_assoc()) {
        $additional_images[] = $row;
    }
    $imgStmt->close();
}
// ===================================

// تجهيز قيم افتراضية
$product['name'] = isset($product['name']) ? $product['name'] : '';
$product['price'] = isset($product['price']) ? $product['price'] : 0;
$product['image'] = isset($product['image']) ? $product['image'] : '';
$product['category_id'] = isset($product['category_id']) ? (int)$product['category_id'] : 0;
$product['stock'] = isset($product['stock']) ? (int)$product['stock'] : 0;
$product['description'] = isset($product['description']) ? $product['description'] : '';
$product['countdown'] = isset($product['countdown']) ? $product['countdown'] : '';

$msg = "";
$msgClass = "";
if (isset($_GET['updated']) && $_GET['updated'] == '1') {
    $msg = "✅ تم تحديث المنتج بنجاح.";
    $msgClass = "success";
}

// تأكد من وجود CSRF token
$csrf_token = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

    // تحقق CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $err = 'فشل التحقق. حاول إعادة تحميل الصفحة.';
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array('status' => 'error', 'msg' => $err));
            exit;
        }
        $msg = $err;
        $msgClass = 'danger';
    } else {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
        $category_id = isset($_POST['category']) ? (int)$_POST['category'] : 0;
        $stock = isset($_POST['stock']) ? max(0, intval($_POST['stock'])) : 0;

        // الوصف
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $description = strip_tags($description);
        $description = preg_replace('/(http|https|ftp|ftps )\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', '', $description);
        
        if (function_exists('mb_strlen') && mb_strlen($description, 'UTF-8') > 500) {
            $description = mb_substr($description, 0, 500, 'UTF-8') . '...';
        } elseif (strlen($description) > 500) {
            $description = substr($description, 0, 500) . '...';
        }
        $description_safe = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

        // حقل العداد التنازلي (Countdown)
        $countdown_time = isset($_POST['countdown_time']) ? trim($_POST['countdown_time']) : '';
        $countdown_timestamp = !empty($countdown_time) ? strtotime($countdown_time) * 1000 : null;

        if ($name === '' || $price <= 0 || !array_key_exists($category_id, $categories)) {
            $msg = "❌ الرجاء ملء الحقول الأساسية بشكل صحيح (الاسم، السعر، الفئة).";
            $msgClass = "danger";
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array('status' => 'error', 'msg' => $msg));
                exit;
            }
        } else {
            $current_image = $product['image'];
            $image_to_save = $current_image;
            $uploadDirServer = __DIR__ . "/assets/images/products/";
            if (!is_dir($uploadDirServer)) @mkdir($uploadDirServer, 0755, true);

            // معالجة الصورة الرئيسية
            if (isset($_FILES['image']) && isset($_FILES['image']['error']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $tmpPath = $_FILES['image']['tmp_name'];
                $file_mime = '';
                if (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $file_mime = finfo_file($finfo, $tmpPath);
                    finfo_close($finfo);
                } else {
                    $image_info = @getimagesize($tmpPath);
                    if ($image_info !== false) $file_mime = $image_info['mime'];
                }

                $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed_mimes = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/webp');
                $allowed_exts = array('jpg', 'jpeg', 'png', 'gif', 'webp');

                if (!in_array($file_mime, $allowed_mimes) && !in_array($file_extension, $allowed_exts)) {
                    $msg = "❌ صيغة الصورة غير مدعومة. استخدم JPG أو PNG أو GIF أو WEBP.";
                    $msgClass = "danger";
                    if ($isAjax) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(array('status' => 'error', 'msg' => $msg));
                        exit;
                    }
                } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $msg = "❌ حجم الصورة كبير جدًا. الحد الأقصى 5MB.";
                    $msgClass = "danger";
                    if ($isAjax) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(array('status' => 'error', 'msg' => $msg));
                        exit;
                    }
                } else {
                    $safe_basename = uniqid('p_', true);
                    $safe_filename = $safe_basename . '.' . $file_extension;
                    $imagePathServer = $uploadDirServer . $safe_filename;
                    $publicImagePath = 'products/' . $safe_filename;

                    $upload_success = move_uploaded_file($tmpPath, $imagePathServer);

                    if ($upload_success) {
                        $thumb300 = $uploadDirServer . $safe_basename . '_300.jpg';
                        create_square_thumbnail($imagePathServer, $thumb300, 300, 85);

                        $image_to_save = $publicImagePath;

                        if (!empty($current_image)) {
                            safe_unlink($current_image);
                        }
                        @chmod($imagePathServer, 0644);
                        @chmod($thumb300, 0644);
                    } else {
                        error_log("Failed to move uploaded file in edit_product.php for product {$product_id}");
                        $msg = "❌ حدث خطأ أثناء رفع الصورة.";
                        $msgClass = "danger";
                        if ($isAjax) {
                            header('Content-Type: application/json; charset=utf-8');
                            echo json_encode(array('status' => 'error', 'msg' => $msg));
                            exit;
                        }
                    }
                }
            }

            // ===== معالجة حذف الصور الإضافية =====
            if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                $delete_ids = array_map('intval', $_POST['delete_images']);
                $delete_ids = array_filter($delete_ids, function($id) { return $id > 0; });

                if (!empty($delete_ids)) {
                    // 1. جلب مسارات الملفات للحذف الآمن
                    $ids_str = implode(',', $delete_ids);
                    $selectStmt = $conn->prepare("SELECT image_path FROM product_images WHERE id IN ($ids_str) AND product_id = ?");
                    $selectStmt->bind_param("i", $product_id);
                    $selectStmt->execute();
                    $res = $selectStmt->get_result();
                    $paths_to_delete = [];
                    while ($row = $res->fetch_assoc()) {
                        $paths_to_delete[] = $row['image_path'];
                    }
                    $selectStmt->close();

                    // 2. حذف السجلات من قاعدة البيانات
                    $deleteStmt = $conn->prepare("DELETE FROM product_images WHERE id IN ($ids_str) AND product_id = ?");
                    $deleteStmt->bind_param("i", $product_id);
                    $deleteStmt->execute();
                    $deleteStmt->close();

                    // 3. حذف الملفات من الخادم
                    foreach ($paths_to_delete as $path) {
                        safe_unlink($path);
                    }
                }
            }
            // ===================================

            // ===== معالجة رفع الصور الإضافية الجديدة =====
            if (isset($_FILES['additional_images']) && is_array($_FILES['additional_images']['tmp_name'])) {
                $uploadDirServer = __DIR__ . "/assets/images/products/";
                $allowed_mimes = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/webp');
                
                // تحديد الـ sort_order الصحيح
                $last_image_sort_order = 0;
                $imgStmt = $conn->prepare("SELECT sort_order FROM product_images WHERE product_id = ? ORDER BY sort_order DESC LIMIT 1");
                if ($imgStmt) {
                    $imgStmt->bind_param("i", $product_id);
                    $imgStmt->execute();
                    $imgRes = $imgStmt->get_result();
                    $last_image = $imgRes->fetch_assoc();
                    $imgStmt->close();
                    $last_image_sort_order = $last_image ? (int)$last_image['sort_order'] : 0;
                }
                $sort_order = $last_image_sort_order + 1;

                $insertImageStmt = $conn->prepare("INSERT INTO product_images (product_id, image_path, sort_order) VALUES (?, ?, ?)");

                foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['additional_images']['error'][$key] === UPLOAD_ERR_OK) {
                        $file_size = $_FILES['additional_images']['size'][$key];
                        $file_name = $_FILES['additional_images']['name'][$key];

                        if ($file_size > 5 * 1024 * 1024) {
                            error_log("Additional image too large: " . $file_name);
                            continue;
                        }

                        $file_mime = '';
                        if (function_exists('finfo_open')) {
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $file_mime = finfo_file($finfo, $tmp_name);
                            finfo_close($finfo);
                        }

                        if (!in_array($file_mime, $allowed_mimes)) {
                            error_log("Additional image type not allowed: " . $file_name);
                            continue;
                        }

                        $safe_basename = uniqid('p_', true) . '_add_' . $sort_order;
                        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        $imagePathServer = $uploadDirServer . $safe_basename . '.' . $file_extension;
                        $publicImagePath = 'products/' . $safe_basename . '.' . $file_extension;

                        if (move_uploaded_file($tmp_name, $imagePathServer)) {
                            @chmod($imagePathServer, 0644);
                            
                            // إدراج في جدول الصور الإضافية
                            $insertImageStmt->bind_param("isi", $product_id, $publicImagePath, $sort_order);
                            $insertImageStmt->execute();
                            $sort_order++;
                        } else {
                            error_log("Failed to move uploaded additional file: " . $file_name);
                        }
                    }
                }
                if ($insertImageStmt) $insertImageStmt->close();
            }
            // ===================================

            if ($msg === '') {
                $stmt_upd = $conn->prepare("UPDATE products SET name = ?, price = ?, image = ?, category_id = ?, stock = ?, description = ?, countdown = ? WHERE id = ?");
                if (!$stmt_upd) {
                    error_log("DB prepare error (update product) in edit_product.php: " . $conn->error);
                    $msg = "❌ خطأ في الخادم أثناء تجهيز التحديث.";
                    $msgClass = "danger";
                    if ($isAjax) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(array('status' => 'error', 'msg' => $msg));
                        exit;
                    }
                } else {
                    $stmt_upd->bind_param("sdsiissi", $name, $price, $image_to_save, $category_id, $stock, $description_safe, $countdown_timestamp, $product_id);
                    if ($stmt_upd->execute()) {
                        $new_csrf = rotate_csrf_token();

                        // إعادة جلب المنتج والـ additional_images بعد التحديث
                        $stmt = $conn->prepare("SELECT id, name, price, image, category_id, stock, description, countdown FROM products WHERE id = ? LIMIT 1");
                        $stmt->bind_param("i", $product_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $updated_product = $result->fetch_assoc();
                        $stmt->close();

                        // إعادة جلب الصور الإضافية المحدثة
                        $additional_images_updated = [];
                        $imgStmt = $conn->prepare("SELECT id, image_path FROM product_images WHERE product_id = ? ORDER BY sort_order ASC");
                        if ($imgStmt) {
                            $imgStmt->bind_param("i", $product_id);
                            $imgStmt->execute();
                            $imgRes = $imgStmt->get_result();
                            while ($row = $imgRes->fetch_assoc()) {
                                $additional_images_updated[] = $row;
                            }
                            $imgStmt->close();
                        }

                        if ($isAjax) {
                            $p = array(
                                'id' => $product_id,
                                'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                                'price' => number_format((float)$price, 2),
                                'image' => htmlspecialchars($image_to_save, ENT_QUOTES, 'UTF-8'),
                                'image_url' => 'assets/images/' . ltrim($image_to_save, '/'),
                                'category_id' => $category_id,
                                'stock' => intval($stock),
                                'description' => htmlspecialchars($description_safe, ENT_QUOTES, 'UTF-8'),
                                'additional_images' => $additional_images_updated
                            );
                            header('Content-Type: application/json; charset=utf-8');
                            echo json_encode(array('status' => 'success', 'msg' => '✅ تم تحديث المنتج بنجاح.', 'product' => $p, 'csrf_token' => $new_csrf), JSON_UNESCAPED_UNICODE);
                            exit;
                        } else {
                            header("Location: edit_product.php?id={$product_id}&updated=1");
                            exit;
                        }
                    } else {
                        error_log("DB execute error (update product) in edit_product.php: " . $stmt_upd->error);
                        $msg = "❌ فشل التحديث.";
                        $msgClass = "danger";
                        if ($isAjax) {
                            header('Content-Type: application/json; charset=utf-8');
                            echo json_encode(array('status' => 'error', 'msg' => $msg));
                            exit;
                        }
                    }
                    $stmt_upd->close();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>✏️ تعديل المنتج</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <style>
        :root {
            --bg: #f4f7fb;
            --card: #fff;
            --text: #222;
            --accent: #4f46e5;
            --accent-2: #2563eb;
        }

        body {
            background: var(--bg);
            font-family: 'Cairo', sans-serif;
            color: var(--text);
            margin: 0;
        }

        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 240px;
            background: linear-gradient(180deg, var(--accent), var(--accent-2));
            padding-top: 60px;
            color: white;
            overflow: auto;
        }

        .sidebar h3 {
            font-weight: 700;
            padding: 0 16px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px 18px;
            text-decoration: none;
            border-radius: 8px;
            margin: 8px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255, 255, 255, 0.12);
        }

        .main-content {
            margin-right: 240px;
            padding: 30px;
            min-height: 100vh;
        }

        .card.form-card {
            max-width: 880px;
            margin: 10px auto;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            background: var(--card);
        }

        .img-preview {
            width: 140px;
            height: 140px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px dashed #ddd;
            background: #fff;
        }

        .existing-image-wrapper {
            position: relative;
            display: inline-block;
            margin: 5px;
        }

        .delete-image-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            padding: 0;
            font-size: 14px;
            z-index: 10;
        }

        @media(max-width:992px) {
            .sidebar {
                width: 64px;
            }
            .sidebar span {
                display: none;
            }
            .main-content {
                margin-right: 64px;
            }
        }

        @media(max-width:768px) {
            .main-content {
                margin-right: 0;
                padding: 16px;
            }
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                padding-top: 10px;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-around;
            }
            .sidebar a {
                flex-grow: 1;
                text-align: center;
            }
            .sidebar span {
                display: inline;
            }
        }

        .dark-mode {
            background: #0b1220;
            color: #e6eef8;
        }

        .dark-mode .card.form-card {
            background: #1e293b;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
        }

        .dark-mode .form-label {
            color: #e6eef8;
        }

        .dark-mode .form-control,
        .dark-mode .form-select {
            background: #334155;
            border-color: #475569;
            color: #e6eef8;
        }

        .dark-mode .form-control::placeholder {
            color: #94a3b8;
        }

        .dark-mode .btn-outline-secondary {
            color: #94a3b8;
            border-color: #475569;
        }

        .dark-mode .btn-outline-secondary:hover {
            background: #475569;
            color: #e6eef8;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3 class="text-center">لوحة التحكم</h3>
        <a href="dashboard.php"><i class="bi bi-house-fill"></i> <span>الرئيسية</span></a>
        <a href="add_product.php"><i class="bi bi-plus-square"></i> <span>إضافة منتج</span></a>
        <a href="../index.php"><i class="bi bi-card-list"></i> <span>المنتجات</span></a>
        <a href="logout.php" class="mt-4"><i class="bi bi-box-arrow-right"></i> <span>خروج</span></a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>✏️ تعديل المنتج</h2>
            <button id="modeToggle" class="btn btn-sm btn-outline-secondary">🌙 الوضع الداكن</button>
        </div>

        <?php if ($msg): ?>
            <div class="alert <?php echo ($msgClass === 'success') ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo safe($msg); ?>
            </div>
        <?php endif; ?>

        <div class="card form-card">
            <form id="editProductForm" method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="ajax" id="ajaxFlag" value="0">
                <input type="hidden" name="csrf_token" value="<?php echo safe($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="id" value="<?php echo $product_id; ?>">

                <div class="row g-3">
                    <div class="col-12">
                        <label for="name" class="form-label">اسم المنتج</label>
                        <input type="text" id="name" name="name" class="form-control" required value="<?php echo safe($product['name']); ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="price" class="form-label">السعر (ج.م)</label>
                        <input type="number" step="0.01" id="price" name="price" class="form-control" required min="0.01" value="<?php echo number_format((float)$product['price'], 2, '.', ''); ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="stock" class="form-label">الكمية (المخزون)</label>
                        <input type="number" id="stock" name="stock" class="form-control" min="0" value="<?php echo intval($product['stock']); ?>">
                    </div>

                    <div class="col-12">
                        <label for="category" class="form-label">الفئة</label>
                        <select id="category" name="category" class="form-select" required>
                            <option value="">اختر الفئة</option>
                            <?php foreach ($categories as $id => $cname): ?>
                                <option value="<?php echo intval($id); ?>" <?php echo ($product['category_id'] == $id) ? 'selected' : ''; ?>><?php echo $cname; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">الوصف (اختياري)</label>
                        <textarea id="description" name="description" class="form-control" rows="4"><?php echo safe($product['description']); ?></textarea>
                        <div class="form-text">سيتم تنقية الوصف وإزالة أي أكواد HTML أو روابط. الحد الأقصى 500 حرف.</div>
                    </div>

                    <div class="col-12">
                        <label for="countdown_time" class="form-label">وقت انتهاء العرض (العداد التنازلي)</label>
                        <?php 
                            $current_countdown = "";
                            if (!empty($product['countdown'])) {
                                $timestamp = (int)($product['countdown'] / 1000);
                                $current_countdown = date('Y-m-d\TH:i', $timestamp);
                            }
                        ?>
                        <input type="datetime-local" id="countdown_time" name="countdown_time" class="form-control" value="<?php echo $current_countdown; ?>">
                        <div class="form-text">اتركه فارغاً لإلغاء العداد التنازلي.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">الصورة الحالية</label>
                        <?php
                        $rawImage = ltrim($product['image'], '/');
                        $imgSrc = 'assets/images/' . $rawImage;
                        if (empty($product['image']) || !is_file(__DIR__ . '/assets/images/' . $rawImage)) {
                            $imgSrc = 'assets/images/default-product.png';
                        }
                        ?>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="<?php echo safe($imgSrc); ?>" alt="الصورة الحالية" class="img-preview" id="currentImage">
                            <div style="flex:1">
                                <label for="image" class="form-label">تغيير الصورة (اختياري)</label>
                                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                                <div class="small-note">مسموح JPG / PNG / GIF / WEBP — الحد الأقصى: 5MB</div>
                            </div>
                        </div>
                        <img id="preview" class="img-preview mt-2" src="#" alt="معاينة" style="display:none;">
                    </div>

                    <!-- ===== قسم الصور الإضافية ===== -->
                    <div class="col-12 mt-4">
                        <label class="form-label">الصور الإضافية</label>
                        <div id="additionalImagesContainer" class="d-flex flex-wrap gap-3 mb-3">
                            <?php foreach ($additional_images as $img): ?>
                                <div class="existing-image-wrapper" data-image-id="<?= $img['id'] ?>">
                                    <img src="../assets/images/<?= safe($img['image_path']) ?>" alt="صورة إضافية" class="img-preview">
                                    <button type="button" class="btn btn-sm btn-danger delete-image-btn" data-id="<?= $img['id'] ?>">❌</button>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <label for="additional_images" class="form-label">إضافة صور جديدة (اختياري)</label>
                        <input type="file" id="additional_images" name="additional_images[]" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
                        <div class="form-text">يمكنك اختيار أكثر من صورة لإضافتها لمعرض المنتج.</div>
                    </div>
                    <!-- ============================== -->

                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        <button type="submit" class="btn btn-success">تحديث المنتج ✅</button>
                        <a href="dashboard.php" class="btn btn-secondary">🔙 العودة للوحة التحكم</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // dark mode toggle
        const modeToggle = document.getElementById('modeToggle');
        const body = document.body;
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            modeToggle.textContent = '☀️ الوضع الفاتح';
        }
        modeToggle.addEventListener('click', function() {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
                modeToggle.textContent = '☀️ الوضع الفاتح';
            } else {
                localStorage.setItem('darkMode', 'disabled');
                modeToggle.textContent = '🌙 الوضع الداكن';
            }
        });

        // معاينة الصورة عند اختيار ملف
        const imageInput = document.getElementById('image');
        const preview = document.getElementById('preview');
        const currentImage = document.getElementById('currentImage');
        const additionalImagesContainer = document.getElementById('additionalImagesContainer');
        let imagesToDelete = [];

        imageInput.addEventListener('change', function() {
            const f = this.files[0];
            if (!f) {
                preview.style.display = 'none';
                return;
            }

            const allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (allowed.indexOf(f.type) === -1) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'صيغة الصورة غير مدعومة.',
                    showConfirmButton: false,
                    timer: 2500
                });
                this.value = '';
                preview.style.display = 'none';
                return;
            }
            if (f.size > 5 * 1024 * 1024) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'حجم الصورة أكبر من 5MB.',
                    showConfirmButton: false,
                    timer: 2500
                });
                this.value = '';
                preview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(f);
        });

        // منطق حذف الصور الإضافية
        additionalImagesContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-image-btn')) {
                e.preventDefault();
                const imageId = e.target.dataset.id;
                const wrapper = e.target.closest('.existing-image-wrapper');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: 'هل تريد حذف هذه الصورة الإضافية؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذفها',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        imagesToDelete.push(imageId);
                        wrapper.remove();
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'تم وضع الصورة للحذف',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
        });

        // إرسال AJAX بدل POST التقليدي
        const form = document.getElementById('editProductForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const ajaxFlag = document.getElementById('ajaxFlag');
            ajaxFlag.value = '1';

            const fd = new FormData(form);

            // إضافة قائمة الصور المحذوفة إلى FormData
            imagesToDelete.forEach(id => {
                fd.append('delete_images[]', id);
            });

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'جاري التحديث...';

            $.ajax({
                url: 'edit_product.php?id=<?php echo intval($product_id); ?>',
                method: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                dataType: 'json'
            }).done(function(res) {
                if (res && res.status === 'success') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: res.msg,
                        showConfirmButton: false,
                        timer: 2500
                    });
                    
                    if (res.product && res.product.image_url) {
                        document.getElementById('currentImage').src = res.product.image_url + '?t=' + Date.now();
                    }
                    
                    if (res.csrf_token) {
                        document.querySelector('input[name="csrf_token"]').value = res.csrf_token;
                    }

                    // تحديث الصور الإضافية في الواجهة
                    additionalImagesContainer.innerHTML = '';
                    if (res.product.additional_images && res.product.additional_images.length > 0) {
                        res.product.additional_images.forEach(img => {
                            const imgHtml = `
                                <div class="existing-image-wrapper" data-image-id="${img.id}">
                                    <img src="../assets/images/${img.image_path}" alt="صورة إضافية" class="img-preview">
                                    <button type="button" class="btn btn-sm btn-danger delete-image-btn" data-id="${img.id}">❌</button>
                                </div>
                            `;
                            additionalImagesContainer.insertAdjacentHTML('beforeend', imgHtml);
                        });
                    }
                    
                    imagesToDelete = []; // مسح قائمة الحذف بعد التحديث الناجح
                    document.getElementById('additional_images').value = ''; // مسح حقل رفع الصور الجديدة
                    document.getElementById('preview').style.display = 'none'; // إخفاء المعاينة

                } else {
                    const m = (res && res.msg) ? res.msg : 'حدث خطأ';
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: m,
                        showConfirmButton: false,
                        timer: 3500
                    });
                }
            }).fail(function(xhr, status) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'فشل الاتصال بالخادم.',
                    showConfirmButton: false,
                    timer: 3500
                });
            }).always(function() {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                ajaxFlag.value = '0';
            });
        });
    </script>
</body>
</html>