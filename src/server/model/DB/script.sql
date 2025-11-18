CREATE DATABASE NetlogiX;
USE NetlogiX;

CREATE TABLE Erabiltzaileak (
    ID INT(11) PRIMARY KEY AUTO_INCREMENT,
    CIF VARCHAR(50),
    Email VARCHAR(255),
    psswd VARCHAR(255)
);

CREATE TABLE Helbideak (
    ID INT(11) PRIMARY KEY AUTO_INCREMENT,
    Helbidea TEXT,
    CP INT(5),
    Hiria VARCHAR(100),
    Probintzia VARCHAR(100),
    ID_erab INT(11),
    FOREIGN KEY (ID_erab) REFERENCES Erabiltzaileak(ID)
);

CREATE TABLE Eskaera (
    ID INT(11) PRIMARY KEY AUTO_INCREMENT,
    Jatorria INT(11),
    Helmuga INT(11),
    Biltegia INT(11),
    Tamaina ENUM('Txikia', 'Ertaina', 'Handia'),
    Pisua INT(11) UNSIGNED,
    Eskaera_Data DATE,
    Egoera ENUM('Pendiente', 'Bidaltzen', 'Entregatuta'),
    ID_erab INT(11),
    FOREIGN KEY (Jatorria) REFERENCES Helbideak(ID),
    FOREIGN KEY (Helmuga) REFERENCES Helbideak(ID),
    FOREIGN KEY (ID_erab) REFERENCES Erabiltzaileak(ID)
);

CREATE TABLE Biltegiak (
    ID INT(11) PRIMARY KEY,
    Helbidea INT(11),
    Edukiera INT(11),
    FOREIGN KEY (Helbidea) REFERENCES Helbideak(ID)
);

CREATE TABLE Zerbitzu_Gehigarriak (
    ID INT(11) PRIMARY KEY,
    Izena VARCHAR(100),
    Deskribapena TEXT,
    Prezioa INT(11)
);

CREATE TABLE EskZerbitzu (
    IdEskaera INT(11),
    IdZerbitzu INT(11),
    PRIMARY KEY (IdEskaera, IdZerbitzu),
    FOREIGN KEY (IdEskaera) REFERENCES Eskaera(ID),
    FOREIGN KEY (IdZerbitzu) REFERENCES Zerbitzu_Gehigarriak(ID)
);





