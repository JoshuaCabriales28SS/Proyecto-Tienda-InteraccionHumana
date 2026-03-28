-- Crear base de datos
CREATE DATABASE IF NOT EXISTS db_tiendaonline_crud;
USE db_tiendaonline_crud;


CREATE TABLE categorias (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            nombre VARCHAR(100) NOT NULL
);


CREATE TABLE productos (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           nombre VARCHAR(150) NOT NULL,
                           precio DECIMAL(10,2) NOT NULL,
                           imagen VARCHAR(255),
                           descripcion TEXT,
                           codigo VARCHAR(50),
                           categoria_id INT,
                           stock INT DEFAULT 0,
                           FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);


INSERT INTO categorias (nombre) VALUES
                                    ('Electronica'),
                                    ('Ropa'),
                                    ('Hogar'),
                                    ('Deportes'),
                                    ('Juguetes'),
                                    ('Libros'),
                                    ('Belleza'),
                                    ('Automotriz'),
                                    ('Tecnología'),
                                    ('Accesorios'),
                                    ('Salud'),
                                    ('Alimentos');


CREATE TABLE carrito (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         producto_id INT,
                         cantidad INT DEFAULT 1,
                         fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         FOREIGN KEY (producto_id) REFERENCES productos(id)
);

INSERT INTO productos (nombre, precio, imagen, descripcion, codigo, categoria_id, stock) VALUES

                                                                                             ('Laptop HP',14500.00,'laptop.jpg','Laptop portátil ideal para trabajo y estudio con 8GB RAM','1234567890',1,10),
                                                                                             ('Mouse Logitech',350.00,'mouse.jpg','Mouse inalámbrico ergonómico','1234567891',9,25),
                                                                                             ('Teclado Mecánico',1200.00,'teclado.jpg','Teclado RGB para gaming','1234567892',9,15),
                                                                                             ('Monitor 24"',3200.00,'monitor.jpg','Monitor Full HD','1234567893',1,8),
                                                                                             ('Playera Nike',500.00,'playera.jpg','Playera cómoda','1234567894',2,50),
                                                                                             ('Pantalón Jeans',800.00,'jeans.jpg','Pantalón resistente','1234567895',2,40),
                                                                                             ('Sofá 3 plazas',8500.00,'sofa.jpg','Sofá cómodo','1234567896',3,5),
                                                                                             ('Lámpara LED',450.00,'lampara.jpg','Lámpara ahorro energía','1234567897',3,20),
                                                                                             ('Balón fútbol',300.00,'balon.jpg','Balón resistente','1234567898',4,30),
                                                                                             ('Raqueta tenis',1200.00,'raqueta.jpg','Raqueta ligera','1234567899',4,12),
                                                                                             ('Muñeca Barbie',600.00,'barbie.jpg','Muñeca clásica','1234567800',5,18),
                                                                                             ('LEGO Set',1500.00,'lego.jpg','Set creativo','1234567801',5,10),
                                                                                             ('Libro Java',400.00,'libro.jpg','Aprende Java','1234567802',6,22),
                                                                                             ('Libro SQL',350.00,'sql.jpg','Guía SQL','1234567803',6,18),
                                                                                             ('Perfume Dior',2500.00,'perfume.jpg','Fragancia elegante','1234567804',7,7),
                                                                                             ('Crema Facial',300.00,'crema.jpg','Cuidado de piel','1234567805',7,25),
                                                                                             ('Aceite Motor',900.00,'aceite.jpg','Aceite sintético','1234567806',8,14),
                                                                                             ('Batería Auto',2200.00,'bateria.jpg','Batería 12V','1234567807',8,6),
                                                                                             ('Tablet Samsung',6500.00,'tablet.jpg','Tablet Android','1234567808',9,9),
                                                                                             ('Smartwatch',1800.00,'watch.jpg','Reloj inteligente','1234567809',9,13);