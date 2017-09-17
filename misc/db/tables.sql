DROP TABLE IF EXISTS division;
CREATE TABLE division(
did	 	VARCHAR(50) 	NOT NULL,
division 	VARCHAR(100) 	NOT NULL
);

DROP TABLE IF EXISTS District;
CREATE TABLE District(
divid	 	VARCHAR(50) 	NOT NULL,
dsid 	 	VARCHAR(50) 	NOT NULL,
district 	VARCHAR(100) 	NOT NULL
);

DROP TABLE IF EXISTS Upazilla;
CREATE TABLE Upazilla(
disid 	 	VARCHAR(50) 	NOT NULL,
uid 	 	VARCHAR(50) 	NOT NULL,
upazilla 	VARCHAR(100) 	NOT NULL
);


/*
truncate table division;
truncate table District;
truncate table Upazilla;
*/

LOAD DATA
INFILE "D:\\division.txt"
INTO TABLE division
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\r\n'
(did, division);


LOAD DATA
INFILE "D:\\District.txt"
INTO TABLE District
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\r\n'
(divid, dsid, district);

LOAD DATA
INFILE "D:\\Upazilla.txt"
INTO TABLE Upazilla
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\r\n'
(disid, uid, upazilla);


SELECT * FROM division;
SELECT * FROM District;
SELECT * FROM Upazilla;