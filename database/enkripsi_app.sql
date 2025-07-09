-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2025 at 08:08 AM
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
-- Database: `enkripsi_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `google_id`, `email`, `password`, `role`, `created_at`, `updated_at`, `profile_picture`) VALUES
(6, 'Ardiansyah', '109253003969312307455', 'wisnuardiansyah94370@gmail.com', '$2y$10$Td14n86ANl78ZdE6UMmhHe1Jt94EO9g0mVBIuxX/yibXIdzMD8PPG', 'admin', '2025-04-21 05:02:19', '2025-07-08 09:06:19', '6_1746193693.jpeg'),
(11, 'wisnua', '113254875584558087100', 'wisnuardiansyah94360@gmail.com', '$2y$10$UtSPVMj8V87Lx9Z9cvYpT.EmjQau8/M9M3Hi.TczbibiG3Y8kQray', 'user', '2025-04-21 06:46:16', '2025-05-08 08:31:10', '11_1746117691.jpeg'),
(17, 'kensha', NULL, NULL, '$2y$10$JfRBvr6MnCDXk2yfFZOkbuvGyMhas35F70wCA8rJLa.4YSZf4uxoO', 'user', '2025-04-23 17:39:16', '2025-04-27 14:02:32', NULL),
(19, 'agila', NULL, NULL, '$2y$10$a3QFHDHqR/pNKqwleF3e9OE7TjcQoT6tG0zD/kzDA1pS97EGgEmTe', 'admin', '2025-05-02 15:53:41', '2025-05-02 15:54:47', '19_1746201287.jpg'),
(29, 'ardian', NULL, 'Tidak ada', '$2y$10$zpGwe69wTzJV3H4Jyaj9ru1j0XIxPQoQNfphhTEwyW6zUZpTPCK9C', 'user', '2025-07-08 18:02:36', '2025-07-08 18:02:36', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_google_id` (`google_id`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
