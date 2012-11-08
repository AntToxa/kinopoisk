-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 5.0.97.1
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 08.11.2012 8:57:46
-- Версия сервера: 5.5.20
-- Версия клиента: 4.1

-- 
-- Отключение внешних ключей
-- 
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

-- 
-- Установка кодировки, с использованием которой клиент будет посылать запросы на сервер
--
SET NAMES 'utf8';

--
-- Описание для таблицы film
--
DROP TABLE IF EXISTS film;
CREATE TABLE film (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор фильма',
  name VARCHAR(500) NOT NULL COMMENT 'Название фильма',
  `year` INT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Год выпуска',
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 751
AVG_ROW_LENGTH = 1489
CHARACTER SET cp1251
COLLATE cp1251_general_ci
COMMENT = 'Данные о фильме';

--
-- Описание для таблицы film_info
--
DROP TABLE IF EXISTS film_info;
CREATE TABLE film_info(
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор информации о фильме',
  film_id INT(10) UNSIGNED NOT NULL COMMENT 'Идентификатор фильма',
  position INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Позиция фильма в рейтинге',
  rating DECIMAL(10, 4) UNSIGNED NOT NULL DEFAULT 0.0000 COMMENT 'Рейтинг фильма',
  vote INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Количество проголосовавших людей',
  `date` DATETIME NOT NULL COMMENT 'Дата голосования',
  PRIMARY KEY (id),
  UNIQUE INDEX UK_film_info (film_id, `date`),
  CONSTRAINT FK_film_info_film_id FOREIGN KEY (film_id)
  REFERENCES film (id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AUTO_INCREMENT = 23
AVG_ROW_LENGTH = 1489
CHARACTER SET cp1251
COLLATE cp1251_general_ci
COMMENT = 'Информация о фильме соответствующая определенной дате';


-- 
-- Включение внешних ключей
-- 
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;