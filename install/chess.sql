-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 01. September 2011 um 18:49
-- Server Version: 5.1.41
-- PHP-Version: 5.3.2-1ubuntu4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `chess`
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


