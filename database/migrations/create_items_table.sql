CREATE TABLE items(
    id   TINYINT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id),
    text VARCHAR(255),
    checked boolean default false
);