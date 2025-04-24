<?php
session_start();
require_once '../api/DB.php';
require_once '../api/employees/OutputEmployees.php';
require_once '../api/employees/EmployeesSearch.php';

// Проверка авторизации
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'администратор') {
    header('Location: ../login.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe | Управление сотрудниками</title>
    <link rel="stylesheet" href="../styles/admin.css">
</head>
<body>
    <header class="header">
        <div class="header-left">
            <span class="user-name"><?php echo $_SESSION['full_name']; ?></span>
        </div>
        <nav class="header-center">
            <a href="employees.php" class="nav-link active">Сотрудники</a>
            <a href="shifts.php" class="nav-link">Смены</a>
            <a href="orders.php" class="nav-link">Заказы</a>
        </nav>
        <div class="header-right">
            <a href="#" onclick="logout(); return false;" class="logout-btn">Выход</a>
        </div>
    </header>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Управление сотрудниками</h1>
        </div>

        <div class="filter-section">
            <form class="filter-form" method="GET">
                <div class="form-group">
                    <label>Поиск</label>
                    <input type="text" class="form-control" name="search" placeholder="Введите имя" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Сортировка</label>
                    <select class="form-control" name="sort">
                        <option value="">По умолчанию</option>
                        <option value="ASC" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'ASC') ? 'selected' : ''; ?>>По возрастанию</option>
                        <option value="DESC" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'DESC') ? 'selected' : ''; ?>>По убыванию</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Применить</button>
                <a href="employees.php" class="btn btn-secondary">Сбросить</a>
            </form>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ФИО</th>
                    <th>Логин</th>
                    <th>Роль</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $employees = EmployeesSearch($_GET, $db);
                OutputEmployees($employees);
                ?>
            </tbody>
        </table>

        <!-- Контейнер для форм -->
        <div class="forms-container">
            <!-- Форма добавления сотрудника -->
            <div class="form-section">
                <h2>Добавить сотрудника</h2>
                <form action="api/employees/AddEmployee.php" method="POST" class="form">
                    <div class="form-group">
                        <label>ФИО</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Логин</label>
                        <input type="text" name="login" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Пароль</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Роль</label>
                        <select name="role" class="form-control" required>
                            <option value="admin">Администратор</option>
                            <option value="chef">Повар</option>
                            <option value="waiter">Официант</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </form>
            </div>

            <!-- Форма редактирования сотрудника -->
            <div class="form-section">
                <h2>Редактировать сотрудника</h2>
                <form action="api/employees/EditEmployee.php" method="POST" class="form">
                    <div class="form-group">
                        <label>Выберите сотрудника для редактирования</label>
                        <select class="form-control" name="id" onchange="loadEmployeeData(this.value)" required>
                            <option value="">Выберите сотрудника</option>
                            <?php
                            foreach($employees as $employee) {
                                echo "<option value='{$employee['id']}'>{$employee['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ФИО</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Логин</label>
                        <input type="text" name="login" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Новый пароль</label>
                        <input type="password" name="password" class="form-control">
                        <small>Оставьте пустым, если не хотите менять пароль</small>
                    </div>
                    <div class="form-group">
                        <label>Роль</label>
                        <select name="role" class="form-control" required>
                            <option value="admin">Администратор</option>
                            <option value="chef">Повар</option>
                            <option value="waiter">Официант</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function logout() {
            fetch('../api/auth/Logout.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '../login.php';
                } else {
                    alert('Ошибка при выходе из системы');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка при выходе из системы');
            });
        }
    </script>
</body>
</html> 