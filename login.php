<?php session_start();

require_once 'api/auth/AuthUser.php';
require_once 'api/auth/AuthCheck.php';

// Проверяем, авторизован ли пользователь
$user = AuthCheck($db);
if ($user) {
    // Перенаправляем в зависимости от роли
    switch ($user['role']) {
        case 'администратор':
            header('Location: admin/employees.php');
            break;
        case 'повар':
            header('Location: chef/orders.php');
            break;
        case 'официант':
            header('Location: waiter/orders.php');
            break;
    }
    exit;
}

// Обработка формы авторизации
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Проверяем существование пользователя
    $stmt = $db->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$username]);
    $userExists = $stmt->fetch();
    
    if (!$userExists) {
        $error = 'Пользователь не существует';
    } else {
        $user = AuthUser($username, $password, $db);
        if ($user) {
            $_SESSION['token'] = $user['token'];
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            // Перенаправление в зависимости от роли
            switch ($user['role']) {
                case 'администратор':
                    header('Location: admin/employees.php');
                    break;
                case 'повар':
                    header('Location: chef/orders.php');
                    break;
                case 'официант':
                    header('Location: waiter/orders.php');
                    break;
            }
            exit;
        } else {
            $error = 'Неверный пароль';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe | Авторизация</title>
    <link rel="stylesheet" href="styles/login.css">
    <style>
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1 class="auth-title">Авторизация</h1>
        <p class="auth-subtitle">Войти в аккаунт<br>введите свой логин и пароль</p>
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form class="auth-form" method="POST">
            <label for="username">Логин</label>
            <input type="text" id="username" name="username" class="auth-input" placeholder="логин" required>
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" class="auth-input" placeholder="пароль" required>
            <button type="submit" class="auth-button">Войти</button>
        </form>
    </div>
</body>
</html>