<?php
session_start();
require_once '../api/DB.php';
require_once '../api/orders/OutputOrders.php';
require_once '../api/orders/OrdersSearch.php';

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe | Управление заказами</title>
    <link rel="stylesheet" href="../styles/admin.css">
</head>
<body>
    <header class="header">
        <div class="header-left">
            <span class="user-name">Иванов И.И.</span>
        </div>
        <nav class="header-center">
            <a href="employees.php" class="nav-link">Сотрудники</a>
            <a href="shifts.php" class="nav-link">Смены</a>
            <a href="orders.php" class="nav-link active">Заказы</a>
        </nav>
        <div class="header-right">
            <a href="#" onclick="logout(); return false;" class="logout-btn">Выход</a>
        </div>
    </header>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Управление заказами</h1>
        </div>

        <div class="filter-section">
            <form class="filter-form">
                <div class="form-group">
                    <label>Смена</label>
                    <select class="form-control" id="shiftFilter">
                        <option value="">Все смены</option>
                        <option value="1">01.03.2024 (08:00-16:00)</option>
                        <option value="2">01.03.2024 (16:00-00:00)</option>
                        <option value="3">02.03.2024 (08:00-16:00)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Статус</label>
                    <select class="form-control" id="statusFilter">
                        <option value="">Все статусы</option>
                        <option value="created">Создан</option>
                        <option value="in_progress">В процессе</option>
                        <option value="ready">Готов</option>
                        <option value="paid">Оплачен</option>
                        <option value="closed">Закрыт</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Применить фильтр</button>
            </form>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Дата и время</th>
                    <th>Смена</th>
                    <th>Стол</th>
                    <th>Официант</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>01.03.2024 10:30</td>
                    <td>01.03.2024 (08:00-16:00)</td>
                    <td>3</td>
                    <td>Смирнова А.С.</td>
                    <td>Оплачен</td>
                    <td>1500₽</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>01.03.2024 11:15</td>
                    <td>01.03.2024 (08:00-16:00)</td>
                    <td>5</td>
                    <td>Смирнова А.С.</td>
                    <td>Закрыт</td>
                    <td>2300₽</td>
                </tr>
            </tbody>
        </table>
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