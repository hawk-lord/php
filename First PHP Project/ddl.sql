CREATE SCHEMA `test1` DEFAULT CHARACTER SET `utf8mb4` COLLATE `utf8mb4_general_ci`;

CREATE TABLE `test1`.`alko` (
  `number` int(11) NOT NULL,
  `name` text,
  `bottlesize` text,
  `price` decimal(10,2) DEFAULT NULL,
  `priceGBP` decimal(10,2) DEFAULT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `orderamount` int(11) DEFAULT '0',
  PRIMARY KEY (`number`)
);

  
CREATE USER 'alko1'@'localhost' IDENTIFIED BY 'alko1234';

GRANT ALL ON test1.* TO 'alko1'@'localhost';
