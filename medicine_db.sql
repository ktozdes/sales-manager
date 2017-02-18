-- phpMyAdmin SQL Dump
-- version 4.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Feb 19, 2017 at 02:56 AM
-- Server version: 5.5.38
-- PHP Version: 5.6.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medicine_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `balance_change`
--

CREATE TABLE `balance_change` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `balance_was` int(11) NOT NULL,
  `balance_became` int(11) NOT NULL,
  `debt_was` int(11) NOT NULL,
  `debt_became` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `client_id` int(11) NOT NULL,
  `client_firstname` varchar(20) NOT NULL,
  `client_lastname` varchar(20) NOT NULL,
  `client_phone` varchar(40) NOT NULL,
  `client_email` varchar(50) NOT NULL,
  `client_address` varchar(100) NOT NULL,
  `client_company` varchar(50) NOT NULL,
  `client_image` varchar(200) NOT NULL,
  `client_other` text NOT NULL,
  `client_birthday` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `client_balance`
--

CREATE TABLE `client_balance` (
  `client_balance_id` int(11) NOT NULL,
  `client_balance_client_id` int(11) NOT NULL,
  `client_balance_currency_id` int(11) NOT NULL,
  `client_balance_debt` decimal(10,2) NOT NULL,
  `client_balance_balance` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `country_id` int(11) NOT NULL,
  `country_barcode` text NOT NULL,
  `country_name` varchar(20) NOT NULL,
  `country_short_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

CREATE TABLE `currency` (
  `currency_id` int(11) NOT NULL,
  `currency_code` varchar(10) NOT NULL,
  `currency_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `medicine`
--

CREATE TABLE `medicine` (
  `medicine_id` int(11) NOT NULL,
  `medicine_code` varchar(30) NOT NULL,
  `medicine_name` varchar(200) NOT NULL,
  `medicine_country` varchar(50) NOT NULL,
  `medicine_production_date` date NOT NULL,
  `medicine_price` decimal(10,2) NOT NULL,
  `medicine_currency_id` int(11) NOT NULL DEFAULT '1',
  `medicine_quantity` int(11) NOT NULL,
  `medicine_image` varchar(200) NOT NULL,
  `medicine_other` text NOT NULL,
  `medicine_manufacture_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_income`
--

CREATE TABLE `medicine_income` (
  `medicine_income_id` int(11) NOT NULL,
  `medicine_income_medicine_id` int(11) NOT NULL,
  `medicine_income_price` decimal(10,2) NOT NULL,
  `medicine_income_currency_id` int(11) NOT NULL DEFAULT '1',
  `medicine_income_quantity` int(11) NOT NULL,
  `medicine_income_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_manufacture`
--

CREATE TABLE `medicine_manufacture` (
  `medicine_manufacture_id` int(11) NOT NULL,
  `medicine_manufacture_name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `payment_client_id` int(11) NOT NULL,
  `payment_amount` decimal(10,0) NOT NULL,
  `payment_currency_id` int(11) NOT NULL DEFAULT '1',
  `payment_exchange_currency_id` int(11) NOT NULL DEFAULT '2',
  `payment_exchange_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sale`
--

CREATE TABLE `sale` (
  `sale_id` int(11) NOT NULL,
  `sale_client_id` int(11) NOT NULL,
  `sale_medicine_id` int(11) NOT NULL,
  `sale_quantity` int(11) NOT NULL,
  `sale_price` decimal(10,0) NOT NULL,
  `sale_currency_id` int(11) NOT NULL DEFAULT '1',
  `sale_status` varchar(30) NOT NULL,
  `sale_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sale_transaction`
--

CREATE TABLE `sale_transaction` (
  `sale_transaction_id` int(11) NOT NULL,
  `sale_transaction_sale_id` int(11) NOT NULL,
  `sale_transaction_price` decimal(10,2) NOT NULL,
  `sale_transaction_currency_id` int(11) NOT NULL DEFAULT '1',
  `sale_transaction_quantity` int(11) NOT NULL,
  `sale_transaction_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `balance_change`
--
ALTER TABLE `balance_change`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `client_balance`
--
ALTER TABLE `client_balance`
  ADD PRIMARY KEY (`client_balance_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`currency_id`);

--
-- Indexes for table `medicine`
--
ALTER TABLE `medicine`
  ADD PRIMARY KEY (`medicine_id`);

--
-- Indexes for table `medicine_income`
--
ALTER TABLE `medicine_income`
  ADD PRIMARY KEY (`medicine_income_id`);

--
-- Indexes for table `medicine_manufacture`
--
ALTER TABLE `medicine_manufacture`
  ADD PRIMARY KEY (`medicine_manufacture_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `sale`
--
ALTER TABLE `sale`
  ADD PRIMARY KEY (`sale_id`);

--
-- Indexes for table `sale_transaction`
--
ALTER TABLE `sale_transaction`
  ADD PRIMARY KEY (`sale_transaction_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `balance_change`
--
ALTER TABLE `balance_change`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `client_balance`
--
ALTER TABLE `client_balance`
  MODIFY `client_balance_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `currency_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `medicine`
--
ALTER TABLE `medicine`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `medicine_income`
--
ALTER TABLE `medicine_income`
  MODIFY `medicine_income_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `medicine_manufacture`
--
ALTER TABLE `medicine_manufacture`
  MODIFY `medicine_manufacture_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sale`
--
ALTER TABLE `sale`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sale_transaction`
--
ALTER TABLE `sale_transaction`
  MODIFY `sale_transaction_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
