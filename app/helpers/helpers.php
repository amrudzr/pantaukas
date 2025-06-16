<?php

function view($viewName, $data = [], $layout = 'app')
{
    extract($data);
    $contentView = $viewName . '.php';
    require APP_PATH . "views/layout/{$layout}.php";
}

if (!function_exists('base_url')) {
    function base_url($path = '')
    {
        return 'http://localhost:8000/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $url = base_url($url);
        }

        header('Location: ' . $url);
        exit;
    }
}


if (!function_exists('format_rupiah')) {
    function format_rupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('is_post')) {
    function is_post()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
