CREATE TABLE users (
    id int(11) NOT NULL AUTO_INCREMENT,
    unique_id varchar(23) NOT NULL,
    name varchar(50) NOT NULL,
    email varchar(50) NOT NULL,
    encrypted_password varchar(256) NOT NULL,
    salt varchar(10) NOT NULL,
    created_at datetime DEFAULT NULL,
    PRIMARY KEY (id)
)
CREATE TABLE password_reset_request (
    id int(11) NOT NULL AUTO_INCREMENT,
    email varchar(50) NOT NULL,
    encrypted_temp_password varchar(256) NOT NULL,
    salt varchar(10) NOT NULL,
    created_at datetime DEFAULT NULL,
    PRIMARY KEY (id)
)
