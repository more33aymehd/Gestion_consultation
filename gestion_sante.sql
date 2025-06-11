-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2025 at 02:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestion_sante`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id_admin` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `commandes`
--

CREATE TABLE `commandes` (
  `id_commande` int(11) NOT NULL,
  `id_patient` int(11) DEFAULT NULL,
  `id_medicament` int(11) DEFAULT NULL,
  `id_pharmacy` int(11) DEFAULT NULL,
  `date_commande` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--

CREATE TABLE `consultations` (
  `id_consultation` int(11) NOT NULL,
  `id_patient` int(11) DEFAULT NULL,
  `id_medecin` int(11) DEFAULT NULL,
  `contenu` text DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `prix` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultations`
--

INSERT INTO `consultations` (`id_consultation`, `id_patient`, `id_medecin`, `contenu`, `date`, `prix`) VALUES
(1, 6, 1, 'hypertension', '2025-06-11 04:46:50', 15000.00),
(2, 6, 1, 'mal', '2025-06-11 09:33:10', 15000.00),
(3, 6, 1, 'j\'ai mal', '2025-06-11 13:03:42', 15000.00);

-- --------------------------------------------------------

--
-- Table structure for table `groupes`
--

CREATE TABLE `groupes` (
  `id_groupe` int(11) NOT NULL,
  `nom_groupe` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `id_medecin` int(11) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groupes`
--

INSERT INTO `groupes` (`id_groupe`, `nom_groupe`, `type`, `description`, `id_medecin`, `photo`) VALUES
(1, 'Groupe Coeur Sain', 'Éducation Cardiaque', 'Sensibilisation sur les maladies cardiovasculaires', 1, 'coeur.jpg'),
(2, 'Groupe Santé Femme', 'Santé Féminine', 'Discussions sur la grossesse, les règles, et la ménopause', 2, 'femme.jpg'),
(3, 'Groupe Enfants Bien Portants', 'Pédiatrie', 'Conseils pour les parents sur la santé des enfants', 3, 'enfants.jpg'),
(4, 'Groupe Peau Claire', 'Dermatologie', 'Traitements et soins dermatologiques adaptés au climat tropical', 4, 'derma.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `hopitaux`
--

CREATE TABLE `hopitaux` (
  `id_hopital` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `adresse` text DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medecins`
--

CREATE TABLE `medecins` (
  `id_medecin` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `adresse` text DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `specialite` varchar(100) DEFAULT NULL,
  `affiliation` varchar(150) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `tarif` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medecins`
--

INSERT INTO `medecins` (`id_medecin`, `nom`, `email`, `adresse`, `telephone`, `specialite`, `affiliation`, `photo`, `mot_de_passe`, `tarif`) VALUES
(1, 'Dr. Essomba', 'essomba.alex@gmail.com', 'Centre Médical la Cathédrale, Yaoundé', '698570533', 'Cardiologue', 'Hôpital Central de Yaoundé', 'essomba.jpg', 'drpass1', 15000.00),
(2, 'Dr. Manga', 'manga.lydia@gmail.com', 'Hôpital Général de Douala', '678123456', 'Gynécologue', 'Hôpital Général de Douala', 'manga.jpg', 'lydiapass', 20000.00),
(3, 'Dr. Njoya', 'njoya.mohamed@gmail.com', 'Centre de Santé de Bafoussam', '693456789', 'Pédiatre', 'Centre Régional de Santé Ouest', 'njoya.jpg', 'njoyapass', 12000.00),
(4, 'Dr. Koumba', 'koumba.sylvie@gmail.com', 'Polyclinique Bastos, Yaoundé', '699112233', 'Dermatologue', 'Polyclinique Bastos', 'koumba.jpg', 'derma123', 18000.00),
(5, 'Mbarga Daniel', 'daniel.mbarga@cliniqueyaounde.cm', 'Rue Elig-Essono, Yaoundé', '670123456', 'Cardiologue', 'Clinique du Centre Yaoundé', 'images/mbarga.jpg', '15cbf0d3fcb06da3bdf98d0370a38f00343d0747eecdf416d27556c0f3812fd6', 15000.00),
(6, 'Ngono Mireille', 'mireille.ngono@hopitaldouala.cm', 'Bonapriso, Douala', '699876543', 'Gynécologue', 'Hôpital Général de Douala', 'images/ngono.jpg', '430893cdce2e7074821444975c1b6929f88957c6aa63f9e335673b61d241d1ef', 12000.00),
(7, 'Abessolo Serge', 'serge.abessolo@cmcgaroua.cm', 'Avenue du Marché, Garoua', '655112233', 'Pédiatre', 'Centre Médical Communal Garoua', 'images/abessolo.jpg', '14c28260dd730c72801cd9eea54aeee1c7a059bc969fae03b08fe23ca9ff8ec4', 10000.00),
(8, 'Biloa Nadège', 'nadege.biloa@cliniqueberthua.cm', 'Quartier Mokolo, Bertoua', '678334455', 'Dermatologue', 'Clinique Berthua', 'images/biloa.jpg', 'c955f01321b0761f22aee14b339a477c430ccdc9513aa79943b6d49599b245e5', 13000.00),
(9, 'Tchouang Victor', 'victor.tchouang@hopitalbafoussam.cm', 'Marché A, Bafoussam', '690556677', 'Ophtalmologue', 'Hôpital Régional de Bafoussam', 'images/tchouang.jpg', 'fc2c40745e850b0bd81ea703c9320073e2b29003f63a813fbf061db1ee0ae8a0', 11000.00);

-- --------------------------------------------------------

--
-- Table structure for table `medicaments`
--

CREATE TABLE `medicaments` (
  `id_medicament` int(11) NOT NULL,
  `id_pharmacy` int(11) DEFAULT NULL,
  `nom` varchar(150) NOT NULL,
  `quantite` int(11) DEFAULT 0,
  `prix` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicaments`
--

INSERT INTO `medicaments` (`id_medicament`, `id_pharmacy`, `nom`, `quantite`, `prix`) VALUES
(6, 1, 'Paracétamol 500mg', 100, 250.00),
(7, 1, 'Amoxicilline 500mg', 80, 400.00),
(8, 2, 'Quinine 250mg', 50, 600.00),
(9, 2, 'Ibuprofène 400mg', 70, 300.00),
(10, 3, 'Azithromycine 250mg', 40, 1500.00);

-- --------------------------------------------------------

--
-- Table structure for table `membres_groupe`
--

CREATE TABLE `membres_groupe` (
  `id_groupe` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL,
  `id_medecin` int(11) DEFAULT NULL,
  `id_patient` int(11) DEFAULT NULL,
  `expediteur` enum('medecin','patient') NOT NULL,
  `message` text NOT NULL,
  `date_envoi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id_patient` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL CHECK (`age` >= 0),
  `sexe` enum('M','F') NOT NULL,
  `adresse` text DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `maladie` text DEFAULT NULL,
  `statut` enum('actif','inactif','décédé') DEFAULT 'actif',
  `photo` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id_patient`, `nom`, `age`, `sexe`, `adresse`, `email`, `telephone`, `maladie`, `statut`, `photo`, `mot_de_passe`) VALUES
(1, 'Nana', 32, 'F', 'Biyem-Assi, Yaoundé', 'nana.marie@gmail.com', '670112233', 'Hypertension', 'actif', 'nana.jpg', 'motdepasse123'),
(2, 'Tchoumi', 45, 'M', 'Bonabéri, Douala', 'tchoumi.paul@gmail.com', '690998877', 'Diabète', 'actif', 'tchoumi.jpg', '123456'),
(3, 'Mbarga', 27, 'M', 'Mvog-Mbi, Yaoundé', 'mbarga.joel@gmail.com', '674332211', 'Paludisme chronique', 'actif', 'joel.jpg', 'pass2024'),
(4, 'Ngono', 60, 'F', 'Ebolowa centre', 'ngono.rose@gmail.com', '662345678', 'Arthrose', 'actif', 'rose.jpg', 'rosepass'),
(6, 'morel', 12, 'M', 'oyomabang', 'aymerickngassa@icloud.com', '698570533', 'hypertension', 'actif', 'photo.jpg', '$2y$10$urV4PKawZU.lOeaJCW5FA.dfCglvtuDfIh49X2d1XBXamls.ZD/cO');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacies`
--

CREATE TABLE `pharmacies` (
  `id_pharmacy` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `adresse` text DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacies`
--

INSERT INTO `pharmacies` (`id_pharmacy`, `nom`, `adresse`, `telephone`) VALUES
(1, 'Pharmacie du Marché Central', 'Marché Central, Yaoundé', '690001122'),
(2, 'Pharmacie Bastos', 'Bastos, Yaoundé', '699443322'),
(3, 'Pharmacie Akwa Santé', 'Rue Joss, Douala', '677112233');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_patient` (`id_patient`),
  ADD KEY `id_medicament` (`id_medicament`),
  ADD KEY `id_pharmacy` (`id_pharmacy`);

--
-- Indexes for table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`id_consultation`),
  ADD KEY `id_patient` (`id_patient`),
  ADD KEY `id_medecin` (`id_medecin`);

--
-- Indexes for table `groupes`
--
ALTER TABLE `groupes`
  ADD PRIMARY KEY (`id_groupe`),
  ADD KEY `id_medecin` (`id_medecin`);

--
-- Indexes for table `hopitaux`
--
ALTER TABLE `hopitaux`
  ADD PRIMARY KEY (`id_hopital`);

--
-- Indexes for table `medecins`
--
ALTER TABLE `medecins`
  ADD PRIMARY KEY (`id_medecin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `medicaments`
--
ALTER TABLE `medicaments`
  ADD PRIMARY KEY (`id_medicament`),
  ADD KEY `id_pharmacy` (`id_pharmacy`);

--
-- Indexes for table `membres_groupe`
--
ALTER TABLE `membres_groupe`
  ADD PRIMARY KEY (`id_groupe`,`id_patient`),
  ADD KEY `id_patient` (`id_patient`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `id_medecin` (`id_medecin`),
  ADD KEY `id_patient` (`id_patient`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id_patient`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pharmacies`
--
ALTER TABLE `pharmacies`
  ADD PRIMARY KEY (`id_pharmacy`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id_consultation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `groupes`
--
ALTER TABLE `groupes`
  MODIFY `id_groupe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hopitaux`
--
ALTER TABLE `hopitaux`
  MODIFY `id_hopital` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medecins`
--
ALTER TABLE `medecins`
  MODIFY `id_medecin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `medicaments`
--
ALTER TABLE `medicaments`
  MODIFY `id_medicament` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id_patient` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pharmacies`
--
ALTER TABLE `pharmacies`
  MODIFY `id_pharmacy` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patients` (`id_patient`) ON DELETE CASCADE,
  ADD CONSTRAINT `commandes_ibfk_2` FOREIGN KEY (`id_medicament`) REFERENCES `medicaments` (`id_medicament`) ON DELETE SET NULL,
  ADD CONSTRAINT `commandes_ibfk_3` FOREIGN KEY (`id_pharmacy`) REFERENCES `pharmacies` (`id_pharmacy`) ON DELETE SET NULL;

--
-- Constraints for table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `consultations_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patients` (`id_patient`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultations_ibfk_2` FOREIGN KEY (`id_medecin`) REFERENCES `medecins` (`id_medecin`) ON DELETE CASCADE;

--
-- Constraints for table `groupes`
--
ALTER TABLE `groupes`
  ADD CONSTRAINT `groupes_ibfk_1` FOREIGN KEY (`id_medecin`) REFERENCES `medecins` (`id_medecin`) ON DELETE SET NULL;

--
-- Constraints for table `medicaments`
--
ALTER TABLE `medicaments`
  ADD CONSTRAINT `medicaments_ibfk_1` FOREIGN KEY (`id_pharmacy`) REFERENCES `pharmacies` (`id_pharmacy`) ON DELETE SET NULL;

--
-- Constraints for table `membres_groupe`
--
ALTER TABLE `membres_groupe`
  ADD CONSTRAINT `membres_groupe_ibfk_1` FOREIGN KEY (`id_groupe`) REFERENCES `groupes` (`id_groupe`) ON DELETE CASCADE,
  ADD CONSTRAINT `membres_groupe_ibfk_2` FOREIGN KEY (`id_patient`) REFERENCES `patients` (`id_patient`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`id_medecin`) REFERENCES `medecins` (`id_medecin`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`id_patient`) REFERENCES `patients` (`id_patient`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
