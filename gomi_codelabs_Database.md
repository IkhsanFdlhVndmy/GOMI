CREATE DATABASE gomi_codelabs;

USE gomi_codelabs;

-- Tabel data_sampah harus dibuat terlebih dahulu
CREATE TABLE data_sampah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_sampah ENUM('Plastik', 'Kaleng', 'Kaca') NOT NULL,
    poin_per_kg INT NOT NULL
);

-- Insert data awal untuk data_sampah
INSERT INTO data_sampah (kategori_sampah, poin_per_kg) VALUES
('Plastik', 5),
('Kaleng', 10),
('Kaca', 15);

-- Tabel data_user
CREATE TABLE data_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    point INT DEFAULT 0,
    sampah_terkumpul INT DEFAULT 0
);

-- Tabel transaksi
CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type ENUM('uang', 'hadiah'),
    value VARCHAR(100),
    points_used INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES data_user(id)
);

-- Tabel transaksi_sampah
CREATE TABLE transaksi_sampah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    sampah_id INT NOT NULL,
    berat_kg FLOAT NOT NULL,
    poin_ditambahkan INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES data_user(id),
    FOREIGN KEY (sampah_id) REFERENCES data_sampah(id)
);
