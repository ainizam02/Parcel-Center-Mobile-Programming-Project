-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2024 at 04:27 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `parcel_centre`
--

-- --------------------------------------------------------

--
-- Table structure for table `parcels`
--

CREATE TABLE `parcels` (
  `parcel_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `arrival_date` date NOT NULL,
  `tracking_number` varchar(50) NOT NULL,
  `status` enum('Picked Up','Not Picked Up') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parcels`
--

INSERT INTO `parcels` (`parcel_id`, `user_id`, `arrival_date`, `tracking_number`, `status`) VALUES
(7, 5, '2024-07-02', 'HANA12345', 'Not Picked Up'),
(8, 7, '2024-07-04', 'ABU12456', 'Picked Up'),
(9, 7, '2024-07-08', 'LZD4556743', 'Not Picked Up'),
(10, 7, '2024-07-10', 'TEMU78967546', 'Not Picked Up');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `role`, `username`, `email`, `password_hash`, `registration_date`) VALUES
(5, 'MIZA RAIHANAH BINTI MISDIFIAN', 'Student', 'hana', 'hana@gmail.com', '$2y$10$MYROlx5cHSFuaNQXe2F5W.AgIl//UBo0u9VwvuJkKxbPzfLoT1RKi', '2024-07-08 14:04:51'),
(6, 'sitia aminah', 'Staff', 'aminah', 'aminah1@gmail.com', '$2y$10$8aQLufpfaOPlsjluO4RvjOcWnvbkPj6aGszqEF6Uwo8TOdA7lk/T.', '2024-07-08 15:15:13'),
(7, 'abu', 'Student', 'abu', 'abu1@gmail.com', '$2y$10$674Shkd86E5ndX0DzMf7pul6iFaRP3ZwbJs1ATik/NSwpSgHwoP4W', '2024-07-08 17:35:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `parcels`
--
ALTER TABLE `parcels`
  ADD PRIMARY KEY (`parcel_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `parcels`
--
ALTER TABLE `parcels`
  MODIFY `parcel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `parcels`
--
ALTER TABLE `parcels`
  ADD CONSTRAINT `parcels_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
