-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Июн 05 2024 г., 20:59
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `budget`
--

-- --------------------------------------------------------

--
-- Структура таблицы `bank`
--

CREATE TABLE IF NOT EXISTS `bank` (
  `IDB` int(11) NOT NULL AUTO_INCREMENT,
  `NameB` char(20) DEFAULT NULL,
  PRIMARY KEY (`IDB`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Структура таблицы `man`
--

CREATE TABLE IF NOT EXISTS `man` (
  `IDM` int(11) NOT NULL AUTO_INCREMENT,
  `NameL` char(20) DEFAULT NULL,
  `NameF` char(20) DEFAULT NULL,
  `NameO` char(20) DEFAULT NULL,
  `BDay` date DEFAULT NULL,
  `Gender` enum('М','Ж') DEFAULT NULL,
  `login` char(20) NOT NULL,
  `password` char(50) NOT NULL,
  `access` int(11) DEFAULT '2',
  PRIMARY KEY (`IDM`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Структура таблицы `oper`
--

CREATE TABLE IF NOT EXISTS `oper` (
  `IDO` int(11) NOT NULL AUTO_INCREMENT,
  `Dat` date DEFAULT NULL,
  `SUMO` int(11) DEFAULT NULL,
  `TYPE` char(20) DEFAULT NULL,
  `IDP` int(11) DEFAULT NULL,
  `IDPP` int(11) DEFAULT NULL,
  `Place` char(30) DEFAULT NULL,
  PRIMARY KEY (`IDO`),
  KEY `FK_PROD` (`IDP`),
  KEY `IDPP` (`IDPP`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Структура таблицы `prod`
--

CREATE TABLE IF NOT EXISTS `prod` (
  `IDP` int(11) NOT NULL AUTO_INCREMENT,
  `NameP` char(20) DEFAULT NULL,
  `SUMP` float DEFAULT NULL,
  `PROC` float DEFAULT NULL,
  `IDM` int(11) DEFAULT NULL,
  `IDB` int(11) DEFAULT NULL,
  `upd` date DEFAULT NULL,
  PRIMARY KEY (`IDP`),
  UNIQUE KEY `SUMP` (`SUMP`),
  KEY `FK_MAN` (`IDM`),
  KEY `FK_BANK` (`IDB`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tovar`
--

CREATE TABLE IF NOT EXISTS `tovar` (
  `IDT` int(11) NOT NULL AUTO_INCREMENT,
  `NameT` char(100) DEFAULT NULL,
  `Cost` int(11) DEFAULT NULL,
  `IDO` int(11) DEFAULT NULL,
  PRIMARY KEY (`IDT`),
  KEY `FK_OPER` (`IDO`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `oper`
--
ALTER TABLE `oper`
  ADD CONSTRAINT `FK_PROD` FOREIGN KEY (`IDP`) REFERENCES `prod` (`IDP`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `oper_ibfk_1` FOREIGN KEY (`IDPP`) REFERENCES `prod` (`IDP`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `prod`
--
ALTER TABLE `prod`
  ADD CONSTRAINT `FK_BANK` FOREIGN KEY (`IDB`) REFERENCES `bank` (`IDB`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_MAN` FOREIGN KEY (`IDM`) REFERENCES `man` (`IDM`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `tovar`
--
ALTER TABLE `tovar`
  ADD CONSTRAINT `FK_OPER` FOREIGN KEY (`IDO`) REFERENCES `oper` (`IDO`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
