SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS Role;
CREATE TABLE Role
(
    id   INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    UNIQUE (name)
);

INSERT INTO Role (id, name)
VALUES (1, 'USER'),
       (2, 'OWNER'),
       (3, 'ADMIN');

DROP TABLE IF EXISTS Shop;
CREATE TABLE Shop
(
    id        INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    -- https://developers.google.com/maps/solutions/store-locator/clothing-store-locator#creating-a-table-in-mysql
    latitude  FLOAT(10, 6) NOT NULL,
    longitude FLOAT(10, 6) NOT NULL,
    address   VARCHAR(255) NOT NULL,
    name      VARCHAR(255) NOT NULL,
    city      VARCHAR(255) NOT NULL
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
    -- If the user is a shop owner, this will reference that shop
    -- One-to-one relation
    shopId   INT                   DEFAULT NULL,
    UNIQUE (shopId),
    UNIQUE (email),
    FOREIGN KEY (roleID) REFERENCES Role (id)
        ON UPDATE CASCADE ON DELETE CASCADE
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
    FOREIGN KEY (userId) REFERENCES User (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (shopId) REFERENCES Shop (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

DROP TABLE IF EXISTS Session;
CREATE TABLE Session
(
    id          INT         NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userId      INT         NOT NULL,
    accessToken VARCHAR(44) NOT NULL,
    -- Ensure a token cannot be used by more than 1 user,
    -- even if that's almost impossible, but not 100% impossible
    UNIQUE (accessToken),
    FOREIGN KEY (userId) REFERENCES User (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

SET FOREIGN_KEY_CHECKS = 1;

-- Apply the haversine formula to calculate
-- the distance between 2 points on Earth in KMs
DELIMITER $$
CREATE FUNCTION DISTANCE_KM(lat0 FLOAT(10, 6),
                            lon0 FLOAT(10, 6),
                            lat1 FLOAT(10, 6),
                            lon1 FLOAT(10, 6))
    RETURNS FLOAT(10, 3)
    DETERMINISTIC
BEGIN
    DECLARE lat1Rad FLOAT(10, 9);
    DECLARE lat0Rad FLOAT(10, 9);
    DECLARE deltaLon FLOAT(10, 9);

    SET lat1Rad = radians(lat1);
    SET lat0Rad = radians(lat0);
    SET deltaLon = radians(lon1 - lon0);

    RETURN 6371 * acos(
                sin(lat0Rad) * sin(lat1Rad) +
                cos(lat0Rad) * cos(lat1Rad) * cos(deltaLon));
END$$
DELIMITER ;
