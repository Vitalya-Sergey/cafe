<?php
session_start();
require_once '../api/DB.php';
require_once '../api/waiter_orders/WaiterOrdersSearch.php';
require_once '../api/waiter_orders/OutputWaiterOrders.php';

// Проверка авторизации
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'официант') {
    header('Location: ../login.php');
    exit;
}

// Получаем параметры поиска
$shift_id = isset($_GET['shift_id']) ? $_GET['shift_id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Получаем список заказов
$params = [
    'shift_id' => $shift_id,
    'status' => $status,
    'waiter_id' => $_SESSION['user_id']
];

// Отладочная информация
error_log("Search parameters: " . print_r($params, true));
error_log("Session data: " . print_r($_SESSION, true));

$orders = WaiterOrdersSearch($params, $db);

// Отладочная информация
error_log("Retrieved orders: " . print_r($orders, true));
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
            <button class="btn btn-primary" onclick="showNewOrderForm()">Создать заказ</button>
        </div>

        <div class="filter-section">
            <form class="filter-form" method="GET">
                <div class="form-group">
                    <label>Смена</label>
                    <select class="form-control" name="shift_id">
                        <option value="">Все смены</option>
                        <?php
                        $shiftsQuery = "
                            SELECT s.* 
                            FROM shifts s
                            JOIN shift_assignments sa ON s.shift_id = sa.shift_id
                            WHERE sa.user_id = :user_id AND sa.role = 'официант'
                            ORDER BY s.shift_date DESC, s.start_time DESC
                        ";
                        $stmt = $db->prepare($shiftsQuery);
                        $stmt->execute([':user_id' => $_SESSION['user_id']]);
                        $shifts = $stmt->fetchAll();

                        foreach($shifts as $shift) {
                            $selected = ($shift_id == $shift['shift_id']) ? 'selected' : '';
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
                        <option value="принят" <?php echo $status === 'принят' ? 'selected' : ''; ?>>Принят</option>
                        <option value="оплачен" <?php echo $status === 'оплачен' ? 'selected' : ''; ?>>Оплачен</option>
                        <option value="закрыт" <?php echo $status === 'закрыт' ? 'selected' : ''; ?>>Закрыт</option>
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
                    <th>Блюда</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php OutputWaiterOrders($orders); ?>
            </tbody>
        </table>

        <!-- Контейнер для форм -->
        <div class="forms-container">
            <!-- Форма добавления заказа -->
            <div class="form-section">
                <h2>Создать заказ</h2>
                <form id="newOrderForm" onsubmit="createOrder(event)" class="form">
                    <div class="form-group">
                        <label>Номер стола</label>
                        <input type="number" name="table_number" class="form-control" required min="1">
                    </div>
                    <div id="orderItems">
                        <div class="order-item">
                            <div class="form-group">
                                <label>Название блюда</label>
                                <input type="text" name="dish_names[]" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Цена</label>
                                <input type="number" name="prices[]" class="form-control" required min="0" step="0.01">
                            </div>
                            <div class="form-group">
                                <label>Количество</label>
                                <input type="number" name="quantities[]" class="form-control" required min="1" value="1">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="addOrderItem()">Добавить блюдо</button>
                    <button type="submit" class="btn btn-primary">Создать заказ</button>
                </form>
            </div>

            <!-- Форма редактирования заказа -->
            <div class="form-section">
                <h2>Редактировать заказ</h2>
                <form id="editOrderForm" onsubmit="updateOrder(event)" class="form">
                    <div class="form-group">
                        <label>Выберите заказ для редактирования</label>
                        <select class="form-control" name="order_id" onchange="loadOrderData(this.value)" required>
                            <option value="">Выберите заказ</option>
                            <?php foreach($orders as $order): ?>
                                <option value="<?php echo htmlspecialchars($order['order_id']); ?>">
                                    Заказ #<?php echo htmlspecialchars($order['order_id']); ?> - 
                                    Стол <?php echo htmlspecialchars($order['table_number']); ?> 
                                    (<?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Номер стола</label>
                        <input type="number" name="edit_table_number" class="form-control" required min="1">
                    </div>
                    <div id="editOrderItems">
                        <!-- Здесь будут динамически добавляться поля для блюд -->
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="addEditOrderItem()">Добавить блюдо</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Обработка изменения статуса
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function() {
                    const orderId = this.getAttribute('data-order-id');
                    const newStatus = this.value;
                    const previousStatus = this.getAttribute('data-previous-status');
                    updateOrderStatus(orderId, newStatus, previousStatus);
                });
            });
        });

        // Функции для работы с заказами
        function addOrderItem() {
            const orderItems = document.getElementById('orderItems');
            const newItem = orderItems.children[0].cloneNode(true);
            // Очищаем значения в новых полях
            newItem.querySelectorAll('input').forEach(input => {
                input.value = input.type === 'number' && input.name === 'quantities[]' ? '1' : '';
            });
            orderItems.appendChild(newItem);
        }

        function addEditOrderItem() {
            const orderItems = document.getElementById('editOrderItems');
            const template = `
                <div class="order-item">
                    <div class="form-group">
                        <label>Название блюда</label>
                        <input type="text" name="edit_dish_names[]" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Цена</label>
                        <input type="number" name="edit_prices[]" class="form-control" required min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Количество</label>
                        <input type="number" name="edit_quantities[]" class="form-control" required min="1" value="1">
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">Удалить</button>
                </div>
            `;
            orderItems.insertAdjacentHTML('beforeend', template);
        }

        function loadOrderData(orderId) {
            if (!orderId) return;

            fetch(`../api/waiter_orders/GetOrder.php?order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const form = document.getElementById('editOrderForm');
                        form.querySelector('[name="edit_table_number"]').value = data.order.table_number;

                        const itemsContainer = document.getElementById('editOrderItems');
                        itemsContainer.innerHTML = ''; // Очищаем существующие элементы

                        data.order.items.forEach(item => {
                            const template = `
                                <div class="order-item">
                                    <div class="form-group">
                                        <label>Название блюда</label>
                                        <input type="text" name="edit_dish_names[]" class="form-control" required value="${item.dish_name}">
                                    </div>
                                    <div class="form-group">
                                        <label>Цена</label>
                                        <input type="number" name="edit_prices[]" class="form-control" required min="0" step="0.01" value="${item.price}">
                                    </div>
                                    <div class="form-group">
                                        <label>Количество</label>
                                        <input type="number" name="edit_quantities[]" class="form-control" required min="1" value="${item.quantity}">
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">Удалить</button>
                                </div>
                            `;
                            itemsContainer.insertAdjacentHTML('beforeend', template);
                        });
                    } else {
                        alert(data.message || 'Ошибка при загрузке данных заказа');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при загрузке данных заказа');
                });
        }

        function createOrder(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            fetch('../api/waiter_orders/CreateOrder.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Ошибка при создании заказа');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка при создании заказа');
            });
        }

        function updateOrder(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const orderId = formData.get('order_id');
            
            fetch('../api/waiter_orders/UpdateOrder.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Ошибка при обновлении заказа');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка при обновлении заказа');
            });
        }

        function updateOrderStatus(orderId, newStatus, previousStatus) {
            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('status', newStatus);

            fetch('../api/waiter_orders/update_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    // Возвращаем предыдущий статус
                    const select = document.querySelector(`select[data-order-id="${orderId}"]`);
                    select.value = previousStatus;
                    alert(data.message || 'Ошибка при обновлении статуса');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Возвращаем предыдущий статус
                const select = document.querySelector(`select[data-order-id="${orderId}"]`);
                select.value = previousStatus;
                alert('Ошибка при обновлении статуса');
            });
        }

        function deleteOrder(orderId) {
            if (confirm('Вы уверены, что хотите удалить этот заказ?')) {
                fetch('../api/waiter_orders/DeleteOrder.php', {
                    method: 'POST',
                    body: JSON.stringify({ order_id: orderId }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Ошибка при удалении заказа');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при удалении заказа');
                });
            }
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