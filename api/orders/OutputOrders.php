<?php

function OutputOrders($orders) {
    foreach($orders as $order) {
        $id = $order['order_id'];
        $date = date('d.m.Y H:i', strtotime($order['created_at']));
        $shift = date('d.m.Y', strtotime($order['shift_date'])) . ' (' . 
                date('H:i', strtotime($order['start_time'])) . '-' . 
                date('H:i', strtotime($order['end_time'])) . ')';
        $table = $order['table_number'];
        $waiter = $order['waiter_name'];
        $status = $order['status'];
        $amount = number_format($order['total_amount'], 2, '.', ' ') . ' ₽';
        
        echo "<tr>
            <td>$id</td>
            <td>$date</td>
            <td>$shift</td>
            <td>$table</td>
            <td>$waiter</td>
            <td>$status</td>
            <td>$amount</td>
        </tr>";
    }
}
?> 