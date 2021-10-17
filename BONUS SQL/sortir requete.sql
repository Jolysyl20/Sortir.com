-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 13 oct. 2021 à 14:49
-- Version du serveur :  5.7.31
-- Version de PHP : 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `sortir`
--

DELIMITER $$
--
-- Procédures
--
DROP PROCEDURE IF EXISTS `Archiver_sortie`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Archiver_sortie` ()  NO SQL
begin
DECLARE idd Int DEFAULT 0;
DECLARE nl varChar(50);
DECLARE ne varchar(50);
DECLARE nsi varchar(50);
DECLARE nso varchar(50);
DECLARE dd datetime;
DECLARE duree int;
DECLARE dc datetime;
DECLARE nbIns int;
DECLARE des longtext;
DECLARE pn varchar(50);
DECLARE pemail varchar(50);
DECLARE lstPart json;

DECLARE c CURSOR for
SELECT sortie.id, l.nom_lieu, e.libelle, s.nom, sortie.nom, sortie.date_debut, sortie.duree, sortie.date_cloture, sortie.nb_inscription_max,
sortie.description_infos, p.nom, p.mail,
(SELECT JSON_ARRAYAGG(JSON_OBJECT('nom',pa.nom, 'prenom', pa.prenom, 'mail', pa.mail)) as t from inscription i
INNER join participant pa on pa.id = i.no_participant_id WHERE i.no_sortie_id = sortie.id) as t
FROM sortie
inner JOIN lieu l on l.id = sortie.no_lieu_id
INNER JOIN etat e on e.id = sortie.no_etat_id
INNER JOIN site s on s.id = sortie.site_sortie_id
INNER JOIN participant p on p.id = sortie.organisateur_id
where e.libelle = 'passee'
and now() >= ADDDATE(sortie.date_debut, INTERVAL 1 MONTH)+ sortie.duree;

open c;
l: LOOP
FETCH c INTO idd,nl,ne,nsi,nso,dd,duree,dc,nbIns,des,pn,pemail,lstPart;
INSERT INTO archive (archive.nom_lieu,archive.etat_sortie,archive.nom_site,archive.nom_sortie,archive.date_debut_sortie,archive.duree_sortie,archive.date_cloture_inscription,archive.nn_inscription_max_sortie,archive.description_sortie,archive.nom_organisateur,archive.email_organisateur,archive.participants_inscrit)
VALUES (nl,ne,nsi,nso,dd,duree,dc,nbIns,des,pn,pemail,lstPart);
DELETE FROM inscription WHERE inscription.no_sortie_id = idd;

DELETE FROM sortie WHERE sortie.id = idd;
end LOOP;
CLOSE c;
end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `archive`
--

DROP TABLE IF EXISTS `archive`;
CREATE TABLE IF NOT EXISTS `archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_lieu` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `etat_sortie` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_site` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_sortie` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut_sortie` datetime NOT NULL,
  `duree_sortie` int(11) NOT NULL,
  `date_cloture_inscription` datetime NOT NULL,
  `nn_inscription_max_sortie` int(11) NOT NULL,
  `description_sortie` longtext COLLATE utf8mb4_unicode_ci,
  `nom_organisateur` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_organisateur` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `participants_inscrit` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `archive`
--


-- --------------------------------------------------------

--
-- Structure de la table `etat`
--

DROP TABLE IF EXISTS `etat`;
CREATE TABLE IF NOT EXISTS `etat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etat`
--

INSERT INTO `etat` (`id`, `libelle`) VALUES
(1, 'creee'),
(2, 'en cours'),
(3, 'cloturee'),
(4, 'annulee'),
(5, 'Ouverte'),
(7, 'passee');

-- --------------------------------------------------------

--
-- Structure de la table `inscription`
--

DROP TABLE IF EXISTS `inscription`;
CREATE TABLE IF NOT EXISTS `inscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_sortie_id` int(11) NOT NULL,
  `no_participant_id` int(11) NOT NULL,
  `date_inscription` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5E90F6D61BFE0BBF` (`no_sortie_id`),
  KEY `IDX_5E90F6D6DA33DAE9` (`no_participant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lieu`
--

DROP TABLE IF EXISTS `lieu`;
CREATE TABLE IF NOT EXISTS `lieu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `villes_no_ville_id` int(11) NOT NULL,
  `nom_lieu` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rue` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2F577D5927E30153` (`villes_no_ville_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `lieu`
--



-- --------------------------------------------------------

--
-- Structure de la table `participant`
--

DROP TABLE IF EXISTS `participant`;
CREATE TABLE IF NOT EXISTS `participant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL,
  `site_participant_id` int(11) DEFAULT NULL,
  `image_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D79F6B1186CC499D` (`pseudo`),
  KEY `IDX_D79F6B11DC4AE911` (`site_participant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `participant`
--


-- --------------------------------------------------------

--
-- Structure de la table `reinit_mot_de_passe`
--

DROP TABLE IF EXISTS `reinit_mot_de_passe`;
CREATE TABLE IF NOT EXISTS `reinit_mot_de_passe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `participant_id` int(11) NOT NULL,
  `date_expiration` datetime NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_75FC29489D1C3019` (`participant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `site`
--

DROP TABLE IF EXISTS `site`;
CREATE TABLE IF NOT EXISTS `site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `site`
--

INSERT INTO `site` (`id`, `nom`) VALUES
(1, 'Nantes'),
(2, 'Niort'),
(3, 'Rennes'),
(4, 'Angers');

-- --------------------------------------------------------

--
-- Structure de la table `sortie`
--

DROP TABLE IF EXISTS `sortie`;
CREATE TABLE IF NOT EXISTS `sortie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` datetime NOT NULL,
  `duree` int(11) DEFAULT NULL,
  `date_cloture` datetime NOT NULL,
  `nb_inscription_max` int(11) NOT NULL,
  `description_infos` longtext COLLATE utf8mb4_unicode_ci,
  `no_ville_id` int(11) NOT NULL,
  `organisateur_id` int(11) NOT NULL,
  `no_lieu_id` int(11) NOT NULL,
  `no_etat_id` int(11) NOT NULL,
  `site_sortie_id` int(11) DEFAULT NULL,
  `motif` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3C3FD3F2263F02D7` (`no_ville_id`),
  KEY `IDX_3C3FD3F2D936B2FA` (`organisateur_id`),
  KEY `IDX_3C3FD3F2D4A63F18` (`no_lieu_id`),
  KEY `IDX_3C3FD3F2B34AAA2B` (`no_etat_id`),
  KEY `IDX_3C3FD3F2AA78AF26` (`site_sortie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sortie`
--


-- --------------------------------------------------------

--
-- Structure de la table `ville`
--

DROP TABLE IF EXISTS `ville`;
CREATE TABLE IF NOT EXISTS `ville` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_ville` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_postal` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ville`
--

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD CONSTRAINT `FK_5E90F6D61BFE0BBF` FOREIGN KEY (`no_sortie_id`) REFERENCES `sortie` (`id`),
  ADD CONSTRAINT `FK_5E90F6D6DA33DAE9` FOREIGN KEY (`no_participant_id`) REFERENCES `participant` (`id`);

--
-- Contraintes pour la table `lieu`
--
ALTER TABLE `lieu`
  ADD CONSTRAINT `FK_2F577D5927E30153` FOREIGN KEY (`villes_no_ville_id`) REFERENCES `ville` (`id`);

--
-- Contraintes pour la table `participant`
--
ALTER TABLE `participant`
  ADD CONSTRAINT `FK_D79F6B11DC4AE911` FOREIGN KEY (`site_participant_id`) REFERENCES `site` (`id`);

--
-- Contraintes pour la table `reinit_mot_de_passe`
--
ALTER TABLE `reinit_mot_de_passe`
  ADD CONSTRAINT `FK_75FC29489D1C3019` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`id`);

--
-- Contraintes pour la table `sortie`
--
ALTER TABLE `sortie`
  ADD CONSTRAINT `FK_3C3FD3F2263F02D7` FOREIGN KEY (`no_ville_id`) REFERENCES `ville` (`id`),
  ADD CONSTRAINT `FK_3C3FD3F2AA78AF26` FOREIGN KEY (`site_sortie_id`) REFERENCES `site` (`id`),
  ADD CONSTRAINT `FK_3C3FD3F2B34AAA2B` FOREIGN KEY (`no_etat_id`) REFERENCES `etat` (`id`),
  ADD CONSTRAINT `FK_3C3FD3F2D4A63F18` FOREIGN KEY (`no_lieu_id`) REFERENCES `lieu` (`id`),
  ADD CONSTRAINT `FK_3C3FD3F2D936B2FA` FOREIGN KEY (`organisateur_id`) REFERENCES `participant` (`id`);

DELIMITER $$
--
-- Évènements
--
DROP EVENT `cloture_sortie`$$
CREATE DEFINER=`root`@`localhost` EVENT `cloture_sortie` ON SCHEDULE EVERY '0:1' HOUR_MINUTE STARTS '2021-10-13 13:51:59' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE sortie set sortie.no_etat_id = (SELECT id FROM `etat` WHERE libelle='passee') WHERE (sortie.date_debut + sortie.duree) < now()$$

DROP EVENT `cloture`$$
CREATE DEFINER=`root`@`localhost` EVENT `cloture` ON SCHEDULE EVERY 1 WEEK STARTS '2021-10-13 16:14:06' ON COMPLETION NOT PRESERVE ENABLE DO call cloture_sortie()$$

DROP EVENT `passage_en_cours`$$
CREATE DEFINER=`root`@`localhost` EVENT `passage_en_cours` ON SCHEDULE EVERY '0:1' HOUR_MINUTE STARTS '2021-10-13 16:36:05' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE sortie set sortie.no_etat_id = (SELECT id FROM `etat` WHERE libelle='en cours') WHERE sortie.date_debut <= now() AND (sortie.date_debut + sortie.duree) >= now()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
