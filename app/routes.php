<?php

/**
 * File: app/routes.php
 * Deskripsi: Mendefinisikan aturan routing untuk aplikasi, mendukung multiple resources.
 */

// Sertakan semua controller yang diperlukan
require_once APP_PATH . 'controllers/AuthController.php';
require_once APP_PATH . 'controllers/ExampleController.php'; // Controller untuk halaman contoh (Example)
// require_once APP_PATH . 'controllers/MemberController.php'; // BARU: Sertakan MemberController

/**
 * Peta rute yang mendefinisikan semua URL dan handler-nya.
 * Format: 'URL_REGEX_PATTERN' => ['method' => 'Controller@method', 'http_method' => 'GET/POST/ANY']
 * 'ANY' berarti rute ini akan ditangani oleh controller method itu sendiri (misal: create/edit menangani GET dan POST)
 */
$routes = [
    // --- Rute Khusus untuk Setup Database ---
    // PERINGATAN: Rute ini HANYA UNTUK PENGEMBANGAN!
    // Di produksi, Anda tidak boleh memiliki rute ini atau harus dilindungi ketat.
    '^db/init$' => [
        'handler' => function () {
            if ($_SERVER['HTTP_HOST'] !== 'localhost:8000' && $_SERVER['HTTP_HOST'] !== 'localhost') {
                header("HTTP/1.0 403 Forbidden");
                echo "<h1>403 Forbidden</h1>";
                echo "<p>Akses ke fitur setup database dilarang di lingkungan ini.</p>";
                exit();
            }
            echo "<pre>";
            require_once __DIR__ . '/../database/setup.php';
            echo "</pre>";
        },
        'http_method' => 'GET',
        'requires_auth' => false
    ],

    // --- Rute Default (Halaman Utama / Welcome Page) ---
    // Menggunakan closure untuk langsung merender halaman welcome.php
    '^$' => [
        'handler' => function () {
            $pageTitle = "Pantaukas";
            $breadcrumbs = [
                ['label' => 'Home', 'url' => '']
            ];
            $contentView = 'welcome.php'; // Nama file view untuk konten spesifik
            require_once APP_PATH . 'views/layout/app.php'; // Muat layout utama
        },
        'http_method' => 'GET',
        'requires_auth' => false
    ],

    // --- Rute untuk Fitur Auth ---
    '^login$' => [ // Untuk /login (halaman login)
        'handler' => 'AuthController@login',
        'http_method' => 'ANY',
        'requires_auth' => false
    ],
    '^register$' => [ // Untuk /register (halaman registrasi)
        'handler' => 'AuthController@register',
        'http_method' => 'ANY',
        'requires_auth' => false
    ],
    '^logout$' => [ // Untuk /logout
        'handler' => 'AuthController@logout',
        'http_method' => 'GET',
        'requires_auth' => true
    ],

    // --- Rute untuk Halaman Dashboard ---
    '^dashboard$' => [
        'handler' => function () {
            $pageTitle = "Dashboard";
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => '/dashboard']
            ];
            $contentView = 'dashboard.php'; // Nama file view untuk konten spesifik
            require_once APP_PATH . 'views/layout/app.php'; // Muat layout utama
        },
        'http_method' => 'GET',
        'requires_auth' => true
    ],

    // --- Rute untuk Halaman Laporan ---
    '^reports$' => [
        'handler' => function () {
            $pageTitle = "Laporan";
            $breadcrumbs = [
                ['label' => 'Laporan', 'url' => '/reports']
            ];
            $contentView = 'report/index.php'; // Nama file view untuk konten spesifik
            require_once APP_PATH . 'views/layout/app.php'; // Muat layout utama
        },
        'http_method' => 'GET',
        'requires_auth' => true
    ],

    // --- Rute untuk Halaman Example (Dummy CRUD) ---
    '^examples$' => [ // Tampilkan tabel data
        'handler' => 'ExampleController@index',
        'http_method' => 'GET',
        'requires_auth' => true
    ],
    '^examples/create$' => [ // Tampilkan form create dan proses simpan
        'handler' => 'ExampleController@create',
        'http_method' => 'ANY',
        'requires_auth' => true
    ],
    '^examples/(\d+)/edit$' => [ // Tampilkan form edit dan proses update
        'handler' => 'ExampleController@edit',
        'http_method' => 'ANY',
        'requires_auth' => true
    ],
    '^examples/(\d+)/delete$' => [ // Proses delete data
        'handler' => 'ExampleController@delete',
        'http_method' => 'POST',
        'requires_auth' => true
    ],

    // // --- BARU: Rute untuk Member (Members) ---
    // '^members$' => [ // Untuk /members (daftar semua member)
    //     'handler' => 'MemberController@index',
    //     'http_method' => 'GET'
    // ],
    // '^members/create$' => [ // Untuk /members/create (form create dan proses create)
    //     'handler' => 'MemberController@create',
    //     'http_method' => 'ANY'
    // ],
    // '^members/(\d+)/detail$' => [ // Untuk /members/ID/detail
    //     'handler' => 'MemberController@detail',
    //     'http_method' => 'GET'
    // ],
    // '^members/(\d+)/edit$' => [ // Untuk /members/ID/edit (form edit dan proses edit)
    //     'handler' => 'MemberController@edit',
    //     'http_method' => 'ANY'
    // ],
    // '^members/(\d+)/delete$' => [ // Untuk /members/ID/delete (proses delete)
    //     'handler' => 'MemberController@delete',
    //     'http_method' => 'POST'
    // ],

    // // --- Rute Default (Halaman Utama / Index) ---
    // '^$' => [ // Jika URI kosong (misal: localhost:8000/)
    //     'handler' => 'BookController@index', // Arahkan ke daftar buku
    //     'http_method' => 'GET'
    // ]
];

/**
 * Fungsi untuk mengarahkan permintaan masuk ke controller dan method yang sesuai.
 * @param string $uri URI yang diminta oleh pengguna.
 */
function dispatchRequest($uri)
{
    global $routes;
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    foreach ($routes as $pattern => $route) {
        if (preg_match('#' . $pattern . '#', $uri, $matches)) {

            /* ========  C E K  W A J I B  L O G I N  ======== */
            $needsLogin = $route['requires_auth'] ?? false;
            if ($needsLogin && !isset($_SESSION['user_id'])) {
                // Belum login → arahkan ke halaman login (atau 403)
                header('Location: /login');
                exit;
            }
            /* ================================================= */

            // --- cek metode HTTP ---
            if ($route['http_method'] === 'ANY' || $route['http_method'] === $requestMethod) {

                $handler = $route['handler'];

                /* 1) Jika handler berupa closure (callable) */
                if (is_callable($handler)) {
                    array_shift($matches);                 // buang full‑match
                    call_user_func_array($handler, $matches);
                    return;
                }

                /* 2) Jika handler berupa "Controller@method" */
                if (is_string($handler) && strpos($handler, '@') !== false) {
                    [$controllerName, $methodName] = explode('@', $handler, 2);

                    // Pastikan file controllernya sudah di‑require
                    if (!class_exists($controllerName)) {
                        throw new Exception("Controller $controllerName tidak ditemukan");
                    }

                    $controller = new $controllerName;

                    if (!method_exists($controller, $methodName)) {
                        throw new Exception("Method $methodName pada $controllerName tidak ditemukan");
                    }

                    array_shift($matches);                 // buang full‑match
                    call_user_func_array([$controller, $methodName], $matches);
                    return;
                }

                /* 3) Handler tidak valid */
                throw new Exception('Handler route tidak valid');
            }
        }
    }

    // Jika tidak ada rute yang cocok, tampilkan halaman 404 kustom
    header("HTTP/1.0 404 Not Found");

    $pageTitle   = '404 – Halaman Tidak Ditemukan';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/dashboard'], // Ganti ke dashboard kalau sudah login
        ['label' => '404',  'url' => '']
    ];
    $contentView = 'errors/404.php';

    // Layout dipilih berdasarkan login atau tidak
    if (isset($_SESSION['user_id'])) {
        require_once APP_PATH . 'views/layout/app.php';     // user login → pakai layout dengan sidebar
    } else {
        require_once APP_PATH . 'views/layout/blank.php';   // tamu → pakai layout kosong
    }

    exit;
}
