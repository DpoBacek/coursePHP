<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Складской учёт</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
  </head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="/index.php">Склад</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <?php if (isset($_SESSION['username'])): ?>
            <li class="nav-item"><a href="/dashboard.php" class="nav-link">Dashboard</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="/products/products.php">Товары</a></li>
        <li class="nav-item"><a class="nav-link" href="/incoming/incoming.php">Поступления</a></li>
        <li class="nav-item"><a class="nav-link" href="/outgoing/outgoing.php">Отгрузки</a></li>
      </ul>
      <span class="navbar-text me-3">Привет, <?= $_SESSION['username'] ?? 'Гость' ?></span>
      <?php if (isset($_SESSION['username'])): ?>
          <a href="/auth/logout.php" class="btn btn-outline-light btn-sm">Выход</a>
      <?php else: ?>
          <a href="/auth/login.php" class="btn btn-success mt-3">Войти в систему</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<div id="content" class="flex-grow-1 container py-4">
