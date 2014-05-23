
CREATE SCHEMA IF NOT EXISTS `db_projeto3` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;

CREATE TABLE IF NOT EXISTS `db_projeto3`.`tb_celular` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `marca` VARCHAR(100) NOT NULL,
  `modelo` VARCHAR(100) NOT NULL,
  `ativo` TINYINT(4) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


INSERT INTO `db_projeto2`.`tb_celular` (`marca`, `modelo`, `ativo`) VALUES ('Samsung', 'Galaxy 5', '1');
INSERT INTO `db_projeto2`.`tb_celular` (`id`, `marca`, `modelo`, `ativo`) VALUES ('', 'Motorola', 'Moto G', '1');
INSERT INTO `db_projeto2`.`tb_celular` (`id`, `marca`, `modelo`, `ativo`) VALUES ('', 'Nokia', 'Lumia', '1');
