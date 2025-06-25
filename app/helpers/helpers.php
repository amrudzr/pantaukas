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

function translateDuration($duration) {
    return match($duration) {
        'daily'    => 'Harian',
        'weekly'   => 'Mingguan',
        'monthly'  => 'Bulanan',
        'annually' => 'Tahunan',
        default    => $duration
    };
}

function pctBadge(float $pct): string
{
    $pctText = ($pct >= 0 ? '+' : '') . number_format($pct * 100, 2) . '%';
    $icon    = $pct >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right';
    $color   = $pct >= 0 ? 'text-success' : 'text-danger';
    return "<span class='d-inline-flex align-items-center small $color'><i class='bi $icon me-1'></i>$pctText</span>";
}

/**
 * Helper untuk mengubah tanggal/waktu menjadi format "x waktu yang lalu"
 * 
 * @param string|DateTime $datetime Tanggal/waktu (string format Y-m-d H:i:s atau DateTime object)
 * @param bool $full Tampilkan detail penuh (default: false)
 * @return string Format "x waktu yang lalu"
 */
function time_ago($datetime, $full = false) {
    $now = new DateTime;
    $ago = is_string($datetime) ? new DateTime($datetime) : $datetime;
    $diff = $now->diff($ago);
    
    // Hitung minggu secara manual dari hari
    $weeks = floor($diff->d / 7);
    $days = $diff->d % 7;
    
    // Susun satuan waktu
    $units = [
        'tahun'   => $diff->y,
        'bulan'   => $diff->m,
        'minggu'  => $weeks,
        'hari'    => $days,
        'jam'     => $diff->h,
        'menit'   => $diff->i,
        'detik'   => $diff->s
    ];
    
    $parts = [];
    foreach ($units as $unit => $value) {
        if ($value > 0) {
            $parts[] = $value . ' ' . $unit;
        }
        
        if (!$full && !empty($parts)) {
            break;
        }
    }
    
    return !empty($parts) ? implode(', ', $parts) . ' yang lalu' : 'baru saja';
}