<?php
session_start();
require_once '../api/DB.php';
require_once '../api/shifts/OutputShifts.php';
require_once '../api/shifts/ShiftsSearch.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe | Управление сменами </title>
    <link rel="stylesheet" href="../styles/admin.css">
</head>
<body>
    <header class="header">
        <div class="header-left">
            <span class="user-name">Иванов И.И.</span>
        </div>
        <nav class="header-center">
            <a href="employees.php" class="nav-link">Сотрудники</a>
            <a href="shifts.php" class="nav-link active">Смены</a>
            <a href="orders.php" class="nav-link">Заказы</a>
        </nav>
        <div class="header-right">
            <a href="#" onclick="logout(); return false;" class="logout-btn">Выход</a>
        </div>
    </header>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Управление сменами</h1>
        </div>

        <div class="filter-section">
            <form class="filter-form">
                <div class="form-group">
                    <label>Дата начала</label>
                    <input type="date" class="form-control" id="startDate">
                </div>
                <div class="form-group">
                    <label>Дата окончания</label>
                    <input type="date" class="form-control" id="endDate">
                </div>
                <button type="submit" class="btn btn-primary">Применить фильтр</button>
            </form>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Дата</th>
                    <th>Начало</th>
                    <th>Окончание</th>
                    <th>Повар</th>
                    <th>Официант</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>01.03.2024</td>
                    <td>08:00</td>
                    <td>16:00</td>
                    <td>Петров П.П.</td>
                    <td>Смирнова А.С.</td>
                    <td>
                        <button class="btn btn-danger" onclick="deleteShift(1)">Удалить</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Контейнер для форм -->
        <div class="forms-container">
            <!-- Форма добавления смены -->
            <div class="form-section">
                <h2>Добавить смену</h2>
                <form onsubmit="addShift(event)" class="form">
                    <div class="form-group">
                        <label>Дата</label>
                        <input type="date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Время начала</label>
                        <input type="time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Время окончания</label>
                        <input type="time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Повар</label>
                        <select class="form-control" required>
                            <option value="">Выберите повара</option>
                            <option value="1">Петров П.П.</option>
                            <option value="2">Сидоров С.С.</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Официант</label>
                        <select class="form-control" required>
                            <option value="">Выберите официанта</option>
                            <option value="3">Смирнова А.С.</option>
                            <option value="4">Козлова Е.Д.</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </form>
            </div>

            <!-- Форма редактирования смены -->
            <div class="form-section">
                <h2>Редактировать смену</h2>
                <form onsubmit="updateShift(event)" class="form">
                    <div class="form-group">
                        <label>Выберите смену для редактирования</label>
                        <select class="form-control" onchange="loadShiftData(this.value)" required>
                            <option value="">Выберите смену</option>
                            <option value="1">Смена #1 (01.03.2024)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Дата</label>
                        <input type="date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Время начала</label>
                        <input type="time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Время окончания</label>
                        <input type="time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Повар</label>
                        <select class="form-control" required>
                            <option value="">Выберите повара</option>
                            <option value="1">Петров П.П.</option>
                            <option value="2">Сидоров С.С.</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Официант</label>
                        <select class="form-control" required>
                            <option value="">Выберите официанта</option>
                            <option value="3">Смирнова А.С.</option>
                            <option value="4">Козлова Е.Д.</option>
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