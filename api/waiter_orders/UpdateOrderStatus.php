<?php
require_once __DIR__ . '/../DB.php';

function UpdateOrderStatus($order_id, $new_status) {
    global $db;
    
    // Validate status
    $valid_statuses = ['принят', 'оплачен', 'закрыт'];
    if (!in_array($new_status, $valid_statuses)) {
        return [
            'success' => false,
            'message' => 'Недопустимый статус'
        ];
    }

    try {
        // Prepare and execute the update query
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$new_status, $order_id]);

        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Статус успешно обновлен'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Заказ не найден'
            ];
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Ошибка при обновлении статуса: ' . $e->getMessage()
        ];
    }
} 