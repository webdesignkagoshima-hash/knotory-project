<?php
/**
 * クラスオートローダー
 */
spl_autoload_register(function ($class_name) {
    $lib_path = __DIR__ . '/lib/' . $class_name . '.php';
    if (file_exists($lib_path)) {
        require_once $lib_path;
    }
});