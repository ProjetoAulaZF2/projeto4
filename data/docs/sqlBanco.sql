CREATE SCHEMA IF NOT EXISTS `db_projeto4` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;

CREATE TABLE IF NOT EXISTS `db_projeto4`.`tb_celular` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `marca` VARCHAR(100) NOT NULL,
  `modelo` VARCHAR(100) NOT NULL,
  `ativo` TINYINT(4) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `db_projeto4`.`tb_usuario` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `login` VARCHAR(20) NOT NULL,
  `senha` VARCHAR(32) NOT NULL,
  `ativo` TINYINT(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


INSERT INTO `db_projeto4`.`tb_celular` (`marca`, `modelo`, `ativo`) VALUES ('Samsung', 'Galaxy 5', '1');
INSERT INTO `db_projeto4`.`tb_celular` (`id`, `marca`, `modelo`, `ativo`) VALUES ('', 'Motorola', 'Moto G', '1');
INSERT INTO `db_projeto4`.`tb_celular` (`id`, `marca`, `modelo`, `ativo`) VALUES ('', 'Nokia', 'Lumia', '1');

INSERT INTO `db_projeto4`.`tb_usuario` (`nome`, `email`, `login`, `senha`) VALUES ('Nataniel Paiva', 'nataniel.paiva@gmail.com', 'nataniel.paiva', md5('123'));

