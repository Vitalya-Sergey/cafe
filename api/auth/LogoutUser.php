<?php

function LogoutUser($db) {
    if (isset($_SESSION['token'])) {
        $token = $_SESSION['token'];
        
        // Очистка токена в базе данных
        $stmt = $db->prepare("UPDATE users SET token = NULL WHERE token = :token");
        $stmt->execute([':token' => $token]);
        
        // Очистка сессии
        unset($_SESSION['token']);
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['full_name']);
        unset($_SESSION['role']);
    }
    
    return true;
}
?>