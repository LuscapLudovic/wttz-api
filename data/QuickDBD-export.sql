-- Exported from QuickDBD: https://www.quickdatabasediagrams.com/
-- NOTE! If you have used non-SQL datatypes in your design, you will have to change these here.


CREATE TABLE `User` (
    `id` int(10)  NOT NULL AUTO_INCREMENT,
    `username` varchar(255)  NOT NULL ,
    `password` varchar(255)  NOT NULL ,
    `team_id` int  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Message` (
    `id` int(10)  NOT NULL AUTO_INCREMENT ,
    `text` varchar(255)  NOT NULL ,
    `posted_at` datetime  NOT NULL ,
    `team_id` int  NOT NULL ,
    `user_id` int  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Team` (
    `id` int(10)  NOT NULL AUTO_INCREMENT ,
    `libelle` varchar(50)  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Cryptage` (
    `id` int(10)  NOT NULL AUTO_INCREMENT ,
    `text` varchar(255)  NOT NULL ,
    `team_id` int  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

ALTER TABLE `User` ADD CONSTRAINT `fk_User_team_id` FOREIGN KEY(`team_id`)
REFERENCES `Team` (`id`);

ALTER TABLE `Message` ADD CONSTRAINT `fk_Message_team_id` FOREIGN KEY(`team_id`)
REFERENCES `Team` (`id`);

ALTER TABLE `Message` ADD CONSTRAINT `fk_Message_user_id` FOREIGN KEY(`user_id`)
REFERENCES `User` (`id`);

ALTER TABLE `Cryptage` ADD CONSTRAINT `fk_Cryptage_team_id` FOREIGN KEY(`team_id`)
REFERENCES `Team` (`id`);

CREATE INDEX `idx_User_username`
ON `User` (`username`);
