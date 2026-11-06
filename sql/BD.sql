
START TRANSACTION;

DROP TABLE IF EXISTS commentaire;
DROP TABLE IF EXISTS article;
DROP TABLE IF EXISTS categorie;
DROP TABLE IF EXISTS "user";

CREATE TABLE IF NOT EXISTS "user"(
    id number(4) AUTO_INCREMENT,
    email varchar2(50) not null,
    mdp varchar2(30) not null,
    pseudo varchar2(30) not null,
    administrateur number(1),
    constraint pk_user primary key (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
insert INTO "user" values(0, 'admin@localhost.fr', 'Admin69IUT', 'admin', 1);

create table IF NOT EXISTS categorie(
    id number(4) AUTO_INCREMENT,
    nom varchar2(100),
    constraint pk_categorie primary key (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table IF NOT EXISTS article(
    id number(6) AUTO_INCREMENT,
    id_auteur number(4),
    titre varchar2(50),
    description varchar2(1000),
    pseudo varchar2(30),
    categorie number(4),
    constraint pk_article primary key (id),
    constraint fk_article_user foreign key (id_auteur) references "user"(id),
    constraint fk_article_categorie foreign key (categorie) references categorie(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table IF NOT EXISTS commentaire(
    id number(4) AUTO_INCREMENT,
    id_auteur number(4),
    pseudo varchar2(30),
    article number(6),
    description varchar2(1000),
    constraint pk_commentaire primary key (id),
    constraint fk_commentaire_article foreign key (article) references article(id),
    constraint fk_commentaire_user foreign key (id_auteur) references "user"(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

COMMIT;