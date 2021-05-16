
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- Hardback implementation : © quietmint
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

-- This is the file where you are describing the database schema of your game
-- Basically, you just have to export from PhpMyAdmin your table structure and copy/paste
-- this export here.
-- Note that the database itself and the standard tables ("global", "stats", "gamelog" and "player") are
-- already created and must not be created here

-- Note: The database schema is created from this file when the game starts. If you modify this file,
--       you have to restart a game to see your changes in database.

CREATE TABLE IF NOT EXISTS `card` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `refId` int(3) unsigned NOT NULL,
  `location` varchar(32) NOT NULL,
  `order` int(3) NOT NULL DEFAULT -1,
  `origin` varchar(32) NOT NULL,
  `factor` int(1) NOT NULL DEFAULT 1,
  `ink` int(1),
  `wild` char(1),
  `age` timestamp(6) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `resolve` (
  `id` int(10) unsigned NOT NULL,
  `benefit_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`, `benefit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `player` ADD `coins` INT NOT NULL DEFAULT 0;
ALTER TABLE `player` ADD `ink` INT NOT NULL DEFAULT 0;
ALTER TABLE `player` ADD `remover` INT NOT NULL DEFAULT 0;
ALTER TABLE `player` ADD `advert` INT NOT NULL DEFAULT 0;
ALTER TABLE `player` ADD `word` VARCHAR(32) NULL DEFAULT NULL;
ALTER TABLE `player` ADD `vote` INT(1) NULL DEFAULT NULL;
