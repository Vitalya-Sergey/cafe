<?php

function ShiftsSearch($params, $db) {
    $search = isset($params['search']) ? $params['search'] : '';
    $sort = isset($params['sort']) ? $params['sort'] : '';
    
    $sql = "SELECT s.*, 
            GROUP_CONCAT(CASE WHEN sa.role = 'повар' THEN u.full_name END) as chef_name,
            GROUP_CONCAT(CASE WHEN sa.role = 'официант' THEN u.full_name END) as waiter_name
            FROM shifts s
            LEFT JOIN shift_assignments sa ON s.shift_id = sa.shift_id
            LEFT JOIN users u ON sa.user_id = u.user_id";
    
    $where = [];
    $params = [];
    
    if ($search) {
        $where[] = "DATE_FORMAT(s.shift_date, '%d.%m.%Y') LIKE :search";
        $params[':search'] = "%$search%";
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    $sql .= " GROUP BY s.shift_id";
    
    if ($sort) {
        $sql .= " ORDER BY s.shift_date $sort, s.start_time $sort";
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
?> 