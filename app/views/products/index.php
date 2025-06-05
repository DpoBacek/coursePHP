<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between mb-4">
    <h2>Управление товарами</h2>
    <a href="/products/create" class="btn btn-primary">Добавить товар</a>
</div>

<?php include __DIR__ . '/../partials/alerts.php'; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Наименование</th>
                    <th>Категория</th>
                    <th>Количество</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['category_name']) ?></td>
                    <td class="<?= $product['quantity'] < 10 ? 'text-danger fw-bold' : '' ?>">
                        <?= $product['quantity'] ?>
                    </td>
                    <td>
                        <a href="/products/edit/<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="/products/delete/<?= $product['id'] ?>" method="POST" class="d-inline">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php include __DIR__ . '/../partials/pagination.php'; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>