CREATE DATABASE IF NOT EXISTS coffix_bd CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE coffix_bd;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS usuarios_bloqueados, mensaje, chat, reservaciones, reseñas_servicios, reseñas_web, disponibilidad, registros_eliminacion_servicios, servicios, productos, usuarios;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    fecha_nacimiento DATE,
    sexo ENUM('Masculino', 'Femenino', 'Otro') NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    numero_contacto VARCHAR(20),
    rol ENUM('Cliente', 'Proveedor', 'Psicologo', 'Empleado', 'Administrador') NOT NULL,
    biografia TEXT,
    foto_perfil VARCHAR(255) DEFAULT 'recursos/img/perfiles/default.png',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS productos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    categoria ENUM('Bebidas Calientes', 'Repostería', 'Aperitivos', 'Café Grano') NOT NULL,
    stock INT(11) NOT NULL DEFAULT 0,
    imagen_ruta VARCHAR(255),
    destacado BOOLEAN DEFAULT FALSE,
    ventas_acumuladas INT(11) DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS servicios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_usuario INT(11) NOT NULL COMMENT 'ID del Proveedor o Psicólogo',
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    ubicacion VARCHAR(255) NOT NULL COMMENT 'Ciudad, zona o dirección general',
    precio DECIMAL(10, 2) NOT NULL,
    disponibilidad VARCHAR(255) COMMENT 'Horarios o días disponibles',
    imagenes VARCHAR(255),
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    calificacion_promedio DECIMAL(2, 1) DEFAULT 0.0,
    estado ENUM('Activo', 'Eliminado') DEFAULT 'Activo',
    PRIMARY KEY (id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reservaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_servicio INT NOT NULL,
    id_cliente INT NOT NULL,
    id_proveedor INT NOT NULL COMMENT 'Usuario dueño del servicio',
    fecha_reserva DATE NOT NULL,
    hora_reserva TIME NOT NULL,
    estado ENUM('PENDIENTE', 'ACEPTADA', 'RECHAZADA', 'CANCELADA', 'FINALIZADA') NOT NULL DEFAULT 'PENDIENTE',
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_servicio) REFERENCES servicios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_cliente) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_proveedor) REFERENCES usuarios(id) ON DELETE CASCADE,

    UNIQUE KEY unique_reserva_servicio (id_servicio, id_cliente, fecha_reserva, hora_reserva)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_servicio INT NOT NULL,
    id_cliente INT NOT NULL,
    id_proveedor INT NOT NULL,
    estado ENUM('PENDIENTE', 'ACTIVO', 'RECHAZADO') DEFAULT 'PENDIENTE',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_servicio) REFERENCES servicios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_cliente) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_proveedor) REFERENCES usuarios(id) ON DELETE CASCADE,

    UNIQUE KEY unique_chat (id_servicio, id_cliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS usuarios_bloqueados (
    id INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    nombre_usuario VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    fecha_nacimiento DATE NULL,
    sexo ENUM('Masculino', 'Femenino', 'Otro') NOT NULL,
    correo VARCHAR(191) NOT NULL UNIQUE, 
    numero_contacto VARCHAR(20) NULL,
    rol ENUM('Cliente', 'Proveedor', 'Psicologo', 'Empleado', 'Administrador') NOT NULL,
    biografia TEXT NULL,
    foto_perfil VARCHAR(255) DEFAULT 'recursos/img/perfiles/default.png',
    fecha_registro DATETIME NOT NULL,
    motivo_bloqueo TEXT NOT NULL,
    fecha_bloqueo DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 

CREATE TABLE IF NOT EXISTS mensaje (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_chat INT NOT NULL,
    id_emisor INT NOT NULL,
    contenido TEXT NOT NULL,
    leido BOOLEAN DEFAULT FALSE,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_chat) REFERENCES chat(id) ON DELETE CASCADE,
    FOREIGN KEY (id_emisor) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS registros_eliminacion_servicios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_servicio INT(11) NOT NULL,
    id_administrador INT(11) NOT NULL COMMENT 'ID del usuario Administrador que realizó la acción',
    motivo_eliminacion TEXT NOT NULL,
    fecha_eliminacion DATETIME NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (id_servicio) REFERENCES servicios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_administrador) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reseñas_servicios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_servicio INT(11) NOT NULL,
    id_usuario INT(11) NOT NULL COMMENT 'ID del Cliente que deja la reseña',
    calificacion TINYINT(1) NOT NULL COMMENT 'Puntuación de 1 a 5',
    comentario TEXT,
    fecha_reseña DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (id_servicio) REFERENCES servicios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_servicio (id_servicio, id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reseñas_web (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_usuario INT(11) NOT NULL,
    calificacion TINYINT(1) NOT NULL COMMENT 'Puntuación de 1 a 5',
    comentario TEXT NOT NULL,
    fecha_reseña DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO usuarios (id, nombre, apellido, nombre_usuario, contrasena, fecha_nacimiento, sexo, correo, rol, biografia) VALUES
(1, 'Admin', 'Global', 'admin01', '$2y$10$w8T9Lz3Yt0S2Qf9A7r8vO.p2F2b3c4d5e6f7g8h9', '1990-01-01', 'Otro', 'admin@coffix.cl', 'Administrador', 'Administrador principal del sitio web.'),
(2, 'Ana', 'Perez', 'proveedora_ana', '$2y$10$w8T9Lz3Yt0S2Qf9A7r8vO.p2F2b3c4d5e6f7g8h9', '1995-05-15', 'Femenino', 'ana@proveedores.cl', 'Proveedor', 'Ofrezco servicios de diseño web freelance.'),
(3, 'Dr.', 'Lopez', 'psicologo_lopez', '$2y$10$w8T9Lz3Yt0S2Qf9A7r8vO.p2F2b3c4d5e6f7g8h9', '1980-11-20', 'Masculino', 'lopez@salud.cl', 'Psicologo', 'Terapia cognitiva y de pareja.'),
(4, 'Carlos', 'Cliente', 'carlos_c', '$2y$10$w8T9Lz3Yt0S2Qf9A7r8vO.p2F2b3c4d5e6f7g8h9', '2000-03-03', 'Masculino', 'carlos@cliente.cl', 'Cliente', 'Usuario buscando servicios y productos.'),
(5, 'Maria', 'Gomez', 'maria_g', '$2y$10$w8T9Lz3Yt0S2Qf9A7r8vO.p2F2b3c4d5e6f7g8h9', '1998-07-25', 'Femenino', 'maria@cliente.cl', 'Cliente', 'Amante del buen café.');

INSERT INTO productos (nombre, descripcion, precio, categoria, stock, imagen_ruta, destacado, ventas_acumuladas) VALUES
('Latte Medieval', 'Exquisito café latte con sirope de vainilla y un toque de canela.', 3.50, 'Bebidas Calientes', 50, 'recursos/img/productos/latte.jpg', TRUE, 120),
('Muffin de Nuez', 'Muffin casero con trozos de nuez y un glaseado de miel.', 2.80, 'Repostería', 30, 'recursos/img/productos/muffin.jpg', TRUE, 85),
('Café Grano Origen Épico', 'Café de especialidad, tueste medio, notas achocolatadas.', 15.00, 'Café Grano', 20, 'recursos/img/productos/grano.jpg', FALSE, 40),
('Té Chai Especiado', 'Mezcla premium de té negro, leche y especias dulces.', 4.00, 'Bebidas Calientes', 45, 'recursos/img/productos/chai.jpg', TRUE, 60),
('Croissant de Chocolate', 'Fino croissant de mantequilla relleno de chocolate negro.', 3.20, 'Repostería', 25, 'recursos/img/productos/croissant.jpg', FALSE, 75);

INSERT INTO servicios (id, id_usuario, titulo, descripcion, categoria, ubicacion, precio, disponibilidad, imagenes, calificacion_promedio) VALUES
(1, 2, 'Diseño de Logos Profesionales', 'Diseño de identidad visual y logos de alta calidad para pequeñas empresas y startups. Paquete básico incluye 3 revisiones.', 'Diseño Web', 'Remoto (Chile)', 85.00, 'Lunes a Viernes 9:00-17:00', 'recursos/img/servicios/logo1.jpg', 4.8),
(2, 3, 'Terapia Online (Primera Sesión)', 'Sesiones de terapia psicológica online enfocadas en manejo de estrés y ansiedad. 50 minutos de duración.', 'Psicología', 'Online', 45.00, 'Miércoles y Jueves 18:00-21:00', 'recursos/img/servicios/terapia1.jpg', 4.9),
(3, 2, 'Clases Particulares de Python', 'Clases virtuales de programación Python, desde nivel básico hasta intermedio. Enfocado en desarrollo web y scripting.', 'Educación', 'Remoto (Internacional)', 30.00, 'Sábados 10:00-14:00', 'recursos/img/servicios/python.jpg', 4.5),
(4, 3, 'Terapia de Pareja', 'Sesiones conjuntas para mejorar la comunicación y resolver conflictos en la relación.', 'Psicología', 'Online', 70.00, 'Martes y Viernes 17:00-20:00', 'recursos/img/servicios/pareja.jpg', 5.0);

INSERT INTO reseñas_servicios (id_servicio, id_usuario, calificacion, comentario) VALUES
(1, 4, 5, 'Excelente diseño de logo. Muy profesional y rápido.'),
(1, 5, 4, 'Buen servicio, aunque tardó un poco más de lo esperado.'),
(2, 4, 5, 'La primera sesión con el Dr. Lopez fue muy útil. Altamente recomendado.');

INSERT INTO reservaciones (id_servicio, id_cliente, id_proveedor, fecha_reserva, hora_reserva, estado) VALUES
(1, 4, 2, '2025-11-07', '10:00:00', 'ACEPTADA'),
(3, 5, 2, '2025-11-07', '12:00:00', 'ACEPTADA');

INSERT INTO reservaciones (id_servicio, id_cliente, id_proveedor, fecha_reserva, hora_reserva, estado) VALUES
(2, 4, 3, '2025-11-09', '18:30:00', 'ACEPTADA');