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
	index (page, sorting)
);
