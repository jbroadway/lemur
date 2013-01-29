create table lemur_category (
	id integer primary key,
	owner int not null,
	title char(72) not null,
	sorting int not null
);

create index lemur_category_owner on lemur_category (owner, sorting);

create table lemur_course (
	id integer primary key,
	title char(72) not null,
	thumb char(128),
	summary char(255) not null,
	created datetime not null,
	owner int not null,
	category int not null,
	sorting int not null,
	availability int not null,
	price float not null,
	status int not null
);

create index lemur_course_owner_category on lemur_course (owner, category, sorting, status);

create table lemur_page (
	id integer primary key,
	title char(72) not null,
	course int not null,
	sorting int not null
);

create index lemur_page_course on lemur_page (course, sorting);

create table lemur_item (
	id integer primary key,
	title char(72) not null,
	page int not null,
	sorting int not null,
	type int not null,
	content text not null,
	answer char(128) not null default ''
);

create index lemur_item_page on lemur_item (page, sorting);

create table lemur_learner (
	user integer not null,
	course integer not null,
	ts datetime not null,
	primary key (user, course)
);

create index lemur_learner_ts on lemur_learner (ts);
