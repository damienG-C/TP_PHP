CREATE TABLE utilisateur(
    id number(4),
    email varchar2(50),
    mdp varchar2(30) not null,
    pseudo varchar2(30),
    administrateur number(1),
    constraint pk_user primary key (id)
);

create table categorie(
    id number(4),
    nom varchar2(100),
    constraint pk_commentaire primary key (id)
);

create table article(
    id number(6),
    id_auteur number(4),
    titre varchar2(50),
    description varchar2(1000),
    pseudo varchar2(30),
    categorie number(4),
    constraint pk_article primary key (id),
    constraint fk_article_user foreign key (id_auteur) references utilisateur(id),
    constraint fk_article_categorie foreign key (categorie) references categorie(id)
);


create table commentaire (
    id number(4),
    id_auteur number(4),
    pseudo varchar2(30),
    article number(6),
    description varchar2(1000),
    constraint pk_commentaire primary key (id),
    constraint fk_commentaire_article foreign key (article) references article(id),
    constraint fk_commentaire_user foreign key (id_auteur) references utilisateur(id)
);


