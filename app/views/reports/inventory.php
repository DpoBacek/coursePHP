<?php
$pageTitle = 'Отчёт по остаткам на складе';
$activeMenu = 'reports';
include __DIR__ . '/../layout/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Остатки товаров на складе</h5>
    </div>
    
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Категория</label>
                <select name="category_id" class="form-select">
                    <option value="">Все категории</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" 
                        <?= $categoryId == $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Местоположение</label>
                <select name="location_id" class="form-select">
                    <option value="">Все местоположения</option>
                    <?php foreach ($locations as $location): ?>
                    <option value="<?= $location['id'] ?>" 
                        <?= $locationId == $location['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($location['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Минимальный остаток</label>
                <input type="number" name="min_quantity" class="form-control" 
                       value="<?= $minQuantity ?? '' ?>" min="0">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Сортировка</label>
                <select name="sort" class="form-select">
                    <option value="name" <?= $sort == 'name' ? 'selected' : '' ?>>По наименованию</option>
                    <option value="quantity_asc" <?= $sort == 'quantity_asc' ? 'selected' : '' ?>>По количеству (возр.)</option>
                    <option value="quantity_desc" <?= $sort == 'quantity_desc' ? 'selected' : '' ?>>По количеству (убыв.)</option>
                    <option value="category" <?= $sort == 'category' ? 'selected' : '' ?>>По категории</option>
                </select>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-funnel me-1"></i> Применить фильтры
                </button>
                <a href="/reports/inventory" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Сбросить
                </a>
                
                <a href="/reports/export/inventory?category_id=<?= $categoryId ?>&location_id=<?= $locationId ?>&min_quantity=<?= $minQuantity ?>&sort=<?= $sort ?>" 
                   class="btn btn-success float-end">
                    <i class="bi bi-file-earmark-excel me-1"></i> Экспорт в Excel
                </a>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Наименование</th>
                        <th>Категория</th>
                        <th>Артикул</th>
                        <th>Местоположение</th>
                        <th class="text-end">Количество</th>
                        <th class="text-end">Минимальный остаток</th>
                        <th class="text-end">Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventory as $item): ?>
                    <tr class="<?= $item['quantity'] <= $item['min_quantity'] ? 'table-warning' : '' ?>">
                        <td><?= $item['id'] ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['category_name']) ?></td>
                        <td><?= $item['sku'] ?></td>
                        <td><?= htmlspecialchars($item['location_name']) ?></td>
                        <td class="text-end"><?= number_format($item['quantity'], 0) ?></td>
                        <td class="text-end"><?= number_format($item['min_quantity'], 0) ?></td>
                        <td class="text-end">
                            <?php if ($item['quantity'] <= $item['min_quantity']): ?>
                                <span class="badge bg-danger">Низкий остаток</span>
                            <?php elseif ($item['quantity'] <= ($item['min_quantity'] * 1.5)): ?>
                                <span class="badge bg-warning text-dark">Средний остаток</span>
                            <?php else: ?>
                                <span class="badge bg-success">Норма</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white">
            <?php
            $urlParams = [
                'category_id' => $categoryId,
                'location_id' => $locationId,
                'min_quantity' => $minQuantity,
                'sort' => $sort
            ];
            include __DIR__ . '/../partials/pagination.php';
            ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>