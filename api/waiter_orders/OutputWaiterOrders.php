<?php
function OutputWaiterOrders($orders) {
    if (empty($orders)) {
        echo '<tr><td colspan="8" class="no-orders">Заказов не найдено</td></tr>';
        return;
    }

    // Определяем маппинг статусов
    $statusMap = [
        'создан' => 'принят',
        'оплачен' => 'оплачен',
        'закрыт' => 'закрыт'
    ];

    foreach ($orders as $order) {
        try {
            // Подготавливаем данные
            $orderId = htmlspecialchars($order['order_id'] ?? '');
            $createdAt = !empty($order['created_at']) ? date('d.m.Y H:i', strtotime($order['created_at'])) : '';
            $shiftDate = !empty($order['shift_date']) ? date('d.m.Y', strtotime($order['shift_date'])) : '';
            $shiftTime = '';
            if (!empty($order['start_time']) && !empty($order['end_time'])) {
                $shiftTime = date('H:i', strtotime($order['start_time'])) . '-' . 
                            date('H:i', strtotime($order['end_time']));
            }
            $tableNumber = htmlspecialchars($order['table_number'] ?? '');
            $status = htmlspecialchars($order['status'] ?? '');
            $totalAmount = !empty($order['total_amount']) ? 
                number_format($order['total_amount'], 2, '.', ' ') : '0.00';

            // Преобразуем статус для отображения
            $currentStatus = isset($statusMap[$status]) ? $statusMap[$status] : $status;

            // Выводим строку таблицы
            ?>
            <tr>
                <td><?php echo $orderId; ?></td>
                <td><?php echo $createdAt; ?></td>
                <td><?php echo $shiftDate; ?> (<?php echo $shiftTime; ?>)</td>
                <td><?php echo $tableNumber; ?></td>
                <td>
                    <?php if (!empty($order['items'])): ?>
                        <ul class="order-items">
                            <?php foreach (explode("\n", $order['items']) as $item): ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <span class="no-items">Нет позиций</span>
                    <?php endif; ?>
                </td>
                <td>
                    <select class="status-select" data-order-id="<?php echo $orderId; ?>" 
                            data-previous-status="<?php echo $currentStatus; ?>"
                            onchange="updateOrderStatus(<?php echo $orderId; ?>, this.value, this.getAttribute('data-previous-status'))">
                        <?php foreach (array_values($statusMap) as $statusOption): ?>
                            <option value="<?php echo $statusOption; ?>" 
                                    <?php echo ($currentStatus === $statusOption) ? 'selected' : ''; ?>>
                                <?php echo $statusOption; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><?php echo $totalAmount; ?>₽</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="deleteOrder(<?php echo $orderId; ?>)">Удалить</button>
                </td>
            </tr>
            <?php
        } catch (Exception $e) {
            error_log("Error processing order: " . $e->getMessage());
            error_log("Order data: " . print_r($order, true));
        }
    }
}