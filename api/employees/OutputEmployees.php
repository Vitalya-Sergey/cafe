<?php

function OutputEmployees($employees) {
    foreach($employees as $employee) {
        $id = $employee['user_id'];
        $full_name = $employee['full_name'];
        $login = $employee['login'];
        $role = $employee['role'];
        
        echo "<tr>
            <td>$id</td>
            <td>$full_name</td>
            <td>$login</td>
            <td>$role</td>
            <td>
                <button class='btn btn-danger' onclick='deleteEmployee($id)'>Удалить</button>
            </td>
        </tr>";
    }
}
?> 