create table t_article
(
    art_id int auto_increment
        primary key,
    art_title varchar(100) not null,
    art_content varchar(2000) not null,
    art_chapter int not null,
    art_visible tinyint(1) not null,
    art_commentsNb int default '0' null,
    art_viewsNb int default '0' not null,
    art_lastUpdated datetime default CURRENT_TIMESTAMP not null,
    constraint art_chapter
    unique (art_chapter)
)
;

create table t_comment
(
    com_id int auto_increment
        primary key,
    com_content varchar(500) not null,
    com_date datetime not null,
    art_id int not null,
    usr_id int not null,
    constraint fk_com_art
    foreign key (art_id) references db688769058.t_article (art_id)
)
;

create index fk_com_art
    on t_comment (art_id)
;

create index fk_com_usr
    on t_comment (usr_id)
;

create table t_comment_reported
(
    com_rep_id int auto_increment
        primary key,
    com_rep_date datetime not null,
    com_id int not null,
    com_rep_counter int not null,
    constraint fk_com_rep_com
    foreign key (com_id) references db688769058.t_comment (com_id)
        on delete cascade
)
;

create index fk_com_rep_com
    on t_comment_reported (com_id)
;

create table t_user
(
    usr_id int auto_increment
        primary key,
    usr_name varchar(50) not null,
    usr_email varchar(200) not null,
    usr_password varchar(88) not null,
    usr_salt varchar(23) not null,
    usr_role varchar(50) not null,
    usr_lastViewArt int default '1' not null
)
;

alter table t_comment
    add constraint fk_com_usr
foreign key (usr_id) references db688769058.t_user (usr_id)
;

