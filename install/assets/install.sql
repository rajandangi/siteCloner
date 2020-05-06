-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Apr 16, 2015 at 02:20 PM
-- Server version: 5.5.34
-- PHP Version: 5.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sitecloner`
--

-- --------------------------------------------------------

--
-- Table structure for table `ancors`
--

CREATE TABLE `ancors` (
  `ancor_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(111) NOT NULL,
  `crawl_id` int(11) NOT NULL,
  `ancor_href` varchar(255) NOT NULL,
  `ancor_stored_at` text NOT NULL,
  `ancor_crawled` int(11) NOT NULL DEFAULT '0',
  `ancor_404` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ancor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `last_activity_idx` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`id`, `ip_address`, `timestamp`, `data`) VALUES
('117432419a224b6591f61ee4350a912188ac7dcc', '127.0.0.1', 1429147461, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393134373139363b6964656e746974797c733a31353a2261646d696e4061646d696e2e636f6d223b757365726e616d657c733a31333a2261646d696e6973747261746f72223b656d61696c7c733a31353a2261646d696e4061646d696e2e636f6d223b757365725f69647c733a313a2231223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231343239303736383236223b),
('1425437f6343038df56d6c9455ec3e1953a1b453', '127.0.0.1', 1429147849, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393134373834393b6964656e746974797c733a31353a2261646d696e4061646d696e2e636f6d223b757365726e616d657c733a31333a2261646d696e6973747261746f72223b656d61696c7c733a31353a2261646d696e4061646d696e2e636f6d223b757365725f69647c733a313a2231223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231343239303736383236223b),
('15e282f1fc1bb344d44622f9e3da89d3b8918f23', '127.0.0.1', 1429149671, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393134393537383b6964656e746974797c733a31353a2261646d696e4061646d696e2e636f6d223b757365726e616d657c733a31333a2261646d696e6973747261746f72223b656d61696c7c733a31353a2261646d696e4061646d696e2e636f6d223b757365725f69647c733a313a2231223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231343239303736383236223b),
('39560ba5d5cdf43a96f34c0439becdc6e73c8d51', '127.0.0.1', 1429155110, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393135343839303b6964656e746974797c733a31353a2261646d696e4061646d696e2e636f6d223b757365726e616d657c733a31333a2261646d696e6973747261746f72223b656d61696c7c733a31353a2261646d696e4061646d696e2e636f6d223b757365725f69647c733a313a2231223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231343239303736383236223b),
('3c9aa99612f71f8eb5da876163e5540e2081e81e', '127.0.0.1', 1429158005, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393135383030353b6964656e746974797c733a31353a2261646d696e4061646d696e2e636f6d223b757365726e616d657c733a31333a2261646d696e6973747261746f72223b656d61696c7c733a31353a2261646d696e4061646d696e2e636f6d223b757365725f69647c733a313a2231223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231343239303736383236223b),
('608b6feac3daa4b05b03e7b8a66668d6c4baebbe', '127.0.0.1', 1429149355, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393134393236323b6964656e746974797c733a31353a2261646d696e4061646d696e2e636f6d223b757365726e616d657c733a31333a2261646d696e6973747261746f72223b656d61696c7c733a31353a2261646d696e4061646d696e2e636f6d223b757365725f69647c733a313a2231223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231343239303736383236223b),
('61956f84b784d3801e10720e6c2bc1e6312867f2', '127.0.0.1', 1429154297, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393135343239373b6964656e746974797c733a31353a2261646d696e4061646d696e2e636f6d223b757365726e616d657c733a31333a2261646d696e6973747261746f72223b656d61696c7c733a31353a2261646d696e4061646d696e2e636f6d223b757365725f69647c733a313a2231223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231343239303736383236223b),
('735a8345ae75833037d39af14b51ab9e5ba6b006', '127.0.0.1', 1429159677, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393135393637373b),
('a52cc2090a5b1151f4163fc2aa19f5b57c69aca8', '127.0.0.1', 1429168800, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393136383739333b6964656e746974797c733a31353a2261646d696e4061646d696e2e636f6d223b757365726e616d657c733a31333a2261646d696e6973747261746f72223b656d61696c7c733a31353a2261646d696e4061646d696e2e636f6d223b757365725f69647c733a313a2231223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231343239313638373633223b),
('c0ea3e59ee15b0960a918a8718f83fbef8658e21', '127.0.0.1', 1429150019, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393134393938373b6964656e746974797c733a31353a2261646d696e4061646d696e2e636f6d223b757365726e616d657c733a31333a2261646d696e6973747261746f72223b656d61696c7c733a31353a2261646d696e4061646d696e2e636f6d223b757365725f69647c733a313a2231223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231343239303736383236223b),
('c691574d31679ca06643c8dad345fb72211055df', '127.0.0.1', 1429161529, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393136313532393b),
('fd04c6b07afcaa58b483c2fd92ded857246e4988', '127.0.0.1', 1429153712, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393135303331383b6964656e746974797c733a31353a2261646d696e4061646d696e2e636f6d223b757365726e616d657c733a31333a2261646d696e6973747261746f72223b656d61696c7c733a31353a2261646d696e4061646d696e2e636f6d223b757365725f69647c733a313a2231223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231343239303736383236223b),
('fdb97882a0fbf90dffb2d5fa68087650d1d8b78f', '127.0.0.1', 1429160813, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393136303736333b),
('ff73801e007d74313324ff807d6701ca734ede17', '127.0.0.1', 1429147803, 0x5f5f63695f6c6173745f726567656e65726174657c693a313432393134373530383b6964656e746974797c733a31353a2261646d696e4061646d696e2e636f6d223b757365726e616d657c733a31333a2261646d696e6973747261746f72223b656d61696c7c733a31353a2261646d696e4061646d696e2e636f6d223b757365725f69647c733a313a2231223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231343239303736383236223b);

-- --------------------------------------------------------

--
-- Table structure for table `crawls`
--

CREATE TABLE `crawls` (
  `crawl_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'building',
  `progress` text NOT NULL,
  `timeout` int(1) NOT NULL,
  `onComplete` text NOT NULL,
  PRIMARY KEY (`crawl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `crawl_id` int(11) NOT NULL,
  `file_url` text NOT NULL,
  `file_crawled` int(1) DEFAULT '0',
  `file_404` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator'),
(2, 'members', 'General User');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `crawl_id` int(11) NOT NULL,
  `image_src` varchar(255) NOT NULL,
  `image_crawled` int(1) NOT NULL,
  `image_404` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `scripts`
--

CREATE TABLE `scripts` (
  `script_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `crawl_id` int(11) NOT NULL,
  `script_url` varchar(255) NOT NULL,
  `script_crawled` int(1) NOT NULL DEFAULT '0',
  `script_404` int(1) DEFAULT '0',
  PRIMARY KEY (`script_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sites`
--

CREATE TABLE `sites` (
  `site_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_url` text NOT NULL,
  `site_domain` varchar(255) NOT NULL,
  `sites_created` int(11) NOT NULL,
  `sites_lastcrawl` int(11) NOT NULL,
  `sites_excludekeywords` text NOT NULL,
  `sites_timeout` int(11) NOT NULL DEFAULT '1800',
  PRIMARY KEY (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `stylesheets`
--

CREATE TABLE `stylesheets` (
  `stylesheet_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `crawl_id` int(11) NOT NULL,
  `stylesheet_url` varchar(255) NOT NULL,
  `stylesheet_crawled` int(1) NOT NULL DEFAULT '0',
  `stylesheet_404` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`stylesheet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
(1, '127.0.0.1', 'administrator', '$2y$08$StArf6qYEA7I2AjrAG7m8u7ud4o1Os4JieaYN.RI/fp/Ht/HHWOQa', '', 'admin@admin.com', '', NULL, NULL, NULL, 1268889823, 1429168797, 1, 'Admin', 'istrator', 'ADMIN', '0');

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE `users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(1, 1, 1),
(2, 1, 2);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
