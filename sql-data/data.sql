/*
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

DROP TABLE IF EXISTS `Persons`;

CREATE TABLE Persons
(
    PersonID  int,
    LastName  varchar(255),
    FirstName varchar(255),
    Address   varchar(255),
    City      varchar(255),
    UNIQUE INDEX `PersonID` (`PersonID`)
);

DROP TABLE IF EXISTS `Information`;

CREATE TABLE `Information`
(
    `PersonID` int,
    `Tel`      varchar(255) NULL,
    `Email`    varchar(255) NULL,
    UNIQUE INDEX `PersonID` (`PersonID`)
);

INSERT INTO Persons (PersonID, LastName, FirstName, Address, City)
VALUES (1, 'Foo', 'Baz', '123 Bar Street', 'FooBazBar City');

INSERT INTO Persons (PersonID, LastName, FirstName, Address, City)
VALUES (2, 'Me', 'Foam', '100 Random Avenue', 'Php City');

INSERT INTO Information (PersonID, Tel, Email)
VALUES (1, '+79169999999', 'noname@example.org');

INSERT INTO Information (PersonID, Tel, Email)
VALUES (2, '+78008008080', 'ihavename@example.org');
