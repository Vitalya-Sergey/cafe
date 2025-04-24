-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Апр 24 2025 г., 06:04
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `cafe`
--

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `table_number` int(11) NOT NULL,
  `waiter_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `status` enum('создан','в процессе','готов','оплачен','закрыт') NOT NULL DEFAULT 'создан',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`order_id`, `table_number`, `waiter_id`, `shift_id`, `status`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 1, 'оплачен', 1500.00, '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(2, 2, 4, 1, 'закрыт', 2300.00, '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(3, 3, 5, 2, 'в процессе', 1800.00, '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(4, 1, 5, 2, 'создан', 0.00, '2025-04-24 02:01:14', '2025-04-24 02:01:14');

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `dish_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('ожидает','в процессе','готово') NOT NULL DEFAULT 'ожидает',
  `chef_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `dish_name`, `quantity`, `price`, `status`, `chef_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'Стейк Рибай', 2, 1200.00, 'готово', 2, '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(2, 1, 'Картофель фри', 2, 300.00, 'готово', 2, '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(3, 2, 'Паста Карбонара', 1, 800.00, 'готово', 2, '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(4, 2, 'Салат Цезарь', 2, 600.00, 'готово', 2, '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(5, 2, 'Тирамису', 1, 900.00, 'готово', 2, '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(6, 3, 'Борщ', 2, 600.00, 'в процессе', 3, '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(7, 3, 'Котлета по-киевски', 2, 1200.00, 'ожидает', NULL, '2025-04-24 02:01:14', '2025-04-24 02:01:14');

-- --------------------------------------------------------

--
-- Структура таблицы `shifts`
--

CREATE TABLE `shifts` (
  `shift_id` int(11) NOT NULL,
  `shift_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `shifts`
--

INSERT INTO `shifts` (`shift_id`, `shift_date`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES
(1, '2024-03-01', '08:00:00', '16:00:00', '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(2, '2024-03-01', '16:00:00', '00:00:00', '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(3, '2024-03-02', '08:00:00', '16:00:00', '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(4, '2024-03-02', '16:00:00', '00:00:00', '2025-04-24 02:01:14', '2025-04-24 02:01:14');

-- --------------------------------------------------------

--
-- Структура таблицы `shift_assignments`
--

CREATE TABLE `shift_assignments` (
  `assignment_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('повар','официант') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `shift_assignments`
--

INSERT INTO `shift_assignments` (`assignment_id`, `shift_id`, `user_id`, `role`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'повар', '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(2, 1, 4, 'официант', '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(3, 2, 3, 'повар', '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(4, 2, 5, 'официант', '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(5, 3, 2, 'повар', '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(6, 3, 5, 'официант', '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(7, 4, 3, 'повар', '2025-04-24 02:01:14', '2025-04-24 02:01:14'),
(8, 4, 4, 'официант', '2025-04-24 02:01:14', '2025-04-24 02:01:14');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('администратор','повар','официант') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `token` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `login`, `password`, `full_name`, `role`, `created_at`, `updated_at`, `token`) VALUES
(1, 'admin', '1234', 'Иванов Иван Иванович', 'администратор', '2025-04-24 02:01:14', '2025-04-24 02:01:14', ''),
(2, 'chef1', '123', 'Петров Петр Петрович', 'повар', '2025-04-24 02:01:14', '2025-04-24 02:01:14', ''),
(3, 'chef2', '321', 'Сидоров Сидор Сидорович', 'повар', '2025-04-24 02:01:14', '2025-04-24 02:01:14', ''),
(4, 'waiter1', '421', 'Смирнова Анна Сергеевна', 'официант', '2025-04-24 02:01:14', '2025-04-24 02:01:14', ''),
(5, 'waiter2', '142', 'Козлова Елена Дмитриевна', 'официант', '2025-04-24 02:01:14', '2025-04-24 02:01:14', '');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `waiter_id` (`waiter_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `chef_id` (`chef_id`);

--
-- Индексы таблицы `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`shift_id`);

--
-- Индексы таблицы `shift_assignments`
--
ALTER TABLE `shift_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `shift_id` (`shift_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `shifts`
--
ALTER TABLE `shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `shift_assignments`
--
ALTER TABLE `shift_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`);

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`chef_id`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `shift_assignments`
--
ALTER TABLE `shift_assignments`
  ADD CONSTRAINT `shift_assignments_ibfk_1` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shift_assignments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
