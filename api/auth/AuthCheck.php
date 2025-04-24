<?php
require_once __DIR__ . '/../DB.php';

function AuthCheck($db) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['token'])) {
        return false;
    }
    
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :user_id AND token = :token");
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':token' => $_SESSION['token']
    ]);
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        return [
            'user_id' => $user['user_id'],
            'login' => $user['login'],
            'full_name' => $user['full_name'],
            'role' => $user['role']
        ];
    }
    
    return false;
}
?>
