<?php
require_once __DIR__ . '/../DB.php';

// Проверка авторизации
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'повар') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Получаем данные из POST-запроса
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : null;

// Проверяем наличие необходимых данных
if (!$order_id || !$status) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    // Обновляем статус всех позиций заказа
    $sql = "UPDATE order_items 
            SET status = :status 
            WHERE order_id = :order_id AND chef_id = :chef_id";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':status' => $status,
        ':order_id' => $order_id,
        ':chef_id' => $_SESSION['user_id']
    ]);
    
    // Проверяем, были ли обновлены какие-либо записи
    if ($stmt->rowCount() > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No records were updated']);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 