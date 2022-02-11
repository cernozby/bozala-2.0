-- Adminer 4.8.1 MySQL 5.5.5-10.4.17-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
                            `id_category` int(11) NOT NULL AUTO_INCREMENT,
                            `comp_id` int(10) NOT NULL,
                            `name` varchar(255) NOT NULL,
                            `gender` varchar(255) NOT NULL,
                            `year_young` int(11) NOT NULL,
                            `year_old` int(11) NOT NULL,
                            `visible_result` tinyint(4) NOT NULL DEFAULT 0,
                            PRIMARY KEY (`id_category`),
                            KEY `comp_id` (`comp_id`),
                            CONSTRAINT `category_ibfk_1` FOREIGN KEY (`comp_id`) REFERENCES `comp` (`id_comp`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `comp`;
CREATE TABLE `comp` (
                        `id_comp` int(10) NOT NULL AUTO_INCREMENT,
                        `user_id` int(10) unsigned DEFAULT NULL,
                        `name` varchar(255) NOT NULL,
                        `date` date NOT NULL,
                        `online_registration` int(11) NOT NULL DEFAULT 0,
                        `editable_result` tinyint(1) NOT NULL,
                        `visible_prereg` tinyint(1) NOT NULL DEFAULT 0,
                        `boulder` int(10) NOT NULL DEFAULT 0,
                        `boulder_final` int(10) NOT NULL DEFAULT 0,
                        `boulder_result` varchar(255) DEFAULT NULL,
                        `speed` int(10) NOT NULL DEFAULT 0,
                        `speed_final` int(10) NOT NULL DEFAULT 0,
                        `speed_result` varchar(255) DEFAULT NULL,
                        `lead` int(10) NOT NULL DEFAULT 0,
                        `lead_final` int(10) NOT NULL DEFAULT 0,
                        `lead_result` varchar(255) DEFAULT NULL,
                        `propo_path` text DEFAULT NULL,
                        `created` timestamp NULL DEFAULT current_timestamp(),
                        `last_mod` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                        PRIMARY KEY (`id_comp`),
                        KEY `user_id` (`user_id`),
                        CONSTRAINT `comp_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `compcat`;
CREATE TABLE `compcat` (
                           `id_compcat` int(10) NOT NULL AUTO_INCREMENT,
                           `category_id` int(11) NOT NULL,
                           `competitor_id` int(11) NOT NULL,
                           PRIMARY KEY (`id_compcat`),
                           KEY `category_id` (`category_id`),
                           KEY `competitor_id` (`competitor_id`),
                           CONSTRAINT `compcat_ibfk_1` FOREIGN KEY (`id_compcat`) REFERENCES `comp` (`id_comp`),
                           CONSTRAINT `compcat_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id_category`) ON DELETE CASCADE,
                           CONSTRAINT `compcat_ibfk_3` FOREIGN KEY (`competitor_id`) REFERENCES `competitor` (`id_competitor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `competitor`;
CREATE TABLE `competitor` (
                              `id_competitor` int(11) NOT NULL AUTO_INCREMENT,
                              `first_name` varchar(250) NOT NULL,
                              `last_name` varchar(250) NOT NULL,
                              `club` varchar(250) NOT NULL,
                              `year` int(11) NOT NULL,
                              `gender` varchar(255) NOT NULL,
                              `user_id` int(10) unsigned NOT NULL,
                              `created` timestamp NOT NULL DEFAULT current_timestamp(),
                              `modify` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
                              PRIMARY KEY (`id_competitor`),
                              KEY `user_id` (`user_id`),
                              CONSTRAINT `competitor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `prereg`;
CREATE TABLE `prereg` (
                          `id_prereg` int(11) NOT NULL AUTO_INCREMENT,
                          `competitor_id` int(11) NOT NULL,
                          `category_id` int(11) NOT NULL,
                          PRIMARY KEY (`id_prereg`),
                          KEY `competitor_id` (`competitor_id`),
                          KEY `category_id` (`category_id`),
                          CONSTRAINT `prereg_ibfk_4` FOREIGN KEY (`competitor_id`) REFERENCES `competitor` (`id_competitor`) ON DELETE CASCADE,
                          CONSTRAINT `prereg_ibfk_5` FOREIGN KEY (`category_id`) REFERENCES `category` (`id_category`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `result`;
CREATE TABLE `result` (
                          `id_result` int(10) NOT NULL AUTO_INCREMENT,
                          `competitor_id` int(11) NOT NULL,
                          `category_id` int(11) NOT NULL,
                          `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
                          `result` longtext CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
                          PRIMARY KEY (`id_result`),
                          KEY `competitor_id` (`competitor_id`),
                          KEY `category_id` (`category_id`),
                          CONSTRAINT `result_ibfk_1` FOREIGN KEY (`competitor_id`) REFERENCES `competitor` (`id_competitor`) ON DELETE NO ACTION,
                          CONSTRAINT `result_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id_category`) ON DELETE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=312 DEFAULT CHARSET=utf8mb4;


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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
                             `id_user_role` int(10) NOT NULL AUTO_INCREMENT COMMENT ' id ',
                             `role` varchar(50) NOT NULL COMMENT 'role_uzivatele',
                             PRIMARY KEY (`id_user_role`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


INSERT INTO `user_role` (`id_user_role`, `role`) VALUES
(0,	'user'),
(1,	'admin');

-- 2021-02-25 13:01:17