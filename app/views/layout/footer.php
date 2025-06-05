        </div> <!-- /container-fluid -->
    </div> <!-- /main-content -->

    <!-- Скрипты -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>
    
    <script>
        // Активация бокового меню на мобильных устройствах
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('mainContent').classList.toggle('active');
        });
        
        // Инициализация всплывающих подсказок
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Инициализация подтверждения действий
        document.querySelectorAll('.confirm-action').forEach(function(button) {
            button.addEventListener('click', function(e) {
                if (!confirm('Вы уверены, что хотите выполнить это действие?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
    
    <?php if (isset($customScripts)): ?>
        <!-- Кастомные скрипты страницы -->
        <?= $customScripts ?>
    <?php endif; ?>
</body>
</html>