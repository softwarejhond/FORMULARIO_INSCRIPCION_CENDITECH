-- ==========================================================
-- Tabla para registrar las matriculas automaticas en Moodle
-- (cuando el estudiante confirma su correo)
-- ==========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` varchar(10) NOT NULL,
  `number_id` bigint(20) NOT NULL COMMENT 'Cedula del estudiante',
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL COMMENT 'Correo personal',
  `institutional_email` varchar(255) NOT NULL COMMENT 'Correo institucional generado',
  `username` varchar(100) NOT NULL COMMENT 'Usuario Moodle (cedula)',
  `password` varchar(255) NOT NULL COMMENT 'Contrasena inicial',
  `program` varchar(50) NOT NULL COMMENT 'Codigo del area tecnica (CIBER, IA, ...)',
  `program_name` varchar(255) NOT NULL COMMENT 'Nombre del tecnico',
  `moodle_user_id` int(11) NOT NULL COMMENT 'ID del usuario creado en Moodle',
  `set_id` int(11) NOT NULL COMMENT 'ID del set en sets_cursos',
  `course_tecnico_id` int(11) NOT NULL,
  `course_ingles_id` int(11) NOT NULL,
  `course_habilidades_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'enrolled' COMMENT 'enrolled | pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_number_set` (`number_id`, `set_id`),
  KEY `idx_enrollments_number` (`number_id`),
  KEY `idx_enrollments_moodle_user` (`moodle_user_id`),
  KEY `idx_enrollments_set` (`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
