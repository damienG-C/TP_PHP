SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE DATABASE IF NOT EXISTS blog;

USE blog;

DROP TABLE IF EXISTS commentaire;
DROP TABLE IF EXISTS article;
DROP TABLE IF EXISTS categorie;
DROP TABLE IF EXISTS `user`;

CREATE TABLE IF NOT EXISTS `user`(
    id int(4) AUTO_INCREMENT,
    email varchar(50),
    mdp varchar(255) not null,
    pseudo varchar(30),
    administrateur int(1),
    constraint pk_user primary key (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

create table IF NOT EXISTS categorie(
    id int(4) AUTO_INCREMENT,
    nom varchar(100),
    constraint pk_categorie primary key (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

create table IF NOT EXISTS article(
    id int(6) AUTO_INCREMENT,
    id_auteur int(4),
    titre varchar(50),
    description varchar(1000),
    pseudo varchar(30),
    categorie int(4),
    constraint pk_article primary key (id),
    constraint fk_article_user foreign key (id_auteur) references `user`(id),
    constraint fk_article_categorie foreign key (categorie) references categorie(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS commentaire;
create table IF NOT EXISTS commentaire (
    id int(4) AUTO_INCREMENT,
    id_auteur int(4),
    pseudo varchar(30),
    article int(6),
    description varchar(1000),
    constraint pk_commentaire primary key (id),
    constraint fk_commentaire_article foreign key (article) references article(id),
    constraint fk_commentaire_user foreign key (id_auteur) references `user`(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;