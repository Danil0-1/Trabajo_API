CREATE DATABASE  taller_api;

DROP DATABASE taller_api;

USE taller_api;

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100)
);

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    precio DECIMAL(10,2),
    categoria_id INT,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE promociones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descripcion TEXT,
    descuento DECIMAL(5,2),
    producto_id INT,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

INSERT INTO categorias (nombre) VALUES ('Electrodomesticos'), ('Ropa'), ('Comida');

INSERT INTO productos (nombre, precio, categoria_id) VALUES
('Camisa', 29.99, 2),
('Smartphone', 1500.00, 1),
('Pizza', 20.00, 3),
('Auriculares', 250.00, 1),
('Hamburguesa', 15.00, 3);

INSERT INTO promociones (descripcion, descuento, producto_id) VALUES
('Descuento', 25.00, 1),
('Promo de navidad', 40.00, 2);
