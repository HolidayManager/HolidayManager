CREATE DATABASE IF not exists ProjDB;

USE ProjDB;

CREATE TABLE IF NOT EXISTS Users(
iduser INT NOT NULL auto_increment primary key UNIQUE ,
username VARCHAR(150) NOT NULL unique,
password VARCHAR(255) NOT NULL,
previlege VARCHAR(1) NOT NULL,
email VARCHAR(255) NOT NULL UNIQUE,
verified_email VARCHAR(1) NOT NULL,
INDEX(iduser,username,email)
) engine InnoDB;

CREATE TABLE IF NOT EXISTS Profile(
idinfo INT auto_increment NOT NULL unique primary key,
iduser INT NOT NULL,
firstname VARCHAR(150) NOT null,
lastname VARCHAR(150) NOT NULL,
phone INT(14) UNIQUE NOT NULL ,
address VARCHAR(300) NOT NULL,
birthday DATE NOT NULL,
department VARCHAR(150) NOT NULL,
company VARCHAR(150) NOT NULL,
INDEX(idinfo,phone,firstname),
foreign key(iduser) references Users(iduser)
) engine InnoDB;

CREATE TABLE IF NOT EXISTS Accesses(
idaccess INT NOT NULL auto_increment primary key UNIQUE,
iduser INT NOT NULL,
dateaccess DATETIME default current_timestamp NOT NULL,
ip INT(12) NOT NULL,
INDEX(idaccess,iduser,ip),
foreign key(iduser) references Users(iduser)
) engine InnoDB;

CREATE TABLE IF NOT EXISTS Holidays(
idholiday INT auto_increment NOT NULL primary key UNIQUE,
iduser INT NOT NULL,
begin_date DATETIME NOT NULL,
end_date DATETIME NOT NULL,
status VARCHAR(1) NOT NULL,
type VARCHAR(1) NOT NULL,
foreign key(iduser) references Users(iduser),
INDEX(idholiday,iduser)
) engine InnoDB;

CREATE TABLE IF NOT EXISTS Task(
idtask INT NOT NULL auto_increment PRIMARY key UNIQUE,
iduserFrom INT NOT NULL,
iduserTo INT NOT NULL,
taskmessage VARCHAR(300) NOT NULL,
dateTask DATETIME NOT NULL DEFAULT current_timestamp,
idholiday INT NOT NULL,
INDEX(idtask, iduserFrom,iduserTo,idholiday),
foreign key(iduserFrom) references Users(iduser),
foreign key(iduserTo) references Users(iduser),
foreign key(idholiday) references Holidays(idholiday)
) engine InnoDB;

