-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS sistema_erp_construccion;

-- Seleccionar la base de datos
USE sistema_erp_construccion;

-- Tabla de empleados
CREATE TABLE IF NOT EXISTS empleados(
    empleados_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    rol VARCHAR(355) NOT NULL,
    fecha_contratacion DATE,
    telefono VARCHAR(15) NOT NULL,
    correo VARCHAR(255) NOT NULL
);

-- Tabla de proyectos
CREATE TABLE IF NOT EXISTS proyectos(
    proyectos_id INT AUTO_INCREMENT PRIMARY KEY,
    responsable_id INT,
    estado ENUM ('pendiente','en progreso','finalizado') DEFAULT 'pendiente',
    nombre_proyecto VARCHAR(255) NOT NULL,
    fecha_inicio DATE,
    fecha_fin DATE,
    descripcion TEXT,
    FOREIGN KEY (responsable_id) REFERENCES empleados(empleados_id)
);

-- Tabla de tareas
CREATE TABLE IF NOT EXISTS tareas(
    tareas_id INT AUTO_INCREMENT PRIMARY KEY,
    estado ENUM ('pendiente','en progreso','finalizado') DEFAULT 'pendiente',
    nombre_tarea VARCHAR(255) NOT NULL,
    descripcion TEXT,
    proyectos_id INT,
    fecha_inicio DATE,
    fecha_fin DATE,
    FOREIGN KEY (proyectos_id) REFERENCES proyectos(proyectos_id)
);

-- Tabla de presupuestos
CREATE TABLE IF NOT EXISTS presupuestos(
    presupuestos_id INT AUTO_INCREMENT PRIMARY KEY,
    proyectos_id INT,
    equipos DECIMAL(10, 2) DEFAULT 0,
    mano_obra DECIMAL(10, 2) DEFAULT 0,
    materiales DECIMAL(10, 2) DEFAULT 0,
    FOREIGN KEY (proyectos_id) REFERENCES proyectos(proyectos_id)
);

-- Tabla de relación entre empleados y tareas
CREATE TABLE IF NOT EXISTS empleados_tareas(
    tareas_id INT,
    empleados_id INT,
    PRIMARY KEY (tareas_id, empleados_id),
    FOREIGN KEY (tareas_id) REFERENCES tareas(tareas_id),
    FOREIGN KEY (empleados_id) REFERENCES empleados(empleados_id)
);

-- Tabla de roles
CREATE TABLE IF NOT EXISTS roles(
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    rol ENUM ('usuario', 'admin') NOT NULL
);

-- Tabla de autenticación
CREATE TABLE IF NOT EXISTS autenticacion (
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
('David Martínez', 'Electricista', '2022-05-25', '655678901', 'david.martinez@email.com'),
('Laura Fernández', 'Diseñadora', '2021-06-12', '656789012', 'laura.fernandez@email.com'),
('Sergio Ramírez', 'Albañil', '2020-03-08', '657890123', 'sergio.ramirez@email.com'),
('Patricia Torres', 'Ingeniera', '2019-09-25', '658901234', 'patricia.torres@email.com'),
('Javier Castillo', 'Topógrafo', '2018-12-11', '659012345', 'javier.castillo@email.com'),
('Elena Morales', 'Jefa de obra', '2022-07-15', '660123456', 'elena.morales@email.com');

-- Insertar presupuestos
INSERT INTO presupuestos (proyectos_id, equipos, mano_obra, materiales) VALUES
(1, 12000.00, 22000.00, 8000.00),
(2, 18000.00, 40000.00, 20000.00),
(3, 25000.00, 50000.00, 35000.00),
(4, 10000.00, 15000.00, 7000.00),
(5, 22000.00, 35000.00, 17000.00),
(6, 30000.00, 55000.00, 40000.00),
(7, 50000.00, 90000.00, 60000.00),
(8, 16000.00, 28000.00, 12000.00),
(9, 28000.00, 60000.00, 35000.00),
(10, 34000.00, 75000.00, 45000.00);

-- Insertar empleados en tareas
INSERT INTO empleados_tareas (tareas_id, empleados_id) VALUES
(1, 1),  -- 'Juan Pérez' en 'Inspección estructural inicial'
(1, 3),  -- 'Carlos López' en 'Inspección estructural inicial'
(2, 2),  -- 'Ana Gómez' en 'Limpieza de escombros'
(2, 4),  -- 'María Sánchez' en 'Limpieza de escombros'
(3, 1),  -- 'Juan Pérez' en 'Revisión arqueológica'
(3, 3),  -- 'Carlos López' en 'Revisión arqueológica'
(4, 5),  -- 'David Martínez' en 'Refuerzo de cimientos'
(4, 2),  -- 'Ana Gómez' en 'Refuerzo de cimientos'
(5, 4),  -- 'María Sánchez' en 'Reparación de muros exteriores'
(5, 5),  -- 'David Martínez' en 'Reparación de muros exteriores'
(6, 1),  -- 'Juan Pérez' en 'Tratamiento contra la humedad'
(6, 2),  -- 'Ana Gómez' en 'Tratamiento contra la humedad'
(7, 3),  -- 'Carlos López' en 'Reforzamiento de torres'
(7, 4),  -- 'María Sánchez' en 'Reforzamiento de torres'
(8, 2),  -- 'Ana Gómez' en 'Instalación de iluminación histórica'
(8, 5),  -- 'David Martínez' en 'Instalación de iluminación histórica'
(9, 3),  -- 'Carlos López' en 'Restauración de accesos y escaleras'
(9, 4),  -- 'María Sánchez' en 'Restauración de accesos y escaleras'
(10, 1),  -- 'Juan Pérez' en 'Supervisión final y entrega'
(11, 6),  -- 'Laura Fernández' en 'Diseño y planificación arquitectónica'
(11, 8),  -- 'Patricia Torres' en 'Diseño y planificación arquitectónica'
(12, 7),  -- 'Sergio Ramírez' en 'Preparación del terreno'
(12, 10), -- 'Elena Morales' en 'Preparación del terreno'
(13, 5),  -- 'David Martínez' en 'Cimentación y estructuras básicas'
(13, 6),  -- 'Laura Fernández' en 'Cimentación y estructuras básicas'
(14, 9),  -- 'Javier Castillo' en 'Construcción de vestuarios y baños'
(14, 10), -- 'Elena Morales' en 'Construcción de vestuarios y baños'
(15, 7),  -- 'Sergio Ramírez' en 'Instalación eléctrica y de fontanería'
(15, 8),  -- 'Patricia Torres' en 'Instalación eléctrica y de fontanería'
(16, 5),  -- 'David Martínez' en 'Construcción de nuevas pistas deportivas'
(16, 9),  -- 'Javier Castillo' en 'Construcción de nuevas pistas deportivas'
(17, 6),  -- 'Laura Fernández' en 'Colocación de techado y cerramientos'
(17, 8),  -- 'Patricia Torres' en 'Colocación de techado y cerramientos'
(18, 10), -- 'Elena Morales' en 'Pintura y acabados finales'
(18, 5),  -- 'David Martínez' en 'Pintura y acabados finales'
(19, 9),  -- 'Javier Castillo' en 'Instalación de mobiliario y equipamiento'
(19, 6),  -- 'Laura Fernández' en 'Instalación de mobiliario y equipamiento'
(20, 7),  -- 'Sergio Ramírez' en 'Revisión y entrega del proyecto'
(21, 6),  -- 'Laura Fernández' en 'Evaluación estructural del edificio'
(21, 8),  -- 'Patricia Torres' en 'Evaluación estructural del edificio'
(22, 7),  -- 'Sergio Ramírez' en 'Refuerzo de cimentaciones y estructura'
(22, 10), -- 'Elena Morales' en 'Refuerzo de cimentaciones y estructura'
(23, 5),  -- 'David Martínez' en 'Renovación del sistema eléctrico'
(23, 6),  -- 'Laura Fernández' en 'Renovación del sistema eléctrico'
(24, 9),  -- 'Javier Castillo' en 'Instalación de sistema contra incendios'
(24, 10), -- 'Elena Morales' en 'Instalación de sistema contra incendios'
(25, 7),  -- 'Sergio Ramírez' en 'Reparación y mejora de la acústica'
(25, 8),  -- 'Patricia Torres' en 'Reparación y mejora de la acústica'
(26, 5),  -- 'David Martínez' en 'Modernización del escenario'
(26, 9),  -- 'Javier Castillo' en 'Modernización del escenario'
(27, 6),  -- 'Laura Fernández' en 'Sustitución de butacas y mobiliario'
(27, 8),  -- 'Patricia Torres' en 'Sustitución de butacas y mobiliario'
(28, 10), -- 'Elena Morales' en 'Restauración de la fachada y ornamentos'
(28, 5),  -- 'David Martínez' en 'Restauración de la fachada y ornamentos'
(29, 9),  -- 'Javier Castillo' en 'Pintura y acabados interiores'
(29, 6),  -- 'Laura Fernández' en 'Pintura y acabados interiores'
(30, 7),  -- 'Sergio Ramírez' en 'Inspección final y apertura'
(31, 1),  -- 'Juan Pérez' en 'Demolición del pavimento antiguo'
(31, 4),  -- 'María Sánchez' en 'Demolición del pavimento antiguo'
(32, 2),  -- 'Ana Gómez' en 'Renovación de las redes de agua y alcantarillado'
(32, 5),  -- 'David Martínez' en 'Renovación de las redes de agua y alcantarillado'
(33, 3),  -- 'Carlos López' en 'Instalación del nuevo sistema de drenaje'
(33, 6),  -- 'Laura Fernández' en 'Instalación del nuevo sistema de drenaje'
(34, 4),  -- 'María Sánchez' en 'Colocación de nueva base de pavimento'
(34, 9),  -- 'Javier Castillo' en 'Colocación de nueva base de pavimento'
(35, 5),  -- 'David Martínez' en 'Asfaltado y señalización vial'
(35, 10), -- 'Elena Morales' en 'Asfaltado y señalización vial'
(36, 6),  -- 'Laura Fernández' en 'Renovación del alumbrado público'
(36, 7),  -- 'Sergio Ramírez' en 'Renovación del alumbrado público'
(37, 3),  -- 'Carlos López' en 'Colocación de mobiliario urbano'
(37, 8),  -- 'Patricia Torres' en 'Colocación de mobiliario urbano'
(38, 2),  -- 'Ana Gómez' en 'Plantación de árboles y áreas verdes'
(38, 4),  -- 'María Sánchez' en 'Plantación de árboles y áreas verdes'
(39, 9),  -- 'Javier Castillo' en 'Construcción de carril bici'
(39, 10), -- 'Elena Morales' en 'Construcción de carril bici'
(40, 1),  -- 'Juan Pérez' en 'Inspección final y apertura al público'
(40, 5),  -- 'David Martínez' en 'Inspección final y apertura al público'
(41, 1),  -- 'Juan Pérez' en 'Excavación y nivelación del terreno'
(41, 5),  -- 'David Martínez' en 'Excavación y nivelación del terreno'
(42, 2),  -- 'Ana Gómez' en 'Cimentación y estructura de hormigón'
(42, 6),  -- 'Laura Fernández' en 'Cimentación y estructura de hormigón'
(43, 3),  -- 'Carlos López' en 'Levantamiento de muros y tabiques'
(43, 7),  -- 'Sergio Ramírez' en 'Levantamiento de muros y tabiques'
(44, 4),  -- 'María Sánchez' en 'Instalación de cubiertas y tejados'
(44, 9),  -- 'Javier Castillo' en 'Instalación de cubiertas y tejados'
(45, 5),  -- 'David Martínez' en 'Instalación de sistemas eléctricos'
(45, 10), -- 'Elena Morales' en 'Instalación de sistemas eléctricos'
(46, 6),  -- 'Laura Fernández' en 'Instalación de fontanería y saneamiento'
(46, 2),  -- 'Ana Gómez' en 'Instalación de fontanería y saneamiento'
(47, 7),  -- 'Sergio Ramírez' en 'Revestimientos y acabados interiores'
(47, 3),  -- 'Carlos López' en 'Revestimientos y acabados interiores'
(48, 8),  -- 'Patricia Torres' en 'Colocación de ventanas y puertas'
(48, 4),  -- 'María Sánchez' en 'Colocación de ventanas y puertas'
(49, 9),  -- 'Javier Castillo' en 'Equipamiento de aulas y mobiliario'
(49, 1),  -- 'Juan Pérez' en 'Equipamiento de aulas y mobiliario'
(50, 10), -- 'Elena Morales' en 'Inspección y pruebas de seguridad'
(50, 5),  -- 'David Martínez' en 'Inspección y pruebas de seguridad'
(51, 1),  -- 'Juan Pérez' en 'Estudio de viabilidad y planificación'
(51, 5),  -- 'David Martínez' en 'Estudio de viabilidad y planificación'
(52, 2),  -- 'Ana Gómez' en 'Obtención de permisos y regulaciones'
(52, 6),  -- 'Laura Fernández' en 'Obtención de permisos y regulaciones'
(53, 3),  -- 'Carlos López' en 'Compra y transporte de materiales'
(53, 7),  -- 'Sergio Ramírez' en 'Compra y transporte de materiales'
(54, 4),  -- 'María Sánchez' en 'Preparación de los techos e infraestructuras'
(54, 9),  -- 'Javier Castillo' en 'Preparación de los techos e infraestructuras'
(55, 5),  -- 'David Martínez' en 'Instalación de soportes y estructura base'
(55, 10), -- 'Elena Morales' en 'Instalación de soportes y estructura base'
(56, 6),  -- 'Laura Fernández' en 'Montaje de paneles solares'
(56, 2),  -- 'Ana Gómez' en 'Montaje de paneles solares'
(57, 7),  -- 'Sergio Ramírez' en 'Conexión del sistema eléctrico'
(57, 3),  -- 'Carlos López' en 'Conexión del sistema eléctrico'
(58, 8),  -- 'Patricia Torres' en 'Pruebas de funcionamiento y eficiencia'
(58, 4),  -- 'María Sánchez' en 'Pruebas de funcionamiento y eficiencia'
(59, 9),  -- 'Javier Castillo' en 'Capacitación del personal técnico'
(59, 1),  -- 'Juan Pérez' en 'Capacitación del personal técnico'
(60, 10), -- 'Elena Morales' en 'Inspección final y puesta en marcha'
(60, 5),  -- 'David Martínez' en 'Inspección final y puesta en marcha'
(61, 1),  -- 'Juan Pérez' en 'Estudio de conservación y restauración'
(61, 5),  -- 'David Martínez' en 'Estudio de conservación y restauración'
(62, 2),  -- 'Ana Gómez' en 'Obtención de permisos y licencias'
(62, 6),  -- 'Laura Fernández' en 'Obtención de permisos y licencias'
(63, 3),  -- 'Carlos López' en 'Limpieza y desescombro'
(63, 7),  -- 'Sergio Ramírez' en 'Limpieza y desescombro'
(64, 4),  -- 'María Sánchez' en 'Refuerzo estructural'
(64, 9),  -- 'Javier Castillo' en 'Refuerzo estructural'
(65, 5),  -- 'David Martínez' en 'Restauración de fachadas y elementos arquitectónicos'
(65, 10), -- 'Elena Morales' en 'Restauración de fachadas y elementos arquitectónicos'
(66, 6),  -- 'Laura Fernández' en 'Instalación de sistemas eléctricos y de iluminación'
(66, 2),  -- 'Ana Gómez' en 'Instalación de sistemas eléctricos y de iluminación'
(67, 7),  -- 'Sergio Ramírez' en 'Adecuación de espacios para exposiciones y actividades culturales'
(67, 3),  -- 'Carlos López' en 'Adecuación de espacios para exposiciones y actividades culturales'
(68, 8),  -- 'Patricia Torres' en 'Pintura y acabado interior'
(68, 4),  -- 'María Sánchez' en 'Pintura y acabado interior'
(69, 9),  -- 'Javier Castillo' en 'Instalación de sistemas de climatización'
(69, 1),  -- 'Juan Pérez' en 'Instalación de sistemas de climatización'
(70, 10), -- 'Elena Morales' en 'Inspección final y puesta en marcha'
(70, 5),  -- 'David Martínez' en 'Inspección final y puesta en marcha'
(71, 1),  -- 'Juan Pérez' en 'Inspección inicial de la red de alcantarillado'
(71, 9),  -- 'Javier Castillo' en 'Inspección inicial de la red de alcantarillado'
(72, 2),  -- 'Ana Gómez' en 'Desvío de tráfico y señalización'
(72, 6),  -- 'Laura Fernández' en 'Desvío de tráfico y señalización'
(73, 3),  -- 'Carlos López' en 'Preparación de zanjas para tuberías nuevas'
(73, 5),  -- 'David Martínez' en 'Preparación de zanjas para tuberías nuevas'
(74, 4),  -- 'María Sánchez' en 'Instalación de nuevas tuberías de alcantarillado'
(74, 7),  -- 'Sergio Ramírez' en 'Instalación de nuevas tuberías de alcantarillado'
(75, 5),  -- 'David Martínez' en 'Conexión de tuberías a la red principal'
(75, 10), -- 'Elena Morales' en 'Conexión de tuberías a la red principal'
(76, 6),  -- 'Laura Fernández' en 'Rehabilitación de pozos de acceso'
(76, 2),  -- 'Ana Gómez' en 'Rehabilitación de pozos de acceso'
(77, 7),  -- 'Sergio Ramírez' en 'Verificación de la integridad de las nuevas tuberías'
(77, 3),  -- 'Carlos López' en 'Verificación de la integridad de las nuevas tuberías'
(78, 8),  -- 'Patricia Torres' en 'Relleno y compactación de zanjas'
(78, 4),  -- 'María Sánchez' en 'Relleno y compactación de zanjas'
(79, 9),  -- 'Javier Castillo' en 'Pruebas de funcionamiento de la nueva red'
(79, 1),  -- 'Juan Pérez' en 'Pruebas de funcionamiento de la nueva red'
(80, 10), -- 'Elena Morales' en 'Restauración del pavimento y limpieza final'
(80, 5),  -- 'David Martínez' en 'Restauración del pavimento y limpieza final'
(81, 1),  -- 'Juan Pérez' en 'Desbroce y limpieza del terreno'
(81, 6),  -- 'Laura Fernández' en 'Desbroce y limpieza del terreno'
(82, 2),  -- 'Ana Gómez' en 'Estudio de la flora y fauna local'
(82, 5),  -- 'David Martínez' en 'Estudio de la flora y fauna local'
(83, 3),  -- 'Carlos López' en 'Marcado de las áreas de césped y senderos'
(83, 7),  -- 'Sergio Ramírez' en 'Marcado de las áreas de césped y senderos'
(84, 4),  -- 'María Sánchez' en 'Excavación y nivelación del terreno'
(84, 10), -- 'Elena Morales' en 'Excavación y nivelación del terreno'
(85, 5),  -- 'David Martínez' en 'Instalación de sistemas de riego'
(85, 9),  -- 'Javier Castillo' en 'Instalación de sistemas de riego'
(86, 6),  -- 'Laura Fernández' en 'Plantación de árboles y arbustos'
(86, 2),  -- 'Ana Gómez' en 'Plantación de árboles y arbustos'
(87, 7),  -- 'Sergio Ramírez' en 'Construcción de caminos peatonales'
(87, 3),  -- 'Carlos López' en 'Construcción de caminos peatonales'
(88, 8),  -- 'Patricia Torres' en 'Construcción de zonas de descanso'
(88, 4),  -- 'María Sánchez' en 'Construcción de zonas de descanso'
(89, 9),  -- 'Javier Castillo' en 'Instalación de alumbrado público'
(89, 1),  -- 'Juan Pérez' en 'Instalación de alumbrado público'
(90, 10), -- 'Elena Morales' en 'Revisión final y apertura del parque'
(90, 5),  -- 'David Martínez' en 'Revisión final y apertura del parque'
(91, 1),  -- 'Juan Pérez' en 'Excavación para los cimientos'
(91, 5),  -- 'David Martínez' en 'Excavación para los cimientos'
(92, 2),  -- 'Ana Gómez' en 'Preparación y vertido de hormigón'
(92, 6),  -- 'Laura Fernández' en 'Preparación y vertido de hormigón'
(93, 3),  -- 'Carlos López' en 'Instalación de estructuras de soporte'
(93, 9),  -- 'Javier Castillo' en 'Instalación de estructuras de soporte'
(94, 4),  -- 'María Sánchez' en 'Impermeabilización de la estructura'
(94, 7),  -- 'Sergio Ramírez' en 'Impermeabilización de la estructura'
(95, 5),  -- 'David Martínez' en 'Construcción de los niveles del aparcamiento'
(95, 10), -- 'Elena Morales' en 'Construcción de los niveles del aparcamiento'
(96, 6),  -- 'Laura Fernández' en 'Instalación de sistemas eléctricos'
(96, 2),  -- 'Ana Gómez' en 'Instalación de sistemas eléctricos'
(97, 7),  -- 'Sergio Ramírez' en 'Instalación de sistemas de ventilación'
(97, 3),  -- 'Carlos López' en 'Instalación de sistemas de ventilación'
(98, 8),  -- 'Patricia Torres' en 'Colocación de pavimento en los niveles'
(98, 4),  -- 'María Sánchez' en 'Colocación de pavimento en los niveles'
(99, 9),  -- 'Javier Castillo' en 'Señalización del aparcamiento'
(99, 1),  -- 'Juan Pérez' en 'Señalización del aparcamiento'
(100, 10), -- 'Elena Morales' en 'Revisión final y apertura del aparcamiento'
(100, 5);  -- 'David Martínez' en 'Revisión final y apertura del aparcamiento'

-- Insertar autenticación
INSERT INTO autenticacion (empleados_id, username, password_hash, id_rol) VALUES
(1, 'juan.perez', '$2y$10$vvMpIGSl1tnS8QMr19HOge1l0ZjxeMBZqvzqgbA92nTNKM1AiYkLi', 1), -- contraseña: jupe
(2, 'ana.gomez', '$2y$10$3caxYvh/3Zg8MsQvXs1Y0.rmCdXb3MqM6I4z7lH.7Y9QfM4MEIRfK', 2), -- contraseña: ango
(3, 'carlos.lopez', '$2y$10$oN2KkZO7o8Nn3bWmTjN4puGd9yfz0CEbWk7qf9nLptB2JafnWNK4q', 2), -- contraseña: calo
(4, 'maria.sanchez', '$2y$10$e01DHVlZqUKg4uK3RCuPme5Hpx97nJVAIwDKUwkOA8vgMeXBzHU36', 2), -- contraseña: masa
(5, 'david.martinez', '$2y$10$KtqKEQJpIrGo2lnKYoF.nOn0uvS.UGE0ZRLo7CqKO10A9qE7gzDTy', 2), -- contraseña: dama
(6, 'laura.fernandez', '$2y$10$39U7yWN6TwsT8nswCQUdZ.E.dSeWrncpD7QvdyZD0GRJWVuDOwzHi', 2), -- contraseña: lafe
(7, 'sergio.ramirez', '$2y$10$9FDMiG7WjGKpztkrH1EqhuyITJY1J1q0RIgHTZcKGLnr2N.d5CgU2', 2), -- contraseña: sera
(8, 'patricia.torres', '$2y$10$N4PJPDxJrV9bJ.4LCfwc5uHVTg1x6BZeZiQRZrBGIMAqfU0DXgdUa', 2), -- contraseña: pato
(9, 'javier.castillo', '$2y$10$FIuBfI2gW2T4I0AE7zDBzughXvw1A6Jy0DGMYPWijLvbZb1v.Vr6O', 2), -- contraseña: jaca
(10, 'elena.morales', '$2y$10$gTqbzYWS5/ZpDWVIECZxc.nDxhujJWBFWYGQtIKjyXzAPnYbhECee', 1); -- contraseña: elmo

-- Insertar proyectos
INSERT INTO proyectos (responsable_id, estado, nombre_proyecto, fecha_inicio, fecha_fin, descripcion) VALUES
(1, 'pendiente', 'Restauración del Castillo de Xàtiva', '2024-03-10', NULL, 'Proyecto de restauración de las murallas y torres del castillo de Xàtiva.'),
(2, 'en progreso', 'Ampliación del Polideportivo Municipal', '2024-01-15', '2024-12-20', 'Construcción de nuevas instalaciones deportivas en el polideportivo municipal.'),
(3, 'pendiente', 'Rehabilitación del Teatro Principal', '2024-05-01', NULL, 'Renovación del teatro, modernización del escenario y nuevas butacas.'),
(4, 'finalizado', 'Urbanización de la Avenida del Cid', '2023-06-01', '2023-11-30', 'Renovación del pavimento, alumbrado y mobiliario urbano en la avenida principal.'),
(5, 'en progreso', 'Construcción de un nuevo colegio público', '2024-02-10', NULL, 'Edificación de un nuevo centro educativo con capacidad para 500 alumnos.'),
(1, 'pendiente', 'Instalación de energía solar en edificios públicos', '2024-04-05', NULL, 'Colocación de paneles solares en edificios municipales para mejorar eficiencia energética.'),
(2, 'pendiente', 'Nuevo centro cultural en el casco histórico', '2024-06-01', NULL, 'Habilitación de un edificio antiguo para actividades culturales y exposiciones.'),
(3, 'en progreso', 'Renovación de la red de alcantarillado', '2023-12-15', NULL, 'Sustitución de tuberías antiguas en el centro de la ciudad.'),
(4, 'finalizado', 'Creación de un parque ecológico', '2023-05-10', '2023-10-20', 'Conversión de un terreno baldío en un parque con zonas verdes y rutas peatonales.'),
(5, 'pendiente', 'Construcción de un aparcamiento subterráneo', '2024-07-01', NULL, 'Aparcamiento de tres niveles en el centro urbano para reducir congestión vehicular.');

-- Insertar tareas
INSERT INTO tareas (estado, nombre_tarea, descripcion, proyectos_id, fecha_inicio, fecha_fin) VALUES
('pendiente', 'Inspección estructural inicial', 'Evaluación del estado actual de las murallas y torres.', 1, '2024-03-12', '2024-03-20'),
('pendiente', 'Limpieza de escombros', 'Retirar escombros y materiales sueltos en las áreas afectadas.', 1, '2024-03-21', '2024-03-25'),
('pendiente', 'Revisión arqueológica', 'Supervisión arqueológica para evitar daños a elementos históricos.', 1, '2024-03-26', '2024-04-05'),
('pendiente', 'Refuerzo de cimientos', 'Aplicación de técnicas de consolidación en los cimientos debilitados.', 1, '2024-04-06', '2024-04-20'),
('pendiente', 'Reparación de muros exteriores', 'Reconstrucción parcial de las murallas con materiales compatibles.', 1, '2024-04-21', '2024-05-15'),
('pendiente', 'Tratamiento contra la humedad', 'Aplicación de tratamientos para evitar filtraciones en la estructura.', 1, '2024-05-16', '2024-05-30'),
('pendiente', 'Reforzamiento de torres', 'Fortalecimiento de la base y estructura de las torres principales.', 1, '2024-06-01', '2024-06-20'),
('pendiente', 'Instalación de iluminación histórica', 'Colocación de sistemas de iluminación adecuados para la conservación.', 1, '2024-06-21', '2024-07-05'),
('pendiente', 'Restauración de accesos y escaleras', 'Reparación y refuerzo de escaleras y accesos al castillo.', 1, '2024-07-06', '2024-07-25'),
('pendiente', 'Supervisión final y entrega', 'Evaluación final del proyecto y entrega a las autoridades.', 1, '2024-07-26', '2024-08-05'),
('pendiente', 'Diseño y planificación arquitectónica', 'Elaboración de planos y especificaciones técnicas para la ampliación.', 2, '2024-01-16', '2024-02-05'),
('pendiente', 'Preparación del terreno', 'Despeje del área y nivelación del suelo para la construcción.', 2, '2024-02-06', '2024-02-20'),
('pendiente', 'Cimentación y estructuras básicas', 'Colocación de cimientos y estructura principal del edificio.', 2, '2024-02-21', '2024-03-15'),
('pendiente', 'Construcción de vestuarios y baños', 'Edificación de nuevas instalaciones sanitarias en el polideportivo.', 2, '2024-03-16', '2024-04-05'),
('pendiente', 'Instalación eléctrica y de fontanería', 'Montaje del sistema eléctrico y de agua en las nuevas áreas.', 2, '2024-04-06', '2024-04-25'),
('pendiente', 'Construcción de nuevas pistas deportivas', 'Pavimentación y señalización de nuevas áreas deportivas.', 2, '2024-04-26', '2024-05-20'),
('pendiente', 'Colocación de techado y cerramientos', 'Instalación de techos y cerramientos en las nuevas zonas.', 2, '2024-05-21', '2024-06-10'),
('pendiente', 'Pintura y acabados finales', 'Pintura, decoración y acabados interiores y exteriores.', 2, '2024-06-11', '2024-06-30'),
('pendiente', 'Instalación de mobiliario y equipamiento', 'Colocación de bancos, canastas, porterías y otros elementos.', 2, '2024-07-01', '2024-07-15'),
('pendiente', 'Revisión y entrega del proyecto', 'Supervisión final y entrega de las instalaciones al municipio.', 2, '2024-07-16', '2024-07-30'),
('pendiente', 'Evaluación estructural del edificio', 'Análisis del estado actual del teatro para determinar daños y necesidades de refuerzo.', 3, '2024-05-02', '2024-05-15'),
('pendiente', 'Refuerzo de cimentaciones y estructura', 'Trabajo en la cimentación y refuerzo de muros y vigas.', 3, '2024-05-16', '2024-06-05'),
('pendiente', 'Renovación del sistema eléctrico', 'Sustitución del cableado y mejora del sistema de iluminación.', 3, '2024-06-06', '2024-06-25'),
('pendiente', 'Instalación de sistema contra incendios', 'Implementación de rociadores, alarmas y salidas de emergencia adecuadas.', 3, '2024-06-26', '2024-07-10'),
('pendiente', 'Reparación y mejora de la acústica', 'Instalación de nuevos materiales acústicos para mejorar el sonido en la sala.', 3, '2024-07-11', '2024-07-30'),
('pendiente', 'Modernización del escenario', 'Cambio del suelo del escenario, iluminación escénica y equipos de sonido.', 3, '2024-08-01', '2024-08-20'),
('pendiente', 'Sustitución de butacas y mobiliario', 'Instalación de nuevas butacas más cómodas y resistentes.', 3, '2024-08-21', '2024-09-10'),
('pendiente', 'Restauración de la fachada y ornamentos', 'Limpieza y restauración de la fachada para conservar su estilo original.', 3, '2024-09-11', '2024-09-30'),
('pendiente', 'Pintura y acabados interiores', 'Aplicación de pintura, restauración de techos y detalles decorativos.', 3, '2024-10-01', '2024-10-15'),
('pendiente', 'Inspección final y apertura', 'Supervisión de todas las obras y reapertura del teatro.', 3, '2024-10-16', '2024-10-31'),
('finalizado', 'Demolición del pavimento antiguo', 'Retiro del asfalto y materiales obsoletos en la avenida.', 4, '2023-06-02', '2023-06-10'),
('finalizado', 'Renovación de las redes de agua y alcantarillado', 'Sustitución de tuberías antiguas para evitar filtraciones.', 4, '2023-06-11', '2023-06-30'),
('finalizado', 'Instalación del nuevo sistema de drenaje', 'Implementación de desagües modernos para evitar inundaciones.', 4, '2023-07-01', '2023-07-15'),
('finalizado', 'Colocación de nueva base de pavimento', 'Compactación del suelo y aplicación de una nueva base de hormigón.', 4, '2023-07-16', '2023-07-31'),
('finalizado', 'Asfaltado y señalización vial', 'Pavimentación de la avenida con nuevo asfalto y pintura vial.', 4, '2023-08-01', '2023-08-20'),
('finalizado', 'Renovación del alumbrado público', 'Instalación de farolas LED de bajo consumo en toda la avenida.', 4, '2023-08-21', '2023-09-05'),
('finalizado', 'Colocación de mobiliario urbano', 'Bancos, papeleras y zonas de descanso para peatones.', 4, '2023-09-06', '2023-09-20'),
('finalizado', 'Plantación de árboles y áreas verdes', 'Reforestación de la avenida con especies autóctonas.', 4, '2023-09-21', '2023-10-05'),
('finalizado', 'Construcción de carril bici', 'Implementación de un carril bici seguro y bien señalizado.', 4, '2023-10-06', '2023-10-20'),
('finalizado', 'Inspección final y apertura al público', 'Verificación de todos los trabajos y reapertura de la avenida.', 4, '2023-10-21', '2023-11-30'),
('en progreso', 'Excavación y nivelación del terreno', 'Preparación del terreno para la cimentación del colegio.', 5, '2024-02-11', '2024-02-20'),
('en progreso', 'Cimentación y estructura de hormigón', 'Construcción de los cimientos y la estructura principal del edificio.', 5, '2024-02-21', '2024-03-15'),
('pendiente', 'Levantamiento de muros y tabiques', 'Construcción de paredes exteriores e interiores del colegio.', 5, '2024-03-16', NULL),
('pendiente', 'Instalación de cubiertas y tejados', 'Colocación del techo para proteger la estructura del clima.', 5, '2024-04-01', NULL),
('pendiente', 'Instalación de sistemas eléctricos', 'Cableado, iluminación y conexiones eléctricas en todas las aulas.', 5, '2024-04-15', NULL),
('pendiente', 'Instalación de fontanería y saneamiento', 'Tuberías, baños y sistemas de desagüe para el colegio.', 5, '2024-04-30', NULL),
('pendiente', 'Revestimientos y acabados interiores', 'Pintura, pisos y acabados en aulas, oficinas y pasillos.', 5, '2024-05-15', NULL),
('pendiente', 'Colocación de ventanas y puertas', 'Instalación de carpintería metálica y de madera en el edificio.', 5, '2024-06-01', NULL),
('pendiente', 'Equipamiento de aulas y mobiliario', 'Colocación de pupitres, pizarras y estanterías en cada aula.', 5, '2024-06-15', NULL),
('pendiente', 'Inspección y pruebas de seguridad', 'Verificación de seguridad en estructuras, electricidad y accesibilidad.', 5, '2024-07-01', NULL),
('pendiente', 'Estudio de viabilidad y planificación', 'Evaluación de la capacidad de los edificios y cálculo de la energía necesaria.', 6, '2024-04-06', NULL),
('pendiente', 'Obtención de permisos y regulaciones', 'Gestión de licencias municipales y certificaciones ambientales.', 6, '2024-04-10', NULL),
('pendiente', 'Compra y transporte de materiales', 'Adquisición de paneles solares, inversores y baterías.', 6, '2024-04-20', NULL),
('pendiente', 'Preparación de los techos e infraestructuras', 'Limpieza y acondicionamiento de las superficies donde se instalarán los paneles.', 6, '2024-04-25', NULL),
('pendiente', 'Instalación de soportes y estructura base', 'Colocación de estructuras de anclaje para los paneles solares.', 6, '2024-05-01', NULL),
('pendiente', 'Montaje de paneles solares', 'Fijación de los paneles solares en los techos de los edificios públicos.', 6, '2024-05-10', NULL),
('pendiente', 'Conexión del sistema eléctrico', 'Instalación de inversores, baterías y cableado para conectar los paneles a la red eléctrica.', 6, '2024-05-20', NULL),
('pendiente', 'Pruebas de funcionamiento y eficiencia', 'Verificación de la generación de energía y su conexión con el sistema eléctrico.', 6, '2024-06-01', NULL),
('pendiente', 'Capacitación del personal técnico', 'Formación a empleados municipales para el mantenimiento de los paneles solares.', 6, '2024-06-10', NULL),
('pendiente', 'Inspección final y puesta en marcha', 'Evaluación de cumplimiento normativo y activación del sistema de energía solar.', 6, '2024-06-20', NULL),
('pendiente', 'Estudio de conservación y restauración', 'Evaluación del estado del edificio histórico y planificación de la restauración.', 7, '2024-06-02', NULL),
('pendiente', 'Obtención de permisos y licencias', 'Gestión de permisos para la intervención en un edificio protegido del casco histórico.', 7, '2024-06-05', NULL),
('pendiente', 'Limpieza y desescombro', 'Eliminación de escombros y limpieza de áreas afectadas por el deterioro.', 7, '2024-06-10', NULL),
('pendiente', 'Refuerzo estructural', 'Reforzar la estructura del edificio para garantizar su seguridad durante las obras.', 7, '2024-06-15', NULL),
('pendiente', 'Restauración de fachadas y elementos arquitectónicos', 'Reparación y conservación de elementos originales como ventanas, puertas y molduras.', 7, '2024-06-20', NULL),
('pendiente', 'Instalación de sistemas eléctricos y de iluminación', 'Colocación de cableado eléctrico y luces LED en el edificio.', 7, '2024-07-01', NULL),
('pendiente', 'Adecuación de espacios para exposiciones y actividades culturales', 'Transformación de espacios internos para la realización de eventos y actividades culturales.', 7, '2024-07-10', NULL),
('pendiente', 'Pintura y acabado interior', 'Pintura de paredes, techos y colocación de acabados interiores adecuados para la función del centro.', 7, '2024-07-15', NULL),
('pendiente', 'Instalación de sistemas de climatización', 'Instalación de aire acondicionado y calefacción en las áreas de exposición y oficinas.', 7, '2024-07-20', NULL),
('pendiente', 'Inspección final y puesta en marcha', 'Inspección de las obras realizadas y apertura oficial del centro cultural.', 7, '2024-07-30', NULL),
('pendiente', 'Inspección inicial de la red de alcantarillado', 'Revisión y evaluación del estado actual de las tuberías para determinar las áreas de mayor deterioro.', 8, '2023-12-16', NULL),
('pendiente', 'Desvío de tráfico y señalización', 'Planificación y ejecución de rutas alternativas para desviar el tráfico durante las obras de alcantarillado.', 8, '2023-12-18', NULL),
('pendiente', 'Preparación de zanjas para tuberías nuevas', 'Excavación de zanjas en las zonas afectadas para la instalación de las nuevas tuberías de alcantarillado.', 8, '2023-12-20', NULL),
('pendiente', 'Instalación de nuevas tuberías de alcantarillado', 'Colocación de tuberías de PVC o material adecuado para la nueva red de alcantarillado.', 8, '2023-12-22', NULL),
('pendiente', 'Conexión de tuberías a la red principal', 'Conexión de las nuevas tuberías con la red de alcantarillado existente en el centro de la ciudad.', 8, '2024-01-05', NULL),
('pendiente', 'Rehabilitación de pozos de acceso', 'Restauración y adaptación de los pozos de acceso al sistema de alcantarillado para garantizar su funcionalidad.', 8, '2024-01-10', NULL),
('pendiente', 'Verificación de la integridad de las nuevas tuberías', 'Inspección de las nuevas tuberías mediante cámaras para garantizar que no haya fugas o defectos.', 8, '2024-01-15', NULL),
('pendiente', 'Relleno y compactación de zanjas', 'Relleno de las zanjas excavadas con material adecuado y compactación para evitar hundimientos.', 8, '2024-01-20', NULL),
('pendiente', 'Pruebas de funcionamiento de la nueva red', 'Realización de pruebas para comprobar que la nueva red de alcantarillado funciona correctamente.', 8, '2024-01-25', NULL),
('pendiente', 'Restauración del pavimento y limpieza final', 'Restauración del pavimento en las zonas excavadas y limpieza final del área de trabajo.', 8, '2024-02-01', NULL),
('pendiente', 'Desbroce y limpieza del terreno', 'Eliminación de maleza, escombros y otros materiales no deseados del terreno para preparar la zona para la urbanización.', 9, '2023-05-11', NULL),
('pendiente', 'Estudio de la flora y fauna local', 'Realización de un estudio sobre las especies vegetales y animales presentes en el terreno para planificar la integración ecológica del parque.', 9, '2023-05-15', NULL),
('pendiente', 'Marcado de las áreas de césped y senderos', 'Definición de los espacios que serán cubiertos con césped y de las rutas peatonales dentro del parque.', 9, '2023-05-20', NULL),
('pendiente', 'Excavación y nivelación del terreno', 'Preparación del terreno para la construcción de las zonas de césped, caminos, zonas recreativas y otras áreas del parque.', 9, '2023-05-25', NULL),
('pendiente', 'Instalación de sistemas de riego', 'Colocación de un sistema de riego automático para garantizar el mantenimiento adecuado de las zonas ajardinadas.', 9, '2023-06-01', NULL),
('pendiente', 'Plantación de árboles y arbustos', 'Plantación de árboles y plantas autóctonas en el parque para promover la biodiversidad y crear áreas de sombra.', 9, '2023-06-05', NULL),
('pendiente', 'Construcción de caminos peatonales', 'Colocación de materiales adecuados y nivelación para la construcción de caminos accesibles dentro del parque.', 9, '2023-06-10', NULL),
('pendiente', 'Construcción de zonas de descanso', 'Instalación de bancos, papeleras y otros elementos de mobiliario urbano para las áreas de descanso dentro del parque.', 9, '2023-06-15', NULL),
('pendiente', 'Instalación de alumbrado público', 'Colocación de postes de luz y sistema de alumbrado para la seguridad y visibilidad dentro del parque durante la noche.', 9, '2023-06-20', NULL),
('pendiente', 'Revisión final y apertura del parque', 'Inspección general del parque para garantizar que todo esté en orden y la zona esté lista para su apertura al público.', 9, '2023-06-25', NULL),
('pendiente', 'Excavación para los cimientos', 'Excavación de la tierra para crear los cimientos del aparcamiento subterráneo, incluyendo la remoción de rocas y escombros.', 10, '2024-07-02', NULL),
('pendiente', 'Preparación y vertido de hormigón', 'Colocación de encofrado y preparación para el vertido de hormigón para la base del aparcamiento.', 10, '2024-07-05', NULL),
('pendiente', 'Instalación de estructuras de soporte', 'Colocación de pilares y vigas de soporte para la estructura del aparcamiento subterráneo.', 10, '2024-07-10', NULL),
('pendiente', 'Impermeabilización de la estructura', 'Aplicación de materiales impermeabilizantes en las paredes y el techo del aparcamiento para evitar filtraciones de agua.', 10, '2024-07-15', NULL),
('pendiente', 'Construcción de los niveles del aparcamiento', 'Edificación de los tres niveles del aparcamiento, incluyendo la instalación de rampas y divisiones entre plazas de aparcamiento.', 10, '2024-07-20', NULL),
('pendiente', 'Instalación de sistemas eléctricos', 'Colocación de cableado eléctrico para iluminación, señalización y otros sistemas dentro del aparcamiento.', 10, '2024-07-25', NULL),
('pendiente', 'Instalación de sistemas de ventilación', 'Montaje de los sistemas de ventilación para garantizar un flujo de aire adecuado en los tres niveles del aparcamiento.', 10, '2024-07-30', NULL),
('pendiente', 'Colocación de pavimento en los niveles', 'Colocación de pavimento antideslizante y adecuado para vehículos en los tres niveles del aparcamiento.', 10, '2024-08-05', NULL),
('pendiente', 'Señalización del aparcamiento', 'Instalación de señalización horizontal y vertical para la correcta distribución de las plazas y circulación de los vehículos.', 10, '2024-08-10', NULL),
('pendiente', 'Revisión final y apertura del aparcamiento', 'Inspección final del aparcamiento para garantizar que todo está en funcionamiento y listo para su apertura al público.', 10, '2024-08-15', NULL);
