-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Giu 18, 2026 alle 22:35
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mybookshelf`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `book`
--

CREATE TABLE `book` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `category` enum('Book','Comics','Manga','Mhanwa') NOT NULL,
  `literary_genre` enum('Fantasy','Science Fiction','Mystery','Thriller','Romance','Historical','Horror','Dystopian','Contemporary Fiction','Action & Adventure','Graphic Novel','Poetry','Satire','Biography','Autobiography','Memoir','History','True Crime','Self-Help','Philosophy','Science & Technology','Religion & Spirituality','Business & Economics','Politics & Social Sciences','Health & Wellness','Psychological') NOT NULL,
  `language` enum('Italiano','English') NOT NULL,
  `isread` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `book`
--

INSERT INTO `book` (`id`, `title`, `author`, `category`, `literary_genre`, `language`, `isread`, `description`, `image`, `date_added`) VALUES
(1, 'Il principe', 'Niccolo Macchiavelli', 'Book', 'Politics & Social Sciences', 'Italiano', 0, 'Scritto da Niccolò Machiavelli nel 1513, \"Il Principe\" è il trattato politico più celebre del Rinascimento. L\'opera rompe con la tradizione medievale, separando la \"politica dalla morale\": il sovrano non deve essere necessariamente \"buono\", ma efficace nel mantenere lo Stato.\r\n\r\nMachiavelli introduce concetti rivoluzionari come la dialettica tra \"Virtù\" (capacità e ingegno) e \"Fortuna\" (il caso), sostenendo che il leader debba saper essere \"volpe\" per scoprire le trappole e \"leone\" per sbigottire i lupi. Attraverso uno stile asciutto e pragmatico, l\'autore analizza la realtà effettuale delle cose, consegnando ai posteri una riflessione cruda ma necessaria sul potere.', 'cover_69e4fa4d654e71.05969541.jpg', '2026-04-19 15:52:45'),
(2, 'Vinland Saga vol 1', 'Makoto Yukimura', 'Manga', 'Historical', 'Italiano', 1, 'Nel primo volume di \"Vinland Saga\", ambientato nell\'XI secolo, nel mondo dei vichinghi attraverso gli occhi di Thorfinn. Il ragazzo è un guerriero formidabile e colmo di odio che milita nella banda di mercenari guidata da Askeladd, l\'astuto comandante che anni prima uccise suo padre, Thors, durante un’imboscata. Thorfinn non cerca ricchezza o gloria, ma vive unicamente per ottenere un duello d\'onore e vendicare il genitore. Tra sanguinose invasioni in Inghilterra e intrighi politici, emerge il contrasto tra la violenza del presente e il sogno lontano di una terra pacifica chiamata Vinland.', 'cover_69e67edd911ee4.79247160.jpg', '2026-04-20 19:30:37'),
(4, 'Dune', 'Frank Herbrant', 'Book', 'Science Fiction', 'Italiano', 1, 'Dune è un romanzo di fantascienza, in un mondo nel quale i computer sono stati banditi e l\'uomo a sviluppato capacità sovraumane per sopravvivere.\r\nLa storia segue le vicende di Paul Artreides, che insieme alla sua famiglia si trasferisce nel pianeta di Arrakis, Dune. Fonte della risorsa più importante dell\'universo la spezia', 'cover_69e68badc3f3a7.87648369.png', '2026-04-20 20:25:17'),
(5, 'Note from the underground', 'Fëdor Dostoevskij', 'Book', 'Philosophy', 'English', 1, 'Fëdor Dostoevskij’s \"Notes from the Underground\" is a foundational existentialist novella presented as the rambling, bitter memoirs of a retired civil servant in St. Petersburg. The anonymous narrator, known as the **Underground Man**, rejects the prevailing rationalism and utopianism of his era, arguing that human beings possess an inherent, self-destructive need to assert their free will—even at the cost of their own advantage. Through his profound social alienation and spiteful internal monologue, the protagonist exposes the complexities of the human psyche, the paralysis of over-analysis, and the painful search for identity in a modernizing world.', 'cover_69fb526d515947.35187316.png', '2026-05-06 14:38:37'),
(6, 'Crime and Punishment', 'Fëdor Dostoevskij', 'Book', 'Philosophy', 'English', 0, '\"Crime and Punishment\" follows Rodion Raskolnikov, a destitute former student in St. Petersburg who formulates a dangerous theory: that \"extraordinary\" men are entitled to commit crimes for a higher purpose. To test this, he murders a cynical pawnbroker, only to descend into a feverish state of **paranoia and moral isolation**. The novel shifts from a psychological thriller into a profound exploration of guilt and redemption as Raskolnikov is pursued by the cunning investigator Porfiry Petrovich and eventually finds a path toward spiritual rebirth through the selfless Sonia Marmeladova. It remains a definitive study of the human conscience.', 'cover_69fb52badc1da1.81702217.png', '2026-05-06 14:39:54'),
(7, 'Lo squalificato ', 'Osamu Dazai', 'Book', 'Dystopian', 'Italiano', 1, '\"No Longer Human\" (Japanese: *Ningen Shikkaku*) is a hauntingly semi-autobiographical novel by Osamu Dazai that chronicles the life of **Yozo Oba**, a man who feels profoundly alienated from society. Unable to comprehend human behavior, Yozo adopts a mask of \"clowning\" to hide his intense anxiety and bridge the gap between himself and others. His life becomes a tragic spiral of drug addiction, failed relationships, and suicide attempts as he views himself as a \"disqualified\" human being. The work is a masterpiece of Japanese literature, capturing the crushing weight of **social estrangement** and the struggle for authenticity.', 'cover_69fb539d1e92a5.54522037.jpg', '2026-05-06 14:43:41'),
(8, 'Percy Jackson e gli Dei dell\'Olimpo: Il ladro di fulmini', 'Rick Riordan', 'Book', 'Fantasy', 'Italiano', 1, '\"Percy Jackson e gli Dei dell\'Olimpo: Il ladro di fulmini\" segue le avventure di Percy, un dodicenne con dislessia e iperattività che scopre di essere un **semidio**, figlio di Poseidone. Accusato ingiustamente di aver rubato la Folgore di Zeus, Percy deve intraprendere una missione attraverso l\'America moderna per recuperare l\'arma e prevenire una guerra catastrofica tra gli dei. Insieme ai suoi amici Annabeth e Grover, affronta creature mitologiche e scopre la realtà del **Campo Mezzosangue**, imparando che la vera forza risiede nell\'accettazione del proprio destino e nel valore dell\'amicizia.', 'cover_69fb542e1fee97.83164837.jpg', '2026-05-06 14:46:06');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
