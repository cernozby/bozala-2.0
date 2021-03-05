-- Adminer 4.7.8 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT ' id uzivatele',
  `first_name` varchar(250) NOT NULL COMMENT 'jméno',
  `last_name` varchar(250) NOT NULL COMMENT 'přijmení',
  `passwd` varchar(500) NOT NULL COMMENT 'heslo',
  `email` varchar(250) NOT NULL COMMENT 'email',
  `role` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'role',
  `created` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'vytvoreno',
  `modify` datetime DEFAULT NULL COMMENT 'zmeneno',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `id_user_role` int(10) NOT NULL AUTO_INCREMENT COMMENT ' id ',
  `role` varchar(50) NOT NULL COMMENT 'role_uzivatele',
  PRIMARY KEY (`id_user_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_role` (`id_user_role`, `role`) VALUES
(0,	'user'),
(1,	'admin');

-- 2021-02-25 13:01:17