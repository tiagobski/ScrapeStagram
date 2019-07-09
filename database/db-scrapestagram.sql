-- MySQL Script generated by MySQL Workbench
-- Tue Jul  9 16:33:18 2019
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema scrapestagram
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema scrapestagram
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `scrapestagram` DEFAULT CHARACTER SET utf8 ;
USE `scrapestagram` ;

-- -----------------------------------------------------
-- Table `scrapestagram`.`accounts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `scrapestagram`.`accounts` (
  `id_accounts` INT NOT NULL AUTO_INCREMENT,
  `username_accounts` VARCHAR(45) NOT NULL,
  `external_id_accounts` INT NOT NULL,
  `external_url_accounts` VARCHAR(500) NOT NULL,
  `biography_accounts` VARCHAR(5000) NULL,
  `profile_pic_accounts` VARCHAR(500) NULL,
  `count_followers_accounts` VARCHAR(45) NOT NULL DEFAULT 0,
  `count_following_accounts` VARCHAR(45) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_accounts`),
  UNIQUE INDEX `id_accounts_UNIQUE` (`id_accounts` ASC),
  UNIQUE INDEX `external_id_accounts_UNIQUE` (`external_id_accounts` ASC),
  UNIQUE INDEX `username_accounts_UNIQUE` (`username_accounts` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `scrapestagram`.`images`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `scrapestagram`.`images` (
  `id_images` INT NOT NULL AUTO_INCREMENT,
  `external_url_images` VARCHAR(500) NOT NULL,
  `width_images` INT NULL,
  `height_images` INT NULL,
  PRIMARY KEY (`id_images`),
  UNIQUE INDEX `id_images_UNIQUE` (`id_images` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `scrapestagram`.`locations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `scrapestagram`.`locations` (
  `id_locations` INT NOT NULL AUTO_INCREMENT,
  `external_id_locations` INT NOT NULL,
  `slug_locations` VARCHAR(500) NOT NULL,
  `name_locations` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`id_locations`),
  UNIQUE INDEX `id_locations_UNIQUE` (`id_locations` ASC),
  UNIQUE INDEX `external_id_locations_UNIQUE` (`external_id_locations` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `scrapestagram`.`posts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `scrapestagram`.`posts` (
  `id_posts` INT NOT NULL AUTO_INCREMENT,
  `ref_accounts_posts` INT NOT NULL,
  `ref_locations_posts` INT NULL,
  `external_id_posts` BIGINT NOT NULL,
  `external_url_posts` VARCHAR(500) NOT NULL,
  `shortcode_posts` VARCHAR(45) NOT NULL,
  `description_posts` VARCHAR(5000) NULL,
  PRIMARY KEY (`id_posts`),
  UNIQUE INDEX `id_posts_UNIQUE` (`id_posts` ASC),
  UNIQUE INDEX `external_id_posts_UNIQUE` (`external_id_posts` ASC),
  INDEX `fk_posts_accounts1_idx` (`ref_accounts_posts` ASC),
  INDEX `fk_posts_locations1_idx` (`ref_locations_posts` ASC),
  CONSTRAINT `fk_posts_accounts1`
    FOREIGN KEY (`ref_accounts_posts`)
    REFERENCES `scrapestagram`.`accounts` (`id_accounts`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_posts_locations1`
    FOREIGN KEY (`ref_locations_posts`)
    REFERENCES `scrapestagram`.`locations` (`id_locations`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `scrapestagram`.`accounts_has_images`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `scrapestagram`.`accounts_has_images` (
  `accounts_id_accounts` INT NOT NULL,
  `images_id_images` INT NOT NULL,
  PRIMARY KEY (`accounts_id_accounts`, `images_id_images`),
  INDEX `fk_accounts_has_images_images1_idx` (`images_id_images` ASC),
  INDEX `fk_accounts_has_images_accounts_idx` (`accounts_id_accounts` ASC),
  CONSTRAINT `fk_accounts_has_images_accounts`
    FOREIGN KEY (`accounts_id_accounts`)
    REFERENCES `scrapestagram`.`accounts` (`id_accounts`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_accounts_has_images_images1`
    FOREIGN KEY (`images_id_images`)
    REFERENCES `scrapestagram`.`images` (`id_images`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;