<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Project Sederhana'; ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/bootstrap-5.3.2/css/bootstrap.min.css" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php
    // Dapatkan URI saat ini untuk kebutuhan navigasi sidebar
    $currentUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    ?>
    <!-- Bootstrap JS -->
    <script src="/bootstrap-5.3.2/js/bootstrap.bundle.min.js"></script>
    
    <!-- Tambahkan script jika diperlukan -->

</body>

</html>