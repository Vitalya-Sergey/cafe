<?php

function OutputChefOrders($orders) {
    if (empty($orders)) {
        echo '<tr><td colspan="8" class="text-center">Заказы не найдены</td></tr>';
        return;
    }
    
    foreach ($orders as $order) {
        $date = date('d.m.Y H:i', strtotime($order['created_at']));
        $shift = date('d.m.Y', strtotime($order['shift_date'])) . ' (' . 
                date('H:i', strtotime($order['start_time'])) . '-' . 
                date('H:i', strtotime($order['end_time'])) . ')';
        
        // Формируем список блюд
        $itemsList = '';
        foreach ($order['items'] as $item) {
            $statusClass = '';
            switch ($item['status']) {
                case 'ожидает':
                    $statusClass = 'status-waiting';
                    break;
                case 'в процессе':
                    $statusClass = 'status-processing';
                    break;
                case 'готово':
                    $statusClass = 'status-ready';
                    break;
            }
            
            $itemsList .= '<div class="order-item ' . $statusClass . '">';
            $itemsList .= htmlspecialchars($item['dish_name'] . ' x' . $item['quantity']);
            $itemsList .= ' - ' . htmlspecialchars($item['price'] * $item['quantity'] . ' ₽');
            $itemsList .= '</div>';
        }
        
        // Определяем общий статус заказа
        $allReady = true;
        $allWaiting = true;
        foreach ($order['items'] as $item) {
            if ($item['status'] != 'готово') {
                $allReady = false;
            }
            if ($item['status'] != 'ожидает') {
                $allWaiting = false;
            }
        }
        
        $orderStatus = $allReady ? 'готово' : ($allWaiting ? 'ожидает' : 'в процессе');
        
        echo '<tr>';
        echo '<td>' . htmlspecialchars($order['order_id']) . '</td>';
        echo '<td>' . htmlspecialchars($date) . '</td>';
        echo '<td>' . htmlspecialchars($shift) . '</td>';
        echo '<td>' . htmlspecialchars($order['table_number']) . '</td>';
        echo '<td>' . htmlspecialchars($order['waiter_name']) . '</td>';
        echo '<td>' . $itemsList . '</td>';
        echo '<td>' . htmlspecialchars(number_format($order['total_amount'], 2, '.', ' ') . ' ₽') . '</td>';
        echo '<td>';
        echo '<select class="status-select" onchange="updateOrderStatus(' . $order['order_id'] . ', this.value)">';
        echo '<option value="ожидает" ' . ($orderStatus == 'ожидает' ? 'selected' : '') . '>Ожидает</option>';
        echo '<option value="в процессе" ' . ($orderStatus == 'в процессе' ? 'selected' : '') . '>В процессе</option>';
        echo '<option value="готово" ' . ($orderStatus == 'готово' ? 'selected' : '') . '>Готово</option>';
        echo '</select>';
        echo '</td>';
        echo '</tr>';
    }
}
?> 