<?php
require __DIR__ . '/admin_init.php';
require __DIR__ . '/admin_helpers.php'; 

if (!is_admin()) {
    header("Location: login.php");
    exit;
}


$today = date('Y-m-d');
$defaultStart = date('Y-m-01'); 
$start = isset($_GET['start_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['start_date']) ? $_GET['start_date'] : $defaultStart;
$end = isset($_GET['end_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['end_date']) ? $_GET['end_date'] : $today;
if ($start > $end) {
    $tmp = $start;
    $start = $end;
    $end = $tmp;
}
$startEsc = $conn->real_escape_string($start);
$endEsc = $conn->real_escape_string($end);

$categoryData = array();
$catSql = "SELECT COALESCE(c.name, 'غير محدد') AS category_name, COUNT(*) AS cnt
           FROM products p
           LEFT JOIN categories c ON p.category_id = c.id
           GROUP BY COALESCE(c.name, 'غير محدد')
           ORDER BY cnt DESC";
$catRes = $conn->query($catSql);
if ($catRes) {
    while ($r = $catRes->fetch_assoc()) {
        $categoryData[$r['category_name']] = (int)$r['cnt'];
    }
    $catRes->free();
}

$rangeCounts = array(0, 0, 0, 0);
$avgPrice = 0.0;
$totalProducts = 0;
$priceSql = "SELECT
    SUM(price < 100) AS r0,
    SUM(price >= 100 AND price < 500) AS r1,
    SUM(price >= 500 AND price < 1000) AS r2,
    SUM(price >= 1000) AS r3,
    AVG(price) AS avg_price,
    COUNT(*) AS total_products
    FROM products";
$pr = $conn->query($priceSql);
if ($pr) {
    $p = $pr->fetch_assoc();
    $rangeCounts[0] = (int)val($p, 'r0', 0);
    $rangeCounts[1] = (int)val($p, 'r1', 0);
    $rangeCounts[2] = (int)val($p, 'r2', 0);
    $rangeCounts[3] = (int)val($p, 'r3', 0);
    $avgPrice = (float)val($p, 'avg_price', 0.0);
    $totalProducts = (int)val($p, 'total_products', 0);
    $pr->free();
}

$period = new DatePeriod(new DateTime($start), new DateInterval('P1D'), (new DateTime($end))->modify('+1 day'));
$dates = [];
foreach ($period as $dt) {
    $dates[] = $dt->format('Y-m-d');
}

if (empty($dates)) {
    $dates = [$start]; // fallback
}

$mapRange = array();
$stmtRange = $conn->prepare("SELECT DATE(order_date) AS d, IFNULL(SUM(total),0) AS s
      FROM orders
      WHERE status = 'completed' AND DATE(order_date) BETWEEN ? AND ?
      GROUP BY DATE(order_date)");

if ($stmtRange) {
    $stmtRange->bind_param('ss', $start, $end);
    $stmtRange->execute();
    $resRange = stmt_get_all($stmtRange);
    foreach ($resRange as $r) {
        $mapRange[$r['d']] = (float)$r['s'];
    }
    $stmtRange->close();
}
$rangeSales = array();
foreach ($dates as $d) {
    $rangeSales[] = isset($mapRange[$d]) ? $mapRange[$d] : 0.0;
}
$totalRangeSales = array_sum($rangeSales);

$topProducts = [];
$topSql = "SELECT p.id, p.name, SUM(oi.quantity) AS qty, SUM(oi.quantity * oi.price) AS revenue
           FROM order_items oi
           INNER JOIN orders o ON o.id = oi.order_id
           INNER JOIN products p ON p.id = oi.product_id
           WHERE o.status = 'completed' AND DATE(o.order_date) BETWEEN ? AND ?
           GROUP BY p.id
           ORDER BY qty DESC
           LIMIT 10";
$stmtTop = $conn->prepare($topSql);
if ($stmtTop) {
    $stmtTop->bind_param('ss', $start, $end);
    $stmtTop->execute();
    $topProducts = stmt_get_all($stmtTop);
    $stmtTop->close();
}

// ===== Handle export requests (CSV / PDF) - same file handles exports when ?export=csv/pdf =====
if (isset($_GET['export']) && in_array($_GET['export'], ['csv', 'pdf'])) {
    $format = $_GET['export'];
    $fnSafe = preg_replace('/[^a-z0-9_\\-]/i', '_', "analytics_{$start}_{$end}");

    if ($format === 'csv') {
        // CSV download
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $fnSafe . '.csv"');
        echo "\xEF\xBB\xBF"; // BOM for Excel UTF-8
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Metric', 'Value']);
        fputcsv($out, ['Start Date', $start]);
        fputcsv($out, ['End Date', $end]);
        fputcsv($out, ['Total Sales (selected range)', number_format($totalRangeSales, 2)]);
        fputcsv($out, ['Total Products', $totalProducts]);
        fputcsv($out, []);
        fputcsv($out, ['Sales by Date']);
        fputcsv($out, ['Date', 'Sales']);
        foreach ($dates as $i => $d) {
            fputcsv($out, [$d, number_format($rangeSales[$i], 2)]);
        }
        fputcsv($out, []);
        fputcsv($out, ['Top Products (qty, revenue)']);
        fputcsv($out, ['ID', 'Name', 'Qty', 'Revenue']);
        foreach ($topProducts as $p) {
            fputcsv($out, [(int)$p['id'], $p['name'], (int)$p['qty'], number_format((float)$p['revenue'], 2)]);
        }
        fclose($out);
        exit;
    }

    if ($format === 'pdf') {
        // Try to use Dompdf if installed
        $html = '<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans, \"Segoe UI\", Tahoma, Arial;direction:rtl}</style></head><body>';
        $html .= "<h2>تقرير تحليلات المتجر</h2>";
        $html .= "<div>الفترة: <strong>{$start} إلى {$end}</strong></div>";
        $html .= "<h3>الإجماليات</h3><ul>";
        $html .= "<li>إجمالي المبيعات: " . number_format($totalRangeSales, 2) . " ج.م</li>";
        $html .= "<li>إجمالي المنتجات: " . number_format($totalProducts) . "</li>";
        $html .= "</ul>";
        $html .= "<h3>المبيعات حسب التاريخ</h3><table border=1 cellpadding=6 cellspacing=0 style=border-collapse:collapse;width:100%><thead><tr><th>التاريخ</th><th>المبيعات</th></tr></thead><tbody>";
        foreach ($dates as $i => $d) {
            $html .= "<tr><td>{$d}</td><td>" . number_format($rangeSales[$i], 2) . " ج.م</td></tr>";
        }
        $html .= "</tbody></table>";
        $html .= "<h3>أعلى المنتجات</h3><table border=1 cellpadding=6 cellspacing=0 style=border-collapse:collapse;width:100%\"><thead><tr><th>ID</th><th>الاسم</th><th>الكمية</th><th>الإيراد</th></tr></thead><tbody>";
        foreach ($topProducts as $p) {
            $html .= "<tr><td>" . (int)$p['id'] . "</td><td>" . esc($p['name']) . "</td><td>" . (int)$p['qty'] . "</td><td>" . number_format((float)$p['revenue'], 2) . " ج.م</td></tr>";
        }
        $html .= "</tbody></table>";
        $html .= '</body></html>';

        if (class_exists('Dompdf\\Dompdf')) {
            // Use Dompdf (server-side) if available
            try {
                $dompdf = new Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $fnSafe . '.pdf"');
                echo $dompdf->output();
                exit;
            } catch (Exception $e) {
                // fallback to HTML
            }
        }

        // Fallback: serve printable HTML and suggest user save as PDF from browser
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $fnSafe . '.html"');
        echo $html;
        exit;
    }
}

// ===== Page output (interactive UI) =====
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📈 تحليلات المتجر</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>


        :root {
            --primary-color: #6f42c1;
            /* بنفسجي فخم */
            --primary-light: #8a63d2;
            --secondary-color: #007bff;
            --bg-light: #f6f8fb;
            --bg-dark: #0b1221;
            --card-light: #ffffff;
            --card-dark: #1a1f3a;
            --text-light: #222222;
            --text-dark: #e0e0e0;
            --muted-light: #6c757d;
            --muted-dark: #9ca3af;
            --border-color: #dee2e6;
            --border-dark: #2d3748;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;

            /* Default (Light Mode) */
            --bg: var(--bg-light);
            --card-bg: var(--card-light);
            --text-color: var(--text-light);
            --muted-color: var(--muted-light);
            --border: var(--border-color);
            --sidebar-bg: linear-gradient(180deg, #2b2fc1, #5a3dd6);
            --sidebar-text: #fff;
            --shadow: 0 6px 24px rgba(32, 40, 60, 0.06);
            --shadow-hover: 0 12px 32px rgba(32, 40, 60, 0.12);
        }

        /* Dark Mode */
        body.dark-mode {
            --bg: var(--bg-dark);
            --card-bg: var(--card-dark);
            --text-color: var(--text-dark);
            --muted-color: var(--muted-dark);
            --border: var(--border-dark);
            --sidebar-bg: linear-gradient(180deg, #1a1f3a, #2d3748);
            --sidebar-text: #e0e0e0;
            --shadow: 0 6px 24px rgba(0, 0, 0, 0.2);
            --shadow-hover: 0 12px 32px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', system-ui, Arial, sans-serif;
            background: var(--bg);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
            direction: rtl;
            text-align: right;
        }

        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar (Copied from Dashboard for consistency) */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            padding: 28px 18px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar .brand {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 35px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
        }

        .sidebar nav a {
            color: var(--sidebar-text);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transform: translateX(-5px);
        }

        .sidebar nav a i {
            font-size: 1.1rem;
        }

        .sidebar .user-info {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.9rem;
            color: var(--sidebar-text);
        }

        .sidebar .user-info .logout-btn {
            display: block;
            color: var(--sidebar-text);
            text-decoration: none;
            margin-top: 10px;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .sidebar .user-info .logout-btn:hover {
            opacity: 1;
        }

        .dark-mode-toggle {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s;
        }

        .dark-mode-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Main Content */
        main {
            flex: 1;
            margin-right: 280px;
            /* مساحة للـ Sidebar */
            padding: 30px;
            overflow-y: auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
        }

        .topbar h3 {
            margin: 0;
            font-weight: 700;
            color: var(--text-color);
        }

        .topbar-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .btn-icon {
            background: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .btn-icon:hover {
            background: var(--primary-color);
            color: #fff;
            border-color: var(--primary-color);
            box-shadow: var(--shadow-hover);
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .metric {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .metric:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-3px);
        }

        .metric::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 5px;
            height: 100%;
            background: var(--primary-color);
            transition: background 0.3s;
        }

        .metric:nth-child(1)::before {
            background: var(--primary-color);
        }

        /* Avg Price */
        .metric:nth-child(2)::before {
            background: var(--success-color);
        }

        /* Total Products */
        .metric:nth-child(3)::before {
            background: var(--warning-color);
        }

        /* Total Sales */
        .metric:nth-child(4)::before {
            background: var(--danger-color);
        }

        /* Price Distribution */

        .metric h6 {
            margin: 0;
            color: var(--muted-color);
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric .value {
            font-size: 2.2rem;
            font-weight: 700;
            margin-top: 10px;
            color: var(--primary-color);
            transition: color 0.3s;
        }

        .metric:nth-child(1) .value {
            color: var(--primary-color);
        }

        .metric:nth-child(2) .value {
            color: var(--success-color);
        }

        .metric:nth-child(3) .value {
            color: var(--warning-color);
        }

        .metric:nth-child(4) .value {
            color: var(--danger-color);
        }

        .metric .small-muted {
            color: var(--muted-color);
            font-size: 0.85rem;
            margin-top: 8px;
        }

        /* Card UI for Charts and Tables */
        .card-ui {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 20px;
        }

        .card-ui h6 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-color);
        }

        /* Controls and Form */
        .controls {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap
        }

        .form-control {
            background: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border);
            border-radius: 8px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
        }

        .btn-primary:hover {
            background: var(--primary-light);
            border-color: var(--primary-light);
        }

        .btn-outline-secondary {
            border-color: var(--muted-color);
            color: var(--muted-color);
            border-radius: 8px;
        }

        .btn-outline-secondary:hover {
            background: var(--muted-color);
            color: var(--card-bg);
        }

        /* Table */
        .table {
            color: var(--text-color);
        }

        .table thead th {
            border-bottom: 2px solid var(--border);
            color: var(--muted-color);
        }

        .table tbody tr {
            border-bottom: 1px solid var(--border);
        }

        .table-striped>tbody>tr:nth-of-type(odd)>* {
            background-color: rgba(var(--primary-color), 0.05);
        }

        /* Chart Fix: Set a fixed height for the chart containers */
        .chart-container {
            position: relative;
            height: 300px;
            /* تم زيادة الارتفاع لضمان الثبات */
            width: 100%;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                box-shadow: none;
                padding-bottom: 80px;
            }

            main {
                margin-right: 0;
                padding: 15px;
            }

            .app-shell {
                flex-direction: column;
            }

            .sidebar nav {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 5px;
            }

            .sidebar nav a {
                padding: 10px 15px;
                margin-bottom: 5px;
                flex-grow: 1;
                justify-content: center;
            }

            .sidebar .brand,
            .sidebar .user-info {
                text-align: center;
            }

            .dark-mode-toggle {
                position: fixed;
                bottom: 10px;
                right: 10px;
                z-index: 1001;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="app-shell">
        <!-- Sidebar -->
        <aside class="sidebar" role="navigation" aria-label="القائمة الجانبية">
            <div class="brand">
                <i class="fas fa-gem"></i>
                <span>لوحة الإدارة</span>
            </div>
            <nav>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> الرئيسية</a>
                <a href="#" class="active"><i class="fas fa-chart-line"></i> التحليلات</a>
                <a href="add_product.php"><i class="fas fa-plus-circle"></i> إضافة منتج</a>
                <a href="../index.php" target="_blank"><i class="fas fa-store"></i> عرض المتجر</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
            </nav>
            <div class="user-info">
                <div style="margin-bottom: 8px;">المستخدم:</div>
                <strong><?php echo esc(isset($_SESSION['admin']) ? $_SESSION['admin'] : 'مسؤول'); ?></strong>
            </div>
            <button id="modeToggle" class="dark-mode-toggle" aria-pressed="false">
                <i class="fas fa-moon"></i>
            </button>
        </aside>

        <!-- Main Content -->
        <main>
            <header class="topbar">
                <h3>📈 تحليلات المُتجر</h3>
                <div class="topbar-actions">
                    <div class="small-muted">التاريخ اليومي: <strong><?php echo esc(date('Y-m-d')); ?></strong></div>
                </div>
            </header>

            <!-- Date Range and Export Controls -->
            <div class="card-ui">
                <form id="rangeForm" class="row g-3 align-items-end" method="get" action="">
                    <div class="col-md-3 col-sm-6">
                        <label for="start_date" class="form-label">من تاريخ</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo esc($start); ?>">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label for="end_date" class="form-label">إلى تاريخ</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo esc($end); ?>">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> تطبيق الفلتر</button>
                    </div>
                    <div class="col-auto">
                        <a class="btn btn-outline-secondary" href="?start_date=<?php echo urlencode($defaultStart); ?>&end_date=<?php echo urlencode($today); ?>"><i class="fas fa-redo"></i> إعادة تعيين</a>
                    </div>
                    <div class="col-md-4 col-sm-12 ms-auto text-end controls">
                        <a class="btn btn-sm btn-success" href="?export=csv&start_date=<?php echo urlencode($start); ?>&end_date=<?php echo urlencode($end); ?>">⬇️ تصدير CSV</a>
                        <a class="btn btn-sm btn-info" href="?export=pdf&start_date=<?php echo urlencode($start); ?>&end_date=<?php echo urlencode($end); ?>">📄 تحميل PDF</a>
                    </div>
                </form>
            </div>

            <!-- Key Metrics -->
            <section class="cards-grid">
                <div class="metric">
                    <h6>💰 متوسط سعر المنتج</h6>
                    <div class="value"><?php echo number_format($avgPrice, 2); ?> ج.م</div>
                    <div class="small-muted">متوسط أسعار جميع المنتجات</div>
                </div>
                <div class="metric">
                    <h6>🛍️ إجمالي المنتجات</h6>
                    <div class="value"><?php echo number_format($totalProducts); ?></div>
                    <div class="small-muted">عدد المنتجات في المتجر</div>
                </div>
                <div class="metric">
                    <h6>💵 مبيعات الفترة</h6>
                    <div class="value"><?php echo number_format($totalRangeSales, 2); ?> ج.م</div>
                    <div class="small-muted">إجمالي الإيرادات في الفترة المحددة</div>
                </div>
                <div class="metric">
                    <h6>📦 توزيع الأسعار</h6>
                    <div class="small-muted">
                        0-100: <?php echo (int)$rangeCounts[0]; ?> — 100-500: <?php echo (int)$rangeCounts[1]; ?> — 500-1000: <?php echo (int)$rangeCounts[2]; ?> — 1000+: <?php echo (int)$rangeCounts[3]; ?>
                    </div>
                </div>
            </section>

            <!-- Charts and Tables -->
            <div class="row g-3 mb-4">
                <div class="col-lg-6">
                    <div class="card-ui">
                        <h6>📊 توزيع المنتجات حسب الفئة</h6>
                        <div class="chart-container">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-ui">
                        <h6>📅 مبيعات الفترة (كل يوم)</h6>
                        <div class="chart-container">
                            <canvas id="salesRangeChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card-ui">
                        <h6>📈 أعلى المنتجات مبيعًا (كمية وإيراد)</h6>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>الاسم</th>
                                    <th>الكمية</th>
                                    <th>الإيراد</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topProducts)): foreach ($topProducts as $tp): ?>
                                        <tr>
                                            <td><?php echo (int)$tp['id']; ?></td>
                                            <td><?php echo esc($tp['name']); ?></td>
                                            <td><?php echo (int)$tp['qty']; ?></td>
                                            <td><?php echo number_format((float)$tp['revenue'], 2); ?> ج.م</td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="4" class="text-muted">لا توجد بيانات في النطاق المحدد</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // ===== Dark Mode Toggle (Copied from Dashboard) =====
        document.addEventListener('DOMContentLoaded', function() {
            const modeToggle = document.getElementById('modeToggle');

            // Check if dark mode is saved
            if (localStorage.getItem('darkMode') === 'true') {
                document.body.classList.add('dark-mode');
                modeToggle.innerHTML = '<i class="fas fa-sun"></i>';
                modeToggle.setAttribute('aria-pressed', 'true');
            } else {
                modeToggle.innerHTML = '<i class="fas fa-moon"></i>';
            }

            modeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                const isDark = document.body.classList.contains('dark-mode');
                localStorage.setItem('darkMode', isDark);
                modeToggle.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
                modeToggle.setAttribute('aria-pressed', isDark);
            });
        });

        // ===== Charts Initialization =====
        $(document).ready(function() {
            const categoryLabels = <?php echo json_encode(array_keys($categoryData)); ?> || [];
            const categoryValues = <?php echo json_encode(array_values($categoryData)); ?> || [];
            const rangeLabels = <?php echo json_encode($dates); ?> || [];
            const rangeValues = <?php echo json_encode($rangeSales); ?> || [];

            // Chart 1: Category Distribution
            new Chart(document.getElementById('categoryChart'), {
                type: 'doughnut',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryValues,
                        label: 'المنتجات',
                        backgroundColor: ['#6f42c1', '#007bff', '#28a745', '#ffc107', '#17a2b8', '#dc3545', '#6610f2'],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            rtl: true,
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            rtl: true
                        }
                    }
                }
            });

            // Chart 2: Sales Range
            new Chart(document.getElementById('salesRangeChart'), {
                type: 'line',
                data: {
                    labels: rangeLabels,
                    datasets: [{
                        label: 'ج.م',
                        data: rangeValues,
                        tension: 0.35,
                        fill: true,
                        backgroundColor: 'rgba(40,167,69,0.12)',
                        borderColor: '#28a745',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            rtl: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>