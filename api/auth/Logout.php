<?php
session_start();

// Очищаем все данные сессии
$_SESSION = array();

// Уничтожаем сессию
session_destroy();

// Возвращаем успешный ответ
header('Content-Type: application/json');
echo json_encode(['success' => true]); 