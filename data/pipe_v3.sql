-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 05, 2021 at 03:53 PM
-- Server version: 10.1.48-MariaDB
-- PHP Version: 7.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `serg_pipe`
--

-- --------------------------------------------------------

--
-- Table structure for table `pipe_v3`
--

CREATE TABLE `pipe_v3` (
  `pipe_id` int(5) NOT NULL,
  `pipe_pipeline_id` varchar(10) COLLATE utf8_bin NOT NULL,
  `geometry` text COLLATE utf8_bin NOT NULL,
  `pipe_score` int(10) NOT NULL,
  `pipe_note` text COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `pipe_v3`
--

INSERT INTO `pipe_v3` (`pipe_id`, `pipe_pipeline_id`, `geometry`, `pipe_score`, `pipe_note`) VALUES
(19, 'SK 7', '{\"type\":\"FeatureCollection\",\"features\":[{\"id\":\"c8bc29bbfa6c09587521f22ef3cba9d9\",\"type\":\"Feature\",\"properties\":{\"length\":1152.5826266628576},\"geometry\":{\"coordinates\":[[-109.86959629592846,52.79642759921316],[-109.85847916020992,52.78853781125014]],\"type\":\"LineString\"}}]}', 0, ''),
(17, 'SK 4', '{\"type\":\"FeatureCollection\",\"features\":[{\"id\":\"6687e2f8532300e7577272e71ee73d27\",\"type\":\"Feature\",\"properties\":{\"length\":433.2614805290089},\"geometry\":{\"coordinates\":[[-109.87393498136971,52.7972940546029],[-109.87798482470387,52.79426329073996]],\"type\":\"LineString\"}}]}', 2, 'sadasdasd'),
(18, 'SK 6', '{\"type\":\"FeatureCollection\",\"features\":[{\"id\":\"5fe4afb3cb1fd732c85067ba806683b3\",\"type\":\"Feature\",\"properties\":{\"length\":730.9552968969903},\"geometry\":{\"coordinates\":[[-109.87093375190388,52.78528192391269],[-109.8694683621524,52.791795544003406]],\"type\":\"LineString\"}}]}', 0, ''),
(20, 'SK 5', '{\"type\":\"FeatureCollection\",\"features\":[{\"id\":\"8c68984775abb0d8830a9b56c9c8de49\",\"type\":\"Feature\",\"properties\":{\"length\":272.4071624980461},\"geometry\":{\"coordinates\":[[-109.8720506406591,52.797483619752],[-109.87332338599028,52.796867979215364],[-109.874977954921,52.79687897287306],[-109.87432340017904,52.79663711176778]],\"type\":\"LineString\"}}]}', 3333, 'sdfsdfsdfsdfsdf');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pipe_v3`
--
ALTER TABLE `pipe_v3`
  ADD PRIMARY KEY (`pipe_id`),
  ADD UNIQUE KEY `pipe_pipeline_id` (`pipe_pipeline_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pipe_v3`
--
ALTER TABLE `pipe_v3`
  MODIFY `pipe_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
