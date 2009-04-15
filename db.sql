CREATE DATABASE maindb;
use maindb;
CREATE TABLE sessions (
         id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
         username VARCHAR(100),
         ip VARCHAR(16),
         mac VARCHAR(50),
         start VARCHAR(100),
         end VARCHAR(100)
       );
