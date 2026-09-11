-- ==========================================================
-- Tabla para registrar los datos del acudiente de los
-- aspirantes menores de edad.
-- Se relaciona con user_register a traves de number_id.
-- ==========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `acudientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number_id` bigint(20) NOT NULL COMMENT 'Documento del estudiante (user_register.number_id)',
  `guardian_full_name` varchar(255) NOT NULL,
  `guardian_document` varchar(50) NOT NULL,
  `guardian_phone` varchar(20) NOT NULL,
  `guardian_email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_acudientes_number` (`number_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
