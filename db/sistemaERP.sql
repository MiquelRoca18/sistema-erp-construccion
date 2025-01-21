CREATE DATABASE sistema_erp_construccion;

USE sistema_erp_construccion;

CREATE TABLE empleados(
	empleados_id INT AUTO_INCREMENT PRIMARY KEY,
  	nombre VARCHAR(255) NOT NULL,
  	rol VARCHAR(355) NOT NULL,
  	fecha_contratacion DATE,
  	telefono VARCHAR(15) NOT NULL,
  	correo VARCHAR(255) NOT NULL
);

CREATE TABLE proyectos(
	proyectos_id INT AUTO_INCREMENT PRIMARY KEY,
  	responsable_id INT,
  	estado ENUM ('pendiente','en progreso','finalizado') DEFAULT 'pendiente',
  	nombre_proyecto VARCHAR(255) NOT NULL,
  	fecha_inicio DATE,
  	fecha_fin DATE,
  	descripcion TEXT,
  	FOREIGN KEY (responsable_id) REFERENCES empleados(empleados_id)
);

CREATE TABLE tareas(
	tareas_id INT AUTO_INCREMENT PRIMARY KEY,
    estado ENUM ('pendiente','en progreso','finalizado') DEFAULT 'pendiente',
  	nombre_tarea VARCHAR(255) NOT NULL,
  	descripcion text, 
  	proyectos_id INT,
  	fecha_inicio DATE,
  	fecha_fin DATE,
  	FOREIGN KEY (proyectos_id) REFERENCES proyectos(proyectos_id)
);

CREATE TABLE presupuestos(
	presupuestos_id INT AUTO_INCREMENT PRIMARY KEY,
  	proyectos_id INT,
  	equipos DECIMAL (10, 2) DEFAULT 0,
    mano_obra DECIMAL (10, 2) DEFAULT 0,
  	materiales DECIMAL (10, 2) DEFAULT 0,
    FOREIGN KEY (proyectos_id) REFERENCES proyectos(proyectos_id)
);



CREATE TABLE empleados_tareas(
	tareas_id INT,
  	empleados_id INT,
  	PRIMARY KEY (tareas_id, empleados_id),
  	FOREIGN KEY (tareas_id) REFERENCES tareas(tareas_id),
  	FOREIGN KEY (empleados_id) REFERENCES empleados(empleados_id)
);

CREATE TABLE roles(
	id_rol INT AUTO_INCREMENT PRIMARY KEY,
  	rol ENUM ('usuario', 'admin') NOT NULL
);

CREATE TABLE autenticacion (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    empleados_id INT,
    username VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    id_rol INT,
    FOREIGN KEY (empleados_id) REFERENCES empleados(empleados_id) ON DELETE CASCADE,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);

-- Insertar roles
INSERT INTO roles (rol) VALUES ('admin');
INSERT INTO roles (rol) VALUES ('usuario');

-- Insertar empleados
INSERT INTO empleados (nombre, rol, fecha_contratacion, telefono, correo) VALUES
('Juan Pérez', 'Arquitecto', '2020-01-15', '651234567', 'juan.perez@email.com'),
('Ana Gómez', 'Albañil', '2021-02-20', '652345678', 'ana.gomez@email.com'),
('Carlos López', 'Ingeniero', '2019-07-10', '653456789', 'carlos.lopez@email.com'),
('María Sánchez', 'Jefe de obra', '2018-11-05', '654567890', 'maria.sanchez@email.com'),
('David Martínez', 'Electricista', '2022-05-25', '655678901', 'david.martinez@email.com');

-- Insertar proyectos
INSERT INTO proyectos (responsable_id, estado, nombre_proyecto, fecha_inicio, fecha_fin, descripcion) VALUES
(1, 'pendiente', 'Rehabilitación de la Plaza Mayor', NULL, NULL, 'Rehabilitación de la Plaza Mayor de Xàtiva, incluye cambios en pavimento y zonas verdes.'),
(2, 'en progreso', 'Construcción del Centro de Salud', '2024-02-01', NULL, 'Construcción del nuevo centro de salud en la zona sur de Xàtiva.'),
(3, 'finalizado', 'Urbanización Parque Industrial', '2023-05-15', '2023-11-30', 'Urbanización de la zona industrial en el norte de Xàtiva, con calles, instalaciones y servicios.');

-- Insertar tareas
INSERT INTO tareas (estado, nombre_tarea, descripcion, proyectos_id, fecha_inicio, fecha_fin) VALUES
('pendiente', 'Rehabilitación fachada', 'Rehabilitar la fachada principal del edificio de la Casa Ayora.', 1, NULL, NULL),
('en progreso', 'Instalación de fontanería', 'Instalar sistema de fontanería para las nuevas viviendas del proyecto Xàtiva.', 2, '2024-01-20', NULL),
('finalizado', 'Construcción de la base de la nave', 'Excavación y cimentación para la construcción de la nave industrial.', 3, '2024-04-10', '2024-05-15');


-- Insertar presupuestos
INSERT INTO presupuestos (proyectos_id, equipos, mano_obra, materiales) VALUES
(1, 15000.00, 25000.00, 10000.00),
(2, 20000.00, 45000.00, 25000.00),
(3, 30000.00, 60000.00, 40000.00);

-- Asignar empleados a tareas
INSERT INTO empleados_tareas (tareas_id, empleados_id) VALUES
(1, 1), -- Juan Pérez (Arquitecto) en la tarea 'Rehabilitación fachada'
(2, 2), -- Ana Gómez (Albañil) en la tarea 'Instalación de fontanería'
(3, 4), -- María Sánchez (Jefe de obra) en la tarea 'Construcción de la base de la nave'
(3, 5); -- David Martínez (Electricista) en la tarea 'Construcción de la base de la nave'

-- Insertar autenticación
INSERT INTO autenticacion (empleados_id, username, password_hash, id_rol) VALUES
(1, 'juan.perez', 'hashed_password_juan', 1), -- Admin
(2, 'ana.gomez', 'hashed_password_ana', 2), -- Usuario
(3, 'carlos.lopez', 'hashed_password_carlos', 2), -- Usuario
(4, 'maria.sanchez', 'hashed_password_maria', 2), -- Usuario
(5, 'david.martinez', 'hashed_password_david', 2); -- Usuario
