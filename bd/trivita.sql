-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-07-2025 a las 22:05:54
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `trivita`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre_categoria`) VALUES
(1, 'Experto'),
(2, 'Novato'),
(3, 'Principiante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colaboradores`
--

CREATE TABLE `colaboradores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `hora_inicio_actividad` time DEFAULT '09:00:00',
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `colaboradores`
--

INSERT INTO `colaboradores` (`id`, `nombre`, `email`, `password`, `fecha_registro`, `hora_inicio_actividad`, `activo`) VALUES
(1, 'Emilia Pérez', 'emilia.perez@gmail.com', '$2y$10$2wL/BTklmjtfZmz9eb5HNeamvOR7kTVA6smyOAVfkAdT7Ttd9JHXC', '2025-07-24 05:00:00', '08:36:33', 1),
(2, 'Fernando Delgado', 'fernando.delgado@reichmind.com', '$2y$10$qSE0wvRmCpFv3maKeoXv/eSiUmSl8Vv6jHLa2j4uI9RW4ggT0.npu', '2025-07-24 05:00:00', '09:00:23', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas`
--

CREATE TABLE `preguntas` (
  `id_pregunta` int(11) NOT NULL,
  `texto_pregunta` text NOT NULL,
  `cod_categoria` int(11) NOT NULL,
  `id_tema` int(11) NOT NULL,
  `puntos` int(11) DEFAULT 10,
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preguntas`
--

INSERT INTO `preguntas` (`id_pregunta`, `texto_pregunta`, `cod_categoria`, `id_tema`, `puntos`, `activa`) VALUES
-- CRUD - Principiante (3 preguntas)
(1, '¿Qué significa CRUD?', 3, 1, 10, 1),
(2, 'CRUD incluye operaciones de eliminación de datos', 3, 1, 10, 1),
(3, '¿Cuál es la operación CRUD para agregar nuevos datos?', 3, 1, 10, 1),

-- CRUD - Novato (3 preguntas)
(4, '¿Qué método HTTP se usa comúnmente para la operación UPDATE en CRUD?', 2, 1, 10, 1),
(5, 'En CRUD, la operación READ siempre modifica los datos', 2, 1, 10, 1),
(6, '¿Cuál es la diferencia entre PUT y PATCH en operaciones CRUD?', 2, 1, 10, 1),

-- CRUD - Experto (3 preguntas)
(7, '¿Qué patrón de diseño se implementa comúnmente con CRUD en aplicaciones empresariales?', 1, 1, 10, 1),
(8, 'Las operaciones CRUD siempre deben ser transaccionales', 1, 1, 10, 1),
(9, '¿Cuál es la mejor práctica para implementar soft delete en CRUD?', 1, 1, 10, 1),

-- Laravel - Principiante (3 preguntas)
(10, '¿Qué es Laravel?', 3, 2, 10, 1),
(11, 'Laravel está basado en el patrón MVC', 3, 2, 10, 1),
(12, '¿Cuál es el comando para crear un nuevo proyecto Laravel?', 3, 2, 10, 1),

-- Laravel - Novato (3 preguntas)
(13, '¿Qué es Eloquent en Laravel?', 2, 2, 10, 1),
(14, '¿Cuál es el archivo principal de configuración de rutas en Laravel?', 2, 2, 10, 1),
(15, 'Laravel Blade es solo para crear vistas HTML', 2, 2, 10, 1),

-- Laravel - Experto (3 preguntas)
(16, '¿Qué es el Service Container en Laravel?', 1, 2, 10, 1),
(17, 'Los Event Listeners en Laravel siempre deben ser síncronos', 1, 2, 10, 1),
(18, '¿Cuál es la diferencia entre Facade y Dependency Injection en Laravel?', 1, 2, 10, 1),

-- PHP - Principiante (3 preguntas)
(19, '¿Qué significa PHP?', 3, 3, 10, 1),
(20, 'PHP es un lenguaje compilado', 3, 3, 10, 1),
(21, '¿Cuál es la extensión de archivo para PHP?', 3, 3, 10, 1),

-- PHP - Novato (3 preguntas)
(22, '¿Qué son las superglobales en PHP?', 2, 3, 10, 1),
(23, '¿Cuál es la diferencia entre include y require en PHP?', 2, 3, 10, 1),
(24, 'PHP soporta herencia múltiple', 2, 3, 10, 1),

-- PHP - Experto (3 preguntas)
(25, '¿Qué es el Garbage Collector en PHP?', 1, 3, 10, 1),
(26, '¿Cuál es la diferencia entre early binding y late binding en PHP?', 1, 3, 10, 1),
(27, 'PHP utiliza copy-on-write para la gestión de memoria', 1, 3, 10, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas`
--

CREATE TABLE `respuestas` (
  `id_respuesta` int(11) NOT NULL,
  `id_pregunta` int(11) NOT NULL,
  `texto_respuesta` varchar(255) NOT NULL,
  `es_correcta` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuestas`
--

INSERT INTO `respuestas` (`id_respuesta`, `id_pregunta`, `texto_respuesta`, `es_correcta`) VALUES
-- Pregunta 1: ¿Qué significa CRUD? (Opción múltiple)
(1, 1, 'Create, Read, Update, Delete', 1),
(2, 1, 'Create, Remove, Update, Download', 0),
(3, 1, 'Copy, Read, Upload, Delete', 0),
(4, 1, 'Create, Retrieve, Update, Destroy', 0),

-- Pregunta 2: CRUD incluye operaciones de eliminación de datos (Verdadero/Falso)
(5, 2, 'Verdadero', 1),
(6, 2, 'Falso', 0),

-- Pregunta 3: ¿Cuál es la operación CRUD para agregar nuevos datos? (Opción múltiple)
(7, 3, 'Create', 1),
(8, 3, 'Read', 0),
(9, 3, 'Update', 0),
(10, 3, 'Delete', 0),

-- Pregunta 4: ¿Qué método HTTP se usa comúnmente para la operación UPDATE en CRUD? (Opción múltiple)
(11, 4, 'PUT', 1),
(12, 4, 'GET', 0),
(13, 4, 'POST', 0),
(14, 4, 'DELETE', 0),

-- Pregunta 5: En CRUD, la operación read siempre modifica los datos (Verdadero/Falso)
(15, 5, 'Falso', 1),
(16, 5, 'Verdadero', 0),

-- Pregunta 6: ¿Cuál es la diferencia entre PUT y PATCH en operaciones CRUD? (Opción múltiple)
(17, 6, 'PUT actualiza todo el recurso, PATCH actualiza parcialmente', 1),
(18, 6, 'PUT y PATCH son idénticos', 0),
(19, 6, 'PUT es para crear, PATCH es para actualizar', 0),
(20, 6, 'PUT es más rápido que PATCH', 0),

-- Pregunta 7: ¿Qué patrón de diseño se implementa comúnmente con CRUD en aplicaciones empresariales? (Opción múltiple)
(21, 7, 'Repository Pattern', 1),
(22, 7, 'Singleton Pattern', 0),
(23, 7, 'Observer Pattern', 0),
(24, 7, 'Factory Pattern', 0),

-- Pregunta 8: Las operaciones CRUD siempre deben ser transaccionales (Verdadero/Falso)
(25, 8, 'Falso', 1),
(26, 8, 'Verdadero', 0),

-- Pregunta 9: ¿Cuál es la mejor práctica para implementar soft delete en CRUD? (Opción múltiple)
(27, 9, 'Usar un campo deleted_at en lugar de eliminar físicamente', 1),
(28, 9, 'Eliminar directamente de la base de datos', 0),
(29, 9, 'Mover a una tabla de respaldo', 0),
(30, 9, 'Usar triggers de base de datos', 0),

-- Pregunta 10: ¿Qué es Laravel? (Opción múltiple)
(31, 10, 'Un framework de PHP', 1),
(32, 10, 'Un lenguaje de programación', 0),
(33, 10, 'Una base de datos', 0),
(34, 10, 'Un servidor web', 0),

-- Pregunta 11: Laravel está basado en el patrón MVC (Verdadero/Falso)
(35, 11, 'Verdadero', 1),
(36, 11, 'Falso', 0),

-- Pregunta 12: ¿Cuál es el comando para crear un nuevo proyecto Laravel? (Opción múltiple)
(37, 12, 'composer create-project laravel/laravel', 1),
(38, 12, 'npm install laravel', 0),
(39, 12, 'laravel new project', 0),
(40, 12, 'php artisan create', 0),

-- Pregunta 13: ¿Qué es Eloquent en Laravel? (Opción múltiple)
(41, 13, 'Un ORM (Object Relational Mapping)', 1),
(42, 13, 'Un motor de plantillas', 0),
(43, 13, 'Un sistema de rutas', 0),
(44, 13, 'Un compilador de CSS', 0),

-- Pregunta 14: ¿Cuál es el archivo principal de configuración de rutas en Laravel? (Opción múltiple)
(45, 14, 'routes/web.php', 1),
(46, 14, 'config/routes.php', 0),
(47, 14, 'app/Routes.php', 0),
(48, 14, 'public/routes.php', 0),

-- Pregunta 15: Laravel Blade es solo para crear vistas HTML (Verdadero/Falso)
(49, 15, 'Falso', 1),
(50, 15, 'Verdadero', 0),

-- Pregunta 16: ¿Qué es el Service Container en Laravel? (Opción múltiple)
(51, 16, 'Un sistema de inyección de dependencias', 1),
(52, 16, 'Un motor de base de datos', 0),
(53, 16, 'Un sistema de cache', 0),
(54, 16, 'Un compilador de assets', 0),

-- Pregunta 17: Los Event Listeners en Laravel siempre deben ser síncronos (Verdadero/Falso)
(55, 17, 'Falso', 1),
(56, 17, 'Verdadero', 0),

-- Pregunta 18: ¿Cuál es la diferencia entre Facade y Dependency Injection en Laravel? (Opción múltiple)
(57, 18, 'Facade es un patrón estático, DI es dinámico', 1),
(58, 18, 'Son exactamente lo mismo', 0),
(59, 18, 'Facade es más rápido siempre', 0),
(60, 18, 'DI no se puede usar con Laravel', 0),

-- Pregunta 19: ¿Qué significa PHP? (Opción múltiple)
(61, 19, 'PHP: Hypertext Preprocessor', 1),
(62, 19, 'Personal Home Page', 0),
(63, 19, 'Private Hypertext Processor', 0),
(64, 19, 'Public HTML Protocol', 0),

-- Pregunta 20: PHP es un lenguaje compilado (Verdadero/Falso)
(65, 20, 'Falso', 1),
(66, 20, 'Verdadero', 0),

-- Pregunta 21: ¿Cuál es la extensión de archivo para PHP? (Opción múltiple)
(67, 21, '.php', 1),
(68, 21, '.html', 0),
(69, 21, '.js', 0),
(70, 21, '.py', 0),

-- Pregunta 22: ¿Qué son las superglobales en PHP? (Opción múltiple)
(71, 22, 'Variables predefinidas disponibles en cualquier ámbito', 1),
(72, 22, 'Variables que solo existen en funciones', 0),
(73, 22, 'Variables de configuración del servidor', 0),
(74, 22, 'Variables temporales del sistema', 0),

-- Pregunta 23: ¿Cuál es la diferencia entre include y require en PHP? (Opción múltiple)
(75, 23, 'require genera error fatal si falla, include solo warning', 1),
(76, 23, 'include es más rápido que require', 0),
(77, 23, 'require solo funciona con archivos PHP', 0),
(78, 23, 'No hay diferencia entre ambos', 0),

-- Pregunta 24: PHP soporta herencia múltiple (Verdadero/Falso)
(79, 24, 'Falso', 1),
(80, 24, 'Verdadero', 0),

-- Pregunta 25: ¿Qué es el Garbage Collector en PHP? (Opción múltiple)
(81, 25, 'Sistema automático de liberación de memoria', 1),
(82, 25, 'Un depurador de código', 0),
(83, 25, 'Un optimizador de consultas', 0),
(84, 25, 'Un sistema de cache', 0),

-- Pregunta 26: ¿Cuál es la diferencia entre early binding y late binding en PHP? (Opción múltiple)
(85, 26, 'Early binding en tiempo de compilación, late binding en runtime', 1),
(86, 26, 'Early binding es más lento', 0),
(87, 26, 'Late binding no existe en PHP', 0),
(88, 26, 'Son sinónimos en PHP', 0),

-- Pregunta 27: PHP utiliza copy-on-write para la gestión de memoria (Verdadero/Falso)
(89, 27, 'Verdadero', 1),
(90, 27, 'Falso', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temas`
--

CREATE TABLE `temas` (
  `id_tema` int(11) NOT NULL,
  `nombre_tema` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `temas`
--

INSERT INTO `temas` (`id_tema`, `nombre_tema`, `descripcion`) VALUES
(1, 'CRUD', 'Operaciones básicas de Create, Read, Update, Delete'),
(2, 'Laravel', 'Framework de PHP para desarrollo web'),
(3, 'PHP', 'Lenguaje de programación web del lado del servidor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cod_categoria` int(11) DEFAULT NULL,
  `monedas_totales` int(11) DEFAULT 100,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `colaboradores`
--
ALTER TABLE `colaboradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD PRIMARY KEY (`id_pregunta`),
  ADD KEY `cod_categoria` (`cod_categoria`),
  ADD KEY `id_tema` (`id_tema`);

--
-- Indices de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD PRIMARY KEY (`id_respuesta`),
  ADD KEY `id_pregunta` (`id_pregunta`);

--
-- Indices de la tabla `temas`
--
ALTER TABLE `temas`
  ADD PRIMARY KEY (`id_tema`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `colaboradores`
--
ALTER TABLE `colaboradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  MODIFY `id_pregunta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `temas`
--
ALTER TABLE `temas`
  MODIFY `id_tema` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD CONSTRAINT `preguntas_ibfk_1` FOREIGN KEY (`cod_categoria`) REFERENCES `categoria` (`id_categoria`),
  ADD CONSTRAINT `preguntas_ibfk_2` FOREIGN KEY (`id_tema`) REFERENCES `temas` (`id_tema`);

--
-- Filtros para la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD CONSTRAINT `respuestas_ibfk_1` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id_pregunta`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
