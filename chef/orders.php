<?php
session_start();
require_once '../api/DB.php';
require_once '../api/chef_orders/ChefOrdersSearch.php';
require_once '../api/chef_orders/OutputChefOrders.php';

// Проверка авторизации
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'повар') {
    header('Location: ../login.php');
    exit;
}

// Получаем параметры поиска
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$shift_id = isset($_GET['shift_id']) ? $_GET['shift_id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Получаем список заказов
$params = [
    'search' => $search,
    'sort' => $sort,
    'shift_id' => $shift_id,
    'status' => $status
];

$orders = ChefOrdersSearch($params, $db);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe | Заказы</title>
    <link rel="stylesheet" href="../styles/admin.css">
</head>
<body>
    <header class="header">
        <div class="header-left">
            <span class="user-name"><?php echo $_SESSION['full_name']; ?></span>
        </div>
        <nav class="header-center">
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
            <form class="filter-form" method="GET">
                <div class="form-group">
                    <label>Смена</label>
                    <select class="form-control" name="shift_id">
                        <option value="">Все смены</option>
                        <?php
                        $shifts = $db->query("SELECT * FROM shifts ORDER BY shift_date DESC, start_time DESC")->fetchAll();
                        foreach($shifts as $shift) {
                            $selected = (isset($_GET['shift_id']) && $_GET['shift_id'] == $shift['shift_id']) ? 'selected' : '';
                            $date = date('d.m.Y', strtotime($shift['shift_date']));
                            $time = date('H:i', strtotime($shift['start_time'])) . '-' . date('H:i', strtotime($shift['end_time']));
                            echo "<option value='{$shift['shift_id']}' $selected>$date ($time)</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Статус</label>
                    <select class="form-control" name="status">
                        <option value="">Все статусы</option>
                        <option value="ожидает" <?php echo $status === 'ожидает' ? 'selected' : ''; ?>>Ожидает</option>
                        <option value="в процессе" <?php echo $status === 'в процессе' ? 'selected' : ''; ?>>В процессе</option>
                        <option value="готово" <?php echo $status === 'готово' ? 'selected' : ''; ?>>Готово</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Применить</button>
                <a href="orders.php" class="btn btn-secondary">Сбросить</a>
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
                    <th>Блюда</th>
                    <th>Сумма</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php OutputChefOrders($orders); ?>
            </tbody>
        </table>
    </div>

    <script>
        function updateOrderStatus(orderId, status) {
            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('status', status);

            fetch('../api/chef_orders/UpdateOrderStatus.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Ошибка при обновлении статуса');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка при обновлении статуса');
            });
        }

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