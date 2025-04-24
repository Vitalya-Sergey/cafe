<?php
require_once __DIR__ . '/../DB.php';

function WaiterOrdersSearch($params, $db) {
    $conditions = [];
    $queryParams = [];

    // Фильтр по ID официанта
    if (isset($params['waiter_id'])) {
        $conditions[] = "o.waiter_id = :waiter_id";
        $queryParams[':waiter_id'] = $params['waiter_id'];
    }

    // Фильтр по смене
    if (!empty($params['shift_id'])) {
        $conditions[] = "o.shift_id = :shift_id";
        $queryParams[':shift_id'] = $params['shift_id'];
    }

    // Фильтр по статусу с учетом соответствия статусов
    if (!empty($params['status'])) {
        $statusMap = [
            'принят' => 'создан',
            'оплачен' => 'оплачен',
            'закрыт' => 'закрыт'
        ];
        if (isset($statusMap[$params['status']])) {
            $conditions[] = "o.status = :status";
            $queryParams[':status'] = $statusMap[$params['status']];
        }
    }

    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    $query = "
        SELECT 
            o.*,
            s.shift_date,
            s.start_time,
            s.end_time,
            GROUP_CONCAT(
                CONCAT(oi.quantity, 'x ', oi.dish_name, ' (', oi.status, ')')
                SEPARATOR '\n'
            ) as items
        FROM orders o
        LEFT JOIN shifts s ON o.shift_id = s.shift_id
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        $whereClause
        GROUP BY o.order_id
        ORDER BY o.created_at DESC
    ";

    try {
        // Отладочная информация
        error_log("SQL Query: " . $query);
        error_log("Parameters: " . print_r($queryParams, true));
        error_log("User ID: " . $params['waiter_id']);

        $stmt = $db->prepare($query);
        foreach ($queryParams as $param => $value) {
            $stmt->bindValue($param, $value);
            error_log("Binding $param to $value");
        }
        $stmt->execute();

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Found orders: " . count($orders));
        error_log("Orders data: " . print_r($orders, true));

        return $orders;
    } catch (PDOException $e) {
        error_log("SQL Error in WaiterOrdersSearch: " . $e->getMessage());
        throw $e;
    }
} 