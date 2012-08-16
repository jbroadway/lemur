create table lemur_course (
	id integer primary key,
	title char(72) not null,
	icon char(128) not null,
	summary char(255) not null,
	created datetime not null,
	owner int not null,
	category int not null,
	sorting int not null,
	price float not null
);

create index lemur_course_owner_category on lemur_course (owner, category, sorting);

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
	content text not null
);

create index lemur_item_page on lemur_item (page, sorting);
