create database if not exists alaska character set utf8 collate utf8_unicode_ci;
use alaska;

grant all privileges on alaska.* to 'alaska_user'@'localhost' identified by 'secret';
