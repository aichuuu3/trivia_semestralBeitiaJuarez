-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 26, 2025 at 05:49 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `trivita`
--

-- --------------------------------------------------------

--
-- Table structure for table `administradores`
--

DROP TABLE IF EXISTS `administradores`;
CREATE TABLE IF NOT EXISTS `administradores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'avatar.png',
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `horas_totales` int DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administradores`
--

INSERT INTO `administradores` (`id`, `nombre`, `email`, `password`, `avatar`, `fecha_registro`, `horas_totales`, `activo`) VALUES
(1, 'Edgar Juarez', 'edgar.juarez@reichmind.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'avatar.png', '2025-07-25 00:00:00', 74, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categoria`
--

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE IF NOT EXISTS `categoria` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre_categoria`) VALUES
(1, 'Experto'),
(2, 'Novato'),
(3, 'Principiante');

-- --------------------------------------------------------

--
-- Table structure for table `colaboradores`
--

DROP TABLE IF EXISTS `colaboradores`;
CREATE TABLE IF NOT EXISTS `colaboradores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'avatar.png',
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hora_inicio_actividad` time DEFAULT '09:00:00',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colaboradores`
--

INSERT INTO `colaboradores` (`id`, `nombre`, `email`, `password`, `avatar`, `fecha_registro`, `hora_inicio_actividad`, `activo`) VALUES
(1, 'Emilia Pérez', 'emilia.perez@gmail.com', '$2y$10$2wL/BTklmjtfZmz9eb5HNeamvOR7kTVA6smyOAVfkAdT7Ttd9JHXC', 'avatar.png', '2025-07-24 05:00:00', '08:36:33', 1),
(2, 'Fernando Delgado', 'fernando.delgado@reichmind.com', '$2y$10$qSE0wvRmCpFv3maKeoXv/eSiUmSl8Vv6jHLa2j4uI9RW4ggT0.npu', 'avatar_colaborador_2_1753465393.png', '2025-07-24 05:00:00', '09:00:23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `historial_monedas`
--

DROP TABLE IF EXISTS `historial_monedas`;
CREATE TABLE IF NOT EXISTS `historial_monedas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `monedas_ganadas` int NOT NULL,
  `categoria` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `porcentaje` decimal(5,2) DEFAULT NULL,
  `tiempo_total` int DEFAULT NULL,
  `partida_ganada` tinyint(1) DEFAULT '0',
  `fecha_ganadas` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_fecha` (`usuario_id`,`fecha_ganadas`),
  KEY `idx_categoria` (`categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `historial_monedas`
--

INSERT INTO `historial_monedas` (`id`, `usuario_id`, `monedas_ganadas`, `categoria`, `porcentaje`, `tiempo_total`, `partida_ganada`, `fecha_ganadas`) VALUES
(1, 4, 160, 'Principiante', 100.00, 6, 1, '2025-07-25 16:35:38'),
(2, 4, 177, 'Novato', 100.00, 10, 1, '2025-07-25 17:03:27'),
(3, 4, 160, 'Principiante', 100.00, 7, 1, '2025-07-25 17:23:52'),
(4, 4, 20, 'Experto', 0.00, 7, 0, '2025-07-25 17:24:57'),
(5, 4, 190, 'Experto', 100.00, 47, 1, '2025-07-25 17:26:21'),
(6, 4, 160, 'Principiante', 100.00, 7, 1, '2025-07-25 17:34:50'),
(7, 4, 157, 'Principiante', 100.00, 19, 1, '2025-07-26 17:39:55'),
(8, 4, 160, 'Principiante', 100.00, 7, 1, '2025-07-26 17:40:49');

-- --------------------------------------------------------

--
-- Table structure for table `imagenes`
--

DROP TABLE IF EXISTS `imagenes`;
CREATE TABLE IF NOT EXISTS `imagenes` (
  `id_imagen` int NOT NULL AUTO_INCREMENT,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_display` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ruta_completa` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `extension` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `tamaño_kb` int DEFAULT NULL,
  `tipo_imagen` enum('avatar','fondo','icono','decorativa') COLLATE utf8mb4_general_ci DEFAULT 'decorativa',
  `descripcion` text COLLATE utf8mb4_general_ci,
  `activa` tinyint(1) DEFAULT '1',
  `fecha_subida` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_imagen`),
  UNIQUE KEY `nombre_archivo` (`nombre_archivo`),
  KEY `tipo_imagen` (`tipo_imagen`),
  KEY `activa` (`activa`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `imagenes`
--

INSERT INTO `imagenes` (`id_imagen`, `nombre_archivo`, `nombre_display`, `ruta_completa`, `extension`, `tamaño_kb`, `tipo_imagen`, `descripcion`, `activa`, `fecha_subida`) VALUES
(1, 'avatar.png', 'Avatar por Defecto', '../img/avatar.png', 'png', 50, 'avatar', 'Imagen de avatar genérica para usuarios', 1, '2025-07-24 22:00:00'),
(2, 'Skirk.jpg', 'Skirk', '../img/Skirk.jpg', 'jpg', 180, 'avatar', 'Avatar de personaje Skirk', 1, '2025-07-24 22:00:00'),
(3, 'Tartaglia.jpg', 'Tartaglia', '../img/Tartaglia.jpg', 'jpg', 165, 'avatar', 'Avatar de personaje Tartaglia', 1, '2025-07-24 22:00:00'),
(4, 'avatar_colaborador_2_1753459297.png', 'Avatar Colaborador Personalizado', '../img/avatar_colaborador_2_1753459297.png', 'png', 1476, 'avatar', 'Avatar personalizado subido por administrador', 1, '2025-07-25 16:01:37'),
(5, 'avatar_colaborador_2_1753459350.png', 'Avatar Colaborador Personalizado', '../img/avatar_colaborador_2_1753459350.png', 'png', 1496, 'avatar', 'Avatar personalizado subido por administrador', 1, '2025-07-25 16:02:30'),
(6, 'avatar_colaborador_2_1753459864.png', 'Avatar Colaborador Personalizado', '../img/avatar_colaborador_2_1753459864.png', 'png', 151, 'avatar', 'Avatar personalizado subido por administrador', 1, '2025-07-25 16:11:04'),
(7, 'avatar_user_4_1753464111.png', 'Avatar personalizado - Usuario 4', '../img/avatar_user_4_1753464111.png', 'png', 44, 'avatar', 'Avatar personalizado subido por el usuario', 1, '2025-07-25 17:21:51'),
(8, 'avatar_colaborador_2_1753465393.png', 'Avatar Colaborador Personalizado', '../img/avatar_colaborador_2_1753465393.png', 'png', 130, 'avatar', 'Avatar personalizado subido por administrador', 1, '2025-07-25 17:43:13');

-- --------------------------------------------------------

--
-- Table structure for table `preguntas`
--

DROP TABLE IF EXISTS `preguntas`;
CREATE TABLE IF NOT EXISTS `preguntas` (
  `id_pregunta` int NOT NULL AUTO_INCREMENT,
  `texto_pregunta` text COLLATE utf8mb4_general_ci NOT NULL,
  `cod_categoria` int NOT NULL,
  `id_tema` int NOT NULL,
  `puntos` int DEFAULT '10',
  `activa` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_pregunta`),
  KEY `cod_categoria` (`cod_categoria`),
  KEY `id_tema` (`id_tema`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preguntas`
--

INSERT INTO `preguntas` (`id_pregunta`, `texto_pregunta`, `cod_categoria`, `id_tema`, `puntos`, `activa`) VALUES
(1, '¿Qué significa CRUD?', 3, 1, 10, 1),
(2, 'CRUD incluye operaciones de eliminación de datos', 3, 1, 10, 1),
(3, '¿Cuál es la operación CRUD para agregar nuevos datos?', 3, 1, 10, 1),
(4, '¿Qué método HTTP se usa comúnmente para la operación UPDATE en CRUD?', 2, 1, 10, 1),
(5, 'En CRUD, la operación READ siempre modifica los datos', 2, 1, 10, 1),
(6, '¿Cuál es la diferencia entre PUT y PATCH en operaciones CRUD?', 2, 1, 10, 1),
(7, '¿Qué patrón de diseño se implementa comúnmente con CRUD en aplicaciones empresariales?', 1, 1, 10, 1),
(8, 'Las operaciones CRUD siempre deben ser transaccionales', 1, 1, 10, 1),
(9, '¿Cuál es la mejor práctica para implementar soft delete en CRUD?', 1, 1, 10, 1),
(10, '¿Qué es Laravel?', 3, 2, 10, 1),
(11, 'Laravel está basado en el patrón MVC', 3, 2, 10, 1),
(12, '¿Cuál es el comando para crear un nuevo proyecto Laravel?', 3, 2, 10, 1),
(13, '¿Qué es Eloquent en Laravel?', 2, 2, 10, 1),
(14, '¿Cuál es el archivo principal de configuración de rutas en Laravel?', 2, 2, 10, 1),
(15, 'Laravel Blade es solo para crear vistas HTML', 2, 2, 10, 1),
(16, '¿Qué es el Service Container en Laravel?', 1, 2, 10, 1),
(17, 'Los Event Listeners en Laravel siempre deben ser síncronos', 1, 2, 10, 1),
(18, '¿Cuál es la diferencia entre Facade y Dependency Injection en Laravel?', 1, 2, 10, 1),
(19, '¿Qué significa PHP?', 3, 3, 10, 1),
(20, 'PHP es un lenguaje compilado', 3, 3, 10, 1),
(21, '¿Cuál es la extensión de archivo para PHP?', 3, 3, 10, 1),
(22, '¿Qué son las superglobales en PHP?', 2, 3, 10, 1),
(23, '¿Cuál es la diferencia entre include y require en PHP?', 2, 3, 10, 1),
(24, 'PHP soporta herencia múltiple', 2, 3, 10, 1),
(25, '¿Qué es el Garbage Collector en PHP?', 1, 3, 10, 1),
(26, '¿Cuál es la diferencia entre early binding y late binding en PHP?', 1, 3, 10, 1),
(27, 'PHP utiliza copy-on-write para la gestión de memoria', 1, 3, 10, 1),
(29, '¿Que se enseña en Desarrollo de Software I?', 3, 12, 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `respuestas`
--

DROP TABLE IF EXISTS `respuestas`;
CREATE TABLE IF NOT EXISTS `respuestas` (
  `id_respuesta` int NOT NULL AUTO_INCREMENT,
  `id_pregunta` int NOT NULL,
  `texto_respuesta` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `es_correcta` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_respuesta`),
  KEY `id_pregunta` (`id_pregunta`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `respuestas`
--

INSERT INTO `respuestas` (`id_respuesta`, `id_pregunta`, `texto_respuesta`, `es_correcta`) VALUES
(1, 1, 'Create, Read, Update, Delete', 1),
(2, 1, 'Create, Remove, Update, Download', 0),
(3, 1, 'Copy, Read, Upload, Delete', 0),
(4, 1, 'Create, Retrieve, Update, Destroy', 0),
(5, 2, 'Verdadero', 1),
(6, 2, 'Falso', 0),
(7, 3, 'Create', 1),
(8, 3, 'Read', 0),
(9, 3, 'Update', 0),
(10, 3, 'Delete', 0),
(11, 4, 'PUT', 1),
(12, 4, 'GET', 0),
(13, 4, 'POST', 0),
(14, 4, 'DELETE', 0),
(15, 5, 'Falso', 1),
(16, 5, 'Verdadero', 0),
(17, 6, 'PUT actualiza todo el recurso, PATCH actualiza parcialmente', 1),
(18, 6, 'PUT y PATCH son idénticos', 0),
(19, 6, 'PUT es para crear, PATCH es para actualizar', 0),
(20, 6, 'PUT es más rápido que PATCH', 0),
(21, 7, 'Repository Pattern', 1),
(22, 7, 'Singleton Pattern', 0),
(23, 7, 'Observer Pattern', 0),
(24, 7, 'Factory Pattern', 0),
(25, 8, 'Falso', 1),
(26, 8, 'Verdadero', 0),
(27, 9, 'Usar un campo deleted_at en lugar de eliminar físicamente', 1),
(28, 9, 'Eliminar directamente de la base de datos', 0),
(29, 9, 'Mover a una tabla de respaldo', 0),
(30, 9, 'Usar triggers de base de datos', 0),
(31, 10, 'Un framework de PHP', 1),
(32, 10, 'Un lenguaje de programación', 0),
(33, 10, 'Una base de datos', 0),
(34, 10, 'Un servidor web', 0),
(35, 11, 'Verdadero', 1),
(36, 11, 'Falso', 0),
(37, 12, 'composer create-project laravel/laravel', 1),
(38, 12, 'npm install laravel', 0),
(39, 12, 'laravel new project', 0),
(40, 12, 'php artisan create', 0),
(41, 13, 'Un ORM (Object Relational Mapping)', 1),
(42, 13, 'Un motor de plantillas', 0),
(43, 13, 'Un sistema de rutas', 0),
(44, 13, 'Un compilador de CSS', 0),
(45, 14, 'routes/web.php', 1),
(46, 14, 'config/routes.php', 0),
(47, 14, 'app/Routes.php', 0),
(48, 14, 'public/routes.php', 0),
(49, 15, 'Falso', 1),
(50, 15, 'Verdadero', 0),
(51, 16, 'Un sistema de inyección de dependencias', 1),
(52, 16, 'Un motor de base de datos', 0),
(53, 16, 'Un sistema de cache', 0),
(54, 16, 'Un compilador de assets', 0),
(55, 17, 'Falso', 1),
(56, 17, 'Verdadero', 0),
(57, 18, 'Facade es un patrón estático, DI es dinámico', 1),
(58, 18, 'Son exactamente lo mismo', 0),
(59, 18, 'Facade es más rápido siempre', 0),
(60, 18, 'DI no se puede usar con Laravel', 0),
(61, 19, 'PHP: Hypertext Preprocessor', 1),
(62, 19, 'Personal Home Page', 0),
(63, 19, 'Private Hypertext Processor', 0),
(64, 19, 'Public HTML Protocol', 0),
(65, 20, 'Falso', 1),
(66, 20, 'Verdadero', 0),
(67, 21, '.php', 1),
(68, 21, '.html', 0),
(69, 21, '.js', 0),
(70, 21, '.py', 0),
(71, 22, 'Variables predefinidas disponibles en cualquier ámbito', 1),
(72, 22, 'Variables que solo existen en funciones', 0),
(73, 22, 'Variables de configuración del servidor', 0),
(74, 22, 'Variables temporales del sistema', 0),
(75, 23, 'require genera error fatal si falla, include solo warning', 1),
(76, 23, 'include es más rápido que require', 0),
(77, 23, 'require solo funciona con archivos PHP', 0),
(78, 23, 'No hay diferencia entre ambos', 0),
(79, 24, 'Falso', 1),
(80, 24, 'Verdadero', 0),
(81, 25, 'Sistema automático de liberación de memoria', 1),
(82, 25, 'Un depurador de código', 0),
(83, 25, 'Un optimizador de consultas', 0),
(84, 25, 'Un sistema de cache', 0),
(85, 26, 'Early binding en tiempo de compilación, late binding en runtime', 1),
(86, 26, 'Early binding es más lento', 0),
(87, 26, 'Late binding no existe en PHP', 0),
(88, 26, 'Son sinónimos en PHP', 0),
(89, 27, 'Verdadero', 1),
(90, 27, 'Falso', 0),
(95, 29, 'Algoritmos', 1),
(96, 29, 'php', 0),
(97, 29, 'java', 0),
(98, 29, 'c#', 0);

-- --------------------------------------------------------

--
-- Table structure for table `temas`
--

DROP TABLE IF EXISTS `temas`;
CREATE TABLE IF NOT EXISTS `temas` (
  `id_tema` int NOT NULL AUTO_INCREMENT,
  `nombre_tema` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id_tema`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `temas`
--

INSERT INTO `temas` (`id_tema`, `nombre_tema`, `descripcion`) VALUES
(1, 'CRUD', 'Operaciones básicas de Create, Read, Update, Delete'),
(2, 'Laravel', 'Framework de PHP para desarrollo web'),
(3, 'PHP', 'Lenguaje de programación web del lado del servidor'),
(12, 'Desarrollo de Software', 'Clase de la UTP');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `cod_categoria` int DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'avatar.png',
  `monedas_totales` int DEFAULT '100',
  `partidas_ganadas` int DEFAULT '0',
  `partidas_fallidas` int DEFAULT '0',
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `cod_categoria`, `avatar`, `monedas_totales`, `partidas_ganadas`, `partidas_fallidas`, `fecha_registro`, `activo`) VALUES
(1, 'Ana García', 'ana.garcia@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'avatar.png', 100, 0, 0, '2025-07-24 22:00:00', 1),
(2, 'Carlos Rodriguez', 'carlos.rodriguez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'avatar.png', 150, 0, 0, '2025-07-24 22:00:00', 1),
(3, 'María López', 'maria.lopez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'avatar.png', 200, 0, 0, '2025-07-24 22:00:00', 1),
(4, 'Adan Morris', 'am@gmail.com', '$2y$10$9paTlRcXA2q.6e10z6pDjuOPOSiGfurp/.s5PyNu77PN27Qf5YvS2', 3, 'avatar_user_4_1753464111.png', 577, 7, 1, '2025-07-25 15:54:21', 1),
(5, 'Eduard Lopez', 'el@outlook.com', '$2y$10$ivC8jWAiUO/84EnyK9f3XO3BFWSv9VULJS21G2OuJ2hNuLrZitm0i', 3, 'avatar.png', 100, 0, 0, '2025-07-25 17:30:59', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `preguntas`
--
ALTER TABLE `preguntas`
  ADD CONSTRAINT `preguntas_ibfk_1` FOREIGN KEY (`cod_categoria`) REFERENCES `categoria` (`id_categoria`),
  ADD CONSTRAINT `preguntas_ibfk_2` FOREIGN KEY (`id_tema`) REFERENCES `temas` (`id_tema`);

--
-- Constraints for table `respuestas`
--
ALTER TABLE `respuestas`
  ADD CONSTRAINT `respuestas_ibfk_1` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id_pregunta`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
