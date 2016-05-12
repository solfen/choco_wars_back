-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Jeu 12 Mai 2016 à 13:16
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `sg_chocowars`
--

-- --------------------------------------------------------

--
-- Structure de la table `chocowars_districts`
--

CREATE TABLE IF NOT EXISTS `chocowars_districts` (
  `Index` int(11) NOT NULL,
  `MinPrice` int(11) NOT NULL,
  `MaxMarketingBudget` int(11) NOT NULL,
  `MaxQualityBudget` int(11) NOT NULL,
  `TeamsRepartition` varchar(150) NOT NULL COMMENT '<ID Team>_<StallNb>%',
  PRIMARY KEY (`Index`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `chocowars_districts`
--

INSERT INTO `chocowars_districts` (`Index`, `MinPrice`, `MaxMarketingBudget`, `MaxQualityBudget`, `TeamsRepartition`) VALUES
(0, 10, 1500, 1500, '3_2'),
(1, 10, 1500, 1500, '3_2');

-- --------------------------------------------------------

--
-- Structure de la table `chocowars_games`
--

CREATE TABLE IF NOT EXISTS `chocowars_games` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TimeStart` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `CurrentRound` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Contenu de la table `chocowars_games`
--

INSERT INTO `chocowars_games` (`ID`, `TimeStart`, `CurrentRound`) VALUES
(9, '2016-05-11 13:35:47', 0),
(10, '2016-05-11 13:26:38', 11),
(11, '2016-05-11 14:16:49', 9);

-- --------------------------------------------------------

--
-- Structure de la table `chocowars_teamresults`
--

CREATE TABLE IF NOT EXISTS `chocowars_teamresults` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TeamID` int(11) NOT NULL,
  `Round` int(11) NOT NULL,
  `Price` int(11) NOT NULL,
  `QualityBudget` int(11) NOT NULL,
  `MarketingBudget` int(11) NOT NULL,
  `Placement` text NOT NULL COMMENT '<Index District>_<StallNb>%',
  `Turnover` int(11) NOT NULL,
  `Earnings` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `TeamID` (`TeamID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `chocowars_teamresults`
--

INSERT INTO `chocowars_teamresults` (`ID`, `TeamID`, `Round`, `Price`, `QualityBudget`, `MarketingBudget`, `Placement`, `Turnover`, `Earnings`) VALUES
(1, 3, 2, 10, 1500, 150, '0_2%1_2', 93010, 89760),
(6, 3, 2, 10, 1500, 150, '0_2%1_2', 93010, 89760);

-- --------------------------------------------------------

--
-- Structure de la table `chocowars_teams`
--

CREATE TABLE IF NOT EXISTS `chocowars_teams` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(150) NOT NULL,
  `Password` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `chocowars_teams`
--

INSERT INTO `chocowars_teams` (`ID`, `Name`, `Password`) VALUES
(1, 'Les croustillants', '$2y$10$JUfo78HROM5EjMYr3FtQEucHuIuhW67UiH0OB0LnSaSqDCFkRtFAi'),
(3, 'Les semi croustillants', '$2y$10$CiPa09q.suBU5qIxWm5LY.6pUBe3JPGHMV.nyzi/Db9315Ok6DySK'),
(4, 'Mon AncÃ¨tre Gurdil', '$2y$10$9nzvxBlrTTxMaiYzm9RE.uJFKQUyqaOigHeOnEFrq.ohy3hHIC9r.');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
