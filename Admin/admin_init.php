<?php
error_reporting(0); 
ini_set('display_errors', 0);
ob_start(); 

if (!function_exists('hash_equals')) {
    function hash_equals($known_string, $user_string)
    {
        if (!is_string($known_string) || !is_string($user_string)) return false;
        $len1 = strlen($known_string);
        $len2 = strlen($user_string);
        if ($len1 !== $len2) return false;
        $res = $known_string ^ $user_string;
        $ret = 0;
        for ($i = 0; $i < $len1; $i++) {
            $ret |= ord($res[$i]);
        }
        return $ret === 0;
    }
}

if (session_status() === PHP_SESSION_NONE) session_start();

$baseDir = __DIR__; 
$dbPath = $baseDir . '/../Model/db.php';
if (!file_exists($dbPath)) {
    error_log("admin_init.php: db.php not found at expected path: $dbPath");
    throw new Exception("db.php not found. Ensure admin_init.php is in admin/ and db.php is in parent folder (Model/db.php).");
}
require_once $dbPath;

if (!headers_sent()) {
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: no-referrer-when-downgrade");

    $csp  = "default-src 'self' https:; ";
    $csp .= "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://code.jquery.com https://cdn.datatables.net https://cdnjs.cloudflare.com; ";
    $csp .= "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.datatables.net https://fonts.googleapis.com; ";
    $csp .= "img-src 'self' data: https:; ";
    $csp .= "font-src 'self' https://fonts.gstatic.com data:; ";
    $csp .= "connect-src 'self' https:;";
    header("Content-Security-Policy: $csp");
}

if (!isset($_SESSION['csrf_token'])) {
    if (function_exists('random_bytes')) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(24));
    } else {
        $_SESSION['csrf_token'] = substr(md5(uniqid(mt_rand(), true)), 0, 48);
    }
}

function csrf_token()
{
    return isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';
}

function check_csrf($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

function is_admin()
{
    return isset($_SESSION['admin']) && !empty($_SESSION['admin']);
}

function stmt_get_all($stmt)
{
    if (method_exists($stmt, 'get_result')) {
        $res = $stmt->get_result();
        $out = array();
        if ($res) while ($r = $res->fetch_assoc()) $out[] = $r;
        return $out;
    }
    $meta = $stmt->result_metadata();
    if (!$meta) return array();
    $fields = array();
    $row = array();
    $bind = array();
    while ($f = $meta->fetch_field()) {
        $fields[] = $f->name;
        $bind[] = &$row[$f->name];
    }
    call_user_func_array(array($stmt, 'bind_result'), $bind);
    $out = array();
    while ($stmt->fetch()) {
        $r = array();
        foreach ($row as $k => $v) $r[$k] = $v;
        $out[] = $r;
    }
    return $out;
}

function stmt_get_one($stmt)
{
    $rows = stmt_get_all($stmt);
    return count($rows) ? $rows[0] : null;
}

if (!isset($_SESSION['meta'])) {
    $_SESSION['meta'] = array(
        'ua' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 200) : '',
        'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''
    );
} else {
}
