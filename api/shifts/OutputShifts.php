<?php

function OutputShifts($shifts) {
    foreach($shifts as $shift) {
        $id = $shift['shift_id'];
        $date = date('d.m.Y', strtotime($shift['shift_date']));
        $start_time = date('H:i', strtotime($shift['start_time']));
        $end_time = date('H:i', strtotime($shift['end_time']));
        $chef = $shift['chef_name'] ?: 'Не назначен';
        $waiter = $shift['waiter_name'] ?: 'Не назначен';
        
        echo "<tr>
            <td>$id</td>
            <td>$date</td>
            <td>$start_time</td>
            <td>$end_time</td>
            <td>$chef</td>
            <td>$waiter</td>
            <td>
                <button class='btn btn-danger' onclick='deleteShift($id)'>Удалить</button>
            </td>
        </tr>";
    }
}
?> 