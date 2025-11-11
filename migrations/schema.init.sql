create table if not exists categories
(
    id int auto_increment primary key,
    name varchar(255) not null comment 'Название категории',
)
    comment 'Категории';

create index name_idx on categories (name);

create table if not exists products
(
    id int auto_increment primary key,
    uuid  varchar(36) not null comment 'UUID товара',
    name text not null comment 'Название товара',
    description text not null comment 'Описание товара',
    thumbnail  varchar(255) null comment 'Ссылка на картинку',
    price float not null comment 'Цена'
    is_active tinyint default 1  not null comment 'Флаг активности',
    category_id int comment 'Категория товара',
    foreign key category_id references Categories(id)
)
    comment 'Товары';
