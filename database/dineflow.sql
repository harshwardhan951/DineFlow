-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2026 at 08:41 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dineflow`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `hotel_name` varchar(150) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `hotel_name`, `location`, `contact`, `created_at`, `image`, `status`) VALUES
(1, 'Hotel Mauli', 'Jakat Naka', NULL, '2026-02-17 17:36:34', '1783362885_hotel_mauli.avif', 'active'),
(3, 'Hotel Elite', 'Adgaon', NULL, '2026-02-17 18:01:03', '1783362875_hotel_elite.avif', 'inactive'),
(5, 'Hotel Maharaja', 'Jakat Naka', NULL, '2026-02-17 18:08:33', '1783362860_hotel_maharaja.avif', 'active'),
(6, 'Hotel Courtyard', 'Bombay Naka', NULL, '2026-02-19 07:32:05', '1771486325_BAMBOOS.avif', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `managers`
--

CREATE TABLE `managers` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `managers`
--

INSERT INTO `managers` (`id`, `hotel_id`, `name`, `email`, `password`, `created_at`) VALUES
(2, 3, '', '', 'raj', '2026-02-17 18:01:03'),
(4, 5, 'Raj Patel', 'raj@gmail.com', 'raj', '2026-02-17 18:08:33'),
(5, 6, 'Harsh Malode', 'harsh@gmail.com', 'harsh', '2026-02-19 07:32:05');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `hotel_id`, `item_name`, `description`, `category`, `price`, `image`, `created_at`, `status`) VALUES
(1, 5, 'Panner Masala', 'very delicious dish', 'Veg', 299.00, '../uploads/1771352914_Screenshot 2025-08-11 220843.png', '2026-02-17 18:28:34', 'Available'),
(2, 6, 'Kaju Masala', 'A delicious North Indian delicacy made with roasted cashews simmered in a rich, creamy tomato-based gravy infused with butter, cream, and traditional Indian spices.', 'Veg', 550.00, '../uploads/1771486483_kaju.webp', '2026-02-19 07:34:43', 'Available'),
(3, 6, 'Panner Masala', 'Soft paneer cubes cooked in a rich and flavorful tomato-based masala gravy.', 'Veg', 670.00, '../uploads/1771486789_paneer.avif', '2026-02-19 07:39:49', 'Available'),
(4, 6, 'Tandur Roti', 'The dough is hand-rolled and slapped onto the inner wall of the hot tandoor, where it cooks quickly at high temperature, developing a lightly crisp exterior with soft, chewy layers inside.', 'Veg', 40.00, '../uploads/1771488601_tandoori.jpg', '2026-02-19 08:10:01', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `order_status` enum('Pending','Preparing','Completed','Cancelled') DEFAULT 'Pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending',
  `dishes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `hotel_id`, `total_amount`, `order_status`, `order_date`, `status`, `dishes`) VALUES
(3, 2, 6, 1420.00, 'Pending', '2026-02-19 07:59:23', 'Completed', NULL),
(5, 1, 6, 550.00, 'Pending', '2026-02-19 08:06:34', 'Completed', NULL),
(6, 1, 5, 299.00, 'Pending', '2026-02-19 08:06:48', 'Completed', NULL),
(7, 4, 6, 1380.00, 'Pending', '2026-03-16 05:54:58', 'Completed', NULL),
(8, 7, 6, 590.00, 'Pending', '2026-06-01 10:27:05', 'Completed', NULL),
(9, 8, 6, 1380.00, 'Pending', '2026-06-18 07:55:41', 'Completed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `quantity`, `price`) VALUES
(2, 3, 2, 1, 550.00),
(3, 3, 3, 1, 670.00),
(5, 5, 2, 1, 550.00),
(6, 6, 1, 1, 299.00),
(7, 3, 4, 5, 40.00),
(8, 7, 2, 1, 550.00),
(9, 7, 3, 1, 670.00),
(10, 7, 4, 4, 40.00),
(11, 8, 2, 1, 550.00),
(12, 8, 4, 1, 40.00),
(13, 9, 2, 1, 550.00),
(14, 9, 3, 1, 670.00),
(15, 9, 4, 4, 40.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','manager','user') DEFAULT 'user',
  `profile_image` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `phone`, `password`, `address`, `created_at`, `role`, `profile_image`) VALUES
(1, 'Gokul Malode', '', 'gokul@gmail.com', '1234567890', 'gokul', 'Jatra Hotel, Nashik, Nandur Road, Vishwa Avanue Apartment, Flat No. 07\r\nJatral Hotel, Nashik', '2026-02-17 16:13:14', 'user', '1771347790_3.jpeg'),
(2, 'Vaishali Malode', '', 'vaishali@gmail.com', NULL, 'vaishali', NULL, '2026-02-17 16:13:55', 'user', 'default.png'),
(3, 'Admin', '', 'admin@gmail.com', NULL, 'admin123', NULL, '2026-02-17 17:16:51', 'admin', 'default.png'),
(4, 'Yash Ahirrao', '', 'user4@temp.com', NULL, 'yash', NULL, '2026-03-16 05:54:16', 'user', 'default.png'),
(7, 'Pranav Patil', '', 'pranp12@gmail.com', '8888330047', 'pranav', 'aadgaon', '2026-06-01 10:26:22', 'user', '1780309680_3.jpeg'),
(8, 'Shweta Malode', '', 'shweta03@gmail.com', NULL, 'shweta', NULL, '2026-06-18 07:53:42', 'user', 'default.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `managers`
--
ALTER TABLE `managers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `managers`
--
ALTER TABLE `managers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `managers`
--
ALTER TABLE `managers`
  ADD CONSTRAINT `managers_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
