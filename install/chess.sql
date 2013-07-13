-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 06, 2013 at 11:01 AM
-- Server version: 5.5.31
-- PHP Version: 5.4.6-1ubuntu1.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `chess`
--

-- --------------------------------------------------------

--
-- Table structure for table `chess_challenges`
--

CREATE TABLE IF NOT EXISTS `chess_challenges` (
  `challenge_id` int(11) NOT NULL AUTO_INCREMENT,
  `challenge_name` varchar(255) NOT NULL,
  `challenge_points` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY (`challenge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `chess_users`
--

CREATE TABLE IF NOT EXISTS `chess_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `user_password` varchar(32) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `rank` int(11) NOT NULL DEFAULT '-1',
  `pageRank` double NOT NULL DEFAULT '0.15',
  `software_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `chess_users`
--

INSERT INTO `chess_users` (`user_id`, `user_name`, `user_password`, `user_email`, `rank`, `pageRank`, `software_id`) VALUES
(1, 'abc', '900150983cd24fb0d6963f7d28e17f72', 'abc@martin-thoma.de', -1, 0.15, 0),
(2, 'test', '098f6bcd4621d373cade4e832627b4f6', 'test@martin-thoma.de', -1, 0.15, 0);


-- --------------------------------------------------------

--
-- Table structure for table `chess_software`
--

CREATE TABLE IF NOT EXISTS `chess_software` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `adminUserID` int(11) NOT NULL,
  `version` varchar(20) NOT NULL,
  `lastVersionID` int(11) COMMENT 'NULL if this is the first version',
  `changelog` text NOT NULL,
  `BT2450Rating` int(11) NOT NULL DEFAULT '-1',
  `EloRating` double NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`adminUserID`) REFERENCES `chess_users`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `chess_tournaments`
--

CREATE TABLE IF NOT EXISTS `chess_tournaments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL DEFAULT 'd41d8cd98f00b204e9800998ecf8427e' COMMENT 'Default is md5('''')',
  `description` text NOT NULL,
  `initiationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closingDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `finishedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `chess_games`
--

CREATE TABLE IF NOT EXISTS `chess_games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tournamentID` int(11) NOT NULL DEFAULT '0' COMMENT 'If tournamentID = 0, its not a game of a tournament',
  `timeLimit` mediumint(11) NOT NULL DEFAULT '0' COMMENT 'time limit in seconds to make your move. 0, if no time limit is set. Maximum is 16,777,215 which would be over 194 days',
  `currentBoard` char(64) NOT NULL DEFAULT 'RNBQKBNRPPPPPPPP00000000000000000000000000000000pppppppprnbqkbnr' COMMENT 'See http://code.google.com/p/community-chess/wiki/ChessboardDatastructure for representation',
  `moveList` text NOT NULL,
  `noCaptureAndPawnMoves` smallint(11) unsigned NOT NULL DEFAULT '0',
  `whiteCastlingKingsidePossible` tinyint(1) NOT NULL DEFAULT '1',
  `whiteCastlingQueensidePossible` tinyint(1) NOT NULL DEFAULT '1',
  `blackCastlingKingsidePossible` tinyint(1) NOT NULL DEFAULT '1',
  `blackCastlingQueensidePossible` tinyint(1) NOT NULL DEFAULT '1',
  `whiteUserID` int(11) NOT NULL,
  `blackUserID` int(11) NOT NULL,
  `whitePlayerSoftwareID` int(11) NOT NULL DEFAULT '0',
  `blackPlayerSoftwareID` int(11) NOT NULL DEFAULT '0',
  `whoseTurnIsIt` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 means white, 1 means black',
  `startTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastMove` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `outcome` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '-1 means the game is still running, 0 means white won, 1 means black won, 2 means draw',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`tournamentID`) REFERENCES `chess_tournaments`(`id`),
  FOREIGN KEY (`whiteUserID`) REFERENCES `chess_users`(`user_id`),
  FOREIGN KEY (`blackUserID`) REFERENCES `chess_users`(`user_id`),
  FOREIGN KEY (`whitePlayerSoftwareID`) REFERENCES `chess_software`(`id`),
  FOREIGN KEY (`blackPlayerSoftwareID`) REFERENCES `chess_software`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `chess_gamesThreefoldRepetition`
--

CREATE TABLE IF NOT EXISTS `chess_gamesThreefoldRepetition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameID` int(11) NOT NULL,
  `board` char(64) NOT NULL,
  `whiteCastlingKingsidePossible` tinyint(1) NOT NULL,
  `whiteCastlingQueensidePossible` tinyint(1) NOT NULL,
  `blackCastlingKingsidePossible` tinyint(1) NOT NULL,
  `blackCastlingQueensidePossible` tinyint(1) NOT NULL,
  `enPassantPossible` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`gameID`) REFERENCES `chess_games`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `chess_languages`
--

CREATE TABLE IF NOT EXISTS `chess_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `used` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `chess_languages`
--

INSERT INTO `chess_languages` (`id`, `name`, `used`) VALUES
(1, 'C', 0),
(2, 'C#', 0),
(3, 'C++', 0),
(4, 'Delphi', 0),
(5, 'Java', 0),
(6, 'JavaScript', 0),
(7, 'LISP', 0),
(8, 'Perl', 0),
(9, 'PHP', 0),
(10, 'Python', 0),
(11, 'Ruby', 0);

-- --------------------------------------------------------

--
-- Table structure for table `chess_softwareDeveloper`
--

CREATE TABLE IF NOT EXISTS `chess_softwareDeveloper` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `softwareID` int(11) NOT NULL,
  `task` varchar(255) NOT NULL DEFAULT 'Admin' COMMENT 'What did this person do? What was his/her job?',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `chess_users`(`user_id`),
  FOREIGN KEY (`softwareID`) REFERENCES `chess_software`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `chess_softwareLanguages`
--

CREATE TABLE IF NOT EXISTS `chess_softwareLanguages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `softwareID` int(11) NOT NULL,
  `languageID` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`softwareID`) REFERENCES `chess_software`(`id`),
  FOREIGN KEY (`languageID`) REFERENCES `chess_languages`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `chess_tournamentPlayers`
--

CREATE TABLE IF NOT EXISTS `chess_tournamentPlayers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tournamentID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tournamentNumber` int(11) NOT NULL DEFAULT '-1',
  `joinedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `gamesWon` int(11) NOT NULL DEFAULT '0' COMMENT 'If gamesWon < gamesPlayed, the player can''t play any more games in the current tournament',
  `gamesPlayed` int(11) NOT NULL DEFAULT '0',
  `pageRank` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tournamentID` (`tournamentID`,`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `chess_users`(`user_id`),
  FOREIGN KEY (`tournamentID`) REFERENCES `chess_tournaments`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `chess_userOpenID`
--

CREATE TABLE IF NOT EXISTS `chess_userOpenID` (
  `userOpenID_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `OpenID` text NOT NULL,
  PRIMARY KEY (`userOpenID_id`),
  FOREIGN KEY (`user_id`) REFERENCES `chess_users`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
