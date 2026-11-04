CREATE TABLE utilisateur(
    id number(4),
    email varchar2(50),
    mdp varchar2(30) not null,
    pseudo varchar2(30),
    administrateur number(1),
    constraint pk_user primary key (id)
);



create table article(
    id number(6),
    id_auteur number(4),
    texte varchar2(1000),
    constraint pk_article primary key (id),
    constraint fk_article foreign key (id_auteur) references utilisateur(id)
);


create table commentaire (
    id number(4),
    id_user number(4),
    id_article number(6),
    texte varchar2(1000),
    constraint pk_commentaire primary key (id),
    constraint fk_commentaire_user foreign key (id_user) references utilisateur(id),
    constraint fk_commentaire_article foreign key (id_article) references article(id)
);


