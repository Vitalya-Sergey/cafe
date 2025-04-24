<?php
require_once __DIR__ . '/../DB.php';

function ChefOrdersSearch($params, $db) {
    if (!is_array($params)) {
        throw new Exception('First parameter must be an array');
    }
    
    if (!($db instanceof PDO)) {
        throw new Exception('Second parameter must be a PDO instance');
    }
    
    $search = isset($params['search']) ? $params['search'] : '';
    $sort = isset($params['sort']) ? $params['sort'] : '';
    $shift_id = isset($params['shift_id']) ? $params['shift_id'] : '';
    $status = isset($params['status']) ? $params['status'] : '';
    
    // Сначала получаем все заказы
    $sql = "SELECT o.order_id, o.table_number, o.status as order_status, o.created_at, 
                   oi.item_id, oi.dish_name, oi.quantity, oi.price, oi.status as item_status,
                   u.full_name as waiter_name,
                   s.shift_date, s.start_time, s.end_time
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN users u ON o.waiter_id = u.user_id
            JOIN shifts s ON o.shift_id = s.shift_id";
    
    $where = ["oi.chef_id = :chef_id"];
    $queryParams = [':chef_id' => $_SESSION['user_id']];
    
    if ($search) {
        $where[] = "(oi.dish_name LIKE :search OR o.table_number LIKE :search)";
        $queryParams[':search'] = "%$search%";
    }
    
    if ($shift_id) {
        $where[] = "o.shift_id = :shift_id";
        $queryParams[':shift_id'] = $shift_id;
    }
    
    if ($status) {
        $where[] = "oi.status = :status";
        $queryParams[':status'] = $status;
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    if ($sort) {
        $sql .= " ORDER BY o.created_at $sort";
    } else {
        $sql .= " ORDER BY o.created_at DESC";
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($queryParams);
    $orders = $stmt->fetchAll();
    
    // Группируем позиции заказов и считаем общую сумму
    $groupedOrders = [];
    foreach ($orders as $order) {
        $orderId = $order['order_id'];
        
        if (!isset($groupedOrders[$orderId])) {
            $groupedOrders[$orderId] = [
                'order_id' => $order['order_id'],
                'created_at' => $order['created_at'],
                'shift_date' => $order['shift_date'],
                'start_time' => $order['start_time'],
                'end_time' => $order['end_time'],
                'table_number' => $order['table_number'],
                'waiter_name' => $order['waiter_name'],
                'items' => [],
                'total_amount' => 0
            ];
        }
        
        $item = [
            'item_id' => $order['item_id'],
            'dish_name' => $order['dish_name'],
            'quantity' => $order['quantity'],
            'price' => $order['price'],
            'status' => $order['item_status']
        ];
        
        $groupedOrders[$orderId]['items'][] = $item;
        $groupedOrders[$orderId]['total_amount'] += $order['price'] * $order['quantity'];
    }
    
    return array_values($groupedOrders);
} 