-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 25, 2021 at 03:04 PM
-- Server version: 8.0.21
-- PHP Version: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sortir_eni`
--

-- --------------------------------------------------------

--
-- Table structure for table `campus`
--

DROP TABLE IF EXISTS `campus`;
CREATE TABLE IF NOT EXISTS `campus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `campus`
--

INSERT INTO `campus` (`id`, `name`) VALUES
(1, 'Bordeaux'),
(2, 'Paris'),
(3, 'Biarritz');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
CREATE TABLE IF NOT EXISTS `city` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`id`, `name`, `code`) VALUES
(1, 'Bordeaux', '33000'),
(2, 'Paris', '75000'),
(3, 'Biarritz', '64200');

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20210301135822', '2021-03-01 13:58:30', 660),
('DoctrineMigrations\\Version20210301154221', '2021-03-01 15:42:29', 252),
('DoctrineMigrations\\Version20210301163002', '2021-03-01 16:30:09', 1022),
('DoctrineMigrations\\Version20210301172248', '2021-03-01 17:23:01', 432),
('DoctrineMigrations\\Version20210301172707', '2021-03-01 17:28:40', 4441),
('DoctrineMigrations\\Version20210301174830', '2021-03-01 17:48:49', 592),
('DoctrineMigrations\\Version20210301175141', '2021-03-01 17:52:03', 892),
('DoctrineMigrations\\Version20210301175255', '2021-03-01 17:53:07', 368),
('DoctrineMigrations\\Version20210301175421', '2021-03-01 17:54:32', 2099),
('DoctrineMigrations\\Version20210301175511', '2021-03-01 17:55:27', 2867),
('DoctrineMigrations\\Version20210301175644', '2021-03-01 17:57:06', 3171),
('DoctrineMigrations\\Version20210301175815', '2021-03-01 17:58:24', 1695),
('DoctrineMigrations\\Version20210301184056', '2021-03-01 18:42:23', 802),
('DoctrineMigrations\\Version20210301185703', '2021-03-01 18:57:11', 5420),
('DoctrineMigrations\\Version20210301190259', '2021-03-01 19:03:04', 2697),
('DoctrineMigrations\\Version20210302185600', '2021-03-02 18:56:10', 5769),
('DoctrineMigrations\\Version20210302223207', '2021-03-02 22:32:14', 845),
('DoctrineMigrations\\Version20210302223905', '2021-03-02 22:39:09', 3762),
('DoctrineMigrations\\Version20210303151230', '2021-03-03 15:12:40', 2377),
('DoctrineMigrations\\Version20210308113245', '2021-03-08 11:32:57', 7286),
('DoctrineMigrations\\Version20210309161054', '2021-03-09 16:10:58', 2900);

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
CREATE TABLE IF NOT EXISTS `event` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `duration` int NOT NULL,
  `limit_date` date NOT NULL,
  `spots` int NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_id` int DEFAULT NULL,
  `campus_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `status_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3BAE0AA764D218E` (`location_id`),
  KEY `IDX_3BAE0AA7AF5D55E1` (`campus_id`),
  KEY `IDX_3BAE0AA7A76ED395` (`user_id`),
  KEY `IDX_3BAE0AA76BF700BD` (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id`, `name`, `date`, `duration`, `limit_date`, `spots`, `description`, `location_id`, `campus_id`, `user_id`, `status_id`) VALUES
(1, '2021 New Year Event', '2021-12-31 22:00:00', 180, '2021-12-01', 30, 'Event has been cancelled due to the following reasons: Event postponed', 1, 1, 1, 6),
(2, '2020 New Year Event', '2020-12-31 22:00:00', 180, '2020-12-30', 30, 'Meet new year together, and eat yummy food!', 6, 1, 1, 5),
(3, 'Opera Night', '2021-03-20 17:00:00', 120, '2021-03-10', 10, 'Culture under the stars', 4, 2, 2, 5),
(4, 'Comedy Hour II', '2021-05-01 16:00:00', 60, '2021-04-01', 10, 'Stand up event for everyone! Again!', 7, 2, 2, 2),
(5, 'BBQ Heat', '2021-03-24 10:00:00', 180, '2021-03-01', 20, 'Summer BBQ', 3, 3, 36, 3),
(6, 'SURF THE WAVE', '2021-06-05 08:00:00', 720, '2021-06-01', 3, 'SURFING LESSONS', 5, 3, 36, 2),
(7, 'March Art Show', '2021-03-06 15:00:00', 60, '2021-02-20', 10, 'Show off you art skills', 4, 2, 6, 5),
(8, 'Comedy Hour I', '2020-02-29 16:00:00', 60, '2020-02-22', 10, 'Stand up event for everyone!', 7, 2, 6, 2),
(9, 'Pokemon Day', '2021-04-01 13:00:00', 120, '2021-03-24', 20, 'Catch Pokemon together in the park!', 1, 1, 5, 3),
(10, 'Birthday Dinner', '2021-04-20 21:00:00', 120, '2021-03-31', 10, 'Birthday dinner with friends', 6, 1, 5, 2),
(11, 'Fun in the sun', '2021-03-16 09:00:00', 120, '2021-03-15', 5, 'Suntan with friends:)', 5, 3, 36, 5);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE IF NOT EXISTS `location` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `street` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `city_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5E9E89CB8BAC62AF` (`city_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `name`, `street`, `latitude`, `longitude`, `city_id`) VALUES
(1, 'Parc Bordelais', 'Rue du Bocage', 44.8524, -0.6028, 1),
(3, 'Beach', 'Rue de la Plage', 43.4838, -1.5604, 3),
(4, 'The Opera', 'Some street in Paris', 48.872, 2.3316, 2),
(5, 'Wild Beach', 'Sunset Street', 43.475, -1.5686, 3),
(6, 'Restaurant \'Chez Amis\'', 'Avenue de Bordeaux', 44.8423, -0.5709, 1),
(7, 'La Comedie Francaise', '1 Place Colette', 48.8641, 2.336, 2);

-- --------------------------------------------------------

--
-- Table structure for table `reset_password_request`
--

DROP TABLE IF EXISTS `reset_password_request`;
CREATE TABLE IF NOT EXISTS `reset_password_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `selector` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_7CE748AA76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
CREATE TABLE IF NOT EXISTS `status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `state`) VALUES
(1, 'Created'),
(2, 'Open'),
(3, 'Closed'),
(4, 'Active'),
(5, 'Finished'),
(6, 'Cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

DROP TABLE IF EXISTS `subscription`;
CREATE TABLE IF NOT EXISTS `subscription` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `event_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A3C664D371F7E88B` (`event_id`),
  KEY `IDX_A3C664D3A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription`
--

INSERT INTO `subscription` (`id`, `date`, `event_id`, `user_id`) VALUES
(2, '2021-03-02 23:07:10', 2, 2),
(5, '2021-03-02 23:10:10', 2, 5),
(6, '2021-03-02 23:23:05', 2, 6),
(13, '2021-03-03 08:16:08', 3, 6),
(14, '2021-03-03 08:16:15', 4, 5),
(45, '2021-03-10 09:53:21', 4, 1),
(46, '2021-03-10 09:53:37', 5, 1),
(52, '2021-03-11 09:43:39', 5, 4),
(53, '2021-03-11 09:43:40', 6, 1),
(56, '2021-03-12 11:05:40', 6, 2),
(57, '2021-03-15 17:36:48', 6, 6),
(59, '2021-03-15 17:42:31', 10, 4),
(60, '2021-03-15 17:42:31', 10, 1),
(61, '2021-03-15 17:42:31', 10, 6),
(62, '2021-03-15 17:42:31', 10, 2),
(63, '2021-03-15 17:42:31', 10, 36),
(64, '2021-03-15 17:56:42', 7, 2),
(65, '2021-03-15 17:56:42', 7, 5),
(66, '2021-03-15 17:56:42', 9, 6),
(67, '2021-03-15 17:56:42', 9, 4),
(68, '2021-03-15 17:56:42', 9, 36),
(69, '2021-03-15 17:56:42', 9, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campus_id` int DEFAULT NULL,
  `image_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_dimensions` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  KEY `IDX_8D93D649AF5D55E1` (`campus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `roles`, `password`, `email`, `name`, `last_name`, `phone`, `campus_id`, `image_name`, `image_original_name`, `image_mime_type`, `image_size`, `image_dimensions`, `is_active`) VALUES
(1, 'anyretak', '[]', '$argon2id$v=19$m=65536,t=4,p=1$Y3V3LjNReXh5U2VQN1RvWg$C8UdWrK5CdwWOqbVO0M5oZxO9Mn5hYAehlYvjt5EJRU', 'kat@email.com', 'Kat', 'B', '0102030405', 1, 'temp-60461d8ae5822154534692.jpg', NULL, NULL, NULL, NULL, 0),
(2, 'alopex', '[]', '$argon2id$v=19$m=65536,t=4,p=1$azl2TldBRHkzeW5HN2VtMA$/5B20HAf5uSWCX1Q5OLrd1lPHMKBRnDxwG77J1Ug/hs', 'alopex@email', 'Alo', 'Pex', '1234567890', 2, 'redpanda-603fb4f54ba01588486029.png', NULL, NULL, NULL, NULL, 1),
(4, 'kalypta', '[\"ROLE_ADMIN\"]', '$argon2id$v=19$m=65536,t=4,p=1$cUZ3L0ovSFZsa21DZ1Y2Wg$ASdF1ZXHlc+OeCAgqjJ/eJkencGFd3loFrVGanmFoA8', 'kalypta@email', 'Kalypta', 'Admin', '0987654321', 3, 'polarbear-604080258d65f753892068.jpg', NULL, NULL, NULL, NULL, 1),
(5, 'mr.mime', '[]', '$argon2id$v=19$m=65536,t=4,p=1$c0xVcHR4bmx6SDRFdXI4cQ$eRYfmDxRFjFV5qwniI8Wsk8WkNaDPSEBRzR1aQDAxdY', 'mr.mime@email', 'Mime Jr', 'Kanto', '2998224289', 1, NULL, NULL, NULL, NULL, NULL, 1),
(6, 'pikachu', '[]', '$argon2id$v=19$m=65536,t=4,p=1$WVhKTnV4bk9jL3VPNmN1dw$n+wTQ4LJvGpmUjDiClpeXuFnWxBdKnEUA0NBwezC5c0', 'pikachu@email', 'Pika', 'Chu', '5889053019', 2, NULL, NULL, NULL, NULL, NULL, 1),
(36, 'snore', '[]', '$argon2id$v=19$m=65536,t=4,p=1$ajJrMFRyd2lqQVJuT3lEdA$M4KkvD74G1fYUDSik7So9xhMcl1rtEQxqqMhgXeu66I', 'snorlax@email', 'Snorlax', 'Yawn', '4573144706', 3, NULL, NULL, NULL, NULL, NULL, 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `FK_3BAE0AA764D218E` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`),
  ADD CONSTRAINT `FK_3BAE0AA76BF700BD` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`),
  ADD CONSTRAINT `FK_3BAE0AA7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_3BAE0AA7AF5D55E1` FOREIGN KEY (`campus_id`) REFERENCES `campus` (`id`);

--
-- Constraints for table `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `FK_5E9E89CB8BAC62AF` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`);

--
-- Constraints for table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `subscription`
--
ALTER TABLE `subscription`
  ADD CONSTRAINT `FK_A3C664D371F7E88B` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  ADD CONSTRAINT `FK_A3C664D3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D649AF5D55E1` FOREIGN KEY (`campus_id`) REFERENCES `campus` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
