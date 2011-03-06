-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 06, 2011 at 03:55 PM
-- Server version: 5.0.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `ouray`
--

-- --------------------------------------------------------

--
-- Table structure for table `BridgeCourseUser`
--

CREATE TABLE IF NOT EXISTS `BridgeCourseUser` (
  `courseId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  KEY `CourseCourseUser` (`userId`),
  KEY `CourseCourseUser2` (`courseId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Course`
--

CREATE TABLE IF NOT EXISTS `Course` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(90) NOT NULL,
  `urlTitle` varchar(90) NOT NULL,
  `description` text NOT NULL,
  `creatorId` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `CourseCourse_UserId` (`creatorId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=217 ;

-- --------------------------------------------------------

--
-- Table structure for table `Item`
--

CREATE TABLE IF NOT EXISTS `Item` (
  `id` int(11) NOT NULL auto_increment,
  `enabled` tinyint(4) NOT NULL,
  `renderText` text NOT NULL,
  `originalText` text NOT NULL,
  `courseId` int(11) NOT NULL,
  `originalName` varchar(160) default NULL,
  `serverName` varchar(160) default NULL,
  `fileType` varchar(50) default NULL,
  `uploadDate` int(11) NOT NULL default '0',
  `downloads` int(11) NOT NULL default '0',
  `likes` int(11) NOT NULL default '0',
  `userId` int(11) default NULL,
  `itemType` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `Item_userId` (`userId`),
  KEY `serverName` (`serverName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=417 ;

-- --------------------------------------------------------

--
-- Table structure for table `parameters`
--

CREATE TABLE IF NOT EXISTS `parameters` (
  `key` varchar(10) NOT NULL,
  `value` varchar(90) NOT NULL,
  PRIMARY KEY  (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `id` int(11) NOT NULL auto_increment,
  `fbId` int(11) default NULL,
  `email` varchar(120) NOT NULL,
  `name` varchar(90) NOT NULL,
  `password` varchar(120) NOT NULL,
  `signupDate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `lastLogin` timestamp NULL default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=63 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `BridgeCourseUser`
--
ALTER TABLE `BridgeCourseUser`
  ADD CONSTRAINT `CourseCourseUser` FOREIGN KEY (`userId`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `CourseCourseUser2` FOREIGN KEY (`courseId`) REFERENCES `Course` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Course`
--
ALTER TABLE `Course`
  ADD CONSTRAINT `CourseCourse_UserId` FOREIGN KEY (`creatorId`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Item`
--
ALTER TABLE `Item`
  ADD CONSTRAINT `Item_userId` FOREIGN KEY (`userId`) REFERENCES `User` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

