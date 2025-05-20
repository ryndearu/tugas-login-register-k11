-- Buat database
CREATE DATABASE IF NOT EXISTS login_register_k11;
USE login_register_k11;

-- Buat tabel users
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- password tidak di enkripsi
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert admin default
INSERT INTO users (username, email, password, role) VALUES ('admin', 'admin@gmail.com', 'admin123', 'admin');
