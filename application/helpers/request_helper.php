<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Get方式，编码HTML标记
 * @param sring $key 参数名
 * @param sring $del 默认值
 */
function request_get($key, $def = "") {
    return htmlspecialchars(request_get_html($key, $def));
}

/**
 * Get方式，编码HTML标记
 * @param sring $key 参数名
 * @param sring $del 默认值
 */
function request_get_int($key, $def = 0) {
    $value = htmlspecialchars(request_get_html($key, $def));
    return is_numeric($value) ? intval($value) : $def;
}

/**
 * Get方式，不编码HTML标记
 * @param sring $key 参数名
 * @param sring $del 默认值
 */
function request_get_html($key, $def = "") {
    #return empty($_GET[$key]) == FALSE ? urldecode($_GET[$key]) : $def;
    return  isset($_GET[$key]) && strlen($_GET[$key]) >0 ? urldecode($_GET[$key]) : $def;
}

/**
 * Post方式，编码HTML标记
 * @param sring $key 参数名
 * @param sring $del 默认值
 */
function request_post($key, $def = "") {
    return htmlspecialchars(request_post_html($key, $def));
}

/**
 * Post方式，返回int类型
 * @param sring $key 参数名
 * @param sring $del 默认值
 * @return int 自动动转格式
 */
function request_post_int($key, $def = 0) {
    $value = htmlspecialchars(request_post_html($key, $def));
    return is_numeric($value) ? intval($value) : $def;
}

/**
 * Post方式，不编码HTML标记
 * @param sring $key 参数名
 * @param sring $del 默认值
 */
function request_post_html($key, $def = "") {
    #return empty($_POST[$key]) == FALSE ? urldecode($_POST[$key]) : $def;
    return  isset($_POST[$key]) && strlen($_POST[$key]) > 0 ? urldecode($_POST[$key]) : $def;
}

/**
 * Cookie方式
 * @param sring $key 参数名
 * @param sring $del 默认值
 */
function request_cookie($key, $def = "") {
    #return empty($_COOKIE[$key]) == FALSE ? $_COOKIE[$key] : $def;
    return  isset($_COOKIE[$key]) && strlen($_COOKIE[$key]) > 0 ? $_COOKIE[$key] : $def;
}

/**
 * Session方式
 * @param sring $key 参数名
 * @param sring $del 默认值
 */
function request_session($key, $def = "") {
    #return empty($_SESSION[$key]) == FALSE ? $_SESSION[$key] : $def;
    return isset($_SESSION[$key]) && strlen($_SESSION[$key]) > 0 ? $_SESSION[$key] : $def;
}

/**
 * Session方式
 * @param sring $key 参数名
 * @param sring $del 默认值
 */
function request_session_int($key, $def = 0) {
    $value = request_session($key, $def);
    return is_numeric($value) ? intval($value) : $def;
}


?>