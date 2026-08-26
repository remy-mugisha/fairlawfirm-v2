<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function get_language_file(){
    $_SESSION['lang'] = $_SESSION['lang'] ?? 'en';
    if (isset($_GET['lang'])) {
        $allowed = ['en', 'fr'];
        $lang = strtolower(trim($_GET['lang']));
        if (in_array($lang, $allowed, true)) {
            $_SESSION['lang'] = $lang;
        }
    }
    return __DIR__ . "/" . $_SESSION['lang'] . ".php";
}
require get_language_file();
function __($str){
    global $lang;
    if(!empty($lang[$str])){
        return $lang[$str];
    }
    return $str;
}
