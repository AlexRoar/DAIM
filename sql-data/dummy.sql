/*
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */
CREATE USER `test`@`%` IDENTIFIED WITH mysql_native_password BY 'qwerty123' PASSWORD EXPIRE NEVER;

GRANT Alter, Alter Routine, Create, Create Routine, Create Temporary Tables, Create User, Create View, Delete, Drop, Event, Execute, File, Grant Option, Index, Insert, Lock Tables, Process, References, Reload, Replication Client, Replication Slave, Select, Show Databases, Show View, Shutdown, Super, Trigger, Update ON *.* TO `test`@`%`;

DROP TABLE IF EXISTS `Persons`;

CREATE TABLE Persons
(
    PersonID  int,
    LastName  varchar(255),
    FirstName varchar(255),
    Address   varchar(255),
    City      varchar(255)
);
INSERT INTO Persons
VALUES (1, "Foo", "Baz", "123 Bar Street", "FooBazBar City");
