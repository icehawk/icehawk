-- // Create Changelog

-- Default DDL for changelog table that will keep
-- a record of the migrations that have been run.

-- You can modify this to suit your database before
-- running your first migration.

-- Be sure that ID and DESCRIPTION fields exist in
-- BigInteger and String compatible fields respectively.

CREATE TABLE IF NOT EXISTS ${changelog} (
ID NUMERIC(20,0) NOT NULL,
APPLIED_AT VARCHAR(25) NOT NULL,
DESCRIPTION VARCHAR(255) NOT NULL
);

ALTER TABLE ${changelog}
ADD CONSTRAINT PK_CHANGELOG
PRIMARY KEY (id);

-- //@UNDO

DROP TABLE ${changelog};
