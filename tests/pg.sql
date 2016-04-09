
DROP DATABASE IF EXISTS zsql;
CREATE DATABASE zsql;

DROP USER IF EXISTS zsql;
CREATE USER zsql WITH PASSWORD 'zsql';

GRANT ALL PRIVILEGES ON DATABASE zsql TO zsql;

\connect zsql;

DROP TABLE IF EXISTS fixture1;
CREATE TABLE fixture1 (
  id SERIAL,
  strval varchar(255),
  dval float,
  unused smallint,
  PRIMARY KEY (id)
);

INSERT INTO fixture1 VALUES
(1, 'test', 2.14, NULL),
(2, 'blah', 343434.14, 1);

DROP TABLE IF EXISTS fixture2;
CREATE TABLE fixture2 (LIKE fixture1);
INSERT INTO fixture2 SELECT * FROM fixture1;