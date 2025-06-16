<?php
/**
 * File: app/views/layout/breadcrumb.php
 * Deskripsi: Breadcrumb Bootstrap murni tanpa CSS tambahan.
 */
?>
<?php if (!empty($breadcrumbs)): ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3">
            <?php foreach ($breadcrumbs as $crumb): ?>
                <li class="breadcrumb-item <?php echo empty($crumb['url']) ? 'active' : ''; ?>" <?php echo empty($crumb['url']) ? 'aria-current="page"' : ''; ?>>
                    <?php if (!empty($crumb['url'])): ?>
                        <a href="<?php echo htmlspecialchars($crumb['url']); ?>" class="text-decoration-none link-primary">
                            <?php echo htmlspecialchars($crumb['label']); ?>
                        </a>
                    <?php else: ?>
                        <?php echo htmlspecialchars($crumb['label']); ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
<?php endif; ?>
