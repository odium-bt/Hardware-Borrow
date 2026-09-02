<?php require ROOT . '/app/View/header_view.php'; ?>
<main class="content">
    <article class="box">
        <h2>Liste du matériel</h2>

        <?php foreach ($hardware as $item): ?>

            <div class="hardware">
                <p><?= htmlspecialchars($item['label']) ?></p>

                <p>Type: <?= htmlspecialchars($item['type']) ?></p>

                <a href="?action=borrow&id=<?= $item['id_hardware'] ?>">
                    Réserver ce matériel
                </a>
            </div>

        <?php endforeach; ?>
    </article>
</main>
<?php require ROOT . '/app/View/footer_view.php'; ?>