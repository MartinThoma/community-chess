-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 13. September 2011 um 22:03
-- Server Version: 5.1.41
-- PHP-Version: 5.3.2-1ubuntu4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `chess`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chess_currentGamesThreefoldRepetition`
--

CREATE TABLE IF NOT EXISTS `chess_currentGamesThreefoldRepetition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameID` int(11) NOT NULL,
  `board` char(64) NOT NULL,
  `whiteCastlingKingsidePossible` tinyint(1) NOT NULL,
  `whiteCastlingQueensidePossible` tinyint(1) NOT NULL,
  `blackCastlingKingsidePossible` tinyint(1) NOT NULL,
  `blackCastlingQueensidePossible` tinyint(1) NOT NULL,
  `enPassantPossible` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `chess_currentGamesThreefoldRepetition`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chess_games`
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `chess_games`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chess_languages`
--

CREATE TABLE IF NOT EXISTS `chess_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `used` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Daten für Tabelle `chess_languages`
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
-- Tabellenstruktur für Tabelle `chess_software`
--

CREATE TABLE IF NOT EXISTS `chess_software` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `adminUserID` int(11) NOT NULL,
  `version` varchar(20) NOT NULL,
  `lastVersionID` int(11) NOT NULL COMMENT '0 if this is the first version',
  `changelog` text NOT NULL,
  `BT2450Rating` int(11) NOT NULL,
  `EloRating` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `chess_software`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chess_softwareDeveloper`
--

CREATE TABLE IF NOT EXISTS `chess_softwareDeveloper` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `softwareID` int(11) NOT NULL,
  `task` varchar(255) NOT NULL DEFAULT 'Admin' COMMENT 'What did this person do? What was his/her job?',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `chess_softwareDeveloper`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chess_softwareLangages`
--

CREATE TABLE IF NOT EXISTS `chess_softwareLangages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `softwareID` int(11) NOT NULL,
  `languageID` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `chess_softwareLangages`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chess_turnamentPlayers`
--

CREATE TABLE IF NOT EXISTS `chess_turnamentPlayers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `turnamentID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `turnamentNumber` int(11) NOT NULL,
  `joinedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `gamesWon` int(11) NOT NULL DEFAULT '0' COMMENT 'If gamesWon < gamesPlayed, the player can''t play any more games in the current turnament',
  `gamesPlayed` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `turnamentID` (`turnamentID`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `chess_turnamentPlayers`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chess_turnaments`
--

CREATE TABLE IF NOT EXISTS `chess_turnaments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL DEFAULT 'd41d8cd98f00b204e9800998ecf8427e' COMMENT 'Default is md5('''')',
  `description` text NOT NULL,
  `initiationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closingDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `finishedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(18) NOT NULL DEFAULT 'openForInvitations' COMMENT 'openForInvitations, closed, running, finished',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `chess_turnaments`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chess_users`
--

CREATE TABLE IF NOT EXISTS `chess_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `user_password` varchar(32) NOT NULL,
  `currentChessSoftware` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `chess_users`
--

INSERT INTO `chess_users` (`user_id`, `user_name`, `user_password`, `currentChessSoftware`) VALUES
(1, 'abc', '900150983cd24fb0d6963f7d28e17f72', 0),
(2, 'test', '098f6bcd4621d373cade4e832627b4f6', 0);

