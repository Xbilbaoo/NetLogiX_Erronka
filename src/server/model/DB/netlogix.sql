-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-11-2025 a las 14:59:13
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `netlogix`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biltegiak`
--

CREATE TABLE `biltegiak` (
  `ID` int(11) NOT NULL,
  `Helbidea` int(11) DEFAULT NULL,
  `Edukiera` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `erabiltzaileak`
--

CREATE TABLE `erabiltzaileak` (
  `ID` int(11) NOT NULL,
  `CIF` varchar(50) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Kizena` varchar(100) DEFAULT NULL,
  `Eizena` varchar(100) DEFAULT NULL,
  `Kabizena` varchar(150) DEFAULT NULL,
  `Telefonoa` varchar(30) DEFAULT NULL,
  `psswd` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `erabiltzaileak`
--

INSERT INTO `erabiltzaileak` (`ID`, `CIF`, `Email`, `Kizena`, `Eizena`, `Kabizena`, `Telefonoa`, `psswd`) VALUES
(19, 'G12345678', 'kepaizaguirre991@gmail.com', 'kepa', 'zornotza', 'izaguirre', '666666666', '$2y$10$kxxneZ/r92XOdT.WagMkluG15dHnx1y.P5RzNQVBn5I83MxP8Vdw6'),
(20, 'J12345678', 'k_izaguirrefer@fpzornotza.com', 'paco', 'moodle', 'fivem', '666666665', '$2y$10$qYQ83rzc9hoOMjygR6UEguU9/ANS4SlVHL5aNXDkhaz2dKmr9GGCO'),
(21, 'A23456789', 'kepaizaguirre314@gmail.com', 'unai', 'unai', 'unai', '666666543', '$2y$10$uyZo6oH36R2Uw/BRrxJhae2.DkTrFoeOwf0AL7Sr/epiHJAVfjjqC');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eskaera`
--

CREATE TABLE `eskaera` (
  `ID` int(11) NOT NULL,
  `Jatorria` int(11) DEFAULT NULL,
  `Helmuga` int(11) DEFAULT NULL,
  `Biltegia` int(11) DEFAULT NULL,
  `Tamaina` enum('Txikia','Ertaina','Handia') DEFAULT NULL,
  `Pisua` int(11) UNSIGNED DEFAULT NULL,
  `Eskaera_Data` date DEFAULT NULL,
  `Egoera` enum('Pendiente','Bidaltzen','Entregatuta') DEFAULT NULL,
  `ID_erab` int(11) DEFAULT NULL,
  `zerbitzu_gehigarriak` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eskaera`
--

INSERT INTO `eskaera` (`ID`, `Jatorria`, `Helmuga`, `Biltegia`, `Tamaina`, `Pisua`, `Eskaera_Data`, `Egoera`, `ID_erab`, `zerbitzu_gehigarriak`) VALUES
(6, 9, 10, NULL, '', NULL, '2000-02-20', 'Pendiente', 21, ''),
(7, 11, 12, NULL, '', 0, '2000-02-20', 'Pendiente', 21, '1'),
(8, 13, 14, NULL, '', 0, '2000-02-20', 'Pendiente', 21, '1,2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `helbideak`
--

CREATE TABLE `helbideak` (
  `ID` int(11) NOT NULL,
  `Helbidea` text DEFAULT NULL,
  `CP` int(5) DEFAULT NULL,
  `Hiria` varchar(100) DEFAULT NULL,
  `Probintzia` varchar(100) DEFAULT NULL,
  `ID_erab` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `helbideak`
--

INSERT INTO `helbideak` (`ID`, `Helbidea`, `CP`, `Hiria`, `Probintzia`, `ID_erab`) VALUES
(6, 'durango 9', 48200, 'duranjo', 'bizkaia', 19),
(7, 'pintor benitocamelo 9', 48370, 'galdakao', 'bizkaia', 20),
(8, 'durango', 48450, 'pajero', 'biz', 21),
(9, 'durango, 48450, pajero, biz', NULL, '', '', 21),
(10, 'durango', NULL, '', '', 21),
(11, 'durango, 48450, pajero, biz', NULL, '', '', 21),
(12, 'durango', NULL, '', '', 21),
(13, 'durango, 48450, pajero, biz', NULL, '', '', 21),
(14, 'abadiño', NULL, '', '', 21);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `biltegiak`
--
ALTER TABLE `biltegiak`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Helbidea` (`Helbidea`);

--
-- Indices de la tabla `erabiltzaileak`
--
ALTER TABLE `erabiltzaileak`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `eskaera`
--
ALTER TABLE `eskaera`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Jatorria` (`Jatorria`),
  ADD KEY `Helmuga` (`Helmuga`),
  ADD KEY `ID_erab` (`ID_erab`);

--
-- Indices de la tabla `helbideak`
--
ALTER TABLE `helbideak`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_erab` (`ID_erab`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `erabiltzaileak`
--
ALTER TABLE `erabiltzaileak`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `eskaera`
--
ALTER TABLE `eskaera`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `helbideak`
--
ALTER TABLE `helbideak`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `biltegiak`
--
ALTER TABLE `biltegiak`
  ADD CONSTRAINT `biltegiak_ibfk_1` FOREIGN KEY (`Helbidea`) REFERENCES `helbideak` (`ID`);

--
-- Filtros para la tabla `eskaera`
--
ALTER TABLE `eskaera`
  ADD CONSTRAINT `eskaera_ibfk_1` FOREIGN KEY (`Jatorria`) REFERENCES `helbideak` (`ID`),
  ADD CONSTRAINT `eskaera_ibfk_2` FOREIGN KEY (`Helmuga`) REFERENCES `helbideak` (`ID`),
  ADD CONSTRAINT `eskaera_ibfk_3` FOREIGN KEY (`ID_erab`) REFERENCES `erabiltzaileak` (`ID`);

--
-- Filtros para la tabla `helbideak`
--
ALTER TABLE `helbideak`
  ADD CONSTRAINT `helbideak_ibfk_1` FOREIGN KEY (`ID_erab`) REFERENCES `erabiltzaileak` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
