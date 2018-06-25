-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 19, 2018 at 04:44 PM
-- Server version: 5.6.33-0ubuntu0.14.04.1
-- PHP Version: 5.6.29-1+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wp`
--

-- --------------------------------------------------------

--
-- Table structure for table `wp_search_forms`
--

CREATE TABLE `wp_search_forms` (
  `id` int(11) NOT NULL,
  `keyword` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `count` int(11) NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `meta_desc` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `text_before` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `text_after` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `product_lists` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `active_ingredient` enum('0','1') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '0',
  `author` int(11) NOT NULL,
  `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wp_search_forms`
--
ALTER TABLE `wp_search_forms`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wp_search_forms`
--
ALTER TABLE `wp_search_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

