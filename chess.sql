-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 31, 2011 at 08:54 PM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `chess`
--

-- --------------------------------------------------------

--
-- Table structure for table `chess_currentGames`
--

CREATE TABLE IF NOT EXISTS `chess_currentGames` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timeLimit` int(11) NOT NULL DEFAULT '0' COMMENT 'time limit in seconds to make your move. 0, if no time limit is set.',
  `currentBoard` varchar(64) NOT NULL DEFAULT 'RNBQKBNRPPPPPPPP00000000000000000000000000000000pppppppprnbqkbnr' COMMENT 'See http://code.google.com/p/community-chess/wiki/ChessboardDatastructure for representation',
  `moveList` text NOT NULL,
  `noCaptureAndPawnMoves` int(11) NOT NULL DEFAULT '0',
  `whiteCastlingKingsidePossible` tinyint(1) NOT NULL DEFAULT '1',
  `whiteCastlingQueensidePossible` tinyint(1) NOT NULL DEFAULT '1',
  `blackCastlingKingsidePossible` tinyint(1) NOT NULL DEFAULT '1',
  `blackCastlingQueensidePossible` tinyint(1) NOT NULL DEFAULT '1',
  `whitePlayerID` int(11) NOT NULL,
  `blackPlayerID` int(11) NOT NULL,
  `whitePlayerSoftwareID` int(11) NOT NULL DEFAULT '0',
  `blackPlayerSoftwareID` int(11) NOT NULL DEFAULT '0',
  `whoseTurnIsIt` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 means white, 1 means black',
  `startTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastMove` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `chess_currentGames`
--


-- --------------------------------------------------------

--
-- Table structure for table `chess_currentGamesThreefoldRepetition`
--

CREATE TABLE IF NOT EXISTS `chess_currentGamesThreefoldRepetition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameID` int(11) NOT NULL,
  `board` varchar(64) NOT NULL,
  `whiteCastlingKingsidePossible` tinyint(1) NOT NULL,
  `whiteCastlingQueensidePossible` tinyint(1) NOT NULL,
  `blackCastlingKingsidePossible` tinyint(1) NOT NULL,
  `blackCastlingQueensidePossible` tinyint(1) NOT NULL,
  `enPassantPossible` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `chess_currentGamesThreefoldRepetition`
--


-- --------------------------------------------------------

--
-- Table structure for table `chess_languages`
--

CREATE TABLE IF NOT EXISTS `chess_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `chess_languages`
--

INSERT INTO `chess_languages` (`id`, `name`) VALUES
(1, '0'),
(2, '0'),
(3, 'Python'),
(4, 'C'),
(5, 'C++'),
(6, 'Java'),
(7, 'PHP'),
(8, 'Delphi');

-- --------------------------------------------------------

--
-- Table structure for table `chess_medalPlayerCorrelation`
--

CREATE TABLE IF NOT EXISTS `chess_medalPlayerCorrelation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playerID` int(11) NOT NULL,
  `medalID` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `chess_medalPlayerCorrelation`
--


-- --------------------------------------------------------

--
-- Table structure for table `chess_medals`
--

CREATE TABLE IF NOT EXISTS `chess_medals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL COMMENT 'URL',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `chess_medals`
--


-- --------------------------------------------------------

--
-- Table structure for table `chess_pastGames`
--

CREATE TABLE IF NOT EXISTS `chess_pastGames` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timeLimit` int(11) NOT NULL DEFAULT '0' COMMENT 'time limit in seconds to make your move. 0, if no time limit is set.',
  `moveList` text NOT NULL,
  `whitePlayerID` int(11) NOT NULL,
  `blackPlayerID` int(11) NOT NULL,
  `whitePlayerSoftwareID` int(11) NOT NULL,
  `blackPlayerSoftwareID` int(11) NOT NULL,
  `outcome` tinyint(4) NOT NULL COMMENT '0 means white won, 1 means black won, 2 means draw',
  `startTime` datetime NOT NULL,
  `endTime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `chess_pastGames`
--


-- --------------------------------------------------------

--
-- Table structure for table `chess_players`
--

CREATE TABLE IF NOT EXISTS `chess_players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uname` varchar(255) NOT NULL,
  `upass` varchar(32) NOT NULL,
  `currentChessSoftware` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `chess_players`
--

INSERT INTO `chess_players` (`id`, `uname`, `upass`, `currentChessSoftware`) VALUES
(1, 'abc', '900150983cd24fb0d6963f7d28e17f72', 0),
(2, 'test', '098f6bcd4621d373cade4e832627b4f6', 0);

-- --------------------------------------------------------

--
-- Table structure for table `chess_software`
--

CREATE TABLE IF NOT EXISTS `chess_software` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `version` varchar(20) NOT NULL,
  `lastVersionID` int(11) NOT NULL COMMENT '0 if this is the first version',
  `changelog` text NOT NULL,
  `BT2450Rating` int(11) NOT NULL,
  `EloRating` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `chess_software`
--


-- --------------------------------------------------------

--
-- Table structure for table `chess_softwareDeveloper`
--

CREATE TABLE IF NOT EXISTS `chess_softwareDeveloper` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playerID` int(11) NOT NULL,
  `softwareID` int(11) NOT NULL,
  `task` varchar(255) NOT NULL COMMENT 'What did this person do? What was his/her job?',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `chess_softwareDeveloper`
--


-- --------------------------------------------------------

--
-- Table structure for table `chess_softwareLangages`
--

CREATE TABLE IF NOT EXISTS `chess_softwareLangages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `softwareID` int(11) NOT NULL,
  `languageID` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `chess_softwareLangages`
--


-- --------------------------------------------------------

--
-- Table structure for table `chess_turnamentGames`
--

CREATE TABLE IF NOT EXISTS `chess_turnamentGames` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `turnamentID` int(11) NOT NULL,
  `gameID` int(11) NOT NULL,
  `status` varchar(10) NOT NULL COMMENT 'current or past game?',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `chess_turnamentGames`
--


-- --------------------------------------------------------

--
-- Table structure for table `chess_turnaments`
--

CREATE TABLE IF NOT EXISTS `chess_turnaments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `initiationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closingDate` datetime NOT NULL,
  `finishedDate` datetime NOT NULL,
  `status` varchar(18) NOT NULL DEFAULT 'openForInvitations' COMMENT 'openForInvitations, closed, running, finished',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `chess_turnaments`
--


