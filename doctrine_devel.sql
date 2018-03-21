-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Hostiteľ: localhost
-- Čas generovania: St 21.Mar 2018, 19:23
-- Verzia serveru: 5.7.21-0ubuntu0.16.04.1
-- Verzia PHP: 7.0.28-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `doctrine_devel`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `medicine`
--

CREATE TABLE `medicine` (
  `id` int(11) NOT NULL,
  `id_sukl` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(1) NOT NULL,
  `contribution` double NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `medicine`
--

INSERT INTO `medicine` (`id`, `id_sukl`, `name`, `description`, `type`, `contribution`, `price`) VALUES
(1, '35458', 'IBALGIN 400', 'Liek s protizápalovým a analgetickým účinkom. Napomáha pri bolestiach hlavy, zubov, svalov. Znižuje teplotu. Zmierňuje opuchy.', 0, 0, 2.99),
(2, '96849', 'TALCID', 'Liek na neutralizáciu žalúdočnej kyseliny. Napomáha eliminovať príznaky-reflux, pálenie záhy, vredy. Žuvacie tablety.', 0, 0, 3.99),
(3, 'X31018', 'Dr.Max ProbioMaxík', 'Výživový doplnok obsahujúci komplex laktobacilov a bifidobakterií pre deti.', 0, 0, 10.99),
(4, '00011', 'ACYLPYRIN', 'Liek proti horúčke a bolesti hlavy, zubov, svalov, kĺbov, chrbtice a pod. Možu ho užívať dospelí aj mladiství.', 0, 0, 1.29),
(5, '0381A', 'PARALEN GRIP chrípka a bolesť', 'Liek obsahujúci paracetamol, kofeín a fenylefrín. Znižuje teplotu, tlmí bolesti svalov, kĺbov, hlavy, zmenšuje opuch nosa. Kofeín potláča únavu, pôsobí ako stimulant.', 0, 0, 5.99),
(6, '9320A', 'VALETOL', 'Liek proti bolesti, horúčke a zápalu. Obsahuje paracetamol, propyfenazón a kofeín. Používa sa pri bolesti zubov, hlavy, pri migréne, neuralgii a pod.', 1, 0, 3.49),
(7, '26747', 'Dorithricin', 'Na liečbu zápalu a bolesti hrdla spojenej s bolestivým prehĺtaním a na liečbu zápalu ústnej dutiny, hrtana, hltana a ďasien vyvolaného baktériami.', 1, 0, 6.99),
(8, '00676', 'CALCIUM CHLORATUM-TEVA', 'Liek vo forme roztoku obsahujúci vápenatú soľ. Priaznivo vplýva na pevnosť kostí, podporuje hojenie zlomenín. Vhodný pri nedostatku vápnika .', 1, 0, 4.79),
(9, '33655', 'MUCOSOLVAN', 'Liek vo forme sirupu. Pomáha mierniť kašeľ, zvyšovať vylučovanie hlienu a napomáhať jeho vykašliavaniu. Vhodný pri chorobách pľúc a priedušiek. Pitný režim.', 1, 0, 5.59),
(11, '94683', 'TANTUM VERDE SPRAY FORTE', 'Liek s dezinfekčným a protizápalovým účinkom. Zmierňuje bolesť hrdla a napomáha pri zápaloch v ústnej dutine-afty. Vhodný aj po operáciach.', 1, 0, 6.99),
(12, '45268', 'AKCIA LIOTON gel 100 000', 'Gél obsahuje liečivo heparín vo forme sodnej soli. Patrí do skupiny liekov na ochorenia žíl a je vhodný aj na ošetrenie zápalových alebo úrazových stavov. Gél sa aplikuje na kožu, preniká cez kožu a pôsobí proti opuchu, proti zápalu, proti tvorbe výpotku a znižuje zrážanie krvi.', 0, 0, 9.99),
(13, 'X28129', 'Dr.Max Calcium pantothenicum', 'Masť vhodná pre regeneráciu pokožky.', 0, 0, 2.89),
(14, 'X19529', 'BIODERMA MATRICIUM COFFRET DM (PAPER)', 'Regeneračné sérum obnovujúce poškodené tkanivo. Vhodné aj po estetických zákrokoch - peeling, laser. . . ako aj pri popáleninách a riešení vrások.', 0, 0, 35.99),
(15, '78086', 'Ibuprofen 400 STADA', 'Liek s protizápalovým a analgetickým účinkom. Napomáha pri bolestiach hlavy, zubov, svalov. Znižuje teplotu. Zmierňuje opuchy.', 1, 0, 3.69);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `order_item`
--

CREATE TABLE `order_item` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `price` double NOT NULL,
  `count` int(11) NOT NULL,
  `contribution` double NOT NULL,
  `boolean` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `order_item`
--

INSERT INTO `order_item` (`id`, `order_id`, `medicine_id`, `supplier_id`, `price`, `count`, `contribution`, `boolean`) VALUES
(1, 1, 1, 1, 2.99, 2, 0, ''),
(2, 1, 2, 2, 3.99, 3, 0, ''),
(3, 2, 15, 2, 3.69, 3, 0, ''),
(4, 2, 4, 2, 1.29, 3, 0, ''),
(5, 2, 8, 2, 4.79, 2, 0, ''),
(6, 2, 3, 1, 10.99, 1, 0, ''),
(7, 3, 8, 2, 4.79, 3, 0, ''),
(8, 4, 14, 2, 35.99, 3, 0, '');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `order_medicine`
--

CREATE TABLE `order_medicine` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` int(11) NOT NULL,
  `created_time` datetime NOT NULL,
  `boolean` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `order_medicine`
--

INSERT INTO `order_medicine` (`id`, `user_id`, `total_price`, `created_time`, `boolean`) VALUES
(1, 1, 18, '2018-03-21 15:02:28', ''),
(2, 1, 36, '2018-03-21 15:03:37', ''),
(3, 1, 14, '2018-03-21 15:03:48', ''),
(4, 1, 108, '2018-03-21 15:03:57', '');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `stock_medicine`
--

CREATE TABLE `stock_medicine` (
  `medicine_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `stock_medicine`
--

INSERT INTO `stock_medicine` (`medicine_id`, `supplier_id`, `count`, `price`) VALUES
(1, 1, 98, 2),
(2, 2, 107, 3.02),
(3, 1, 99, 8.8),
(4, 2, 97, 0.5),
(5, 1, 100, 4.33),
(5, 2, 50, 4.3),
(6, 1, 10, 2.8),
(7, 1, 20, 5),
(8, 2, 5, 3.58),
(14, 2, 7, 30),
(15, 2, 97, 3.03);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `supplier`
--

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `street` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `house_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `supplier`
--

INSERT INTO `supplier` (`id`, `name`, `city`, `street`, `house_number`) VALUES
(1, 'PHOENIX Zdravotnícke zásobovanie, a.s.', 'Bratislava', 'Prešovská', '2/A'),
(2, 'UNIPHARMA – 1.slovenská lekárnická akciová spoločnosť', 'Bojnice', 'Opatovská cesta', '4');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `registration_date` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `role` int(11) NOT NULL,
  `deactivation` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `user`
--

INSERT INTO `user` (`id`, `name`, `surname`, `email`, `password`, `registration_date`, `last_login`, `role`, `deactivation`) VALUES
(1, 'panda', 'panda', 'panda@panda.com', '$2y$10$naFsBC1rRFADwGoV.20wgeXdGsS1d3khWmYm0MiUslmyoeKv0Of0e', '2018-04-21 05:07:04', '2018-03-21 19:18:18', 1, 0),
(2, 'Ján', 'Mrkvička', 'predavac@gmail.com', '$2y$10$w6MmgvS2vDS5ILhPn3./DeuIHCG7X8qkjN1nw8Wy0lQquS2ybg1vC', '2018-03-21 14:21:58', '2018-03-21 15:34:20', 0, 0),
(3, 'Peter', 'Mrkvička', 'manager@gmail.com', '$2y$10$y0GuNr/B3C/wA7qAMXC1Hu40ngiHiv6uQmZ9/5AGRzC5l4VHvc5EW', '2018-03-21 14:24:06', '2018-03-21 15:34:04', 1, 0);

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `medicine`
--
ALTER TABLE `medicine`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_58362A8D43D9F9C8` (`id_sukl`);

--
-- Indexy pre tabuľku `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_52EA1F098D9F6D38` (`order_id`),
  ADD KEY `IDX_52EA1F092F7D140A` (`medicine_id`),
  ADD KEY `IDX_52EA1F092ADD6D8C` (`supplier_id`);

--
-- Indexy pre tabuľku `order_medicine`
--
ALTER TABLE `order_medicine`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B9CEC83CA76ED395` (`user_id`);

--
-- Indexy pre tabuľku `stock_medicine`
--
ALTER TABLE `stock_medicine`
  ADD PRIMARY KEY (`medicine_id`,`supplier_id`),
  ADD KEY `IDX_17F07ECF2F7D140A` (`medicine_id`),
  ADD KEY `IDX_17F07ECF2ADD6D8C` (`supplier_id`);

--
-- Indexy pre tabuľku `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pre tabuľku `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `medicine`
--
ALTER TABLE `medicine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT pre tabuľku `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pre tabuľku `order_medicine`
--
ALTER TABLE `order_medicine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pre tabuľku `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pre tabuľku `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `FK_52EA1F092ADD6D8C` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`),
  ADD CONSTRAINT `FK_52EA1F092F7D140A` FOREIGN KEY (`medicine_id`) REFERENCES `medicine` (`id`),
  ADD CONSTRAINT `FK_52EA1F098D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order_medicine` (`id`);

--
-- Obmedzenie pre tabuľku `order_medicine`
--
ALTER TABLE `order_medicine`
  ADD CONSTRAINT `FK_B9CEC83CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Obmedzenie pre tabuľku `stock_medicine`
--
ALTER TABLE `stock_medicine`
  ADD CONSTRAINT `FK_17F07ECF2ADD6D8C` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`),
  ADD CONSTRAINT `FK_17F07ECF2F7D140A` FOREIGN KEY (`medicine_id`) REFERENCES `medicine` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
