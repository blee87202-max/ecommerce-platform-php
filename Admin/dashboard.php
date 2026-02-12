<?php

/**
 * لوحة تحكم متكاملة ومحسّنة - RTL Dashboard
 * الميزات:
 * - واجهة حديثة وواضحة مع تصميم RTL نظيف
 * - جداول محسنة (DataTables) مع بحث مُحسّن وتأخير debounce
 * - نظام إشعارات (toasts) خفيف
 * - إجراءات فردية وجماعية لتحديث حالات الطلبات عبر AJAX
 * - مودال لعرض تفاصيل الطلب مع محاولات استدعاء متعددة
 * - تحسينات أمنية: تعقيم المخرجات، استخدام real_escape_string، التحقق من الجلسة
 * - تحسينات تفاعلية: lazy images، إبراز السطر بعد التحديث، دعم الوضع الداكن
 */

require __DIR__ . '/admin_init.php';
require __DIR__ . '/admin_helpers.php'; // تضمين الدوال المساعدة


// التحقق من الصلاحيات
if (!is_admin()) {
    header("Location: login.php");
    exit;
}



// ===== جلب الإحصائيات (مُحسّن) =====
$statsSql = "SELECT
    COUNT(*) AS totalOrders,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completedOrders,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pendingOrders,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelledOrders,
    IFNULL(SUM(CASE WHEN status = 'completed' THEN total ELSE 0 END), 0) AS totalSales
FROM orders";
$statsRes = $conn->query($statsSql);
$stats = $statsRes ? $statsRes->fetch_assoc() : [];
$totalOrders = (int)val($stats, 'totalOrders');
$completedOrders = (int)val($stats, 'completedOrders');
$pendingOrders = (int)val($stats, 'pendingOrders');
$cancelledOrders = (int)val($stats, 'cancelledOrders');
$totalSales = (float)val($stats, 'totalSales');
if ($statsRes) $statsRes->free();

// ===== مبيعات اليوم (مُحسّن) =====
$today = date('Y-m-d');
$salesToday = 0.0;
$stmtToday = $conn->prepare("SELECT IFNULL(SUM(total),0) AS total FROM orders WHERE status='completed' AND DATE(order_date)=?");
if ($stmtToday) {
    $stmtToday->bind_param('s', $today);
    $stmtToday->execute();
    $r = stmt_get_one($stmtToday);
    $salesToday = (float)val($r, 'total');
    $stmtToday->close();
}

// ===== مبيعات الأسبوع (آخر 7 أيام) - مُحسّن =====
$weekDays = [];
$weekSales = [];
$endDate = date('Y-m-d');
$startDate = date('Y-m-d', strtotime('-6 days'));
$mapWeekSales = [];

$stmtWeek = $conn->prepare("SELECT DATE(order_date) AS d, IFNULL(SUM(total),0) AS s FROM orders WHERE status='completed' AND DATE(order_date) BETWEEN ? AND ? GROUP BY DATE(order_date)");
if ($stmtWeek) {
    $stmtWeek->bind_param('ss', $startDate, $endDate);
    $stmtWeek->execute();
    $resWeek = stmt_get_all($stmtWeek);
    foreach ($resWeek as $r) {
        $mapWeekSales[$r['d']] = (float)$r['s'];
    }
    $stmtWeek->close();
}

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $weekDays[] = date('D', strtotime($date));
    $weekSales[] = val($mapWeekSales, $date, 0.0);
}

// ===== مبيعات الشهر - مُحسّن =====
$monthDays = [];
$monthSales = [];
$daysInMonth = (int)date('t');
$monthStart = date('Y-m-01');
$monthEnd = date('Y-m-t');
$mapMonthSales = [];

$stmtMonth = $conn->prepare("SELECT DATE(order_date) AS d, IFNULL(SUM(total),0) AS s FROM orders WHERE status='completed' AND DATE(order_date) BETWEEN ? AND ? GROUP BY DATE(order_date)");
if ($stmtMonth) {
    $stmtMonth->bind_param('ss', $monthStart, $monthEnd);
    $stmtMonth->execute();
    $resMonth = stmt_get_all($stmtMonth);
    foreach ($resMonth as $r) {
        $mapMonthSales[$r['d']] = (float)$r['s'];
    }
    $stmtMonth->close();
}

for ($d = 1; $d <= $daysInMonth; $d++) {
    $date = date('Y-m-') . str_pad($d, 2, '0', STR_PAD_LEFT);
    $monthDays[] = $d;
    $monthSales[] = val($mapMonthSales, $date, 0.0);
}

// ===== أعلى 5 منتجات مبيعًا =====
$topProducts = [];
$topProductSales = [];
$res = $conn->query("SELECT products.name AS pname, SUM(quantity) AS total_qty FROM order_items INNER JOIN products ON products.id=order_items.product_id GROUP BY product_id ORDER BY total_qty DESC LIMIT 5");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $topProducts[] = $r['pname'];
        $topProductSales[] = (int)$r['total_qty'];
    }
    $res->free();
}

// ===== جلب المنتجات والطلبات =====
$prodRes = $conn->query("SELECT id, name, price, image, stock FROM products ORDER BY id DESC LIMIT 200");
$ordersRes = $conn->query("SELECT id, customer_name, phone, total, status, order_date FROM orders ORDER BY id DESC LIMIT 200");

// دالة لحل مسار الصورة
function resolveImageUrl($imgName)
{
    $imgName = ltrim($imgName ?: 'default-product.png', '/');
    $candidates = [
        __DIR__ . '/assets/images/' . $imgName => 'assets/images/' . $imgName,
        __DIR__ . '/../assets/images/' . $imgName => '../assets/images/' . $imgName,
        __DIR__ . '/admin/assets/images/' . $imgName => 'admin/assets/images/' . $imgName,
        __DIR__ . '/../../assets/images/' . $imgName => '../../assets/images/' . $imgName,
    ];
    foreach ($candidates as $file => $url) {
        if (file_exists($file)) {
            return $url;
        }
    }
    return 'assets/images/default-product.png';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم — المتجر</title>
    <meta name="csrf-token" content="<?php echo esc(csrf_token()); ?>">

    <!-- Bootstrap RTL + Google Fonts Cairo -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- أضف هذا السطر -->

    <style>
        :root {
            --bg: #f6f8fb;
            --card: #ffffff;
            --text: #222222;
            --muted: #6c757d;
            --accent: #6f42c1;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --border: #dee2e6;
        }

        body.dark-mode {
            --bg: #0b1221;
            --card: #1a1f3a;
            --text: #e0e0e0;
            --muted: #9ca3af;
            --border: #2d3748;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', system-ui, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #2b2fc1, #5a3dd6);
            color: #fff;
            padding: 28px 18px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar .brand {
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar nav a {
            color: rgba(255, 255, 255, 0.95);
            display: block;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: rgba(255, 255, 255, 0.15);
            padding-right: 18px;
        }

        .sidebar .user-info {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Main Content */
        main {
            flex: 1;
            margin-right: 260px;
            padding: 22px;
            overflow-y: auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            gap: 20px;
        }

        .topbar h3 {
            margin: 0;
            font-weight: 700;
        }

        .topbar-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .metric {
            background: var(--card);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(32, 40, 60, 0.06);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .metric:hover {
            box-shadow: 0 12px 32px rgba(32, 40, 60, 0.12);
            transform: translateY(-2px);
        }

        .metric h6 {
            margin: 0;
            color: var(--muted);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric .value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-top: 10px;
            color: var(--accent);
        }

        .metric .small-muted {
            color: var(--muted);
            font-size: 0.85rem;
            margin-top: 8px;
        }

        /* Card UI */
        .card-ui {
            background: var(--card);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(32, 40, 60, 0.06);
            border: 1px solid var(--border);
            margin-bottom: 20px;
        }

        .card-ui h6 {
            margin: 0 0 16px 0;
            font-weight: 700;
            font-size: 1rem;
        }

        /* Tables */
        table.dataTable {
            background: var(--card);
        }

        table.dataTable thead th {
            background: var(--bg);
            color: var(--text);
            font-weight: 600;
            border-color: var(--border);
            padding: 12px !important;
        }

        table.dataTable tbody td {
            padding: 12px !important;
            border-color: var(--border);
            vertical-align: middle;
        }

        table.dataTable tbody tr:hover {
            background: var(--bg) !important;
        }

        .product-img {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge.bg-success {
            background: var(--success) !important;
        }

        .badge.bg-warning {
            background: var(--warning) !important;
            color: #000 !important;
        }

        .badge.bg-danger {
            background: var(--danger) !important;
        }

        /* Toast Container */
        #toast-container {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 2200;
            max-width: 400px;
        }

        .toast-item {
            background: #333;
            color: #fff;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s ease;
            border-left: 4px solid #666;
        }

        .toast-item.success {
            background: var(--success);
            border-left-color: #1e7e34;
        }

        .toast-item.error {
            background: var(--danger);
            border-left-color: #a02622;
        }

        .toast-item.warning {
            background: var(--warning);
            color: #000;
            border-left-color: #cc8800;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(-100%);
                opacity: 0;
            }
        }

        .toast-item.removing {
            animation: slideOut 0.3s ease forwards;
        }

        /* Row Update Animation */
        @keyframes pulseBG {
            0% {
                background: rgba(40, 167, 69, 0.2);
            }

            100% {
                background: transparent;
            }
        }

        .row-updated {
            animation: pulseBG 1.6s ease-in-out;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 8px 16px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .btn-primary {
            background: var(--accent);
            border-color: var(--accent);
        }

        .btn-primary:hover {
            background: #5a35a8;
            border-color: #5a35a8;
        }

        /* Modal */
        .modal-content {
            background: var(--card);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .modal-header {
            border-bottom: 1px solid var(--border);
            background: var(--bg);
        }

        .modal-title {
            font-weight: 700;
        }

        .btn-close {
            filter: invert(1);
        }

        body.dark-mode .btn-close {
            filter: invert(0);
        }

        /* Search Input */
        .form-control {
            background: var(--card);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .form-control:focus {
            background: var(--card);
            color: var(--text);
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                padding: 0;
                overflow: hidden;
            }

            main {
                margin-right: 0;
                padding: 14px;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .topbar-actions {
                width: 100%;
                flex-wrap: wrap;
            }

            #toast-container {
                left: 10px;
                right: 10px;
                max-width: none;
            }
        }

        /* DataTables Responsive */
        .dataTables_wrapper {
            color: var(--text);
        }

        .dataTables_info {
            color: var(--muted);
        }

        .paginate_button {
            color: var(--text) !important;
        }

        .paginate_button.current {
            background: var(--accent) !important;
            color: white !important;
        }
    </style>
</head>

<body>
    <div class="app-shell">
        <!-- Sidebar -->
        <aside class="sidebar" role="navigation" aria-label="القائمة الجانبية">
            <div class="brand">
                <span>📊</span>
                <span>لوحة الإدارة</span>
            </div>
            <nav>
                <a href="dashboard.php" class="active">🏠 الرئيسية</a>
                <a href="add_product.php">➕ إضافة منتج</a>
                <a href="analytics.php">📈 التحليلات</a>
                <a href="../index.php" target="_blank">🏪 عرض المتجر</a>
                <a href="logout.php">🚪 تسجيل الخروج</a>
            </nav>
            <div class="user-info">
                <div style="margin-bottom: 8px;">المستخدم:</div>
                <strong><?php echo esc(isset($_SESSION['admin']) ? $_SESSION['admin'] : 'مسؤول'); ?></strong>
            </div>
        </aside>

        <!-- Main Content -->
        <main>
            <!-- Topbar -->
            <div class="topbar">
                <div>
                    <h3>مرحبًا، <?php echo esc(isset($_SESSION['admin']) ? $_SESSION['admin'] : 'مسؤول'); ?> 👋</h3>
                    <div style="color: var(--muted); font-size: 0.9rem;">نظرة سريعة على أداء المتجر</div>
                </div>
                <div class="topbar-actions">
                    <button id="modeToggle" class="btn btn-sm btn-outline-secondary" aria-pressed="false">
                        🌙 الوضع الداكن
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            ⚙️ إجراءات سريعة
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="add_product.php">إضافة منتج جديد</a></li>
                            <li><a class="dropdown-item" href="export_orders_csv.php">تصدير الطلبات (CSV)</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Metrics Cards -->
            <section class="cards-grid">
                <div class="metric">
                    <h6>📦 الطلبات</h6>
                    <div class="value"><?php echo $totalOrders; ?></div>
                    <div class="small-muted">إجمالي الطلبات</div>
                </div>
                <div class="metric">
                    <h6>✅ مكتملة</h6>
                    <div class="value"><?php echo $completedOrders; ?></div>
                    <div class="small-muted">طلبات تم تسليمها</div>
                </div>
                <div class="metric">
                    <h6>⏳ قيد التنفيذ</h6>
                    <div class="value"><?php echo $pendingOrders; ?></div>
                    <div class="small-muted">طلبات معلقة</div>
                </div>
                <div class="metric">
                    <h6>❌ ملغاة</h6>
                    <div class="value"><?php echo $cancelledOrders; ?></div>
                    <div class="small-muted">طلبات ملغاة</div>
                </div>
                <div class="metric">
                    <h6>💰 المبيعات</h6>
                    <div class="value"><?php echo number_format((float)$totalSales, 2); ?></div>
                    <div class="small-muted">إجمالي المبيعات (ج.م)</div>
                </div>
            </section>

            <!-- Charts Section -->
            <div class="row g-3 mb-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card-ui">
                        <h6>📊 مبيعات اليوم</h6>
                        <canvas id="chartToday" height="120" aria-label="مبيعات اليوم" role="img"></canvas>
                        <div class="small-muted mt-2">
                            التاريخ: <strong><?php echo esc($today); ?></strong> — الإجمالي: <strong><?php echo number_format($salesToday, 2); ?> ج.م</strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card-ui">
                        <h6>📈 مبيعات الأسبوع</h6>
                        <canvas id="chartWeek" height="120" aria-label="مبيعات الأسبوع" role="img"></canvas>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card-ui">
                        <h6>📉 مبيعات الشهر</h6>
                        <canvas id="chartMonth" height="120" aria-label="مبيعات الشهر" role="img"></canvas>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card-ui">
                        <h6>🏆 أعلى 5 منتجات مبيعًا</h6>
                        <canvas id="chartTop" height="80" aria-label="أعلى المنتجات" role="img"></canvas>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card-ui">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="m-0">📦 المنتجات</h5>
                    <input id="productSearch" class="form-control form-control-sm" style="max-width: 300px;" placeholder="🔍 ابحث في المنتجات..." aria-label="ابحث في المنتجات">
                </div>
                <div class="table-responsive">
                    <table id="productsTable" class="table table-hover table-bordered align-middle text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>الاسم</th>
                                <th>السعر</th>
                                <th>الكمية</th>
                                <th>الصورة</th>
                                <th>التحكم</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($prodRes && $prodRes->num_rows > 0): ?>
                                <?php while ($row = $prodRes->fetch_assoc()):
                                    $img = resolveImageUrl(isset($row['image']) ? $row['image'] : '');
                                ?>
                                    <tr>
                                        <td><?php echo (int)$row['id']; ?></td>
                                        <td class="text-start"><?php echo esc($row['name']); ?></td>
                                        <td><?php echo number_format((float)$row['price'], 2); ?> ج.م</td>
                                        <td><?php echo isset($row['stock']) ? (int)$row['stock'] : 0; ?></td>
                                        <td>
                                            <img class="product-img" loading="lazy" src="<?php echo esc($img); ?>" alt="<?php echo esc($row['name']); ?>">
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-primary" href="edit_product.php?id=<?php echo (int)$row['id']; ?>">✏️ تعديل</a>
                                            <button class="btn btn-sm btn-outline-danger delete-product-btn" data-id="<?php echo (int)$row['id']; ?>">🗑️ حذف</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php $prodRes->free(); ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-muted">لا توجد منتجات</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Orders Table with Bulk Actions -->
            <div class="card-ui">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="m-0">📋 الطلبات الأخيرة</h5>
                    <div class="d-flex gap-2">
                        <input id="orderSearch" class="form-control form-control-sm" placeholder="🔍 ابحث في الطلبات..." style="max-width: 250px;">
                        <button id="exportCsv" class="btn btn-sm btn-outline-primary">📥 تصدير CSV</button>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div class="mb-3 d-flex gap-2 flex-wrap">
                    <button id="selectAllBtn" class="btn btn-sm btn-outline-secondary">☑️ تحديد الكل</button>
                    <button id="bulkComplete" class="btn btn-sm btn-success">✅ تعيين كمكتمل</button>
                    <button id="bulkPending" class="btn btn-sm btn-warning">⏳ تعيين كقيد</button>
                    <button id="bulkCancel" class="btn btn-sm btn-danger">❌ تعيين كملغى</button>
                </div>

                <div class="table-responsive">
                    <table id="ordersTable" class="table table-hover table-striped text-center align-middle">
                        <thead>
                            <tr>
                                <th><input id="headerCheckbox" type="checkbox" aria-label="تحديد الكل"></th>
                                <th>ID</th>
                                <th>العميل</th>
                                <th>الهاتف</th>
                                <th>الإجمالي</th>
                                <th>الحالة</th>
                                <th>التحكم</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($ordersRes && $ordersRes->num_rows > 0): ?>
                                <?php while ($o = $ordersRes->fetch_assoc()): ?>
                                    <tr data-order-id="<?php echo (int)$o['id']; ?>">
                                        <td><input class="order-checkbox" type="checkbox" value="<?php echo (int)$o['id']; ?>"></td>
                                        <td><?php echo (int)$o['id']; ?></td>
                                        <td class="text-start"><?php echo esc($o['customer_name']); ?></td>
                                        <td><?php echo esc($o['phone']); ?></td>
                                        <td><?php echo number_format((float)$o['total'], 2); ?> ج.م</td>
                                        <td>
                                            <span class="badge <?php echo $o['status'] === 'completed' ? 'bg-success' : ($o['status'] === 'pending' ? 'bg-warning text-dark' : 'bg-danger'); ?>">
                                                <?php echo $o['status'] === 'pending' ? 'قيد التنفيذ' : ($o['status'] === 'completed' ? 'تم التسليم' : 'ملغى'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 justify-content-center">
                                                <button class="btn btn-sm btn-success order-status-btn" data-id="<?php echo (int)$o['id']; ?>" data-status="completed" title="تعيين كمكتمل">✅</button>
                                                <button class="btn btn-sm btn-warning order-status-btn" data-id="<?php echo (int)$o['id']; ?>" data-status="pending" title="تعيين كقيد">⏳</button>
                                                <button class="btn btn-sm btn-danger order-status-btn" data-id="<?php echo (int)$o['id']; ?>" data-status="cancelled" title="تعيين كملغى">❌</button>
                                                <button class="btn btn-sm btn-info view-order-btn" data-id="<?php echo (int)$o['id']; ?>" title="عرض التفاصيل">📄</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php $ordersRes->free(); ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-muted">لا توجد طلبات حالياً</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" aria-live="polite" aria-atomic="true"></div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📋 تفاصيل الطلب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body" id="orderModalBody">جارٍ التحميل...</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ===== Helper Functions =====
        const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

        function toast(msg, type = 'info', timeout = 3000) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const el = document.createElement('div');
            el.className = `toast-item ${type}`;
            el.textContent = msg;
            container.appendChild(el);

            setTimeout(() => {
                el.classList.add('removing');
                setTimeout(() => el.remove(), 300);
            }, timeout);
        }

        function debounce(fn, delay) {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => fn.apply(this, args), delay);
            };
        }

        function wait(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        function esc(s) {
            if (s == null) return '';
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // ===== Toast Notification Function =====
        function showToast(type, message) {
            const container = $('#toast-container');
            const toast = $('<div>').addClass('toast-item').addClass(type).text(message);
            container.prepend(toast);

            setTimeout(() => {
                toast.addClass('removing');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 4000);
        }



        // ===== Dark Mode Toggle =====
        document.addEventListener('DOMContentLoaded', function() {
            const modeToggle = document.getElementById('modeToggle');

            // Check if dark mode is saved
            if (localStorage.getItem('darkMode') === 'true') {
                document.body.classList.add('dark-mode');
                modeToggle.textContent = '☀️ الوضع الفاتح';
                modeToggle.setAttribute('aria-pressed', 'true');
            }

            modeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                const isDark = document.body.classList.contains('dark-mode');
                localStorage.setItem('darkMode', isDark);
                modeToggle.textContent = isDark ? '☀️ الوضع الفاتح' : '🌙 الوضع الداكن';
                modeToggle.setAttribute('aria-pressed', isDark);
            });
        });

        // ===== DataTables Initialization =====
        $(function() {
            const dataTableConfig = {
                lengthChange: false,
                pageLength: 10,
                language: {
                    paginate: {
                        next: 'التالي',
                        previous: 'السابق'
                    },
                    info: 'عرض _START_ إلى _END_ من _TOTAL_',
                    search: 'بحث:'
                },
                dom: 'lrtip'
            };

            const productsTable = $('#productsTable').DataTable(dataTableConfig);
            $('#ordersTable').DataTable(dataTableConfig);

            // Search with debounce
            $('#productSearch').on('input', debounce(function() {
                $('#productsTable').DataTable().search(this.value).draw();
            }, 250));

            $('#orderSearch').on('input', debounce(function() {
                $('#ordersTable').DataTable().search(this.value).draw();
            }, 250));

            // ===== Single Delete using SweetAlert2, reading response as text then JSON.parse() =====
            $('#productsTable').off('click', '.delete-product-btn'); // تأكد من تعطيل أي مستمعين مكررين
            $('#productsTable').on('click', '.delete-product-btn', function() {
                var $btn = $(this);
                var productId = $btn.data('id');
                var csrfToken = getCsrf();
                var row = $btn.closest('tr');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لن تتمكن من التراجع عن هذا الإجراء!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، احذفه!',
                    cancelButtonText: 'إلغاء'
                }).then(function(result) {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: 'delete_product.php', // تأكد المسار (ضع admin/ إن كان الملف بداخله)
                        type: 'POST',
                        data: {
                            id: productId,
                            csrf_token: csrfToken
                        },
                        dataType: 'text', // مهم: نستقبل النص الخام أولاً
                        success: function(responseText) {
                            try {
                                var json = JSON.parse(responseText);
                                if (json.status === 'success') {
                                    Swal.fire('تم', json.msg, 'success');
                                    // حذف الصف من DataTable
                                    try {
                                        productsTable.row(row).remove().draw(false);
                                    } catch (e) {
                                        row.remove();
                                    }
                                    if (json.csrf_token) document.querySelector('meta[name="csrf-token"]').content = json.csrf_token;
                                } else {
                                    Swal.fire('خطأ', json.msg || 'حدث خطأ', 'error');
                                    if (json.csrf_token) document.querySelector('meta[name="csrf-token"]').content = json.csrf_token;
                                }
                            } catch (e) {
                                // استجابة غير JSON -> عرضها للمطور
                                console.error('Invalid JSON response from delete_product:', responseText);
                                Swal.fire({
                                    title: 'استجابة غير صالحة من الخادم',
                                    html: '<pre style="text-align:left; white-space:pre-wrap;">' + $('<div>').text(responseText).html() + '</pre>',
                                    width: 800
                                });
                            }
                        },
                        error: function(xhr, status, err) {
                            Swal.fire('فشل', 'فشل الاتصال بالخادم: ' + (err || status), 'error');
                        }
                    });
                });
            });

            // ===== Checkbox Management =====
            $('#headerCheckbox').on('change', function() {
                $('.order-checkbox').prop('checked', this.checked);
            });

            $(document).on('change', '.order-checkbox', function() {
                const allChecked = $('.order-checkbox').length === $('.order-checkbox:checked').length;
                $('#headerCheckbox').prop('checked', allChecked);
            });

            $('#selectAllBtn').on('click', function() {
                const hasUnchecked = $('.order-checkbox').toArray().some(cb => !cb.checked);
                $('.order-checkbox').prop('checked', hasUnchecked);
                $('#headerCheckbox').prop('checked', hasUnchecked);
            });

            // ===== Export CSV =====
            $('#exportCsv').on('click', () => {
                window.open('export_orders_csv.php', '_blank');
            });

            // ===== Single Order Status Update =====
            $(document).on('click', '.order-status-btn', async function() {
                const btn = this;
                const id = btn.getAttribute('data-id');
                const status = btn.getAttribute('data-status');

                if (!id || !status) return;

                btn.disabled = true;

                try {
                    const response = await fetch('update_status.php', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-Token': getCsrf()
                        },
                        body: `id=${encodeURIComponent(id)}&status=${encodeURIComponent(status)}`
                    });

                    const json = await response.json();

                    if (json && json.success) {
                        toast(json.msg || 'تم التحديث بنجاح', 'success');

                        const tr = document.querySelector(`tr[data-order-id="${id}"]`);
                        if (tr) {
                            tr.classList.add('row-updated');
                            setTimeout(() => tr.classList.remove('row-updated'), 1600);

                            const badge = tr.querySelector('td .badge');
                            if (badge) {
                                badge.className = `badge ${status === 'completed' ? 'bg-success' : (status === 'pending' ? 'bg-warning text-dark' : 'bg-danger')}`;
                                badge.textContent = status === 'pending' ? 'قيد التنفيذ' : (status === 'completed' ? 'تم التسليم' : 'ملغى');
                            }
                        }
                    } else {
                        toast((json && json.msg) ? json.msg : 'فشل التحديث', 'error', 6000);
                    }
                } catch (err) {
                    console.error(err);
                    toast('خطأ في الاتصال بالخادم', 'error', 6000);
                } finally {
                    btn.disabled = false;
                }
            });

            // ===== Bulk Status Update =====
            async function bulkUpdate(ids, status) {
                if (ids.length === 0) {
                    toast('يرجى تحديد طلبات أولاً', 'warning');
                    return;
                }

                toast(`جارٍ تحديث ${ids.length} طلب...`, 'info', 2000);

                for (const id of ids) {
                    try {
                        const response = await fetch('update_status.php', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-Token': getCsrf()
                            },
                            body: `id=${encodeURIComponent(id)}&status=${encodeURIComponent(status)}`
                        });

                        const json = await response.json();

                        if (json && json.success) {
                            const tr = document.querySelector(`tr[data-order-id="${id}"]`);
                            if (tr) {
                                tr.classList.add('row-updated');
                                setTimeout(() => tr.classList.remove('row-updated'), 1600);

                                const badge = tr.querySelector('td .badge');
                                if (badge) {
                                    badge.className = `badge ${status === 'completed' ? 'bg-success' : (status === 'pending' ? 'bg-warning text-dark' : 'bg-danger')}`;
                                    badge.textContent = status === 'pending' ? 'قيد التنفيذ' : (status === 'completed' ? 'تم التسليم' : 'ملغى');
                                }
                            }
                        }
                    } catch (err) {
                        console.error('Bulk update error:', err);
                    }

                    await wait(120);
                }

                toast('انتهى التحديث الجماعي', 'success', 4000);
            }

            document.getElementById('bulkComplete').addEventListener('click', () => {
                const ids = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(i => i.value);
                bulkUpdate(ids, 'completed');
            });

            document.getElementById('bulkPending').addEventListener('click', () => {
                const ids = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(i => i.value);
                bulkUpdate(ids, 'pending');
            });

            document.getElementById('bulkCancel').addEventListener('click', () => {
                const ids = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(i => i.value);
                bulkUpdate(ids, 'cancelled');
            });

            // ===== View Order Details =====
            $(document).on('click', '.view-order-btn', function() {
                const id = this.getAttribute('data-id');
                $('#orderModalBody').text('جارٍ التحميل...');

                (async function() {
                    try {
                        const base = window.location.pathname.replace(/\/[^\/]*$/, '/');
                        const candidates = [
                            base + 'order_details.php?id=' + encodeURIComponent(id),
                            base + '../admin/order_details.php?id=' + encodeURIComponent(id),
                            window.location.origin + '/admin/order_details.php?id=' + encodeURIComponent(id)
                        ];

                        let lastErr = null;

                        for (const url of candidates) {
                            try {
                                const response = await fetch(url, {
                                    credentials: 'same-origin'
                                });

                                if (!response.ok) {
                                    lastErr = new Error(`HTTP ${response.status} - ${url}`);
                                    continue;
                                }

                                const contentType = response.headers.get('content-type') || '';
                                if (contentType.indexOf('application/json') === -1) {
                                    lastErr = new Error(`Non-JSON response: ${url}`);
                                    continue;
                                }

                                const json = await response.json();

                                if (json.success) {
                                    renderOrderModal(json.data);
                                    new bootstrap.Modal(document.getElementById('orderModal')).show();
                                    return;
                                } else {
                                    lastErr = new Error(json.msg || 'خطأ في الخادم');
                                }
                            } catch (e) {
                                lastErr = e;
                            }
                        }

                        throw lastErr;
                    } catch (err) {
                        console.error('Order details error:', err);
                        $('#orderModalBody').html(`<div class="alert alert-danger">تعذر جلب التفاصيل: ${esc(err.message || 'خطأ غير معروف')}</div>`);
                        new bootstrap.Modal(document.getElementById('orderModal')).show();
                    }
                })();
            });

            // ===== Render Order Modal =====
            function renderOrderModal(data) {
                let html = `<div>
                    <h5>طلب #${esc(data.id)}</h5>
                    <div style="color: var(--muted); font-size: 0.9rem;">${esc(data.order_date)}</div>
                    <hr>
                    <p><strong>العميل:</strong> ${esc(data.customer?.name || 'غير معروف')} — <strong>الهاتف:</strong> ${esc(data.customer?.phone || '-')}</p>
                    <p><strong>العنوان:</strong> ${esc(data.customer?.address || '-')}</p>
                    <hr>
                    <h6>العناصر</h6>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>اسم المنتج</th>
                                <th>السعر</th>
                                <th>الكمية</th>
                            </tr>
                        </thead>
                        <tbody>`;

                (data.items || []).forEach(item => {
                    html += `<tr>
                        <td>${esc(item.name || '-')}</td>
                        <td>${(parseFloat(item.price) || 0).toFixed(2)} ج.م</td>
                        <td>${esc(item.quantity)}</td>
                    </tr>`;
                });

                html += `</tbody>
                    </table>
                    <div style="text-align: left; border-top: 1px solid var(--border); padding-top: 12px; margin-top: 12px;">
                        <strong>الإجمالي: ${(parseFloat(data.total) || 0).toFixed(2)} ج.م</strong>
                    </div>
                </div>`;

                $('#orderModalBody').html(html);
            }
        });

        // ===== Charts Initialization =====
        try {
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            };

            new Chart(document.getElementById('chartToday'), {
                type: 'bar',
                data: {
                    labels: ['اليوم'],
                    datasets: [{
                        label: 'ج.م',
                        data: [<?php echo (float)$salesToday; ?>],
                        backgroundColor: 'rgba(111, 66, 193, 0.6)',
                        borderColor: 'rgba(111, 66, 193, 1)',
                        borderWidth: 1
                    }]
                },
                options: chartOptions
            });

            new Chart(document.getElementById('chartWeek'), {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($weekDays); ?>,
                    datasets: [{
                        label: 'ج.م',
                        data: <?php echo json_encode($weekSales); ?>,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: 'rgba(111, 66, 193, 0.1)',
                        borderColor: 'rgba(111, 66, 193, 1)',
                        borderWidth: 2
                    }]
                },
                options: chartOptions
            });

            new Chart(document.getElementById('chartMonth'), {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($monthDays); ?>,
                    datasets: [{
                        label: 'ج.م',
                        data: <?php echo json_encode($monthSales); ?>,
                        tension: 0.35,
                        fill: true,
                        backgroundColor: 'rgba(111, 66, 193, 0.1)',
                        borderColor: 'rgba(111, 66, 193, 1)',
                        borderWidth: 2
                    }]
                },
                options: chartOptions
            });

            new Chart(document.getElementById('chartTop'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($topProducts); ?>,
                    datasets: [{
                        label: 'كمية مباعة',
                        data: <?php echo json_encode($topProductSales); ?>,
                        backgroundColor: 'rgba(40, 167, 69, 0.6)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    }]
                },
                options: chartOptions
            });
        } catch (err) {
            console.error('Chart initialization error:', err);
        }
    </script>
</body>

</html>