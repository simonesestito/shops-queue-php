-- Copyright 2020 Simone Sestito
-- This file is part of Shops Queue.
--
-- Shops Queue is free software: you can redistribute it and/or modify
-- it under the terms of the GNU Affero General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- Shops Queue is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU Affero General Public License for more details.
--
-- You should have received a copy of the GNU Affero General Public License
-- along with Shops Queue.  If not, see <http://www.gnu.org/licenses/>.

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
    name VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS User;
CREATE TABLE User
(
    id               INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(255) NOT NULL,
    surname          VARCHAR(255) NOT NULL,
    -- It can be a commercial email address for operators
    email            VARCHAR(255) NOT NULL,
    active           BOOLEAN      NOT NULL DEFAULT FALSE,
    -- BCrypt hash length
    password         VARCHAR(60)  NOT NULL,
    roleId           INT          NOT NULL DEFAULT 1,
    shopId           INT                   DEFAULT NULL,
    signupDate       DATETIME     NOT NULL DEFAULT NOW(),
    -- Email verification code
    verificationCode VARCHAR(64),
    UNIQUE (email),
    FOREIGN KEY (roleID) REFERENCES Role (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (shopId) REFERENCES Shop (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

DROP TABLE IF EXISTS Booking;
CREATE TABLE Booking
(
    id        INT      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userId    INT      NOT NULL,
    shopId    INT      NOT NULL,
    createdAt DATETIME NOT NULL DEFAULT NOW(),
    finished  BOOLEAN  NOT NULL DEFAULT FALSE,
    FOREIGN KEY (userId) REFERENCES User (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (shopId) REFERENCES Shop (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

DROP TABLE IF EXISTS Session;
CREATE TABLE Session
(
    id            INT                NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userId        INT                NOT NULL,
    accessToken   VARCHAR(88) BINARY NOT NULL,
    loginDate     DATETIME           NOT NULL DEFAULT NOW(),
    lastUsageDate DATETIME           NOT NULL DEFAULT NOW(),
    active        BOOLEAN            NOT NULL DEFAULT TRUE,
    -- Ensure a token cannot be used by more than 1 user,
    -- even if that's almost impossible, but not 100% impossible
    UNIQUE (accessToken),
    FOREIGN KEY (userId) REFERENCES User (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

DROP TABLE IF EXISTS Favourites;
CREATE TABLE Favourites
(
    userId INT NOT NULL,
    shopId INT NOT NULL,
    PRIMARY KEY (userId, shopId),
    FOREIGN KEY (userId) REFERENCES User (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (shopId) REFERENCES Shop (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

DROP TABLE IF EXISTS FcmToken;
CREATE TABLE FcmToken
(
    token  VARCHAR(255) BINARY NOT NULL,
    userId INT                 NOT NULL,
    PRIMARY KEY (token),
    FOREIGN KEY (userId) REFERENCES User (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

DROP TABLE IF EXISTS Product;
CREATE TABLE Product
(
    id     INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(255) NOT NULL,
    ean    VARCHAR(13)  NOT NULL,
    shopId INT          NOT NULL,
    -- A shop can only use an EAN once
    UNIQUE (shopId, ean),
    FOREIGN KEY (shopId) REFERENCES Shop (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

DROP TABLE IF EXISTS ShoppingList;
CREATE TABLE ShoppingList
(
    id        INT      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userId    INT      NOT NULL,
    createdAt DATETIME NOT NULL DEFAULT NOW()
);

DROP TABLE IF EXISTS ShoppingList_Products;
CREATE TABLE ShoppingList_Products
(
    shoppingListId INT NOT NULL,
    productId      INT NOT NULL,
    PRIMARY KEY (shoppingListId, productId),
    FOREIGN KEY (shoppingListId) REFERENCES ShoppingList (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (productId) REFERENCES Product (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

SET FOREIGN_KEY_CHECKS = 1;

DROP VIEW IF EXISTS PendingBooking;
CREATE VIEW PendingBooking AS
SELECT *
FROM Booking
WHERE finished = FALSE;

DROP VIEW IF EXISTS ShopWithCount;
CREATE VIEW ShopWithCount AS
SELECT Shop.*, COUNT(PendingBooking.userId) AS count
FROM Shop
         LEFT JOIN PendingBooking ON Shop.id = PendingBooking.shopId
GROUP BY Shop.id;

DROP VIEW IF EXISTS UserWithRole;
CREATE VIEW UserWithRole AS
SELECT User.*,
       Role.name AS role
FROM User
         JOIN Role ON User.roleId = Role.id;

DROP VIEW IF EXISTS UserDetails;
CREATE VIEW UserDetails AS
SELECT UserWithRole.*,
       ShopWithCount.name  AS shopName,
       ShopWithCount.address,
       ShopWithCount.longitude,
       ShopWithCount.latitude,
       ShopWithCount.count AS shopBookingsCount
FROM UserWithRole
         JOIN Role ON UserWithRole.roleId = Role.id
         LEFT JOIN ShopWithCount ON UserWithRole.shopId = ShopWithCount.id;

DROP VIEW IF EXISTS BookingDetail;
CREATE VIEW BookingDetail AS
SELECT PendingBooking.id  AS bookingId,
       ShopWithCount.id   AS bookingShopId,
       UserWithRole.id    AS userId,
       UserWithRole.name,
       UserWithRole.surname,
       UserWithRole.role,
       UserWithRole.email,
       PendingBooking.createdAt,
       ShopWithCount.name AS shopName,
       ShopWithCount.latitude,
       ShopWithCount.longitude,
       ShopWithCount.address,
       ShopWithCount.count
FROM PendingBooking
         JOIN UserWithRole ON PendingBooking.userId = UserWithRole.id
         JOIN ShopWithCount ON PendingBooking.shopId = ShopWithCount.id
ORDER BY PendingBooking.createdAt;

DROP VIEW IF EXISTS BookingDetailQueueCount;
CREATE VIEW BookingDetailQueueCount AS
SELECT BookingDetail.*, COALESCE(BookingQueueCount.queueCount, 0) AS queueCount
FROM BookingDetail
         LEFT JOIN (SELECT PendingBooking.userId, PendingBooking.shopId, COUNT(*) AS queueCount
                    FROM PendingBooking,
                         (SELECT createdAt, shopId, userId FROM PendingBooking) Booking2
                    WHERE PendingBooking.shopId = Booking2.shopId
                      AND PendingBooking.createdAt > Booking2.createdAt
                    GROUP BY PendingBooking.userId, PendingBooking.shopId) BookingQueueCount
                   ON BookingQueueCount.shopId = BookingDetail.bookingShopId
                       AND BookingQueueCount.userId = BookingDetail.userId
ORDER BY queueCount;

DROP VIEW IF EXISTS SessionDetail;
CREATE VIEW SessionDetail AS
SELECT Session.id     AS sessionId,
       Session.accessToken,
       Session.lastUsageDate,
       Session.loginDate,
       Session.active AS sessionActive,
       UserDetails.*
FROM Session
         JOIN UserDetails ON Session.userId = UserDetails.id;

-- Apply the haversine formula to calculate
-- the distance between 2 points on Earth in KMs
DROP FUNCTION IF EXISTS DISTANCE_KM;
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
