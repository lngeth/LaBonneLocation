-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 08 nov. 2021 à 12:14
-- Version du serveur :  5.7.24
-- Version de PHP : 7.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `labonnelocation`
--

-- --------------------------------------------------------

--
-- Structure de la table `billing`
--

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `id_user_id` int(11) NOT NULL,
  `id_car_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `price` double NOT NULL,
  `paid` tinyint(1) NOT NULL,
  `returned` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `billing`
--

INSERT INTO `billing` (`id`, `id_user_id`, `id_car_id`, `start_date`, `end_date`, `price`, `paid`, `returned`) VALUES
(20, 8, 2, '2021-11-25', '2021-11-30', 1050, 1, 0),
(21, 8, 1, '2021-11-17', '2021-11-20', 300, 1, 0),
(22, 8, 1, '2021-11-23', '2021-11-26', 300, 0, 0),
(23, 8, 1, '2021-11-26', '2021-11-30', 600, 0, 0),
(24, 8, 1, '2021-11-16', '2021-11-30', 1600, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `car`
--

CREATE TABLE `car` (
  `id` int(11) NOT NULL,
  `id_owner_id` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datasheet` json NOT NULL,
  `amount` double NOT NULL,
  `rent` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `car`
--

INSERT INTO `car` (`id`, `id_owner_id`, `type`, `datasheet`, `amount`, `rent`, `image`, `quantity`) VALUES
(1, 1, 'Peugeot 206', '{\"motor\": \"hybride\", \"nbSeat\": \"3\", \"vitesse\": \"automatique\"}', 100, 'disponible', 'peugeot_206.jpg', 1),
(2, 2, 'Peugeot 207', '{\"motor\": \"diesel\", \"nbSeat\": \"2\", \"vitesse\": \"mecanique\"}', 150, 'indisponible', 'peugeot_207.jpg', 0),
(3, 1, 'Citroen C3', '{\"motor\": \"essence\", \"nbSeat\": \"3\", \"vitesse\": \"mecanique\"}', 90, 'disponible', 'citroen_c3.jpg', 1),
(4, 9, 'BMW', '{\"motor\": \"diesel\", \"nbSeat\": 5, \"vitesse\": \"intelligente\"}', 500, 'disponible', 'fou-618861c33734f.png', 3);

-- --------------------------------------------------------

--
-- Structure de la table `rememberme_token`
--

CREATE TABLE `rememberme_token` (
  `series` char(88) NOT NULL,
  `value` varchar(88) NOT NULL,
  `lastUsed` datetime NOT NULL,
  `class` varchar(100) NOT NULL,
  `username` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Ibrahime Mus', 'ibrahime93120@hotmail.com', '123', 'Admin'),
(2, 'Laurent Nghiet', 'sfiabenali@outlook.com', '$2y$13$gWGXvRkJPv6zSgcMCibDpOIbSiCEvat6FqQOrNGZ6uEyMgG3G3z8G', 'Loueur'),
(3, 'Ibrahime Ahbib', 'salut@gmail.com', '$2y$13$gRdwTtxA45M8mIdRcnTZA.d23YEEPkscbOdH38e5onwxAKHyVpycq', 'Loueur'),
(4, 'Slim Bendaali', 'mavoiture@gmail.com', '$2y$13$naHqmm2iVJ9wU8v/oedn.OhD/lvQWbc6UjN79CkJtIbOCIjzmf.Ui', 'Loueur'),
(5, 'Danny Aaaa', '123@hotmail.com', '$2y$13$lMmsdD8dBdrF4Swh6/3lne4WVSlAO7MZv8JN9ovM6fniOAh2oskI.', 'Loueur'),
(6, 'Jean Dubois', 'laurent@gmail.com', '$2y$13$bFdwbIQ1ho1RGjcPilIPZuwEMZrbXXXCR7qxNZ0rkealJlmpHeK6e', 'Loueur'),
(7, 'Ines PasdeNom', 'ines@gmail.com', '$2y$13$3E4dJ9tfSCTCCbO39zophuVS/6P82/It6PpEsPevTJTU5u/HYWRyK', 'Loueur'),
(8, 'Ibra Ahbib', 'ibra@gmail.com', '$2y$13$THJehi2pJ/AnA7InMhqoYOtkB8FTl6wZlrPBinyY4f3.r1mtHKV7a', 'client'),
(9, 'Laurent Manaudou', 'bibi@gmail.com', '$2y$13$kJbAMPY4D5yOkbgRxdc1F.Y9kr7bV86ifg45fWsNv5DCQ2t3plPsm', 'Admin'),
(10, 'Momo Chali', 'abc@gmail.com', '$2y$13$cYOdfxRBIanDH4E/X5cCbOzDO2fpBrYSUJw0jpLmjhL/GUXc0b4uG', 'client'),
(11, 'Ibra Ahbb', 'def@gmail.com', '$2y$13$iBVkxgqs4s3dDJGKoxwl9exk1ZSmuP44Qphv24qjovKlwZOtz77Di', 'client');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_EC224CAA79F37AE5` (`id_user_id`),
  ADD KEY `IDX_EC224CAAE5F14372` (`id_car_id`);

--
-- Index pour la table `car`
--
ALTER TABLE `car`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_773DE69D2EE78D6C` (`id_owner_id`);

--
-- Index pour la table `rememberme_token`
--
ALTER TABLE `rememberme_token`
  ADD PRIMARY KEY (`series`),
  ADD UNIQUE KEY `series` (`series`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `car`
--
ALTER TABLE `car`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `FK_EC224CAA79F37AE5` FOREIGN KEY (`id_user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_EC224CAAE5F14372` FOREIGN KEY (`id_car_id`) REFERENCES `car` (`id`);

--
-- Contraintes pour la table `car`
--
ALTER TABLE `car`
  ADD CONSTRAINT `FK_773DE69D2EE78D6C` FOREIGN KEY (`id_owner_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
