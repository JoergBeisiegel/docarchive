-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 20. Apr 2018 um 10:10
-- Server-Version: 10.1.31-MariaDB
-- PHP-Version: 7.2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `document_archive`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories`
--

CREATE TABLE `categories` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Diverse'),
(2, 'Sport'),
(3, 'Weiterbildung'),
(4, 'Programmieren'),
(6, 'Fun');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `documents`
--

CREATE TABLE `documents` (
  `document_id` int(10) UNSIGNED NOT NULL,
  `document_name` varchar(50) NOT NULL,
  `document_title` varchar(30) NOT NULL,
  `document_description` varchar(255) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `documents`
--

INSERT INTO `documents` (`document_id`, `document_name`, `document_title`, `document_description`, `creation_date`) VALUES
(1, 'uploaded_documents/5ad86110a1165.pdf', 'Ein neues Dokument', 'Die ist eine Beschreibung, die etwas länger werden kann', '2018-04-19 09:27:44'),
(2, 'uploaded_documents/5ad862c7201a5.pdf', 'Neu 2', 'beschreibung 2', '2018-04-19 09:35:03'),
(7, 'uploaded_documents/5ad8643928c4a.pdf', 'geänderter titel', 'und eine Beschreibung', '2018-04-19 09:41:13'),
(8, 'uploaded_documents/5ad864c017b37.pdf', 'Die nächste Änderung!  Jetzt', 'auch in der Beschreibung', '2018-04-19 09:43:28'),
(19, 'uploaded_documents/5ad89d40c3b6d.pdf', 'Mal wieder', 'was neues', '2018-04-19 13:44:32');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `document_categories`
--

CREATE TABLE `document_categories` (
  `doc_number` int(10) UNSIGNED NOT NULL,
  `cat_number` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `document_categories`
--

INSERT INTO `document_categories` (`doc_number`, `cat_number`) VALUES
(7, 2),
(7, 3),
(8, 4),
(8, 6),
(8, 8),
(1, 1),
(1, 1),
(1, 2),
(2, 3),
(7, 1),
(7, 4),
(8, 1),
(8, 2),
(2, 8),
(19, 1),
(19, 2),
(19, 3);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indizes für die Tabelle `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`document_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT für Tabelle `documents`
--
ALTER TABLE `documents`
  MODIFY `document_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
