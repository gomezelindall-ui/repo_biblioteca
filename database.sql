-- Base de datos: jairoapi_envios_repositorio
-- Este archivo se puede importar después de seleccionar la BD en AlwaysData.

CREATE TABLE IF NOT EXISTS usuarios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  cedula VARCHAR(20) UNIQUE NOT NULL,
  telefono VARCHAR(15),
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS libros (
  id INT PRIMARY KEY AUTO_INCREMENT,
  codigo VARCHAR(50) UNIQUE NOT NULL,
  titulo VARCHAR(150) NOT NULL,
  autor VARCHAR(100) NOT NULL,
  unidades INT DEFAULT 1,
  portada LONGBLOB,
  descripcion TEXT,
  genero VARCHAR(50),
  fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS prestamos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_id INT NOT NULL,
  libro_id INT NOT NULL,
  fecha_prestamo DATE NOT NULL,
  fecha_devolucion DATE,
  estado VARCHAR(20) DEFAULT 'Activo',
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Datos iniciales: solo se insertan si las tablas están vacías.
INSERT INTO usuarios (nombre, cedula, telefono)
SELECT * FROM (SELECT 'María López','1234567890','3001234567') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM usuarios LIMIT 1);

INSERT INTO usuarios (nombre, cedula, telefono)
SELECT 'Carlos Ruiz','0987654321','3009876543' WHERE (SELECT COUNT(*) FROM usuarios) = 1;
INSERT INTO usuarios (nombre, cedula, telefono)
SELECT 'Ana García','1122334455','3005555555' WHERE (SELECT COUNT(*) FROM usuarios) = 2;
INSERT INTO usuarios (nombre, cedula, telefono)
SELECT 'Juan Pérez','1000000004','3011111111' WHERE (SELECT COUNT(*) FROM usuarios) = 3;
INSERT INTO usuarios (nombre, cedula, telefono)
SELECT 'Laura Torres','1000000005','3022222222' WHERE (SELECT COUNT(*) FROM usuarios) = 4;
INSERT INTO usuarios (nombre, cedula, telefono)
SELECT 'Pedro Gómez','1000000006','3033333333' WHERE (SELECT COUNT(*) FROM usuarios) = 5;
INSERT INTO usuarios (nombre, cedula, telefono)
SELECT 'Sofía Ramírez','1000000007','3044444444' WHERE (SELECT COUNT(*) FROM usuarios) = 6;
INSERT INTO usuarios (nombre, cedula, telefono)
SELECT 'Diego Castro','1000000008','3055555555' WHERE (SELECT COUNT(*) FROM usuarios) = 7;
INSERT INTO usuarios (nombre, cedula, telefono)
SELECT 'Valentina Herrera','1000000009','3066666666' WHERE (SELECT COUNT(*) FROM usuarios) = 8;
INSERT INTO usuarios (nombre, cedula, telefono)
SELECT 'Andrés Martínez','1000000010','3077777777' WHERE (SELECT COUNT(*) FROM usuarios) = 9;

INSERT INTO libros (codigo,titulo,autor,unidades,descripcion,genero)
SELECT 'L001','El principito','Antoine de Saint-Exupéry',3,'Un pequeño príncipe llega a la Tierra desde un asteroide','Novela infantil'
WHERE NOT EXISTS (SELECT 1 FROM libros LIMIT 1);
INSERT INTO libros (codigo,titulo,autor,unidades,descripcion,genero) SELECT 'L002','Cien años de soledad','Gabriel García Márquez',5,'La historia de la familia Buendía en Macondo','Novela' WHERE (SELECT COUNT(*) FROM libros)=1;
INSERT INTO libros (codigo,titulo,autor,unidades,descripcion,genero) SELECT 'L003','1984','George Orwell',5,'Una distopía sobre un régimen totalitario','Ciencia ficción' WHERE (SELECT COUNT(*) FROM libros)=2;
INSERT INTO libros (codigo,titulo,autor,unidades,descripcion,genero) SELECT 'L004','Don Quijote de la Mancha','Miguel de Cervantes',2,'Las aventuras de un hidalgo que quiere ser caballero','Clásico' WHERE (SELECT COUNT(*) FROM libros)=3;
INSERT INTO libros (codigo,titulo,autor,unidades,descripcion,genero) SELECT 'L005','La vorágine','José Eustasio Rivera',4,'Novela colombiana sobre la selva amazónica','Clásico colombiano' WHERE (SELECT COUNT(*) FROM libros)=4;
INSERT INTO libros (codigo,titulo,autor,unidades,descripcion,genero) SELECT 'L006','El amor en los tiempos del cólera','Gabriel García Márquez',4,'Historia de amor a través de los años','Romance' WHERE (SELECT COUNT(*) FROM libros)=5;
INSERT INTO libros (codigo,titulo,autor,unidades,descripcion,genero) SELECT 'L007','Crónica de una muerte anunciada','Gabriel García Márquez',3,'Novela breve sobre un crimen anunciado','Novela' WHERE (SELECT COUNT(*) FROM libros)=6;
INSERT INTO libros (codigo,titulo,autor,unidades,descripcion,genero) SELECT 'L008','La Odisea','Homero',2,'Viaje de regreso de Odiseo a Ítaca','Clásico' WHERE (SELECT COUNT(*) FROM libros)=7;
INSERT INTO libros (codigo,titulo,autor,unidades,descripcion,genero) SELECT 'L009','Rayuela','Julio Cortázar',3,'Novela experimental y latinoamericana','Novela' WHERE (SELECT COUNT(*) FROM libros)=8;
INSERT INTO libros (codigo,titulo,autor,unidades,descripcion,genero) SELECT 'L010','El túnel','Ernesto Sabato',3,'Novela psicológica argentina','Novela' WHERE (SELECT COUNT(*) FROM libros)=9;

-- Préstamos iniciales, solo si la tabla está vacía.
INSERT INTO prestamos (usuario_id,libro_id,fecha_prestamo,estado)
SELECT 1,1,'2026-09-01','Activo' WHERE NOT EXISTS (SELECT 1 FROM prestamos LIMIT 1);
INSERT INTO prestamos (usuario_id,libro_id,fecha_prestamo,estado)
SELECT 2,2,'2026-08-28','Activo' WHERE (SELECT COUNT(*) FROM prestamos)=1;
INSERT INTO prestamos (usuario_id,libro_id,fecha_prestamo,fecha_devolucion,estado)
SELECT 3,3,'2026-08-20','2026-08-27','Devuelto' WHERE (SELECT COUNT(*) FROM prestamos)=2;
INSERT INTO prestamos (usuario_id,libro_id,fecha_prestamo,estado)
SELECT 4,4,'2026-09-03','Activo' WHERE (SELECT COUNT(*) FROM prestamos)=3;
INSERT INTO prestamos (usuario_id,libro_id,fecha_prestamo,fecha_devolucion,estado)
SELECT 5,5,'2026-08-10','2026-08-17','Devuelto' WHERE (SELECT COUNT(*) FROM prestamos)=4;