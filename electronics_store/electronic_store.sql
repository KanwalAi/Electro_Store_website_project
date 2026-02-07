-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2026 at 10:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `electronic_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `email_replies`
--

CREATE TABLE `email_replies` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `reply_message` longtext NOT NULL,
  `status` enum('draft','sent','failed') DEFAULT 'sent',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_name`, `user_email`, `message`, `status`, `created_at`) VALUES
(5, 'Kanwal Fatima', 'i243128@isb.nu.edu.pk', '[Subject: general] hi', 'read', '2026-01-29 16:07:58'),
(6, 'Kanwal Fatima', 'i243128@isb.nu.edu.pk', '[Subject: s] sd', 'unread', '2026-01-29 16:12:32');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `payment_method`, `shipping_address`, `created_at`, `updated_at`) VALUES
(13, 2, 429.84, 'pending', 'Credit Card', '', '2026-01-29 16:07:32', '2026-01-29 16:07:32');

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
(13, 13, 21, 1, 398.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `rating` float DEFAULT 0,
  `reviews` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category`, `brand`, `price`, `stock`, `image`, `rating`, `reviews`, `created_at`, `updated_at`) VALUES
(17, 'iPhone 16 Pro Max', 'The ultimate iPhone experience featuring a durable titanium design and the powerful A18 Pro chip. It boasts a stunning 6.9-inch Super Retina XDR display and a pro-grade camera system with 5x optical zoom, perfect for capturing professional photos and videos.', 'Smartphone', 'APPLE', 1199.00, 15, 'p_697b4c8b5ad850.83721849.jfif', 0, 0, '2026-01-29 12:03:23', '2026-01-29 12:03:23'),
(18, 'Galaxy S24 Ultra', 'A powerhouse for productivity and creativity, featuring a built-in S Pen and a massive 200MP camera for crystal-clear shots. Powered by Galaxy AI, it offers features like real-time translation and advanced photo editing on a flat, anti-glare display.', 'Smartphone', 'Samsung', 1299.00, 20, 'p_697b4ce490cb69.02853144.jfif', 0, 0, '2026-01-29 12:04:52', '2026-01-29 12:04:52'),
(19, 'MacBook Air (M3 Chip)', 'Incredibly thin and light, this laptop is perfect for travel and daily work. The M3 chip delivers fast performance for multitasking and editing, all while running silently without a fan. Enjoy up to 18 hours of battery life on a single charge.', 'Laptop', 'Apple', 1099.00, 10, 'p_697b4d2cdc0c97.65979044.jfif', 0, 0, '2026-01-29 12:06:04', '2026-01-29 12:06:04'),
(20, 'XPS 13', 'Known for its premium design, the XPS 13 features a virtually borderless InfinityEdge display and a compact aluminum body. It’s lightweight yet powerful, making it the ideal choice for professionals who need performance on the go.', 'Laptop', 'Dell', 1249.00, 10, 'p_697b4d8ddb41a8.63632109.jfif', 0, 0, '2026-01-29 12:07:41', '2026-01-29 12:07:41'),
(21, 'WH-1000XM5', 'Experience industry-leading noise cancellation that blocks out distractions so you can focus on your music or calls. These lightweight headphones offer up to 30 hours of battery life and smart features that pause audio when you start speaking.', 'Noise-Canceling Headphones', 'Sony', 398.00, 9, 'p_697b4e0199afb6.87294550.jfif', 0, 0, '2026-01-29 12:09:37', '2026-01-29 16:07:32'),
(22, 'AirPods Pro (2nd Gen)', 'The world\'s most popular earbuds, now with 2x more active noise cancellation. They feature Transparency mode to hear your surroundings and Personalized Spatial Audio for an immersive sound experience. The case now includes a speaker to help you find it easily.', 'Wireless Earbuds', 'Apple', 398.00, 10, 'p_697b4e3e8c8b76.48686683.jfif', 5, 3, '2026-01-29 12:10:38', '2026-01-29 16:26:16'),
(23, 'Apple Watch Series 10', 'The advanced companion for a healthy life. It tracks your daily activity, monitors heart rate and blood oxygen, and detects falls or car crashes to call for help. The Always-On Retina display is brighter and easier to read outdoors.', 'Smartwatch', 'Apple', 399.00, 10, 'p_697b4eb1d486b4.75105846.jfif', 0, 0, '2026-01-29 12:12:33', '2026-01-29 12:12:33'),
(26, 'clock', '24/7', 'Clock', 'APPLE', 100.00, 10, 'p_697b8619651fb1.24316035.jfif', 0, 0, '2026-01-29 16:08:57', '2026-01-29 16:08:57');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`, `name`) VALUES
(6, 22, 2, 5, 'great', '2026-01-29 16:07:16', 'Kanwal Fatima'),
(7, 22, 1, 5, 'w', '2026-01-29 16:24:40', 'Admin'),
(8, 22, 1, 5, 'ds', '2026-01-29 16:26:16', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `zip` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `city`, `state`, `zipcode`, `country`, `role`, `created_at`, `updated_at`, `zip`) VALUES
(1, 'Admin', 'admin123@gmail.com', '$2y$10$OsXDv1fAL/e.GJDyccutKOX5BzhKKdOmzQnmOVmPtYJIEPSgCcdnC', '00000000000', '', '', '', 'no', 'Pakistan', 'admin', '2026-01-25 10:50:01', '2026-02-07 09:37:12', NULL),
(2, 'Kanwal Fatima', 'i243128@isb.nu.edu.pk', '$2y$10$nZr/.TLLaLubp58UhcI2Q.XEcdDXSZNBjDUOf9di6lYoB8iZWn9kK', '+923297808416', 'pakistan', 'Bahawalpur', '', 'no', 'Pakistan', 'customer', '2026-01-25 10:57:09', '2026-01-29 12:40:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `email_replies`
--
ALTER TABLE `email_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `email_replies`
--
ALTER TABLE `email_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `email_replies`
--
ALTER TABLE `email_replies`
  ADD CONSTRAINT `email_replies_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
