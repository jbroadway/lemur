create table lemur_category (
	id int not null auto_increment primary key,
	owner int not null,
	title char(72) not null,
	sorting int not null,
	index (owner, sorting)
);

create table lemur_course (
	id int not null auto_increment primary key,
	title char(72) not null,
	summary char(255) not null,
	thumb char(128),
	created datetime not null,
	owner int not null,
	category int not null,
	sorting int not null,
	availability int not null,
	price float not null,
	status int not null,
	has_glossary int not null default 0,
	instructor int not null default 0,
	index (owner, category, sorting, status)
);

create table lemur_page (
	id int not null auto_increment primary key,
	title char(72) not null,
	course int not null,
	sorting int not null,
	index (course, sorting)
);

create table lemur_item (
	id int not null auto_increment primary key,
	title char(72) not null,
	page int not null,
	sorting int not null,
	type int not null,
	content text not null,
	answer char(128) not null default '',
	course int not null,
	index (page, sorting),
	index (course, type)
);

create table lemur_learner (
	user int not null,
	course int not null,
	ts datetime not null,
	primary key (user, course),
	index (ts)
);

create table lemur_data (
	id int unsigned not null auto_increment primary key,
	course int not null,
	user int not null,
	item int not null,
	status tinyint not null, // 0-100
	correct tinyint not null, // -1=no, 0=undetermined, 1=yes
	ts datetime not null,
	answer text not null,
	feedback text not null,
	index (user, ts),
	index (course, user)
);