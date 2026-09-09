-- ==========================================================
-- Tablas para registrar los cursos creados en Moodle
-- (crear_set_curso_tecnico.php)
-- ==========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Tabla 1: registro individual de cada curso creado
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL COMMENT 'ID del curso en Moodle',
  `course_code` varchar(50) NOT NULL COMMENT 'Shortname del curso (ej: BLO-1, ING-BLO-1)',
  `course_name` varchar(255) NOT NULL COMMENT 'Fullname del curso',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_course_id` (`course_id`),
  UNIQUE KEY `uk_course_code` (`course_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla 2: registro de la tripleta de cursos
-- (técnico + inglés + habilidades blandas)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `sets_cursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_tecnico` varchar(20) NOT NULL COMMENT 'Código del área técnica (ej: BLO, IA, DT)',
  `serie` int(11) NOT NULL COMMENT 'Número de serie del set',
  `curso_tecnico_id` int(11) NOT NULL COMMENT 'course_id del curso técnico',
  `curso_ingles_id` int(11) NOT NULL COMMENT 'course_id del curso de Inglés',
  `curso_habilidades_id` int(11) NOT NULL COMMENT 'course_id del curso de Habilidades Blandas',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_codigo_serie` (`codigo_tecnico`, `serie`),
  KEY `fk_sets_tecnico` (`curso_tecnico_id`),
  KEY `fk_sets_ingles` (`curso_ingles_id`),
  KEY `fk_sets_habilidades` (`curso_habilidades_id`),
  CONSTRAINT `fk_sets_tecnico` FOREIGN KEY (`curso_tecnico_id`) REFERENCES `cursos` (`course_id`),
  CONSTRAINT `fk_sets_ingles` FOREIGN KEY (`curso_ingles_id`) REFERENCES `cursos` (`course_id`),
  CONSTRAINT `fk_sets_habilidades` FOREIGN KEY (`curso_habilidades_id`) REFERENCES `cursos` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
