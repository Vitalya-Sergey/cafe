<?php

function OrdersSearch($params, $db) {
    $search = isset($params['search']) ? $params['search'] : '';
    $sort = isset($params['sort']) ? $params['sort'] : '';
    $shift_id = isset($params['shift_id']) ? $params['shift_id'] : '';
    $status = isset($params['status']) ? $params['status'] : '';
    
    $sql = "SELECT o.*, 
            u.full_name as waiter_name,
            s.shift_date, s.start_time, s.end_time
            FROM orders o
            LEFT JOIN users u ON o.waiter_id = u.user_id
            LEFT JOIN shifts s ON o.shift_id = s.shift_id";
    
    $where = [];
    $params = [];
    
    if ($search) {
        $where[] = "o.table_number LIKE :search";
        $params[':search'] = "%$search%";
    }
    
    if ($shift_id) {
        $where[] = "o.shift_id = :shift_id";
        $params[':shift_id'] = $shift_id;
    }
    
    if ($status) {
        $where[] = "o.status = :status";
        $params[':status'] = $status;
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    if ($sort) {
        $sql .= " ORDER BY o.created_at $sort";
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
?> 