<?php
session_start();
require_once '../includes/db.php';

include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    $errors = [];

    if (strlen($username) < 3) {
        $errors[] = "Логин должен быть не меньше 3 символов";
    }

    if ($password !== $password_confirm) {
        $errors[] = "Пароли не совпадают";
    }

    if (strlen($password) < 6) {
        $errors[] = "Пароль должен быть не меньше 6 символов";
    }

    if (empty($errors)) {
        // Проверяем, есть ли такой пользователь
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Пользователь с таким логином уже существует";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
        $stmt->bind_param("ss", $username, $hash);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Регистрация прошла успешно! Теперь войдите в систему.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Ошибка при регистрации: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Регистрация</title>
    <link rel="stylesheet" href="/css/style.css" />
    <link rel="stylesheet" href="/path_to_bootstrap.css" />
</head>
<body>
<div class="container mt-5" style="max-width:400px;">
    <h2>Регистрация</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $err) echo "<div>$err</div>"; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Логин</label>
            <input type="text" class="form-control" id="username" name="username" required value="<?=htmlspecialchars($username ?? '')?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="password_confirm" class="form-label">Повторите пароль</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
        </div>
        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
        <a href="login.php" class="btn btn-link">Войти</a>
    </form>
</div>
</body>
</html>