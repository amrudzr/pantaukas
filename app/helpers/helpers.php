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

if (!function_exists('upload_file')) {
    /**
     * Upload file ke folder public/uploads dan kembalikan path relatifnya.
     * @param array $file $_FILES['field']
     * @param string $subfolder opsional: subfolder di dalam /uploads, misal "kas/"
     * @param array $allowed ekstensi yang diizinkan (tanpa titik)
     * @return string|null path relatif (untuk disimpan di database) atau null jika gagal
     */
    function upload_file($file, $subfolder = '', $allowed = ['jpg', 'jpeg', 'png', 'pdf'])
    {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/' . $subfolder;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newName = uniqid() . '.' . $ext;
        $target = $uploadDir . '/' . $newName;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return '/uploads/' . ltrim($subfolder . '/', '/') . $newName;
        }

        return null;
    }
}
