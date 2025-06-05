<?php
$pageTitle = 'Вход в систему';
$activeMenu = 'auth';
include __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="card-title mb-0">Вход в систему</h4>
            </div>
            
            <div class="card-body">
                <form method="POST" action="/auth/login">
                    <div class="mb-3">
                        <label for="username" class="form-label">Логин</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               required autofocus value="<?= htmlspecialchars($username ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Запомнить меня</label>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Войти</button>
                    </div>
                </form>
            </div>
            
            <div class="card-footer bg-white text-center">
                <a href="/auth/forgot-password">Забыли пароль?</a> | 
                <a href="/auth/register">Регистрация</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>