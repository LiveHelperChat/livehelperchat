CREATE TABLE audits (
    category varchar(255) NOT NULL,
    file varchar(255),
    id bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
    line bigint,
    message longtext NOT NULL,
    severity varchar(255) NOT NULL,
    source varchar(255) NOT NULL,
    time timestamp NOT NULL
);
