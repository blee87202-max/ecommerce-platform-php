<?php
/**
 * mobile_detection.php
 * 
 * Server-side mobile detection and user preference management
 * This file contains helper functions for detecting mobile devices and managing user preferences
 */

/**
 * Simple mobile detection function
 * Checks User-Agent for common mobile/tablet indicators
 */
function is_mobile_user() {
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    
    // Pattern to detect mobile devices
    $mobile_pattern = '/Mobile|Android|iPhone|iPad|iPod|Opera Mini|IEMobile|Windows Phone|BlackBerry|Kindle|Silk|webOS|Tablet/i';
    
    return preg_match($mobile_pattern, $user_agent) ? true : false;
}

/**
 * Check if user is a bot/crawler
 * Prevents showing mobile splash to search engines
 */
function is_bot() {
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    
    $bot_pattern = '/Googlebot|Bingbot|Slurp|DuckDuckBot|Baiduspider|YandexBot|Crawler|Spider|Robot|Scraper/i';
    
    return preg_match($bot_pattern, $user_agent) ? true : false;
}

/**
 * Determine if mobile UI should be shown
 * Takes into account user preferences and device type
 */
function should_show_mobile_ui() {
    // Don't show to bots
    if (is_bot()) {
        return false;
    }
    
    // Check if user is on mobile
    if (!is_mobile_user()) {
        return false;
    }
    
    // Check user preference (cookie)
    if (isset($_COOKIE['preferred_view']) && $_COOKIE['preferred_view'] === 'desktop') {
        return false;
    }
    
    // Check session preference (for logged-in users)
    if (isset($_SESSION['preferred_view']) && $_SESSION['preferred_view'] === 'desktop') {
        return false;
    }
    
    // Check if user has dismissed the splash
    if (isset($_COOKIE['hide_mobile_splash']) && $_COOKIE['hide_mobile_splash'] === '1') {
        return false;
    }
    
    return true;
}

/**
 * Set user preference for view type
 */
function set_view_preference($preference = 'mobile') {
    // Set cookie for 1 year
    setcookie('preferred_view', $preference, time() + (365 * 24 * 60 * 60), '/');
    
    // Also set in session
    $_SESSION['preferred_view'] = $preference;
}

/**
 * Dismiss mobile splash for current user
 */
function dismiss_mobile_splash() {
    setcookie('hide_mobile_splash', '1', time() + (30 * 24 * 60 * 60), '/');
}
?>
