SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS Role;
CREATE TABLE Role
(
    id   INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

INSERT INTO Role (id, name)
VALUES (1, 'USER'),
       (2, 'OWNER'),
       (3, 'ADMIN');

DROP TABLE IF EXISTS Shop;
CREATE TABLE Shop
(
    id          INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    -- https://developers.google.com/maps/solutions/store-locator/clothing-store-locator#creating-a-table-in-mysql
    xCoordinate FLOAT(10, 6) NOT NULL,
    yCoordinate FLOAT(10, 6) NOT NULL,
    address     VARCHAR(255) NOT NULL,
    name        VARCHAR(255) NOT NULL,
    city        VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS User;
CREATE TABLE User
(
    id       INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(255) NOT NULL,
    surname  VARCHAR(255) NOT NULL,
    -- It can be a commercial email address for operators
    email    VARCHAR(255) NOT NULL,
    -- BCrypt hash length
    password VARCHAR(60)  NOT NULL,
    roleId   INT          NOT NULL DEFAULT 1,
    -- If the user is a shop owner, this will referenece that shop
    -- One-to-one relation
    shopId   INT                   DEFAULT NULL,
    UNIQUE (shopId),
    UNIQUE (email),
    FOREIGN KEY (roleID) REFERENCES Role (id)
);

DROP TABLE IF EXISTS Booking;
CREATE TABLE Booking
(
    id        INT  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userId    INT  NOT NULL,
    shopId    INT  NOT NULL,
    createdAt DATE NOT NULL,
    -- Ensure a user can only have one booking per shop
    UNIQUE (userId, shopId),
    FOREIGN KEY (userId) REFERENCES User (id),
    FOREIGN KEY (shopId) REFERENCES Shop (id)
);

DROP TABLE IF EXISTS Session;
CREATE TABLE Session
(
    id                     INT         NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userId                 INT         NOT NULL,
    accessToken            VARCHAR(24) NOT NULL,
    accessTokenExpiration  DATETIME    NOT NULL,
    refreshToken           VARCHAR(44) NOT NULL,
    refreshTokenExpiration DATETIME    NOT NULL,
    -- Ensure a token cannot be used by more than 1 user,
    -- even if that's almost impossible, but not 100% impossible
    UNIQUE (accessToken),
    UNIQUE (refreshToken),
    FOREIGN KEY (userId) REFERENCES User (id)
);

SET FOREIGN_KEY_CHECKS = 1;
