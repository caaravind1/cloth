-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2024 at 05:18 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cloth`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(75, 13, 25, 1),
(76, 13, 21, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(4, 'Women'),
(7, 'Unisex'),
(9, 'Men'),
(10, 'Kid');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `shipping_address` text NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending',
  `assigned_staff_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `payment_method`, `shipping_address`, `order_date`, `status`, `assigned_staff_id`) VALUES
(8, 16, 6498.00, 'Cash on Delivery', 'sww', '2024-10-13 11:08:06', 'Paid', NULL),
(9, 18, 5059.00, 'Debit Card/Credit Card', 'aluva \r\n', '2024-10-13 13:53:42', 'Paid', NULL),
(10, 16, 5997.00, 'Debit Card/Credit Card', 'manjummal', '2024-10-15 04:07:57', 'Paid', NULL),
(11, 16, 1000.00, 'Debit Card/Credit Card', 'ellor depo', '2024-10-15 06:11:26', 'Paid', NULL),
(12, 13, 699.00, 'Online Payment', 'thumparambamil houde', '2024-10-15 06:46:31', 'Paid', NULL),
(13, 20, 1999.00, 'Debit Card/Credit Card', 'kochi', '2024-10-15 08:27:52', 'Paid', NULL),
(14, 16, 3999.00, 'Debit Card/Credit Card', 'vwefef', '2024-10-17 18:06:02', 'Paid', NULL),
(15, 17, 19992.00, 'Debit Card/Credit Card', 'regrg', '2024-10-17 18:36:30', 'Delivered', 21),
(16, 17, 11996.00, 'Debit Card/Credit Card', 'efwefef', '2024-10-17 18:45:49', 'Delivered', 21),
(17, 17, 699.00, 'Debit Card/Credit Card', 'wefefef', '2024-10-17 18:46:11', 'Delivered', 21),
(18, 17, 2999.00, 'Debit Card/Credit Card', 'rgrrhr', '2024-10-17 18:47:16', 'Delivered', 21),
(19, 17, 1999.00, 'Debit Card/Credit Card', 'efwefef', '2024-10-17 19:02:12', 'Delivered', 21),
(20, 17, 1999.00, 'Debit Card/Credit Card', 'gergerg', '2024-10-17 19:03:07', 'Paid', NULL),
(21, 16, 0.00, '', 'rgerg', '2024-10-18 17:18:40', 'Paid', NULL),
(22, 16, 0.00, '', 'aluva', '2024-10-22 15:22:50', 'Pending', NULL),
(23, 16, 0.00, '', 'aluva', '2024-10-22 15:23:04', 'Pending', NULL),
(24, 16, 0.00, '', 'aluva', '2024-10-22 15:26:54', 'Pending', NULL),
(25, 16, 0.00, '', 'aluva', '2024-10-22 15:38:06', 'Pending', NULL),
(26, 16, 0.00, '', 'aluva', '2024-10-22 15:52:04', 'Pending', NULL),
(27, 16, 0.00, '', 'aluva', '2024-10-25 15:36:14', 'Pending', NULL),
(28, 16, 0.00, '', 'aluva', '2024-10-25 15:37:17', 'Pending', NULL),
(29, 16, 0.00, '', 'ernakulam', '2024-11-03 13:48:15', 'Pending', NULL),
(30, 16, 0.00, '', 'ehcece', '2024-11-03 17:51:55', 'Pending', NULL),
(31, 16, 0.00, '', 'hfyrfg', '2024-11-03 18:00:21', 'Pending', NULL),
(32, 16, 25053.00, 'Cash on Delivery', 'teguf', '2024-11-03 18:25:26', 'Pending', NULL),
(33, 16, 25053.00, 'Online Payment', 'edapally', '2024-11-03 18:37:45', 'Pending', NULL),
(34, 16, 25053.00, 'Online Payment', 'edatapa', '2024-11-03 18:48:34', 'Pending', NULL),
(35, 16, 25053.00, 'Cash on Delivery', 'edfgghtr', '2024-11-03 18:58:31', 'Pending', NULL),
(36, 13, 5129.00, 'Online Payment', 'aluva', '2024-11-12 17:11:03', 'Delivered', 21),
(37, 13, 5129.00, 'Cash on Delivery', 'hey its you ', '2024-11-12 17:16:38', 'Pending', NULL),
(38, 13, 5129.00, 'Cash on Delivery', 'hey its you ', '2024-11-12 17:17:16', 'Pending', NULL),
(39, 16, 2559.00, 'Debit Card/Credit Card', 'hfrinferf', '2024-11-13 06:12:17', 'Pending', NULL),
(40, 16, 2559.00, 'Debit Card/Credit Card', 'kaloor', '2024-11-13 06:12:23', 'Pending', NULL),
(41, 16, 2559.00, 'Online Payment', 'ernkalulath', '2024-11-13 06:24:28', 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(16, 8, 12, 1, 1999.00),
(23, 9, 21, 1, 560.00),
(24, 10, 12, 1, 1999.00),
(25, 10, 12, 1, 1999.00),
(26, 10, 12, 1, 1999.00),
(28, 12, 24, 1, 699.00),
(29, 13, 12, 1, 1999.00),
(34, 15, 12, 2, 1999.00),
(38, 15, 12, 4, 1999.00),
(40, 17, 24, 1, 699.00),
(42, 19, 12, 1, 1999.00),
(43, 20, 12, 1, 1999.00);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expiry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expiry`) VALUES
(1, 'ashlin@gmail.com', 'ca77149d856f2b8705071999406de599eec1ee38ad078c689a4ab2f6378dcc1af6a4c113562157daeff0e5141d8289a052b6', '2024-08-15 21:01:39');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `image`, `created_at`, `category_id`, `stock`) VALUES
(12, 'Jeans', 1999.00, 'Stripped, blue colored, ballon fit etc...', 'jeans.jpg', '2024-09-29 13:49:57', 4, 6),
(21, 'T-shirt', 560.00, 'Printed, over-sized ', 'over.jpg', '2024-10-03 14:45:36', 4, 2),
(24, 'T-shirt', 699.00, 't-shirt', 'unisex-wild-child-printed-t-shirt-563455-1677582530-1.jpg', '2024-10-15 06:44:13', 7, 5),
(25, 'T-shirt', 4569.00, 'Black, Fit ', 'kids-mockup.jpg', '2024-11-12 17:07:11', 10, 8),
(26, 'Jeans', 789.00, 'Stripped, Blue', 'kidsjeans.jpg', '2024-11-13 06:36:01', 10, 4);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Customer'),
(2, 'Admin'),
(3, 'Staff');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `sale_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `security_question` varchar(255) NOT NULL,
  `security_answer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role_id`, `created_at`, `security_question`, `security_answer`) VALUES
(13, 'Arugh', 'arugh@gmail.com', '$2y$10$pbxUYwuhfUojh8YUusB42ej31feUkqzUnQl9o/qAY9wbX815/a/ZK', 2, '2024-08-16 13:58:41', '', ''),
(16, 'femina', 'femina@gmail.com', '$2y$10$qOa4M6gBhOUNxe/YwQfw7Or2qcUamjew68lRp7olWctQWYR8vwZmS', 1, '2024-08-29 18:10:13', 'What is the name of the town where you were born?', '$2y$10$a2kEKbAmkYI7hKDkdLZCOOlbsBl4cKo9IlUcpbzX42nDXYMneOqDS'),
(17, 'karthi', 'karthi@gmail.com', '$2y$10$JdH.CgyOgkLzroTdVJD4l.vFRZ.DV3AZLzVxfiAy1bVhC7P1Awsl2', 1, '2024-10-04 14:39:01', 'What was your first car?', '$2y$10$YGI7NyLvDMhhpj.895l1ROyZiKx0she4XH0uwZmvfMUf7cViiyxCm'),
(18, 'arthur', 'arthur@gmail.com', '$2y$10$SANRUiRxj6fcIHbI6kOdRuf24maxFZiF4RkXvYCYgjywRz3SI3lfe', 1, '2024-10-13 13:22:09', 'What was your first car?', '$2y$10$IjnZQIMl4CVCTDFhICHIeOG/X1VgYAytPZPFZ9HxGfv43M/zGY.z6'),
(19, 'Aravind', 'aravind@gmail.com', '$2y$10$DuZSH0ZFtQOb7pTFm3q7CeIpT5njKgyp1gU0mlTZxmy5PgQhed1fq', 2, '2024-10-13 14:11:51', 'Your first pet\'s name?', 'ruku'),
(20, 'Ronnie Sir', 'ronnie@gmail.com', '$2y$10$AzJ7JJjZw01GmYSLDTbcpeWqEzbmeYVRggQssb1te.l80NQHovmL.', 1, '2024-10-15 08:11:52', 'What was your first car?', '$2y$10$Wwl6NCb5vy8phgQ3o0hFkuuejZFoJ1UlXg.0spkbwsayc0nOwbFvO'),
(21, 'maria', 'maria@gmail.com', '$2y$10$f8BP2z4QMXv8nlNDJPY33./.Y5AzDq0vS.IzezptAQcjx0zWmT372', 3, '2024-10-18 15:56:51', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `assigned_staff_id` (`assigned_staff_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`assigned_staff_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
