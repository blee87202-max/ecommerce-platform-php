<?php
declare(strict_types=1);

session_start();

require_once '../../Model/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$msg = "";
$msgClass = "alert-danger";
$newProduct = null;

$categories = [];
$res = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $categories[$row['id']] = $row['name'];
    }
}


function create_image_from_path(string $sourcePath, string $mime): GdImage|false
{
    $img = match ($mime) {
        'image/jpeg', 'image/pjpeg' => @imagecreatefromjpeg($sourcePath),
        'image/png' => @imagecreatefrompng($sourcePath),
        'image/gif' => @imagecreatefromgif($sourcePath),
        'image/webp' => @imagecreatefromwebp($sourcePath),
        default => false,
    };

    if (!$img) {
        $data = @file_get_contents($sourcePath);
        if ($data !== false) {
            $img = @imagecreatefromstring($data);
        }
    }

    if ($img) {
        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);
    }

    return $img;
}

function save_resized_jpeg(GdImage $srcImg, string $destPath, int $maxWidth, int $jpegQuality = 85): bool
{
    $w = imagesx($srcImg);
    $h = imagesy($srcImg);

    $new_w = ($w > $maxWidth) ? $maxWidth : $w;
    $new_h = (int) round(($new_w / $w) * $h);

    $dst = imagecreatetruecolor($new_w, $new_h);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);

    imagecopyresampled($dst, $srcImg, 0, 0, 0, 0, $new_w, $new_h, $w, $h);

    $saved = imagejpeg($dst, $destPath, $jpegQuality);
    imagedestroy($dst);

    return $saved;
}

function save_resized_webp(GdImage $srcImg, string $destPath, int $maxWidth, int $webpQuality = 85): bool
{
    $w = imagesx($srcImg);
    $h = imagesy($srcImg);

    $new_w = ($w > $maxWidth) ? $maxWidth : $w;
    $new_h = (int) round(($new_w / $w) * $h);

    $dst = imagecreatetruecolor($new_w, $new_h);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);

    imagecopyresampled($dst, $srcImg, 0, 0, 0, 0, $new_w, $new_h, $w, $h);

    $saved = imagewebp($dst, $destPath, $webpQuality);
    imagedestroy($dst);

    return $saved;
}

function save_square_thumbnail(GdImage $srcImg, string $destPath, int $size, int $jpegQuality = 85): bool
{
    $w = imagesx($srcImg);
    $h = imagesy($srcImg);

    $crop_size = min($w, $h);
    $x = (int) (($w - $crop_size) / 2);
    $y = (int) (($h - $crop_size) / 2);

    $dst = imagecreatetruecolor($size, $size);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);

    imagecopyresampled($dst, $srcImg, 0, 0, $x, $y, $size, $size, $crop_size, $crop_size);

    $saved = imagejpeg($dst, $destPath, $jpegQuality);
    imagedestroy($dst);

    return $saved;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjax = ($_POST['ajax'] ?? '0') === '1';

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $msg = "فشل التحقق من الأمان. حاول إعادة تحميل الصفحة.";
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(403);
            echo json_encode(['status' => 'error', 'msg' => $msg]);
            exit;
        }
    }

    $name = trim($_POST['name'] ?? '');
    $price = (float) ($_POST['price'] ?? 0.0);
    $category_id = (int) ($_POST['category'] ?? 0);
    $stock = max(0, (int) ($_POST['stock'] ?? 0));
    $description = trim(strip_tags($_POST['description'] ?? ''));
    $description = preg_replace('/(http|https|ftp|ftps )\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', '', $description);
    
    $countdown_time = trim($_POST['countdown_time'] ?? '');
    $countdown_timestamp = !empty($countdown_time) ? strtotime($countdown_time) * 1000 : null;

    $max_desc_length = 500;
    if (mb_strlen($description, 'UTF-8') > $max_desc_length) {
        $description = mb_substr($description, 0, $max_desc_length, 'UTF-8') . '...';
    }

    if (empty($name) || $price <= 0 || !array_key_exists($category_id, $categories)) {
        $msg = "الرجاء إدخال اسم صحيح، سعر أكبر من صفر، واختيار فئة.";
    } elseif (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $msg = "الرجاء اختيار صورة المنتج الرئيسية.";
    }

    if ($msg === '') {
        $uploadDirServer = __DIR__ . '/assets/images/products/';
        if (!is_dir($uploadDirServer)) {
            mkdir($uploadDirServer, 0755, true);
        }

        $tmpPath = $_FILES['image']['tmp_name'];
        
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $msg = "حجم الصورة كبير جدًا. الحد الأقصى 5MB.";
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmpPath);
            finfo_close($finfo);

            $allowed_mimes = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (!in_array($mime, $allowed_mimes) && !($mime === 'application/octet-stream' && in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))) {
                $msg = "صيغة الصورة غير مدعومة أو الملف تالف. (MIME: " . htmlspecialchars($mime) . "). تأكد من أن الملف بصيغة صالحة.";
            }
        }

        if ($msg === '') {
            $srcImg = create_image_from_path($tmpPath, $mime);

            if (!$srcImg) {
                $msg = "فشل معالجة الصورة. تأكد من أن الملف غير تالف وأن امتدادات GD مفعلة بشكل كامل على الخادم.";
            } else {
                $safe_filename = uniqid('p_', true);
                
                $mainImagePathServer = $uploadDirServer . $safe_filename . '.jpg';
                $webpPathServer = $uploadDirServer . $safe_filename . '.webp';
                $thumb300PathServer = $uploadDirServer . $safe_filename . '_300x300.jpg';
                $thumb800PathServer = $uploadDirServer . $safe_filename . '_800x800.jpg';

                $converted_jpg = save_resized_jpeg($srcImg, $mainImagePathServer, 1200, 90);
                
                $converted_webp = save_resized_webp($srcImg, $webpPathServer, 1200, 90);

                if (!$converted_jpg) {
                    $msg = "حدث خطأ أثناء تحويل الصورة إلى JPG.";
                } else {
                    save_square_thumbnail($srcImg, $thumb300PathServer, 300, 85);
                    save_square_thumbnail($srcImg, $thumb800PathServer, 800, 85);

                    imagedestroy($srcImg);

                    $publicImagePath = 'products/' . basename($mainImagePathServer);
                    $publicImageWebpPath = $converted_webp ? 'products/' . basename($webpPathServer) : '';
                    $publicImageThumb300Path = 'products/' . basename($thumb300PathServer);
                    $publicImageThumb800Path = 'products/' . basename($thumb800PathServer);

                    $stmt = $conn->prepare("INSERT INTO products (name, price, image, image_webp, image_thumb_300, image_thumb_800, category_id, stock, description, countdown) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                    if (!$stmt) {
                        error_log("DB prepare error: " . $conn->error);
                        $msg = "حدث خطأ في الخادم. يرجى المحاولة مرة أخرى.";
                    } else {
                        $stmt->bind_param("sdssssiiss", $name, $price, $publicImagePath, $publicImageWebpPath, $publicImageThumb300Path, $publicImageThumb800Path, $category_id, $stock, $description, $countdown_timestamp);

                        if ($stmt->execute()) {
                            $insertId = $stmt->insert_id;
                            $stmt->close();

                            if (isset($_FILES['additional_images']) && is_array($_FILES['additional_images']['tmp_name'])) {
                                $sort_order = 1;
                                $insertImageStmt = $conn->prepare("INSERT INTO product_images (product_id, image_path, sort_order) VALUES (?, ?, ?)");

                                foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
                                    if ($_FILES['additional_images']['error'][$key] === UPLOAD_ERR_OK) {
                                        $file_name = $_FILES['additional_images']['name'][$key];
                                        $file_size = $_FILES['additional_images']['size'][$key];

                                        if ($file_size > 5 * 1024 * 1024) {
                                            error_log("Additional image too large: " . $file_name);
                                            continue;
                                        }

                                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                                        $mime = finfo_file($finfo, $tmp_name);
                                        finfo_close($finfo);

                                        if (!in_array($mime, $allowed_mimes)) {
                                            error_log("Additional image type not allowed: " . $file_name);
                                            continue;
                                        }

                                        $srcImgAdd = create_image_from_path($tmp_name, $mime);
                                        if ($srcImgAdd) {
                                            $safe_filename_add = uniqid('p_', true) . '_add_' . $sort_order;
                                            $mainImagePathServerAdd = $uploadDirServer . $safe_filename_add . '.jpg';

                                            // حفظ الصورة الإضافية كـ JPG
                                            if (save_resized_jpeg($srcImgAdd, $mainImagePathServerAdd, 1200, 90)) {
                                                $publicImagePathAdd = 'products/' . basename($mainImagePathServerAdd);
                                                
                                                // إدراج في جدول الصور الإضافية
                                                $insertImageStmt->bind_param("isi", $insertId, $publicImagePathAdd, $sort_order);
                                                $insertImageStmt->execute();
                                                $sort_order++;
                                            }
                                            imagedestroy($srcImgAdd);
                                        }
                                    }
                                }
                                if ($insertImageStmt) $insertImageStmt->close();
                            }

                            $gstmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                            $gstmt->bind_param("i", $insertId);
                            $gstmt->execute();
                            $result = $gstmt->get_result();
                            $newProduct = $result->fetch_assoc();
                            $gstmt->close();

                            if ($newProduct) {
                                $newProduct['name'] = htmlspecialchars($newProduct['name']);
                                $newProduct['price'] = number_format((float)$newProduct['price'], 2);
                                $newProduct['image_url'] = 'assets/images/' . ltrim($newProduct['image_thumb_300'], '/');
                                $newProduct['stock'] = (int)$newProduct['stock'];
                                $newProduct['description'] = htmlspecialchars($newProduct['description']);
                            }

                            $msg = "تم إضافة المنتج بنجاح.";
                            $msgClass = "alert-success";

                            if ($isAjax) {
                                header('Content-Type: application/json; charset=utf-8');
                                echo json_encode(['status' => 'success', 'msg' => $msg, 'product' => $newProduct]);
                                exit;
                            }
                        } else {
                            error_log("DB execute error: " . $stmt->error);
                            $msg = "خطأ في قاعدة البيانات أثناء حفظ المنتج.";
                            $stmt->close();
                        }
                    }
                }
                if ($msgClass === 'alert-danger') {
                    array_map('unlink', glob($uploadDirServer . $safe_filename . "*"));
                }
            }
        }
    }
    if ($isAjax && $msgClass === 'alert-danger') {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(422);
        echo json_encode(['status' => 'error', 'msg' => $msg]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>➕ إضافة منتج متقدم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
            max-width: 820px;
            margin: 10px auto;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
        }

        .img-preview {
            width: 140px;
            height: 140px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px dashed #ddd;
            background: #fff;
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

        .progress-container {
            height: 20px;
            margin-top: 10px;
        }

        .progress-bar {
            transition: width 0.3s ease;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h3 class="text-center">لوحة التحكم</h3>
        <a href="dashboard.php"><i class="bi bi-house-fill"></i> <span>الرئيسية</span></a>
        <a href="add_product.php" class="active"><i class="bi bi-plus-square"></i> <span>إضافة منتج</span></a>
        <a href="analytics.php"><i class="bi bi-graph-up"></i> <span>التحليلات</span></a>
        <a href="logout.php" class="mt-4"><i class="bi bi-box-arrow-right"></i> <span>خروج</span></a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>➕ إضافة منتج جديد</h2>
            <button id="modeToggle" class="btn btn-sm btn-outline-secondary">🌙 الوضع الداكن</button>
        </div>

        <div id="alert-container">
            <?php if ($msg): ?>
                <div class="alert <?php echo htmlspecialchars($msgClass); ?>" role="alert"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
        </div>

        <div class="card form-card">
            <form id="addProductForm" method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="ajax" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div class="row g-3">
                    <div class="col-12">
                        <label for="name" class="form-label">اسم المنتج</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                        <div class="invalid-feedback">الرجاء إدخال اسم المنتج.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="price" class="form-label">السعر (ج.م)</label>
                        <input type="number" step="0.01" id="price" name="price" class="form-control" min="0.01" required>
                        <div class="invalid-feedback">الرجاء إدخال سعر صالح.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="stock" class="form-label">الكمية في المخزون</label>
                        <input type="number" id="stock" name="stock" class="form-control" min="0" value="0" required>
                        <div class="invalid-feedback">الرجاء إدخال كمية صحيحة.</div>
                    </div>

                    <div class="col-12">
                        <label for="category" class="form-label">الفئة</label>
                        <select id="category" name="category" class="form-select" required>
                            <option value="">اختر فئة...</option>
                            <?php foreach ($categories as $id => $name): ?>
                                <option value="<?php echo htmlspecialchars((string)$id); ?>"><?php echo htmlspecialchars($name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">الرجاء اختيار فئة.</div>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">الوصف (اختياري)</label>
                        <textarea id="description" name="description" class="form-control" rows="3" maxlength="500"></textarea>
                        <div class="form-text">سيتم تنقية الوصف وإزالة أي أكواد HTML أو روابط. الحد الأقصى 500 حرف.</div>
                    </div>

                    <div class="col-12">
                        <label for="countdown_time" class="form-label">وقت انتهاء العرض (العداد التنازلي)</label>
                        <input type="datetime-local" id="countdown_time" name="countdown_time" class="form-control">
                        <div class="form-text">اتركه فارغاً إذا كنت لا تريد تفعيل العداد التنازلي لهذا المنتج.</div>
                    </div>

                    <div class="col-12">
                        <label for="image" class="form-label">صورة المنتج الرئيسية (JPG, PNG, GIF, WEBP)</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" required>
                        <div class="invalid-feedback">الرجاء اختيار صورة رئيسية.</div>
                        <div class="progress-container">
                            <div class="progress" style="display: none;">
                                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="additional_images" class="form-label">صور إضافية للمنتج (اختياري - يمكنك اختيار أكثر من صورة)</label>
                        <input type="file" id="additional_images" name="additional_images[]" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
                        <div class="form-text">سيتم استخدام هذه الصور في معرض الصور المتعددة.</div>
                    </div>

                    <div class="col-12 d-flex align-items-center">
                        <img id="imagePreview" src="" alt="معاينة الصورة" class="img-preview me-3" style="display: none;">
                        <div id="previewText" class="text-muted">ستظهر معاينة الصورة هنا بعد الرفع.</div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">إضافة المنتج</button>
                    </div>
                </div>
            </form>
        </div>

        <h3 class="mt-5 mb-3">المنتجات المضافة حديثاً</h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="recentProductsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>الاسم</th>
                        <th>السعر</th>
                        <th>المخزون</th>
                        <th>الفئة</th>
                        <th>الوصف</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('addProductForm');
            const imageInput = document.getElementById('image');
            const additionalImagesInput = document.getElementById('additional_images');
            const imagePreview = document.getElementById('imagePreview');
            const previewText = document.getElementById('previewText');
            const submitBtn = document.getElementById('submitBtn');
            const alertContainer = document.getElementById('alert-container');
            const recentProductsTableBody = document.getElementById('recentProductsTable').querySelector('tbody');
            const progressBar = document.querySelector('.progress');
            const progressBarInner = document.querySelector('.progress-bar');

            imageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                        previewText.style.display = 'none';
                    }
                    reader.readAsDataURL(this.files[0]);
                } else {
                    imagePreview.style.display = 'none';
                    previewText.style.display = 'block';
                }
            });

            // Clear additional images input on main image change
            imageInput.addEventListener('change', function() {
                additionalImagesInput.value = '';
            });

            function showAlert(message, type = 'danger') {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                alertContainer.innerHTML = alertHtml;
            }

            function addProductRow(product) {
                const newRow = recentProductsTableBody.insertRow(0);
                newRow.innerHTML = `
                    <td>${product.id}</td>
                    <td><img src="${product.image_url}" alt="${product.name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"></td>
                    <td>${product.name}</td>
                    <td>${product.price}</td>
                    <td>${product.stock}</td>
                    <td>${document.querySelector(`#category option[value="${product.category_id}"]`).textContent}</td>
                    <td>${product.description}</td>
                `;
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!form.checkValidity()) {
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }
                form.classList.add('was-validated');

                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();
                xhr.open('POST', form.action || 'add_product.php', true);

                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        progressBarInner.style.width = percent + '%';
                        progressBarInner.textContent = percent + '%';
                    }
                });

                xhr.addEventListener('loadstart', () => {
                    progressBar.style.display = 'flex';
                    progressBarInner.style.width = '0%';
                    progressBarInner.textContent = '0%';
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'جاري الإضافة...';
                    alertContainer.innerHTML = '';
                });

                xhr.addEventListener('loadend', () => {
                    progressBar.style.display = 'none';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'إضافة المنتج';
                });

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.status === 'success') {
                                showAlert(response.msg, 'success');
                                addProductRow(response.product);
                                form.reset();
                                form.classList.remove('was-validated');
                                imagePreview.style.display = 'none';
                                previewText.style.display = 'block';
                            } else {
                                showAlert(response.msg || 'حدث خطأ غير متوقع.');
                            }
                        } catch (err) {
                            showAlert('حدث خطأ في تحليل استجابة الخادم.');
                            console.error("Server Response:", xhr.responseText);
                        }
                    } else {
                        let errorMsg = `فشل الاتصال بالخادم. رمز الخطأ: ${xhr.status}`;
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if(response.msg) errorMsg = response.msg;
                        } catch(err){}
                        showAlert(errorMsg);
                    }
                };

                xhr.onerror = function() {
                    showAlert('فشل في إرسال الطلب بسبب خطأ في الشبكة.');
                };

                xhr.send(formData);
            });

            const modeToggle = document.getElementById('modeToggle');
            const body = document.body;

            function applyTheme(theme) {
                if (theme === 'dark') {
                    body.classList.add('dark-mode');
                    modeToggle.textContent = '☀️ الوضع الفاتح';
                } else {
                    body.classList.remove('dark-mode');
                    modeToggle.textContent = '🌙 الوضع الداكن';
                }
            }

            if (localStorage.getItem('darkMode') === 'enabled') {
                applyTheme('dark');
            } else {
                applyTheme('light');
            }

            modeToggle.addEventListener('click', function() {
                const isDarkMode = body.classList.toggle('dark-mode');
                if (isDarkMode) {
                    localStorage.setItem('darkMode', 'enabled');
                    applyTheme('dark');
                } else {
                    localStorage.setItem('darkMode', 'disabled');
                    applyTheme('light');
                }
            });
        });
    </script>
</body>

</html>