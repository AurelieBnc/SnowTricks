-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 25, 2023 at 02:27 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `snowtricks`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int NOT NULL,
  `trick_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id`, `trick_id`, `user_id`, `content`, `created_at`) VALUES
(18, 5, 2, 'Super article sur le Tail Grab 101! J\'adore ce trick, ça ajoute vraiment de la classe à mon ride.', '2023-11-25 13:17:52'),
(19, 5, 3, 'Carrément! J\'ai essayé hier, c\'est un bon guide pour les débutants aussi. Des conseils utiles pour perfectionner le grab. Thumbs up!', '2023-11-25 13:18:05'),
(26, 9, 3, 'Super article sur le Misty Flip, non ?', '2023-11-25 13:46:33'),
(27, 9, 4, 'Carrément! Les astuces pour les variations m\'ont inspiré.', '2023-11-25 13:46:40'),
(28, 9, 5, 'J\'ai tenté le Misty Flip, c\'est intense. Des conseils pour la land ?', '2023-11-25 13:46:49'),
(29, 9, 3, 'Assure-toi de garder les yeux sur la piste pour un atterrissage en douceur.', '2023-11-25 13:47:00'),
(30, 9, 4, 'J\'ai vu un pro faire un Misty Flip aux X Games. Magnifique!', '2023-11-25 13:47:10'),
(31, 9, 5, 'Ça devait être fou! Des conseils pour perfectionner le grab ?', '2023-11-25 13:47:19'),
(32, 9, 3, 'Maintiens une prise ferme sur l\'arrière de la planche, ajuste selon ta vitesse.', '2023-11-25 13:47:28'),
(33, 9, 4, 'Vraiment utile! Et pour filmer ça de manière cool ?', '2023-11-25 13:47:37'),
(34, 9, 5, 'J\'ai essayé le Misty Flip dans la poudreuse, sensationnel !', '2023-11-25 13:47:45'),
(35, 9, 3, 'Tu as filmé ça ? Partage le lien, je veux voir !', '2023-11-25 13:47:52'),
(36, 9, 4, 'Carrément! J\'ai aussi tenté quelques tweaks avec le Misty Flip.', '2023-11-25 13:48:04'),
(37, 9, 5, 'Tweaks avec le Misty Flip ? Ça sonne génial, des astuces ?', '2023-11-25 13:48:12'),
(38, 9, 3, 'Essayez de jouer avec la position de vos jambes pendant le grab. Ça donne un style unique', '2023-11-25 13:48:19'),
(39, 9, 4, 'Bon conseil, je vais tester ça demain !', '2023-11-25 13:48:29'),
(40, 9, 5, 'Merci pour les conseils, les gars. On se retrouve sur les pistes!', '2023-11-25 13:48:38'),
(41, 9, 3, 'Définitivement! Amusez-vous bien !', '2023-11-25 13:48:46'),
(42, 8, 3, 'L\'article sur le corkscrew est génial! Prêt à tenter ça ce week-end.', '2023-11-25 14:09:40'),
(43, 8, 2, 'Carrément! Les corkscrews sont incroyables. Des conseils pour la torsion parfaite ?', '2023-11-25 14:09:48'),
(44, 10, 4, 'Le Rodeo Flip, tellement stylé ! Prêt à le tester dans la poudreuse.', '2023-11-25 14:12:34'),
(45, 10, 5, 'Carrément! Rodeo Flip avec un grab, c\'est la clé du style.', '2023-11-25 14:12:40'),
(46, 10, 2, 'Vu un pro faire un Rodeo Flip hier, incroyable! Des conseils pour les débutants ?', '2023-11-25 14:12:49'),
(47, 4, 4, 'Le Stalefish Grab, une classe folle! Prêt à l\'essayer.', '2023-11-25 14:15:12'),
(48, 4, 2, 'Stalefish Grab tweaké, une tuerie. Conseils pour un tweak stylé ?', '2023-11-25 14:15:20'),
(49, 4, 3, 'Stalefish Grab en backcountry, un délice ! Des astuces pour ajuster en hors-piste ?', '2023-11-25 14:15:30'),
(50, 4, 5, 'Stalefish Grab en milieu urbain, challenge accepted ! Des recommandations pour les rails ?', '2023-11-25 14:15:41'),
(51, 1, 3, 'L\'article sur l\'Indy Grab est super informatif! J\'adore ce grab pour son style unique. Des conseils pour le perfectionner davantage ?', '2023-11-25 14:17:55'),
(52, 11, 2, 'Le boardslide, un classique indémodable! Les conseils sont top pour perfectionner l\'élégance. Prêt à le maîtriser.', '2023-11-25 14:19:58'),
(53, 11, 5, 'Boardslide, une base essentielle. Merci pour les astuces, ça va ajouter du style à mes rides.', '2023-11-25 14:20:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9474526CB281BE2E` (`trick_id`),
  ADD KEY `IDX_9474526CA76ED395` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_9474526CB281BE2E` FOREIGN KEY (`trick_id`) REFERENCES `trick` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
