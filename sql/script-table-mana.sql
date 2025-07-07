CREATE TABLE fund
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    amount DOUBLE NOT NULL,
    added_at    DATETIME NOT NULL DEFAULT NOW(),
    description TEXT
);
