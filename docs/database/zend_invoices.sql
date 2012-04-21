-- phpMyAdmin SQL Dump
-- version 3.5.0
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 21-04-2012 a las 23:53:34
-- Versión del servidor: 5.5.23
-- Versión de PHP: 5.4.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `zend_invoices`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `security_account_reactivation`
--

CREATE TABLE IF NOT EXISTS `security_account_reactivation` (
  `token` char(72) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `creation_time` int(11) NOT NULL,
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `security_bad_logins`
--

CREATE TABLE IF NOT EXISTS `security_bad_logins` (
  `user_id` int(10) unsigned NOT NULL,
  `time` int(11) NOT NULL,
  `address` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`time`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `security_banned_addresses`
--

CREATE TABLE IF NOT EXISTS `security_banned_addresses` (
  `address` int(11) NOT NULL,
  `expire` int(11) NOT NULL,
  PRIMARY KEY (`address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `salt` char(32) NOT NULL,
  `role_id` tinyint(3) unsigned NOT NULL,
  `email` text,
  `must_change_pass` tinyint(1) NOT NULL DEFAULT '0',
  `last_pass_change` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `salt`, `role_id`, `email`, `must_change_pass`, `last_pass_change`, `active`, `last_login`, `created`, `modified`) VALUES
(1, 'admin', '56eb55aa55c1ae72cea63fd26543855a', '868c91c67e4079d1fda10ca87ae6cbe8', 5, NULL, 1, '2012-04-20 20:11:32', 1, '2012-04-21 13:48:16', '2012-04-18 15:12:57', '2012-04-21 23:51:51');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
