USE test
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `test_products` (
  `ID` int(11) NOT NULL,
  `Name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Category` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `CustomerName` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NULL,
  `BDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `test_products`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `test_products`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `test_products` (`ID`, `Name`, `Category`, `CustomerName`, `BDate`) VALUES
(1, 'Chai', 'Condiments', 'Peacock Margaret', '2013-10-10'),
(2, 'Chang', 'Dairy Products', 'Leverling Janet', '2013-08-18'),
(3, 'Aniseed Syrup', 'Beverages', 'Fuller Andrew', '2013-02-14'),
(4, 'Chef Anton''s Cajun Seasoning', 'Beverages', 'Fuller Andrew', '2013-07-15'),
(5, 'Chef Anton''s Gumbo Mix', 'Beverages', 'Peacock Margaret', '2013-02-27'),
(6, 'Grandma''s Boysenberry Spread', 'Beverages', 'Davolio Nancy', '2013-02-09'),
(7, 'Uncle Bob''s Organic Dried Pears', 'Seafood', 'Davolio Nancy', '2013-02-21'),
(8, 'Northwoods Cranberry Sauce', 'Condiments', 'Davolio Nancy', '2013-11-28'),
(9, 'Mishi Kobe Niku', 'Condiments', 'Davolio Nancy', '2013-08-09'),
(10, 'Ikura', 'Seafood', 'Peacock Margaret', '2013-01-04'),
(11, 'Queso Cabrales', 'Dairy Products', 'Buchanan Steven', '2013-09-27'),
(12, 'Queso Manchego La Pastora', 'Condiments', 'Peacock Margaret', '2013-07-06'),
(13, 'Konbu', 'Condiments', 'Buchanan Steven', '2013-01-18'),
(14, 'Tofu', 'Seafood', 'Davolio Nancy', '2013-04-17'),
(15, 'Genen Shouyu', 'Beverages', 'Davolio Nancy', '2013-07-14'),
(16, 'Pavlova', 'Beverages', 'Fuller Andrew', '2013-04-28'),
(17, 'Alice Mutton', 'Condiments', 'Davolio Nancy', '2013-06-24'),
(18, 'Carnarvon Tigers', 'Beverages', 'Leverling Janet', '2013-08-03'),
(19, 'Teatime Chocolate Biscuits', 'Seafood', 'Buchanan Steven', '2013-09-27'),
(20, 'Sir Rodney''s Marmalade', 'Seafood', 'Peacock Margaret', '2013-10-20'),
(21, 'Sir Rodney''s Scones', 'Dairy Products', 'Fuller Andrew', '2013-06-19'),
(22, 'Gustaf''s Knäckebröd', 'Beverages', 'Peacock Margaret', '2013-06-12'),
(23, 'Tunnbröd', 'Beverages', 'Davolio Nancy', '2013-02-22'),
(24, 'Guaraná Fantástica', 'Condiments', 'Buchanan Steven', '2013-01-03'),
(25, 'NuNuCa Nuß-Nougat-Creme', 'Seafood', 'Davolio Nancy', '2013-01-14'),
(26, 'Gumbär Gummibärchen', 'Condiments', 'Fuller Andrew', '2013-03-09'),
(27, 'Schoggi Schokolade', 'Seafood', 'Leverling Janet', '2013-06-23'),
(28, 'Rossle Sauerkraut', 'Condiments', 'Peacock Margaret', '2013-08-05'),
(29, 'Thuringer Rostbratwurst', 'Beverages', 'Peacock Margaret', '2013-01-26'),
(30, 'N''o"r\\d-Ost Mat%123_jes)hering#', 'Seafood', 'Leverling Janet', '2013-11-16'),
(31, 'Camembert Pierrot', 'Dairy Products', NULL, '2013-11-17');
