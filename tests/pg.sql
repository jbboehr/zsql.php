
DROP DATABASE IF EXISTS zsql;
CREATE DATABASE zsql;

DROP USER IF EXISTS zsql;
CREATE USER zsql WITH PASSWORD 'zsql';

GRANT ALL PRIVILEGES ON DATABASE zsql TO zsql;

\connect zsql;

SET ROLE zsql;

DROP TABLE IF EXISTS fixture1;
CREATE TABLE fixture1 (
  id SERIAL PRIMARY KEY,
  strval varchar(255),
  dval float,
  unused smallint
);

INSERT INTO fixture1 (strval, dval, unused) VALUES
('test', 2.14, NULL),
('blah', 343434.14, 1);

DROP TABLE IF EXISTS fixture2;
CREATE TABLE fixture2 (
  id SERIAL PRIMARY KEY,
  strval varchar(255),
  dval float,
  unused smallint
);

INSERT INTO fixture2 (strval, dval, unused) VALUES
('test', 2.14, NULL),
('blah', 343434.14, 1);

/* DROP TABLE IF EXISTS fixture2;
CREATE TABLE fixture2 (LIKE fixture1);
INSERT INTO fixture2 SELECT * FROM fixture1;
 */

GRANT ALL PRIVILEGES ON TABLE fixture1 TO zsql;
GRANT ALL PRIVILEGES ON TABLE fixture2 TO zsql;