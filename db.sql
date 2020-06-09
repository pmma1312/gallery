DROP DATABASE IF EXISTS pGallery;
CREATE DATABASE pGallery;
USE pGallery;

CREATE TABLE user (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(16) NOT NULL,
    password VARCHAR(256) NOT NULL,
    registered TIMESTAMP NOT NULL,
    PRIMARY KEY(id)
);

CREATE TABLE image (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    path VARCHAR(512) NOT NULL,
    deleted TINYINT(1) DEFAULT 0 NOT NULL,
    uploaded_at TIMESTAMP NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY(user_id) REFERENCES user(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE album (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(24) NOT NULL,
    deleted TINYINT(1) DEFAULT 0 NOT NULL,
    created_at TIMESTAMP NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY(user_id) REFERENCES user(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE image_to_album (
    album_id INT NOT NULL,
    image_id INT NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY(album_id, image_id),
    FOREIGN KEY(album_id) REFERENCES album(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY(image_id) REFERENCES image(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY(user_id) REFERENCES user(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE USER 'gallery'@'localhost' IDENTIFIED BY '1337';
GRANT SELECT, INSERT, UPDATE ON pGallery.* TO 'gallery'@'localhost';