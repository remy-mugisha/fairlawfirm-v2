-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 13, 2026 at 01:15 PM
-- Server version: 10.11.18-MariaDB-cll-lve
-- PHP Version: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `helloshi_fairdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_content`
--

CREATE TABLE `about_content` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `more_description` text DEFAULT NULL,
  `client` varchar(50) DEFAULT NULL,
  `cases_won` varchar(50) NOT NULL,
  `achievements` varchar(50) DEFAULT NULL,
  `our_team` varchar(50) DEFAULT NULL,
  `status` enum('Active','Pending') DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_content`
--

INSERT INTO `about_content` (`id`, `image`, `title`, `description`, `more_description`, `client`, `cases_won`, `achievements`, `our_team`, `status`, `created_at`) VALUES
(1, 'nyirabyo-1-1.png', 'About Fair Law Firm LTD', 'Fair Law Firm Ltd, a Rwandan company founded in 2021, provides a full range of legal services and property management solutions. They offer court representation, mediation, business transaction facilitation, contract drafting, and legal advice across various fields.\r\n\r\nIn property management, they handle rental contracts, marketing, rental profit maximization, compliance with administrative directives, tax payments, rent recovery, and sales transactions. Their goal is to make accessible legal services and property management services to their clients.', 'Fair Law Firm Ltd is a specialized Rwandan company offering a comprehensive range of legal services and property management solutions.\r\n\r\nIn the realm of legal services, the firm provides robust representation and assistance in court, ensuring clients have professional support during litigation. Our expertise extends to mediation and conciliation, helping parties to resolve disputes amicably. The firm also facilitates business transactions, ensuring all legal aspects are meticulously handled. Additionally, they draft internal rules and regulations, draft contracts, and offer legal advice across various professional fields, tailoring their services to meet the specific needs of their clients.\r\n\r\nIn terms of property management, Fair Law Firm Ltd offers a suite of services designed to optimize rental and sales transactions. They represent clients in renting houses and apartments, ensuring smooth execution of rental contracts and effective marketing strategies. The firm is committed to maximizing rental profits and ensuring compliance with administrative directives and rental tax obligations. They handle reporting and filing, rent recovery, and provide facilitation in both movable and immovable sales transactions.', '500', '300', '65', '3', 'Active', '2025-03-22 11:56:20');

-- --------------------------------------------------------

--
-- Table structure for table `add_property`
--

CREATE TABLE `add_property` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `title` varchar(100) NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `add_property`
--

INSERT INTO `add_property` (`id`, `image`, `location`, `title`, `status`, `created_at`) VALUES
(1, 'uploads/1740044589_J0400005.PNG', 'kicukiro, kigali, rwanda', 'Cette maison est gérée par Fair Law Firm LTD', 'pending', '2025-02-20 09:43:09'),
(2, '1740398605_house4_property_1.jpg', 'Kicukiro, Kigali', 'This house is managed by Fair Law Firm LTD\r\n', 'Active', '2025-02-24 12:03:25'),
(3, '1740398685_house1_property_1.jpg', ' kacyiru, Gasabo, Kigali', 'This house is managed by Fair Law Firm LTD\r\n', 'Active', '2025-02-24 12:04:45'),
(4, '1740398706_house2_property_1.jpg', ' Kibagabaga, Gasabo, Kigali', 'This house is managed by Fair Law Firm LTD\r\n', 'Active', '2025-02-24 12:05:06'),
(5, '1740398730_promoHouse_1.jpg', 'Kimironko, Gasabo, Kigali', 'This house is managed by Fair Law Firm LTD\r\n', 'Active', '2025-02-24 12:05:30'),
(6, '1740398762_house5_property_1.jpg', 'Kibagabaga, Gasabo, Kigali', 'This house is managed by Fair Law Firm LTD', 'Active', '2025-02-24 12:06:02'),
(7, '1742838983_1740400319_house6_property_1.jpg', ' Kicukiro, Kigali', 'This house is managed by Fair Law Firm LTD', 'Active', '2025-03-24 17:56:23'),
(8, '1742839132_house3_property_1.jpg', ' Kibagabaga, Gasabo, Kigali', 'This house is managed by Fair Law Firm LTD', 'Active', '2025-03-24 17:58:52');

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` int(11) NOT NULL,
  `image` varchar(250) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description_blog` text NOT NULL,
  `blog_description_details` text NOT NULL,
  `date` timestamp NULL DEFAULT current_timestamp(),
  `category_blog` varchar(255) DEFAULT 'Uncategorized',
  `status` enum('active','pending') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`id`, `image`, `title`, `description_blog`, `blog_description_details`, `date`, `category_blog`, `status`) VALUES
(1, '67ec549797c44.jpg', 'Ibyemezo By\'inama Nkuru Y\'ubucamanza	', 'Ibyemezo By\'inama Nkuru Y\'ubucamanza	', 'Ibyemezo By\'inama Nkuru Y\'ubucamanza	', '2025-03-31 23:14:05', 'Law', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `blog_attachments`
--

CREATE TABLE `blog_attachments` (
  `id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_attachments`
--

INSERT INTO `blog_attachments` (`id`, `blog_id`, `file_name`, `file_path`, `file_type`, `file_size`, `upload_date`) VALUES
(1, 0, '22.12.2023_-_ITANGAZO_HCJ.pdf', '67eb1d11a02c6.pdf', 'pdf', 336116, '2025-03-31 22:54:09'),
(2, 37, 'assignment_cryptography.pdf', '67e52f2a141be.pdf', 'pdf', 164513, '2025-03-27 10:57:46'),
(3, 37, 'Development of IoT platform for online milk qualit_2 (1).docx', '67e5309be6a7c.docx', 'docx', 432882, '2025-03-27 11:03:55'),
(5, 2, 'evidence.pdf', '67ebd44a0dd5c.pdf', 'pdf', 367907, '2025-04-01 11:55:54'),
(6, 3, 'Correction_CS_Y4_Artificial_Intelligence Exam_AY 17_18 (4).pdf', '67ec55176d5e5.pdf', 'pdf', 841123, '2025-04-01 21:05:27'),
(7, 1, '67f23b62e823e.pdf', '67f2515866a29.pdf', 'pdf', 336116, '2025-04-06 10:03:04'),
(8, 4, 'evidence.pdf', '67f28d0c1ae64.pdf', 'pdf', 367907, '2025-04-06 14:17:48');

-- --------------------------------------------------------

--
-- Table structure for table `home_backgrounds`
--

CREATE TABLE `home_backgrounds` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` enum('active','pending') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `home_backgrounds`
--

INSERT INTO `home_backgrounds` (`id`, `image_path`, `status`, `created_at`) VALUES
(1, 'backgroundImg', 'active', '2025-03-20 11:49:34');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`email`, `password`, `usertype`) VALUES
('antonycyuzuzo@gmail.com', '$2y$10$XjTFOwBRwNA/iWLLrm63uOk5HepqQJMSaWhhMxWVLrascyrfa8qr6', 'user'),
('jado@gmail.com', '$2y$10$xWUut5kaR/yK9xkK1D9vhOh3imNAUC7jJ29yXOGhRE.DQAPE0A4fW', 'user'),
('kabera@gmail.com', '$2y$10$swEBwarwUEwGxXEK4EfsQujzDhXf6OM7aFhw.PWHHilqAekg9TOIi', 'user'),
('ngirumpetse@gmail.com', '$2y$10$nMZA69Ox5XOMkDKJfgGgG.58GA2ZIfLHRhM4jQkPzbQPcuDftAo92', 'user'),
('ngirumpetse@yahoo.com', '$2y$10$K89GekEmLsT9FiK8xJZWZOfHL.cwz8xMPdWJ5Uo17U3laxAYTMPnC', 'admin'),
('remy@gmail.com', '$2y$10$3pM53C57w..qd.rFY4/xp.hxhlRK.c11dXKk9Ldjq8pO0E2C9Npdi', 'user'),
('remymugisha64@gmail.com', '$2y$10$N4C/rBod6HcuNnrRzXHacOmGQtA65sVgUVoYEJ/6FAuqkfQpcREqm', 'admin'),
('ttt@gmail.com', '$2y$10$.D6UjGqtAwdHrP03G5UN3OUiJWJo/bVCBk9KR5ozdeSuXO2MX1FBK', 'user'),
('user@gmail.com', '$2y$10$cu2h5j1FOchTiPq1SXXaiuF8D5Aca4niyr0g7xMKLq04M9HAWBfTi', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `password_reset`
--

INSERT INTO `password_reset` (`email`, `token`, `expiry`, `created_at`) VALUES
('admin@gmail.com', 'f1bb425f9aaafce7b318179b4f3fb8257c37fac4d0607550d7d7655ccda7f90e', '2025-03-07 23:20:00', '2025-03-07 21:20:00'),
('didier@gmail.com', '6b6dd80fbb4159e26d712eca2367425b7a6159435b1894911b7d32763ab70a6b', '2025-04-01 22:45:34', '2025-04-01 21:44:57'),
('kabera@gmail.com', 'd110d9d822519aec2a4a5cfa4d557ba99324650c6c31c3ff0e04eddc1f9a15f3', '2025-04-06 10:47:35', '2025-03-24 09:56:59'),
('mugisharemy2019@gmail.com', 'dd17553eb9d4856ad60ff1838d8744a0e4f9fb843fe786a8d416ae8f96335d58', '2025-04-06 11:38:33', '2025-04-06 10:38:33'),
('remymugisha64@gmail.com', 'aa5a859d24356485857228e7cd348e942b5031cc7ee72cf7a29dfb61e5503ecc', '2025-04-06 10:31:57', '2025-04-04 08:08:55'),
('user@gmail.com', '77df3760058ca3c9c9c0c5ff0d43d29e747f83860b9f544b229598fc5277e06d', '2025-03-07 15:14:53', '2025-03-07 12:37:10');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `property_status` enum('For Rent','For Sale','Not Available') NOT NULL,
  `property_type` varchar(100) NOT NULL,
  `price` varchar(50) NOT NULL DEFAULT '',
  `property_size` varchar(50) NOT NULL,
  `bedroom` int(11) DEFAULT NULL,
  `bathroom` int(11) DEFAULT NULL,
  `street` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Pending') DEFAULT 'Active',
  `sector` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `floor` varchar(50) DEFAULT NULL,
  `months` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `title`, `description`, `property_status`, `property_type`, `price`, `property_size`, `bedroom`, `bathroom`, `street`, `status`, `sector`, `district`, `country`, `created_at`, `floor`, `months`) VALUES
(4, 'Rental Offices ', 'Located in the bustling area of Kimironko, near the bus station, this commercial building offers a prime location for businesses looking to establish themselves in a vibrant and accessible neighborhood. The building’s strategic position ensures easy access to public transportation, making it convenient for employees and clients alike. Kimironko is known for its dynamic business environment, and being close to the bus station means your office will be easily reachable from various parts of the city.\r\n\r\nThe building itself boasts a modern design with four levels, including the ground floor, first floor, second floor, and third floor. Currently, the rooms available for rent are situated on all four levels, providing a range of options for tenants depending on their space requirements and preferences. Each floor is designed to accommodate different types of businesses, from small startups to larger, established companies. The spacious rooms are well-lit, ventilated, and designed to provide a professional and comfortable working environment.\r\n\r\nTenants will also benefit from shared amenities such as modern restrooms, secure parking, and a welcoming lobby area. The building is maintained to high standards, with regular cleaning and maintenance services to ensure a pleasant atmosphere for all occupants. Whether you\'re looking for a single office space or an entire floor, this commercial property in Kimironko offers flexibility and convenience for your business needs.', 'For Rent', 'Commercial Building', '1000000 - 500000', '200sql -100sql', 0, 0, '120 kg', 'Active', 'Kimironko', 'Gasabo', 'Rwanda', '2025-04-01 21:26:19', 'Ground Floor, 1st Floor, 2nd Floor, 3rd Floor', 1);

-- --------------------------------------------------------

--
-- Table structure for table `property`
--

CREATE TABLE `property` (
  `id` int(11) NOT NULL,
  `img` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property`
--

INSERT INTO `property` (`id`, `img`, `location`) VALUES
(1, 'house1_property_1.jpg', 'kacyiru, Gasabo, Kigali'),
(2, 'house3_property_1.jpg', 'Kibagabaga, Gasabo, Kigali'),
(3, 'house5_property_1.jpg', 'Kibagabaga, Gasabo, Kigali'),
(4, 'house6_property_1.jpg', 'Kicukiro, Kigali'),
(5, 'house4_property_1.jpg', 'Kicukiro, Kigali'),
(6, 'house2_property_1.jpg', 'Kibagabaga, Gasabo, Kigali'),
(7, 'promoHouse_1.jpg', 'Kimironko, Gasabo, Kigali');

-- --------------------------------------------------------

--
-- Table structure for table `property_images`
--

CREATE TABLE `property_images` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_images`
--

INSERT INTO `property_images` (`id`, `property_id`, `image_name`, `is_featured`, `image_path`) VALUES
(1, 0, '67ebc8e0c45fc.jpg', 1, 'propertyMgt/rentalImg/67ebc8e0c45fc.jpg'),
(7, 4, '67ec5a13bdcae.jpg', 1, 'propertyMgt/rentalImg/67ec5a13bdcae.jpg'),
(8, 4, '67ec5a1c878a9.jpg', 1, 'propertyMgt/rentalImg/67ec5a1c878a9.jpg'),
(9, 4, '67ec5a288e90a.jpg', 1, 'propertyMgt/rentalImg/67ec5a288e90a.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`, `created_at`) VALUES
(1, 'Admin', 'Administrator with full access', '2025-03-06 01:33:24'),
(2, 'Employer', 'Regular user with limited access', '2025-03-06 01:33:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('Active','Inactive','Pending') DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `gender`, `profile_image`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Remy', 'MUGISHA', 'remymugisha64@gmail.com', '0788848185', 'Male', 'uploads/67c8e054f080e.jpg', 1, 'Active', '2025-03-06 01:37:56', '2025-03-06 01:43:46'),
(9, 'NGIRUMPETSE', 'JMV', 'ngirumpetse@yahoo.com', '0788411095', 'Male', 'propertyMgt/userImg/67f7c247db59e.jpg', 1, 'Active', '2025-04-10 15:06:15', '2025-04-10 15:06:15'),
(10, 'ANTOINETTE', 'CYUZUZO', 'antonycyuzuzo@gmail.com', '+250784183352', 'Female', '', 2, 'Active', '2025-04-17 16:54:36', '2025-04-17 16:54:36');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `video_link` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('active','pending') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_content`
--
ALTER TABLE `about_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `add_property`
--
ALTER TABLE `add_property`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_attachments`
--
ALTER TABLE `blog_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blog_id` (`blog_id`);

--
-- Indexes for table `home_backgrounds`
--
ALTER TABLE `home_backgrounds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `property`
--
ALTER TABLE `property`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `property_images`
--
ALTER TABLE `property_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_content`
--
ALTER TABLE `about_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `add_property`
--
ALTER TABLE `add_property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `blog_attachments`
--
ALTER TABLE `blog_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `home_backgrounds`
--
ALTER TABLE `home_backgrounds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `property`
--
ALTER TABLE `property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `property_images`
--
ALTER TABLE `property_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
