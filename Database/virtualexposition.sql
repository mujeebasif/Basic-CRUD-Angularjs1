-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 28, 2017 at 04:28 AM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `virtualexposition`
--
CREATE DATABASE IF NOT EXISTS `virtualexposition` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `virtualexposition`;

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE IF NOT EXISTS `company` (
  `company_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `admin_email` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`company_id`, `company_name`, `email`, `phone`, `admin_email`, `logo`, `deleted`) VALUES
(1, 'Company A', 'company1@mailinator.com', '+1234567', 'cadmin@mailinator.com', 'a.jpg', 0),
(23, 'Company c', 'cc@email.com', '+9876543', 'ccadmin@mail.com', 'c.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `date_from` datetime DEFAULT NULL,
  `date_to` datetime DEFAULT NULL,
  `hall_id` int(11) DEFAULT NULL,
  `gps_coordinates` varchar(255) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  `report_sent` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `name`, `address`, `date_from`, `date_to`, `hall_id`, `gps_coordinates`, `deleted`, `report_sent`) VALUES
(1, 'Event1', 'Capital hill', '2017-03-04 09:00:00', '2017-03-05 18:00:00', 1, '-35.306054,149.123592', 0, 0),
(2, 'Event2', 'Surry hills', '2017-03-11 09:00:00', '2017-03-12 18:00:00', 2, '-33.886068,151.211706', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `file_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`file_id`, `file_name`, `company_id`) VALUES
(1, 'readme.txt', 1),
(2, 'design.docx', 1),
(7, 'booth01.jpg', 23),
(8, 'booth-lg.jpg', 24),
(9, 'standard-linear_id4.jpg', 24);

-- --------------------------------------------------------

--
-- Table structure for table `hall`
--

CREATE TABLE IF NOT EXISTS `hall` (
  `hall_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`hall_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `hall`
--

INSERT INTO `hall` (`hall_id`, `name`, `deleted`) VALUES
(1, 'Hall A', 0),
(2, 'Hall B', 0);

-- --------------------------------------------------------

--
-- Table structure for table `stands`
--

CREATE TABLE IF NOT EXISTS `stands` (
  `stand_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `dimensions` varchar(255) DEFAULT NULL,
  `price` decimal(10,0) DEFAULT NULL,
  `hall_id` int(11) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  `stand_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`stand_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `stands`
--

INSERT INTO `stands` (`stand_id`, `name`, `dimensions`, `price`, `hall_id`, `deleted`, `stand_image`) VALUES
(1, 'Stand A', '10 x 15', '110', 1, 0, 'booth_id1.jpg'),
(2, 'Stand B', '10 x 10', '100', 1, 0, 'booth_id2.jpg'),
(3, 'Stand A', '12 x 12', '120', 2, 0, 'booth_id3.jpg'),
(4, 'Stand B', '12 x 15', '130', 2, 0, 'linear_id4.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `stands_reservations`
--

CREATE TABLE IF NOT EXISTS `stands_reservations` (
  `stand_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `date_from` datetime DEFAULT NULL,
  `date_to` datetime DEFAULT NULL,
  `reservation_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`reservation_id`),
  UNIQUE KEY `unique` (`event_id`,`stand_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `stands_reservations`
--

INSERT INTO `stands_reservations` (`stand_id`, `company_id`, `event_id`, `date_from`, `date_to`, `reservation_id`) VALUES
(1, 1, 1, '2017-02-27 09:00:00', '2017-02-27 18:00:00', 1),
(3, 23, 2, '2017-03-11 09:00:00', '2017-03-11 09:00:00', 21);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `deleted`) VALUES
(1, 'sample user1', 'user1@mailinator.com', 'sampleuser1', 0),
(2, 'sample user2', 'user2@mailinator.com', 'sampleuser2', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_visits`
--

CREATE TABLE IF NOT EXISTS `user_visits` (
  `user_id` int(11) NOT NULL,
  `stand_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `company_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_visits`
--

INSERT INTO `user_visits` (`user_id`, `stand_id`, `event_id`, `datetime`, `company_id`) VALUES
(1, 1, 1, '2017-02-27 14:55:16', 1),
(2, 1, 1, '2017-02-27 15:15:58', 1),
(1, 3, 2, '2017-02-27 15:46:15', 23),
(2, 3, 2, '2017-02-27 20:46:40', 23);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
