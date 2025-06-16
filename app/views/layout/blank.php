<?php

/**
 * Layout blank.php — Layout minimalis untuk tamu
 * Digunakan untuk halaman seperti login, register, atau error 404
 */
?>
<!DOCTYPE html>
<html lang="id">
<?php require APP_PATH . 'views/layout/header.php'; ?>

<body class="bg-light">

    <div class="container py-5">

        <?php if (!empty($pageTitle)): ?>
            <h1 class="mb-4"><?= htmlspecialchars($pageTitle) ?></h1>
        <?php endif; ?>

        <?php if (!empty($breadcrumbs)): ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?php foreach ($breadcrumbs as $item): ?>
                        <?php if (!empty($item['url'])): ?>
                            <li class="breadcrumb-item">
                                <a href="<?= $item['url'] ?>"><?= htmlspecialchars($item['label']) ?></a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?= htmlspecialchars($item['label']) ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <?php require APP_PATH . 'views/' . $contentView; ?>
            </div>
        </div>

    </div>

</body>

</html>