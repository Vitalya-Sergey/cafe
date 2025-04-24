<?php

function EmployeesSearch($params, $db) {
    $search = isset($params['search']) ? $params['search'] : '';
    $sort = isset($params['sort']) ? $params['sort'] : '';
    
    $sql = "SELECT * FROM users";
    $where = [];
    $params = [];
    
    if ($search) {
        $where[] = "LOWER(full_name) LIKE LOWER(:search)";
        $params[':search'] = "%$search%";
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    if ($sort) {
        $sql .= " ORDER BY full_name $sort";
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
?>