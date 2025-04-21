-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2025 at 12:52 PM
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
-- Database: `laundry_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `full_name`, `email`, `role`, `created_at`, `updated_at`, `profile_picture`) VALUES
(1, 'admin', 'admin123', 'Allain Admin', 'admin@dryme.com', 'admin', '2025-04-13 02:06:20', '2025-04-19 10:31:21', 'admin_1_1745058681.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `username`, `password`, `email`, `full_name`, `phone`, `address`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Allain', '123', 'allainralphlegaspi@gmail.com', 'Allain Legaspi', '123123', 'Casay', 'customer', '2025-04-12 14:48:13', '2025-04-13 03:36:26');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `item_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('supply','equipment') NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(20) DEFAULT NULL,
  `minimum_stock` decimal(10,2) NOT NULL DEFAULT 10.00,
  `cost_per_unit` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `last_maintenance_date` date DEFAULT NULL,
  `next_maintenance_date` date DEFAULT NULL,
  `supplier` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`item_id`, `name`, `category`, `quantity`, `unit`, `minimum_stock`, `cost_per_unit`, `status`, `last_maintenance_date`, `next_maintenance_date`, `supplier`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Ariel Powder', 'supply', 100.00, 'pieces', 10.00, NULL, 'active', NULL, NULL, NULL, NULL, '2025-04-13 14:15:55', '2025-04-13 14:15:55'),
(2, 'Tide Powder', 'supply', 100.00, 'pieces', 10.00, NULL, 'active', NULL, NULL, NULL, NULL, '2025-04-13 14:16:16', '2025-04-13 14:16:16'),
(3, 'Surf Powder', 'supply', 100.00, 'pieces', 10.00, NULL, 'active', NULL, NULL, NULL, NULL, '2025-04-13 14:16:28', '2025-04-13 14:16:28'),
(4, 'Ariel Liquid Detergent', 'supply', 250.00, 'pieces', 10.00, NULL, 'active', NULL, NULL, NULL, NULL, '2025-04-13 14:17:05', '2025-04-19 10:01:21'),
(5, 'Surf Liquid Detergent', 'supply', 250.00, 'pieces', 10.00, NULL, 'active', NULL, NULL, NULL, NULL, '2025-04-13 14:17:24', '2025-04-13 14:17:24'),
(6, 'Tide Liquid Detergent', 'supply', 250.00, 'pieces', 10.00, NULL, 'active', NULL, NULL, NULL, NULL, '2025-04-13 14:17:40', '2025-04-13 14:17:40'),
(7, 'Breeze Powder', 'supply', 100.00, 'pieces', 10.00, NULL, 'active', NULL, NULL, NULL, NULL, '2025-04-13 14:18:15', '2025-04-13 14:18:15'),
(8, 'Breeze Liquid Detergent', 'supply', 250.00, 'pieces', 10.00, NULL, 'active', NULL, NULL, NULL, NULL, '2025-04-13 14:18:37', '2025-04-13 14:18:37'),
(9, 'Washing Machine', 'equipment', 5.00, NULL, 10.00, NULL, 'active', NULL, '2025-05-13', NULL, NULL, '2025-04-13 14:19:09', '2025-04-13 14:19:09'),
(10, 'Dryers', 'equipment', 5.00, NULL, 10.00, NULL, 'active', NULL, '2025-05-13', NULL, NULL, '2025-04-13 14:20:45', '2025-04-13 14:20:45'),
(11, 'Iron', 'equipment', 10.00, NULL, 10.00, NULL, 'active', NULL, '2025-05-13', NULL, NULL, '2025-04-13 14:21:01', '2025-04-13 14:21:01'),
(12, 'Plastic Bags', 'supply', 500.00, 'pieces', 50.00, NULL, 'active', NULL, NULL, NULL, NULL, '2025-04-13 14:22:07', '2025-04-13 14:22:07'),
(13, 'Water Tank', 'equipment', 3.00, NULL, 10.00, NULL, 'active', NULL, '2025-05-13', NULL, NULL, '2025-04-13 14:22:33', '2025-04-13 14:22:33');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `transaction_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `transaction_type` enum('purchase','usage','adjustment','maintenance') NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `pickup_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `priority` varchar(20) DEFAULT 'normal',
  `delivery` tinyint(1) DEFAULT 0,
  `pickup` tinyint(1) DEFAULT 0,
  `weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `admin_id` int(11) DEFAULT NULL,
  `special_instructions` text DEFAULT NULL,
  `detergent_id` int(11) DEFAULT NULL,
  `detergent_qty` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `total_amount`, `status`, `pickup_date`, `delivery_date`, `created_at`, `priority`, `delivery`, `pickup`, `weight`, `admin_id`, `special_instructions`, `detergent_id`, `detergent_qty`) VALUES
(5, 1, 262.50, 'processing', '0000-00-00', '0000-00-00', '2025-04-19 10:03:47', 'rush', 0, 0, 7.00, NULL, '', NULL, 1),
(6, 1, 175.00, 'completed', '0000-00-00', '0000-00-00', '2025-04-19 10:43:45', 'normal', 0, 0, 7.00, NULL, '', NULL, 1);

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `after_order_complete` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
    IF NEW.status = 'completed' AND OLD.status != 'completed' THEN
        INSERT INTO sales_records (order_id, customer_id, admin_id, amount_paid)
        SELECT 
            NEW.order_id,
            NEW.customer_id,
            NEW.admin_id,
            NEW.total_amount
        FROM orders
        WHERE order_id = NEW.order_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_detail_id`, `order_id`, `service_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 5, 1, 7, 30.00, 210.00),
(2, 5, 2, 7, 25.00, 175.00),
(3, 6, 2, 7, 25.00, 175.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales_records`
--

CREATE TABLE `sales_records` (
  `sale_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_records`
--

INSERT INTO `sales_records` (`sale_id`, `order_id`, `admin_id`, `customer_id`, `amount_paid`, `payment_date`) VALUES
(2, 5, NULL, 1, 262.50, '2025-04-19 10:13:47'),
(3, 6, NULL, 1, 175.00, '2025-04-19 10:48:22');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `description`, `price`, `status`) VALUES
(1, 'Cloth Ironing', 'Professional dry cleaning service', 30.00, 'active'),
(2, 'Wash, Dry, & Fold', 'Regular washing and folding service', 25.00, 'active'),
(3, 'Curtain Cleaning', 'Specialized curtain cleaning service', 10.00, 'active'),
(4, 'Suit Cleaning', 'Professional suit and formal wear cleaning', 25.00, 'active'),
(5, 'Delivery Service', 'Door-to-door delivery service', 25.00, 'active'),
(6, 'Home Pickup', 'Home pickup service', 25.00, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `detergent_id` (`detergent_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `sales_records`
--
ALTER TABLE `sales_records`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales_records`
--
ALTER TABLE `sales_records`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `inventory_transactions_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `inventory` (`item_id`),
  ADD CONSTRAINT `inventory_transactions_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `inventory_transactions_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `admins` (`admin_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`detergent_id`) REFERENCES `inventory` (`item_id`),
  ADD CONSTRAINT `orders_ibfk_4` FOREIGN KEY (`detergent_id`) REFERENCES `inventory` (`item_id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);

--
-- Constraints for table `sales_records`
--
ALTER TABLE `sales_records`
  ADD CONSTRAINT `sales_records_admin_fk` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_records_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_records_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
