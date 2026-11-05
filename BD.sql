SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

DROP TABLE IF EXISTS 'user'
CREATE TABLE IF NOT EXISTS 'user'(
    id number(4) AUTO_INCREMENT,
    email varchar2(50),
    mdp varchar2(30) not null,
    pseudo varchar2(30),
    administrateur number(1),
    constraint pk_user primary key (id)
);

DROP TABLE IF EXISTS categorie
create table IF NOT EXISTS categorie(
    id number(4) AUTO_INCREMENT,
    nom varchar2(100),
    constraint pk_commentaire primary key (id)
);

DROP TABLE IF EXISTS article
create table IF NOT EXISTS article(
    id number(6) AUTO_INCREMENT,
    id_auteur number(4),
    titre varchar2(50),
    description varchar2(1000),
    pseudo varchar2(30),
    categorie number(4),
    constraint pk_article primary key (id),
    constraint fk_article_user foreign key (id_auteur) references 'user'(id),
    constraint fk_article_categorie foreign key (categorie) references categorie(id)
);

DROP TABLE IF EXISTS commentaire
create table IF NOT EXISTS commentaire (
    id number(4) AUTO_INCREMENT,
    id_auteur number(4),
    pseudo varchar2(30),
    article number(6),
    description varchar2(1000),
    constraint pk_commentaire primary key (id),
    constraint fk_commentaire_article foreign key (article) references article(id),
    constraint fk_commentaire_user foreign key (id_auteur) references 'user'(id)
);

ENGINE=MyISAM DEFAULT CHARSET=utf8;
COMMIT;