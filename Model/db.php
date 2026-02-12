<?php
// ملف db.php المصحح
$conn = new mysqli("localhost", "root", "", "ecommerce_db");
if ($conn->connect_error) {
  die("فشل الاتصال: " . $conn->connect_error);
}
$conn->set_charset("utf8"); // ✅ مهم جداً لدعم العربية
// لا يوجد وسم إغلاق لـ PHP لتجنب المسافات البيضاء غير المرغوبة