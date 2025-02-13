-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2025 at 09:41 PM
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
-- Database: `purchase_order_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `total_price` bigint(20) NOT NULL,
  `Stock` int(11) NOT NULL,
  `notes` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `item_id`, `item_name`, `total_price`, `Stock`, `notes`) VALUES
(1, 2, 'Item 102', 111243, 5, 'Our Food'),
(3, 1, 'Item 1', 15417, 100, '');

-- --------------------------------------------------------

--
-- Table structure for table `item_list`
--

CREATE TABLE `item_list` (
  `id` int(30) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT ' 1 = Active, 0 = Inactive',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_list`
--

INSERT INTO `item_list` (`id`, `name`, `description`, `status`, `date_created`) VALUES
(1, 'Item 1', 'Sample Item Only. Test 101', 1, '2021-09-08 10:17:19'),
(2, 'Item 102', 'Sample Only', 1, '2021-09-08 10:21:42'),
(3, 'Item 3', 'Sample product 103. 3x25 per boxes', 1, '2021-09-08 10:22:10');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `po_id` int(30) NOT NULL,
  `item_id` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `unit_price` float NOT NULL,
  `quantity` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`po_id`, `item_id`, `unit`, `unit_price`, `quantity`) VALUES
(1, 1, 'boxes', 15000, 10),
(1, 2, 'pcs', 17999.9, 6),
(2, 1, 'pcs', 3788.99, 10),
(7, 2, 'boxes', 22000, 1),
(8, 3, '2', 300, 2);

-- --------------------------------------------------------

--
-- Table structure for table `po_list`
--

CREATE TABLE `po_list` (
  `id` int(30) NOT NULL,
  `po_no` varchar(100) NOT NULL,
  `supplier_id` int(30) NOT NULL,
  `discount_percentage` float NOT NULL,
  `discount_amount` float NOT NULL,
  `tax_percentage` float NOT NULL,
  `tax_amount` float NOT NULL,
  `notes` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=pending, 1= Approved, 2 = Denied',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `po_list`
--

INSERT INTO `po_list` (`id`, `po_no`, `supplier_id`, `discount_percentage`, `discount_amount`, `tax_percentage`, `tax_amount`, `notes`, `status`, `date_created`, `date_updated`) VALUES
(1, 'REF-27330104609', 1, 2, 5159.99, 12, 30959.9, 'Our Food', 1, '2021-09-08 15:20:57', '2025-02-10 22:11:38'),
(2, 'PO-92093417806', 2, 1, 378.899, 12, 4546.79, 'Sample', 1, '2021-09-08 15:49:55', '2025-02-06 00:15:10'),
(7, 'PO-19150827073', 2, 0, 0, 0, 0, 'GUNDAM', 0, '2025-02-07 19:40:08', NULL),
(8, 'REF-44665893324', 2, 0, 0, 0, 0, '', 0, '2025-02-08 22:37:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_list`
--

CREATE TABLE `purchase_list` (
  `id` int(11) NOT NULL,
  `reference_id` varchar(255) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `discount_percentage` float NOT NULL,
  `discount_amount` float NOT NULL,
  `tax_percentage` float NOT NULL,
  `tax_amount` float NOT NULL,
  `detachment` varchar(255) NOT NULL,
  `requestor_name` varchar(255) NOT NULL,
  `received_by` varchar(255) NOT NULL,
  `amount_requested` float NOT NULL,
  `total_amount` float NOT NULL,
  `date_request` date NOT NULL DEFAULT current_timestamp(),
  `date_purchase` date NOT NULL,
  `date_recieved` date NOT NULL,
  `notes` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0 = pending, 1 approved, 2 = denied'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_list`
--

INSERT INTO `purchase_list` (`id`, `reference_id`, `supplier_id`, `item_id`, `quantity`, `discount_percentage`, `discount_amount`, `tax_percentage`, `tax_amount`, `detachment`, `requestor_name`, `received_by`, `amount_requested`, `total_amount`, `date_request`, `date_purchase`, `date_recieved`, `notes`, `status`) VALUES
(1, 'REF-247192832', 2, 1, 5, 2, 20, 1, 10, 'TESTING', 'SHEEN', 'JUSTIN', 100, 380, '2025-02-12', '2025-02-12', '0000-00-00', 'TEST', 0),
(2, 'REF-377139831', 1, 2, 5, 2, 40, 1, 20, 'Detachment here', 'Sheen Sheen', 'Justin Sheen', 40, 160, '2025-02-13', '0000-00-00', '0000-00-00', 'Good item', 1),
(5, 'REF-963392019', 2, 2, 15, 20, 600, 2, 48, 'Testing', 'Anna', '', 200, 2448, '2025-02-14', '0000-00-00', '0000-00-00', '', 0),
(6, 'REF-736373482', 1, 1, 55, 30, 4950, 2, 231, '', 'Sheen', '', 300, 11781, '2025-02-14', '0000-00-00', '0000-00-00', '', 0),
(7, 'REF-323129682', 2, 1, 45, 20, 900, 1, 36, 'Test', 'Guiriba', '', 100, 3636, '2025-02-14', '0000-00-00', '0000-00-00', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_list`
--

CREATE TABLE `supplier_list` (
  `id` int(30) NOT NULL,
  `name` varchar(250) NOT NULL,
  `address` text NOT NULL,
  `contact_person` text NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT ' 0 = Inactive, 1 = Active',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_list`
--

INSERT INTO `supplier_list` (`id`, `name`, `address`, `contact_person`, `contact`, `email`, `status`, `date_created`) VALUES
(1, 'Supplier 101', 'Sample Address Only', 'George Wilson', '09123459879', 'supplier101@gmail.com', 1, '2021-09-08 09:46:45'),
(2, 'Supplier 102', '23rd St, Sample City, Test Province, ####', 'Samantha Lou', '09332145889', 'sLou@supplier102.com', 1, '2021-09-08 10:25:12');

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Purchase Order Management System'),
(6, 'short_name', 'POMS'),
(11, 'logo', 'uploads/1631064180_sample_compaby_logo.jpg'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/1631064360_sample_bg.jpg'),
(15, 'company_name', 'Araneta Sales'),
(16, 'company_email', 'AranetaSales@araneta.com'),
(17, 'company_address', 'Pily St. San Marcos, Dumaguete City');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`) VALUES
(1, 'Adminstrator', 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', 'uploads/1624240500_avatar.png', NULL, 1, '2021-01-20 14:02:37', '2021-06-21 09:55:07'),
(3, 'Mike ', 'Williams', 'mwilliams', 'a88df23ac492e6e2782df6586a0c645f', 'uploads/1630999200_avatar5.png', NULL, 2, '2021-09-07 15:20:40', NULL),
(5, 'Justin', 'Guiriba', 'sheen', 'e10adc3949ba59abbe56e057f20f883e', 'uploads/1738927560_panipuri.jpg', NULL, 1, '2025-02-07 19:26:36', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `item_list`
--
ALTER TABLE `item_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD KEY `po_id` (`po_id`),
  ADD KEY `item_no` (`item_id`);

--
-- Indexes for table `po_list`
--
ALTER TABLE `po_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `purchase_list`
--
ALTER TABLE `purchase_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_id` (`reference_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `supplier_list`
--
ALTER TABLE `supplier_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `item_list`
--
ALTER TABLE `item_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `po_list`
--
ALTER TABLE `po_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `purchase_list`
--
ALTER TABLE `purchase_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `supplier_list`
--
ALTER TABLE `supplier_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`po_id`) REFERENCES `po_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `po_list`
--
ALTER TABLE `po_list`
  ADD CONSTRAINT `po_list_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `purchase_list`
--
ALTER TABLE `purchase_list`
  ADD CONSTRAINT `purchase_list_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `purchase_list_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `item_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
