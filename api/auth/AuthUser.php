<?php

require_once __DIR__ . '/../DB.php';

function AuthUser($login, $password, $db) {
    // Ищем пользователя в базе данных с использованием подготовленных выражений
    $stmt = $db->prepare("SELECT * FROM users WHERE login = :login AND password = :password");
    $stmt->execute([
        ':login' => $login,
        ':password' => $password
    ]);
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Генерируем новый токен
        $token = bin2hex(random_bytes(32));
        
        // Обновляем токен в базе данных
        $updateStmt = $db->prepare("UPDATE users SET token = :token WHERE user_id = :user_id");
        $updateStmt->execute([
            ':token' => $token,
            ':user_id' => $user['user_id']
        ]);
        
        return [
            'user_id' => $user['user_id'],
            'login' => $user['login'],
            'full_name' => $user['full_name'],
            'role' => $user['role'],
            'token' => $token
        ];
    }
    
    return false;
}
?>