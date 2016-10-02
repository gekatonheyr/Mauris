-- --------------------------------------------------------
-- Хост:                         localhost
-- Версия сервера:               5.6.21 - MySQL Community Server (GPL)
-- ОС Сервера:                   Win32
-- HeidiSQL Версия:              9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры базы данных mauris
CREATE DATABASE IF NOT EXISTS `mauris` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `mauris`;


-- Дамп структуры для таблица mauris.stat
CREATE TABLE IF NOT EXISTS `stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы mauris.stat: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `stat` DISABLE KEYS */;
REPLACE INTO `stat` (`id`, `user_id`, `timestamp`) VALUES
	(1, 1, '2016-10-02 18:44:08'),
	(2, 1, '2016-10-02 17:53:57'),
	(3, 33, '2016-10-02 18:13:16'),
	(4, 33, '2016-10-02 18:13:58'),
	(5, 1, '2016-10-02 18:15:08'),
	(6, 1, '2016-10-02 18:15:13'),
	(7, 33, '2016-10-02 19:09:51'),
	(8, 33, '2016-10-02 19:16:52'),
	(9, 33, '2016-10-02 19:30:54'),
	(10, 33, '2016-10-02 20:18:07'),
	(11, 33, '2016-10-02 20:31:51'),
	(12, 1, '2016-10-02 17:55:30'),
	(13, 2, '2016-10-02 17:57:30'),
	(14, 3, '2016-10-02 17:59:30'),
	(15, 1, '2016-10-02 17:55:30'),
	(16, 2, '2016-10-02 17:57:30'),
	(17, 3, '2016-10-02 17:59:30'),
	(18, 1, '2016-10-02 17:55:30'),
	(19, 2, '2016-10-02 17:57:30'),
	(20, 3, '2016-10-02 17:59:30');
/*!40000 ALTER TABLE `stat` ENABLE KEYS */;


-- Дамп структуры для таблица mauris.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649AA08CB10` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы mauris.user: ~3 rows (приблизительно)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
REPLACE INTO `user` (`id`, `login`, `name`) VALUES
	(1, 'first', 'Ivan'),
	(2, 'second', 'Petr'),
	(3, 'third', 'Sidor'),
	(33, 'anonymous', 'anonymous');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
