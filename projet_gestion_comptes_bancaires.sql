-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 10 mai 2023 à 15:37
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projet_gestion_comptes_bancaires`
--

-- --------------------------------------------------------

--
-- Structure de la table `agents`
--

CREATE TABLE `agents` (
  `id_agent` int(11) NOT NULL,
  `nom_agent` varchar(50) NOT NULL,
  `email_agent` varchar(255) NOT NULL,
  `mot_de_passe_agent` varchar(255) NOT NULL,
  `telephone_agent` int(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `agents`
--

INSERT INTO `agents` (`id_agent`, `nom_agent`, `email_agent`, `mot_de_passe_agent`, `telephone_agent`) VALUES
(1, 'Nourdine', 'mohamed160299@gmail.com', '12345678', 33332023);

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id_client` int(11) NOT NULL,
  `nom_client` varchar(50) NOT NULL,
  `prenom_client` varchar(50) NOT NULL,
  `email_client` varchar(255) NOT NULL,
  `mot_de_passe_client` varchar(255) NOT NULL,
  `telephone_client` int(15) DEFAULT NULL,
  `adresse_client` varchar(255) DEFAULT NULL,
  `status_client` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id_client`, `nom_client`, `prenom_client`, `email_client`, `mot_de_passe_client`, `telephone_client`, `adresse_client`, `status_client`) VALUES
(8, 'baba ', 'mine', 'bib1i@gn.j', '1234', 2147483647, NULL, 'DEL'),
(9, 'mohamed ', 'nourdine', 'mo@snim.mr', '1234', 34567886, NULL, NULL),
(10, 'Ebnou', 'Yeslem', 'yeslemebnou@google.com', '1234', 12345678, NULL, 'DEL'),
(11, 'Lassana', 'Hamady', 'lassana@gmail.com', '1234', 2147483647, NULL, NULL),
(12, 'maerouf', 'Douwah', 'marouf@gb.l', '', 2147483647, NULL, NULL),
(13, 'maerouf1', 'Douwah', 'marouf@gb.li', '', 2147483647, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `compte_bancaire`
--

CREATE TABLE `compte_bancaire` (
  `numero_compte` int(11) NOT NULL,
  `solde` decimal(10,2) DEFAULT NULL,
  `id_client` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `compte_bancaire`
--

INSERT INTO `compte_bancaire` (`numero_compte`, `solde`, `id_client`) VALUES
(1213, 300.00, 8),
(4567, 150.00, 8),
(9898, 895000.00, 9),
(12134, 2200.00, 8),
(23456, 0.00, 8),
(77777, 400.00, 8),
(102322, 0.00, 9),
(121345, 1100.00, 8),
(2345678, 900000.00, 9),
(4256326, 0.00, 8),
(8765432, 6000.00, 10),
(54567543, 566.00, 8),
(98987876, 100000.00, 8),
(123456789, 294600.00, 11);

-- --------------------------------------------------------

--
-- Structure de la table `demandes_creation_comptes_clients`
--

CREATE TABLE `demandes_creation_comptes_clients` (
  `id_demande` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `telephone` int(15) NOT NULL,
  `piece_identite` varchar(50) NOT NULL,
  `date_demande` timestamp NOT NULL DEFAULT current_timestamp(),
  `etat` varchar(50) DEFAULT 'en_attente',
  `numero_piece_identite` varchar(50) NOT NULL,
  `traitee` tinyint(1) NOT NULL DEFAULT 0,
  `mot_de_passe_client` varchar(255) NOT NULL,
  `id_client` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `demandes_creation_comptes_clients`
--

INSERT INTO `demandes_creation_comptes_clients` (`id_demande`, `nom`, `prenom`, `email`, `adresse`, `telephone`, `piece_identite`, `date_demande`, `etat`, `numero_piece_identite`, `traitee`, `mot_de_passe_client`, `id_client`) VALUES
(14, 'baba1', 'mine1', 'bib1i@gn.j', 'c47', 2147483647, 'carte_identite', '2023-04-20 19:13:44', 'approuvee', '09876567', 0, '    ', NULL),
(16, 'ni', 'pp', 'mo@snim.mr', 'c48', 34567887, 'passport', '2023-04-21 19:57:04', 'approuvee', '23456789087654', 0, '1234', NULL),
(17, 'Ebnou', 'Yeslem', 'yeslemebnou@google.com', 'bizerte', 12345678, 'passport', '2023-04-23 12:52:35', 'approuvee', 'Bo34567839876546738', 0, '12345', NULL),
(18, 'Lassana', 'Hamady', 'lassana@gmail.com', 'bassra', 2147483647, 'passeport', '2023-05-09 22:48:47', 'approuvee', 'BI567898t5', 0, '12345678', NULL),
(20, 'maerouf', 'Douwah', 'marouf@gb.l', 'c47', 2147483647, 'carte_identite', '2023-05-10 01:24:37', 'approuvee', '4567890', 0, '    ', NULL),
(21, 'maerouf1', 'Douwah', 'marouf@gb.li', 'c47', 2147483647, 'carte_identite', '2023-05-10 01:26:05', 'approuvee', '4567890', 0, '1234', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `demande_carnetcheque`
--

CREATE TABLE `demande_carnetcheque` (
  `numero_demande` int(11) NOT NULL,
  `etat_demande` varchar(50) DEFAULT NULL,
  `numero_compte` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `demande_carnetcheque`
--

INSERT INTO `demande_carnetcheque` (`numero_demande`, `etat_demande`, `numero_compte`) VALUES
(6, 'approuvee', 9898),
(7, 'en_attente', 102322),
(8, 'en_attente', 2345678);

-- --------------------------------------------------------

--
-- Structure de la table `demande_pret`
--

CREATE TABLE `demande_pret` (
  `numero_demande` int(11) NOT NULL,
  `etat_demande` varchar(50) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `numero_compte` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `demande_pret`
--

INSERT INTO `demande_pret` (`numero_demande`, `etat_demande`, `montant`, `numero_compte`) VALUES
(1, 'Payé', 400.00, 1213),
(2, 'Payé', 400.00, 1213),
(3, 'Payé', 100000.00, 1213),
(6, 'Payé', 400.00, 9898),
(7, 'Payé', 100000.00, 2345678);

-- --------------------------------------------------------

--
-- Structure de la table `operation`
--

CREATE TABLE `operation` (
  `id_operation` int(11) NOT NULL,
  `type_operation` varchar(50) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `date_operation` date DEFAULT NULL,
  `numero_compte` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `operation`
--

INSERT INTO `operation` (`id_operation`, `type_operation`, `montant`, `date_operation`, `numero_compte`) VALUES
(4, 'Transfert', 1000.00, '2023-04-21', 1213),
(5, 'Transfert', 50.00, '2023-04-21', 1213),
(6, 'Transfert', 50.00, '2023-04-21', 1213),
(7, 'Transfert', 500.00, '2023-04-21', 1213),
(8, 'Transfert', 1300.00, '2023-04-21', 1213),
(9, 'Retrait', 50.00, '2023-04-22', 1213),
(10, 'pret', 400.00, '2023-04-22', 1213),
(11, 'Paiement de prêt', 400.00, '2023-04-23', 1213),
(12, 'Paiement de prêt', 400.00, '2023-04-23', 1213),
(13, 'Paiement de prêt', 400.00, '2023-04-23', 1213),
(14, 'Paiement de prêt', 400.00, '2023-04-23', 1213),
(15, 'Paiement de prêt', 400.00, '2023-04-23', 1213),
(16, 'Paiement de prêt', 100000.00, '2023-04-23', 1213),
(17, 'Paiement de prêt', 400.00, '2023-04-23', 1213),
(18, 'pret', 100000.00, '2023-04-23', 1213),
(19, 'Depot', 300750.00, '2023-04-23', 1213),
(20, 'Paiement de prêt', 100000.00, '2023-04-23', 1213),
(21, 'Paiement de prêt', 400.00, '2023-04-23', 1213),
(22, 'Depot', 35000.00, '2023-05-01', 8765432),
(23, 'Depot', 500.00, '2023-05-01', 8765432),
(24, 'Depot', 500.00, '2023-05-01', 8765432),
(25, 'Depot', 50.00, '2023-05-01', 8765432),
(26, 'Depot', 200.00, '2023-05-01', 9898),
(27, 'Depot', 10000.00, '2023-05-01', 8765432),
(28, 'Depot', 500.00, '2023-05-01', 8765432),
(29, 'Retrait', 500.00, '2023-05-01', 8765432),
(30, 'Depot', 1000.00, '2023-05-01', 8765432),
(31, 'Retrait', 1000.00, '2023-05-01', 8765432),
(32, 'Depot', 1000.00, '2023-05-01', 8765432),
(33, 'Depot', 1000.00, '2023-05-01', 8765432),
(34, 'Depot', 1000.00, '2023-05-01', 8765432),
(35, 'Depot', 1000.00, '2023-05-01', 8765432),
(36, 'Retrait', 5000.00, '2023-05-01', 8765432),
(37, 'Retrait', 50.00, '2023-05-01', 8765432),
(38, 'Transfert', 50.00, '2023-05-01', 8765432),
(39, 'Transfert', 50.00, '2023-05-01', 8765432),
(40, 'Transfert', 100.00, '2023-05-01', 8765432),
(41, 'Depot', 1200.00, '2023-05-02', 8765432),
(42, 'Depot', 200.00, '2023-05-02', 9898),
(43, 'Depot', 200.00, '2023-05-02', 9898),
(44, 'Depot', 10.00, '2023-05-02', 4567),
(45, 'Depot', 10.00, '2023-05-02', 4567),
(46, 'Depot', 10.00, '2023-05-02', 4567),
(47, 'Depot', 10.00, '2023-05-02', 4567),
(48, 'Depot', 10.00, '2023-05-02', 4567),
(49, 'Depot', 10.00, '2023-05-02', 4567),
(50, 'Depot', 10.00, '2023-05-02', 4567),
(51, 'Depot', 10.00, '2023-05-02', 4567),
(52, 'Depot', 10.00, '2023-05-02', 4567),
(53, 'Depot', 10.00, '2023-05-02', 4567),
(54, 'Depot', 100300.00, '2023-05-02', 1213),
(55, 'Depot', 10.00, '2023-05-03', 4567),
(56, 'Depot', 10.00, '2023-05-03', 4567),
(57, 'Depot', 10.00, '2023-05-03', 4567),
(58, 'Depot', 10.00, '2023-05-03', 4567),
(59, NULL, 100.00, '2023-05-08', NULL),
(60, NULL, 100.00, '2023-05-08', NULL),
(61, NULL, 100.00, '2023-05-08', NULL),
(62, NULL, 100.00, '2023-05-08', NULL),
(63, NULL, 100.00, '2023-05-08', NULL),
(64, NULL, 100.00, '2023-05-08', NULL),
(65, 'Transfert', 100.00, '2023-05-08', 102322),
(66, 'Transfert', 100.00, '2023-05-08', 102322),
(67, 'Transfert', 100.00, '2023-05-08', 102322),
(70, 'Demande pret', 400.00, '2023-05-08', 9898),
(71, 'Transfert', 1000.00, '2023-05-09', 9898),
(72, 'Demande pret', 100000.00, '2023-05-09', 2345678),
(73, 'pret', 400.00, '2023-05-09', 9898),
(74, 'Transfert', 90000.00, '2023-05-10', 8765432),
(75, 'Paiement de prêt', 400.00, '2023-05-10', 9898),
(76, 'pret', 100000.00, '2023-05-10', 2345678),
(77, 'Paiement de prêt', 100000.00, '2023-05-10', 2345678),
(78, 'Transfert', 91000.00, '2023-05-10', 9898),
(79, 'Transfert', 94500.00, '2023-05-10', 102322),
(80, 'Transfert', 94500.00, '2023-05-10', 9898),
(81, 'Transfert', 100000.00, '2023-05-10', 2345678),
(82, 'Depot', 1000000.00, '2023-05-10', 9898),
(83, 'Retrait', 5000.00, '2023-05-10', 9898),
(84, 'Transfert', 100000.00, '2023-05-10', 9898);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id_agent`),
  ADD UNIQUE KEY `email_agent` (`email_agent`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id_client`),
  ADD UNIQUE KEY `email_client` (`email_client`);

--
-- Index pour la table `compte_bancaire`
--
ALTER TABLE `compte_bancaire`
  ADD PRIMARY KEY (`numero_compte`),
  ADD KEY `fk_comptes_bancaires_clients` (`id_client`);

--
-- Index pour la table `demandes_creation_comptes_clients`
--
ALTER TABLE `demandes_creation_comptes_clients`
  ADD PRIMARY KEY (`id_demande`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_client` (`id_client`);

--
-- Index pour la table `demande_carnetcheque`
--
ALTER TABLE `demande_carnetcheque`
  ADD PRIMARY KEY (`numero_demande`),
  ADD KEY `fk_id_client` (`numero_compte`);

--
-- Index pour la table `demande_pret`
--
ALTER TABLE `demande_pret`
  ADD PRIMARY KEY (`numero_demande`),
  ADD KEY `fk_numero_compte` (`numero_compte`);

--
-- Index pour la table `operation`
--
ALTER TABLE `operation`
  ADD PRIMARY KEY (`id_operation`),
  ADD KEY `numero_compte` (`numero_compte`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `agents`
--
ALTER TABLE `agents`
  MODIFY `id_agent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `demandes_creation_comptes_clients`
--
ALTER TABLE `demandes_creation_comptes_clients`
  MODIFY `id_demande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `demande_carnetcheque`
--
ALTER TABLE `demande_carnetcheque`
  MODIFY `numero_demande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `demande_pret`
--
ALTER TABLE `demande_pret`
  MODIFY `numero_demande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `operation`
--
ALTER TABLE `operation`
  MODIFY `id_operation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `compte_bancaire`
--
ALTER TABLE `compte_bancaire`
  ADD CONSTRAINT `fk_comptes_bancaires_clients` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`);

--
-- Contraintes pour la table `demandes_creation_comptes_clients`
--
ALTER TABLE `demandes_creation_comptes_clients`
  ADD CONSTRAINT `demandes_creation_comptes_clients_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`);

--
-- Contraintes pour la table `demande_pret`
--
ALTER TABLE `demande_pret`
  ADD CONSTRAINT `fk_numero_compte` FOREIGN KEY (`numero_compte`) REFERENCES `compte_bancaire` (`numero_compte`);

--
-- Contraintes pour la table `operation`
--
ALTER TABLE `operation`
  ADD CONSTRAINT `operation_ibfk_1` FOREIGN KEY (`numero_compte`) REFERENCES `compte_bancaire` (`numero_compte`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
