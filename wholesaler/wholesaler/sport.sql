-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2018 at 08:20 AM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 5.6.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sport`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `percent` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `company`, `email`, `password`, `city`, `percent`) VALUES
(1, '', '', 'admin@admin.com', 'e10adc3949ba59abbe56e057f20f883e', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` bigint(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(128) NOT NULL,
  `token` varchar(512) DEFAULT NULL,
  `otp` int(11) DEFAULT NULL,
  `social` int(11) NOT NULL,
  `ios_token` varchar(256) NOT NULL,
  `android_token` varchar(256) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 = not block, 1 = block',
  `verified` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `mobile`, `password`, `image`, `token`, `otp`, `social`, `ios_token`, `android_token`, `created_at`, `status`, `verified`) VALUES
(1, 'CTInfotech Indore', 'ctinfotechindore@gmail.com', 0, '', '', '0', NULL, 2, '', '', '0000-00-00 00:00:00', 0, 0),
(2, 'Darbar Shanky', 'eramit1880@gmail.com', 0, 'e10adc3949ba59abbe56e057f20f883e', '', '0', NULL, 0, '', '', '0000-00-00 00:00:00', 1, 0),
(3, 'devendra', 'd@gmail.com', 0, 'e10adc3949ba59abbe56e057f20f883e', '', '0', NULL, 0, '', '', '2018-03-09 13:22:14', 0, 0),
(4, '', 'devendra@ctinfotech.com', 0, 'e10adc3949ba59abbe56e057f20f883e', '', '', NULL, 0, '', '', '2018-04-25 16:46:28', 1, 0),
(5, 'Devendra Rokade', 'drokade981@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', '', '5d85750420fb6cb83481ba7958ff7af91', NULL, 4, '12345678', '', '2018-04-26 16:35:41', 0, 0),
(6, 'Vijay Patidar', 'viajy@gmail.com', 0, 'e10adc3949ba59abbe56e057f20f883e', '', '', NULL, 0, '', '', '2018-05-09 15:45:16', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
