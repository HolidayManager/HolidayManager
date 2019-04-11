-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema holidayManager
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema holidayManager
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `holidayManager` DEFAULT CHARACTER SET utf8 ;
USE `holidayManager` ;

-- -----------------------------------------------------
-- Table `holidayManager`.`Company`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `holidayManager`.`Company` (
  `idCompany` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `address` VARCHAR(200) NOT NULL,
  `phone` INT NOT NULL,
  `dateCreation` DATE NOT NULL,
  PRIMARY KEY (`idCompany`),
  UNIQUE INDEX `idCompany_UNIQUE` (`idCompany` ASC),
  INDEX `index` (`name` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  UNIQUE INDEX `address_UNIQUE` (`address` ASC),
  UNIQUE INDEX `phone_UNIQUE` (`phone` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `holidayManager`.`Department`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `holidayManager`.`Department` (
  `idDepartment` INT NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(150) NOT NULL,
  `idCompany` INT NOT NULL,
  PRIMARY KEY (`idDepartment`),
  UNIQUE INDEX `idDepartment_UNIQUE` (`idDepartment` ASC),
  UNIQUE INDEX `label_UNIQUE` (`label` ASC),
  INDEX `fk_Department_Company1_idx` (`idCompany` ASC),
  INDEX `index` (`label` ASC, `idCompany` ASC),
  CONSTRAINT `fk_Department_Company1`
    FOREIGN KEY (`idCompany`)
    REFERENCES `holidayManager`.`Company` (`idCompany`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `holidayManager`.`User`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `holidayManager`.`User` (
  `idUser` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(150) NOT NULL,
  `password` VARCHAR(150) NOT NULL,
  `dateCreation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` VARCHAR(200) NOT NULL,
  `verifiedEmail` VARCHAR(1) NOT NULL DEFAULT 'N',
  `previlege` VARCHAR(1) NOT NULL DEFAULT 'U',
  `holidayLeft` INT(2) NOT NULL,
  `referenceYear` VARCHAR(45) NOT NULL,
  `activationToken` VARCHAR(46) NOT NULL,
  `idDepartment` INT NOT NULL,
  PRIMARY KEY (`idUser`),
  UNIQUE INDEX `idUser_UNIQUE` (`idUser` ASC),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC),
  INDEX `index` (`username` ASC, `idUser` ASC, `email` ASC),
  INDEX `fk_User_Department1_idx` (`idDepartment` ASC),
  UNIQUE INDEX `activationToken_UNIQUE` (`activationToken` ASC),
  CONSTRAINT `fk_User_Department1`
    FOREIGN KEY (`idDepartment`)
    REFERENCES `holidayManager`.`Department` (`idDepartment`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `holidayManager`.`Holiday`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `holidayManager`.`Holiday` (
  `idHoliday` INT NOT NULL AUTO_INCREMENT,
  `idUser` INT NOT NULL,
  `start` DATETIME NOT NULL,
  `end` DATETIME NOT NULL,
  `type` VARCHAR(1) NOT NULL DEFAULT 'H',
  `status` VARCHAR(1) NOT NULL DEFAULT 'P',
  `dateRequest` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idHoliday`),
  UNIQUE INDEX `idHoliday_UNIQUE` (`idHoliday` ASC),
  INDEX `fk_Holiday_User_idx` (`idUser` ASC),
  INDEX `index` (`idHoliday` ASC, `idUser` ASC, `start` ASC, `end` ASC, `status` ASC, `type` ASC),
  CONSTRAINT `fk_Holiday_User`
    FOREIGN KEY (`idUser`)
    REFERENCES `holidayManager`.`User` (`idUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `holidayManager`.`Profile`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `holidayManager`.`Profile` (
  `idProfile` INT NOT NULL AUTO_INCREMENT,
  `firstname` VARCHAR(150) NOT NULL,
  `lastname` VARCHAR(150) NOT NULL,
  `phone` INT NOT NULL,
  `birthday` DATE NOT NULL,
  `address` VARCHAR(250) NULL,
  `idUser` INT NOT NULL,
  PRIMARY KEY (`idProfile`, `idUser`),
  UNIQUE INDEX `idProfile_UNIQUE` (`idProfile` ASC),
  UNIQUE INDEX `phone_UNIQUE` (`phone` ASC),
  INDEX `fk_Profile_User1_idx` (`idUser` ASC),
  CONSTRAINT `fk_Profile_User1`
    FOREIGN KEY (`idUser`)
    REFERENCES `holidayManager`.`User` (`idUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `holidayManager`.`Responsable`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `holidayManager`.`Responsable` (
  `idResponsable` INT NOT NULL AUTO_INCREMENT,
  `idDepartment` INT NOT NULL,
  `idUser` INT NOT NULL,
  PRIMARY KEY (`idResponsable`, `idUser`),
  UNIQUE INDEX `idResponsable_UNIQUE` (`idResponsable` ASC),
  INDEX `fk_Responsable_Department1_idx` (`idDepartment` ASC),
  INDEX `fk_Responsable_User1_idx` (`idUser` ASC),
  CONSTRAINT `fk_Responsable_Department1`
    FOREIGN KEY (`idDepartment`)
    REFERENCES `holidayManager`.`Department` (`idDepartment`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Responsable_User1`
    FOREIGN KEY (`idUser`)
    REFERENCES `holidayManager`.`User` (`idUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `holidayManager`.`User_has_Responsable`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `holidayManager`.`User_has_Responsable` (
  `idUser` INT NOT NULL,
  `idResponsable` INT NOT NULL,
  PRIMARY KEY (`idUser`, `idResponsable`),
  INDEX `fk_User_has_Responsable_Responsable1_idx` (`idResponsable` ASC),
  INDEX `fk_User_has_Responsable_User1_idx` (`idUser` ASC),
  CONSTRAINT `fk_User_has_Responsable_User1`
    FOREIGN KEY (`idUser`)
    REFERENCES `holidayManager`.`User` (`idUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_User_has_Responsable_Responsable1`
    FOREIGN KEY (`idResponsable`)
    REFERENCES `holidayManager`.`Responsable` (`idResponsable`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
