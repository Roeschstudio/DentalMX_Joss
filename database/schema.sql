-- ============================================================================
-- DENTALMX - SCHEMA DE BASE DE DATOS
-- ============================================================================
-- Sistema de Gestión Dental Integral
-- Versión: 1.0.0
-- Fecha: 2024
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ============================================================================
-- NOTA: La base de datos debe ser creada previamente por el instalador
-- El nombre predeterminado es: engsigne_magic_dental
-- ============================================================================

-- ============================================================================
-- TABLAS PRINCIPALES (Sin dependencias)
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Tabla: usuarios
-- Usuarios del sistema (administradores, médicos, asistentes)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `apellido` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(150) NOT NULL,
    `telefono` VARCHAR(20) DEFAULT NULL,
    `direccion` TEXT DEFAULT NULL,
    `foto_perfil` VARCHAR(255) DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `rol` ENUM('admin', 'medico', 'asistente') NOT NULL DEFAULT 'medico',
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: pacientes
-- Datos básicos de los pacientes
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `pacientes`;
CREATE TABLE `pacientes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `primer_apellido` VARCHAR(100) NOT NULL,
    `segundo_apellido` VARCHAR(100) DEFAULT NULL,
    `fecha_nacimiento` DATE DEFAULT NULL,
    `nacionalidad` VARCHAR(50) DEFAULT NULL,
    `domicilio` TEXT DEFAULT NULL,
    `telefono` VARCHAR(20) DEFAULT NULL,
    `celular` VARCHAR(20) DEFAULT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_paciente_nombre` (`nombre`, `primer_apellido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: servicios
-- Catálogo de servicios dentales
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `servicios`;
CREATE TABLE `servicios` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(150) NOT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `precio_base` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: medicamentos
-- Catálogo de medicamentos para recetas
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `medicamentos`;
CREATE TABLE `medicamentos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre_comercial` VARCHAR(150) NOT NULL,
    `sustancia_activa` VARCHAR(150) DEFAULT NULL,
    `presentacion` VARCHAR(100) DEFAULT NULL,
    `indicaciones_base` TEXT DEFAULT NULL,
    `stock` INT(11) DEFAULT 0,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: configuracion_clinica
-- Configuración general de la clínica
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `configuracion_clinica`;
CREATE TABLE `configuracion_clinica` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre_clinica` VARCHAR(150) NOT NULL,
    `logo` VARCHAR(255) DEFAULT NULL,
    `telefono` VARCHAR(20) DEFAULT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `direccion` TEXT DEFAULT NULL,
    `horario_atencion` VARCHAR(255) DEFAULT NULL,
    `vigencia_presupuestos` INT(11) DEFAULT 30,
    `mensaje_bienvenida` TEXT DEFAULT NULL,
    `mail_host` VARCHAR(100) DEFAULT NULL,
    `mail_port` INT(11) DEFAULT NULL,
    `mail_username` VARCHAR(100) DEFAULT NULL,
    `mail_password` VARCHAR(255) DEFAULT NULL,
    `mail_encryption` VARCHAR(20) DEFAULT NULL,
    `mail_from_email` VARCHAR(150) DEFAULT NULL,
    `mail_from_name` VARCHAR(100) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: catalogos_odontologicos
-- Catálogos para el odontograma (diagnósticos, tratamientos, condiciones)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `catalogos_odontologicos`;
CREATE TABLE `catalogos_odontologicos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `codigo` VARCHAR(20) NOT NULL,
    `tipo` ENUM('diagnostico', 'tratamiento', 'condicion', 'hallazgo', 'superficie_estado') NOT NULL,
    `nombre` VARCHAR(255) NOT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `icono` VARCHAR(50) DEFAULT NULL,
    `color_hex` VARCHAR(7) DEFAULT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `orden` INT(3) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_codigo` (`codigo`),
    KEY `idx_catalogo_tipo` (`tipo`),
    KEY `idx_catalogo_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLAS DEPENDIENTES DE PACIENTES
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Tabla: datos_generales
-- Datos generales del historial clínico del paciente
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `datos_generales`;
CREATE TABLE `datos_generales` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_paciente` INT(11) NOT NULL,
    `edad` INT(3) DEFAULT NULL,
    `sexo` ENUM('M', 'F', 'Otro') DEFAULT NULL,
    `peso` DECIMAL(5,2) DEFAULT NULL,
    `tipo_sangre` VARCHAR(5) DEFAULT NULL,
    `fecha_nacimiento` DATE DEFAULT NULL,
    `estado_civil` VARCHAR(20) DEFAULT NULL,
    `ocupacion` VARCHAR(100) DEFAULT NULL,
    `lugar_trabajo` VARCHAR(150) DEFAULT NULL,
    `cuenta_seguro` TINYINT(1) DEFAULT 0,
    `seguro` VARCHAR(100) DEFAULT NULL,
    `nombre_contacto_emergencia` VARCHAR(150) DEFAULT NULL,
    `embarazo` TINYINT(1) DEFAULT 0,
    `meses_embarazo` INT(2) DEFAULT NULL,
    `ginecologo` VARCHAR(100) DEFAULT NULL,
    `telefono_ginecologo` VARCHAR(20) DEFAULT NULL,
    `lugar_control` VARCHAR(150) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_datos_paciente` (`id_paciente`),
    CONSTRAINT `fk_datos_generales_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: antecedentes_familiares
-- Antecedentes familiares del paciente
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `antecedentes_familiares`;
CREATE TABLE `antecedentes_familiares` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_paciente` INT(11) NOT NULL,
    `integrante_padece` TEXT DEFAULT NULL,
    `cual_enfermedad` TEXT DEFAULT NULL,
    `padre_alive` TINYINT(1) DEFAULT 1,
    `razon_padre` TEXT DEFAULT NULL,
    `madre_alive` TINYINT(1) DEFAULT 1,
    `razon_madre` TEXT DEFAULT NULL,
    `hermano_alive` TINYINT(1) DEFAULT 1,
    `razon_hermano` TEXT DEFAULT NULL,
    `hermana_alive` TINYINT(1) DEFAULT 1,
    `razon_hermana` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_antfam_paciente` (`id_paciente`),
    CONSTRAINT `fk_antecedentes_familiares_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: antecedentes_patologicos
-- Antecedentes patológicos del paciente
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `antecedentes_patologicos`;
CREATE TABLE `antecedentes_patologicos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_paciente` INT(11) NOT NULL,
    `tratamiento` TEXT DEFAULT NULL,
    `tipo_tratamiento` VARCHAR(100) DEFAULT NULL,
    `sustancia` TEXT DEFAULT NULL,
    `tipo_sustancia` VARCHAR(100) DEFAULT NULL,
    `hospitalizado` TINYINT(1) DEFAULT 0,
    `motivo_hospitalizado` TEXT DEFAULT NULL,
    `alergico` TINYINT(1) DEFAULT 0,
    `sustancia_alergia` TEXT DEFAULT NULL,
    `anestesiado` TINYINT(1) DEFAULT 0,
    `anestesia_reaccion` TEXT DEFAULT NULL,
    `chk_hipertension` TINYINT(1) DEFAULT 0,
    `chk_cardiopatia` TINYINT(1) DEFAULT 0,
    `chk_hepatica` TINYINT(1) DEFAULT 0,
    `chk_pulmonar` TINYINT(1) DEFAULT 0,
    `chk_digestivas` TINYINT(1) DEFAULT 0,
    `chk_diabetes` TINYINT(1) DEFAULT 0,
    `chk_asma` TINYINT(1) DEFAULT 0,
    `chk_transtornos` TINYINT(1) DEFAULT 0,
    `chk_vih` TINYINT(1) DEFAULT 0,
    `chk_epilepcia` TINYINT(1) DEFAULT 0,
    `chk_respiratorias` TINYINT(1) DEFAULT 0,
    `chk_nerviosa` TINYINT(1) DEFAULT 0,
    `txt_otra_enfermedad` TEXT DEFAULT NULL,
    `tiroides` TINYINT(1) DEFAULT 0,
    `reumatica` TINYINT(1) DEFAULT 0,
    `alcoholicas` TINYINT(1) DEFAULT 0,
    `alcoholicas_frecuencia` VARCHAR(50) DEFAULT NULL,
    `cigarrillos` TINYINT(1) DEFAULT 0,
    `cigarrillos_frecuencia` VARCHAR(50) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_antpat_paciente` (`id_paciente`),
    CONSTRAINT `fk_antecedentes_patologicos_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: historia_bucodental
-- Historial bucodental del paciente
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `historia_bucodental`;
CREATE TABLE `historia_bucodental` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_paciente` INT(11) NOT NULL,
    `primera_vez_dentista` VARCHAR(50) DEFAULT NULL,
    `cuanto_ultima_vez` VARCHAR(50) DEFAULT NULL,
    `tratamiento_realizaron` TEXT DEFAULT NULL,
    `tomaron_radiografia` TINYINT(1) DEFAULT 0,
    `movilidad_dientes` TINYINT(1) DEFAULT 0,
    `sangran_encias` TINYINT(1) DEFAULT 0,
    `frecuencia_sangran_encias` VARCHAR(50) DEFAULT NULL,
    `mal_sabor_boca` TINYINT(1) DEFAULT 0,
    `resequedad_boca` TINYINT(1) DEFAULT 0,
    `tenido_infeccion_dientes` TINYINT(1) DEFAULT 0,
    `cuanto_tiempo_infeccion_dientes` VARCHAR(50) DEFAULT NULL,
    `rechinan_dientes` TINYINT(1) DEFAULT 0,
    `dolor_cabeza` TINYINT(1) DEFAULT 0,
    `frecuencia_dolor_cabeza` VARCHAR(50) DEFAULT NULL,
    `veces_cepilla_al_dia` INT(2) DEFAULT NULL,
    `cuando_cambia_cepillo` VARCHAR(50) DEFAULT NULL,
    `usa_hilo_dental` TINYINT(1) DEFAULT 0,
    `enjuages_bucales` TINYINT(1) DEFAULT 0,
    `motivo_consulta` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_histbuco_paciente` (`id_paciente`),
    CONSTRAINT `fk_historia_bucodental_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: notas_evolucion
-- Notas de evolución del tratamiento del paciente
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `notas_evolucion`;
CREATE TABLE `notas_evolucion` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_paciente` INT(11) NOT NULL,
    `fecha` DATE NOT NULL,
    `tratamiento_realizado` TEXT DEFAULT NULL,
    `total` DECIMAL(10,2) DEFAULT 0.00,
    `abono` DECIMAL(10,2) DEFAULT 0.00,
    `saldo` DECIMAL(10,2) DEFAULT 0.00,
    `firma` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_notas_paciente` (`id_paciente`),
    CONSTRAINT `fk_notas_evolucion_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: odontogramas
-- Odontogramas de los pacientes
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `odontogramas`;
CREATE TABLE `odontogramas` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_paciente` INT(11) NOT NULL,
    `tipo_dentadura` ENUM('adulto', 'infantil', 'mixta') DEFAULT 'adulto',
    `observaciones_generales` TEXT DEFAULT NULL,
    `estado_general` VARCHAR(50) DEFAULT 'activo',
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_odontograma_paciente` (`id_paciente`),
    CONSTRAINT `fk_odontogramas_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: odontograma_dientes
-- Detalle de cada diente en el odontograma
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `odontograma_dientes`;
CREATE TABLE `odontograma_dientes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_odontograma` INT(11) NOT NULL,
    `numero_diente` INT(2) NOT NULL,
    `estado` VARCHAR(50) DEFAULT 'sano',
    `sup_oclusal` VARCHAR(50) DEFAULT NULL,
    `sup_vestibular` VARCHAR(50) DEFAULT NULL,
    `sup_lingual` VARCHAR(50) DEFAULT NULL,
    `sup_mesial` VARCHAR(50) DEFAULT NULL,
    `sup_distal` VARCHAR(50) DEFAULT NULL,
    `movilidad` VARCHAR(20) DEFAULT NULL,
    `sensibilidad` VARCHAR(50) DEFAULT NULL,
    `diagnosticos` TEXT DEFAULT NULL,
    `tratamientos_realizados` TEXT DEFAULT NULL,
    `tratamientos_pendientes` TEXT DEFAULT NULL,
    `condiciones_especiales` TEXT DEFAULT NULL,
    `hallazgos` TEXT DEFAULT NULL,
    `notas` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_diente_odontograma` (`id_odontograma`, `numero_diente`),
    CONSTRAINT `fk_odontograma_dientes_odontograma` FOREIGN KEY (`id_odontograma`) REFERENCES `odontogramas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLAS DEPENDIENTES DE USUARIOS Y PACIENTES
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Tabla: citas
-- Citas de los pacientes
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `citas`;
CREATE TABLE `citas` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_paciente` INT(11) NOT NULL,
    `id_usuario` INT(11) UNSIGNED NOT NULL,
    `id_servicio` INT(11) UNSIGNED DEFAULT NULL,
    `titulo` VARCHAR(255) NOT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `fecha_inicio` DATETIME NOT NULL,
    `fecha_fin` DATETIME NOT NULL,
    `estado` ENUM('programada', 'confirmada', 'en_progreso', 'completada', 'cancelada') NOT NULL DEFAULT 'programada',
    `tipo_cita` ENUM('consulta', 'tratamiento', 'revision', 'urgencia') NOT NULL DEFAULT 'consulta',
    `color` VARCHAR(7) DEFAULT '#5ccdde',
    `notas` TEXT DEFAULT NULL,
    `recordatorio_enviado` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_cita_paciente` (`id_paciente`),
    KEY `idx_cita_usuario` (`id_usuario`),
    KEY `idx_cita_fecha` (`fecha_inicio`),
    KEY `idx_cita_estado` (`estado`),
    CONSTRAINT `fk_citas_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_citas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_citas_servicio` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: presupuestos
-- Presupuestos para pacientes
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `presupuestos`;
CREATE TABLE `presupuestos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_paciente` INT(11) NOT NULL,
    `id_usuario` INT(11) UNSIGNED NOT NULL,
    `folio` VARCHAR(50) DEFAULT NULL,
    `fecha_emision` DATE NOT NULL,
    `fecha_vigencia` DATE DEFAULT NULL,
    `subtotal` DECIMAL(10,2) DEFAULT 0.00,
    `iva` DECIMAL(10,2) DEFAULT 0.00,
    `total` DECIMAL(10,2) DEFAULT 0.00,
    `estado` ENUM('borrador', 'enviado', 'aprobado', 'rechazado', 'vencido') DEFAULT 'borrador',
    `observaciones` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_presupuesto_paciente` (`id_paciente`),
    KEY `idx_presupuesto_usuario` (`id_usuario`),
    KEY `idx_presupuesto_folio` (`folio`),
    CONSTRAINT `fk_presupuestos_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_presupuestos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: presupuestos_detalles
-- Detalle de servicios en presupuestos
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `presupuestos_detalles`;
CREATE TABLE `presupuestos_detalles` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_presupuesto` INT(11) UNSIGNED NOT NULL,
    `id_servicio` INT(11) UNSIGNED DEFAULT NULL,
    `descripcion` VARCHAR(255) DEFAULT NULL,
    `cantidad` INT(11) DEFAULT 1,
    `precio_unitario` DECIMAL(10,2) DEFAULT 0.00,
    `descuento_porcentaje` DECIMAL(5,2) DEFAULT 0.00,
    `subtotal` DECIMAL(10,2) DEFAULT 0.00,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_detalle_presupuesto` (`id_presupuesto`),
    CONSTRAINT `fk_presupuestos_detalles_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuestos` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_presupuestos_detalles_servicio` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: cotizaciones
-- Cotizaciones para pacientes
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `cotizaciones`;
CREATE TABLE `cotizaciones` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_paciente` INT(11) NOT NULL,
    `id_usuario` INT(11) UNSIGNED NOT NULL,
    `fecha_emision` DATE NOT NULL,
    `fecha_vigencia` DATE DEFAULT NULL,
    `total` DECIMAL(10,2) DEFAULT 0.00,
    `estado` ENUM('borrador', 'enviada', 'aprobada', 'rechazada', 'vencida') DEFAULT 'borrador',
    `observaciones` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_cotizacion_paciente` (`id_paciente`),
    KEY `idx_cotizacion_usuario` (`id_usuario`),
    CONSTRAINT `fk_cotizaciones_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cotizaciones_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: cotizaciones_detalles
-- Detalle de servicios en cotizaciones
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `cotizaciones_detalles`;
CREATE TABLE `cotizaciones_detalles` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_cotizacion` INT(11) UNSIGNED NOT NULL,
    `id_servicio` INT(11) UNSIGNED DEFAULT NULL,
    `cantidad` INT(11) DEFAULT 1,
    `precio_unitario` DECIMAL(10,2) DEFAULT 0.00,
    `subtotal` DECIMAL(10,2) DEFAULT 0.00,
    PRIMARY KEY (`id`),
    KEY `idx_detalle_cotizacion` (`id_cotizacion`),
    CONSTRAINT `fk_cotizaciones_detalles_cotizacion` FOREIGN KEY (`id_cotizacion`) REFERENCES `cotizaciones` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cotizaciones_detalles_servicio` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: recetas
-- Recetas médicas
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `recetas`;
CREATE TABLE `recetas` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_paciente` INT(11) NOT NULL,
    `id_usuario` INT(11) UNSIGNED DEFAULT NULL,
    `folio` VARCHAR(50) DEFAULT NULL,
    `fecha` DATE NOT NULL,
    `notas_adicionales` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_receta_paciente` (`id_paciente`),
    KEY `idx_receta_usuario` (`id_usuario`),
    CONSTRAINT `fk_recetas_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_recetas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: recetas_detalles
-- Detalle de medicamentos en recetas
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `recetas_detalles`;
CREATE TABLE `recetas_detalles` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_receta` INT(11) UNSIGNED NOT NULL,
    `id_medicamento` INT(11) UNSIGNED DEFAULT NULL,
    `dosis` VARCHAR(100) DEFAULT NULL,
    `duracion` VARCHAR(100) DEFAULT NULL,
    `cantidad` INT(11) DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_detalle_receta` (`id_receta`),
    CONSTRAINT `fk_recetas_detalles_receta` FOREIGN KEY (`id_receta`) REFERENCES `recetas` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_recetas_detalles_medicamento` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamentos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: tratamientos_realizados
-- Registro de tratamientos realizados
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tratamientos_realizados`;
CREATE TABLE `tratamientos_realizados` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_paciente` INT(11) NOT NULL,
    `id_servicio` INT(11) UNSIGNED DEFAULT NULL,
    `id_usuario` INT(11) UNSIGNED DEFAULT NULL,
    `diente` VARCHAR(10) DEFAULT NULL,
    `superficie` VARCHAR(50) DEFAULT NULL,
    `estado` VARCHAR(50) DEFAULT 'pendiente',
    `fecha_inicio` DATE DEFAULT NULL,
    `fecha_fin` DATE DEFAULT NULL,
    `observaciones` TEXT DEFAULT NULL,
    `costo` DECIMAL(10,2) DEFAULT 0.00,
    `pagado` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_tratamiento_paciente` (`id_paciente`),
    KEY `idx_tratamiento_usuario` (`id_usuario`),
    CONSTRAINT `fk_tratamientos_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tratamientos_servicio` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_tratamientos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: historial_actividades
-- Registro de actividades del sistema
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `historial_actividades`;
CREATE TABLE `historial_actividades` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_paciente` INT(11) DEFAULT NULL,
    `id_usuario` INT(11) UNSIGNED DEFAULT NULL,
    `tipo_actividad` VARCHAR(50) NOT NULL,
    `id_referencia` INT(11) DEFAULT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `fecha_actividad` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_historial_paciente` (`id_paciente`),
    KEY `idx_historial_usuario` (`id_usuario`),
    KEY `idx_historial_tipo` (`tipo_actividad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: odontograma_historial
-- Historial de cambios en odontogramas
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `odontograma_historial`;
CREATE TABLE `odontograma_historial` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_odontograma` INT(11) NOT NULL,
    `numero_diente` INT(2) DEFAULT NULL,
    `tipo_accion` VARCHAR(50) DEFAULT NULL,
    `campo_modificado` VARCHAR(100) DEFAULT NULL,
    `valor_anterior` TEXT DEFAULT NULL,
    `valor_nuevo` TEXT DEFAULT NULL,
    `descripcion_cambio` TEXT DEFAULT NULL,
    `usuario_modificacion` INT(11) UNSIGNED DEFAULT NULL,
    `fecha_modificacion` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_historial_odontograma` (`id_odontograma`),
    CONSTRAINT `fk_odontograma_historial_odontograma` FOREIGN KEY (`id_odontograma`) REFERENCES `odontogramas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: preferencias_usuario
-- Preferencias de cada usuario
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `preferencias_usuario`;
CREATE TABLE `preferencias_usuario` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_usuario` INT(11) UNSIGNED NOT NULL,
    `tema` VARCHAR(20) DEFAULT 'light',
    `idioma` VARCHAR(10) DEFAULT 'es',
    `notificaciones_email` TINYINT(1) DEFAULT 1,
    `notificaciones_sistema` TINYINT(1) DEFAULT 1,
    `formato_fecha` VARCHAR(20) DEFAULT 'd/m/Y',
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_preferencias_usuario` (`id_usuario`),
    CONSTRAINT `fk_preferencias_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: historial_adjuntos
-- Archivos adjuntos de pacientes
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `historial_adjuntos`;
CREATE TABLE `historial_adjuntos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_paciente` INT(11) NOT NULL,
    `nombre_archivo` VARCHAR(255) NOT NULL,
    `ruta_archivo` VARCHAR(500) NOT NULL,
    `tipo_archivo` VARCHAR(50) DEFAULT NULL,
    `tamano` INT(11) DEFAULT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_adjuntos_paciente` (`id_paciente`),
    CONSTRAINT `fk_adjuntos_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLAS DE INVENTARIO (Opcional)
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Tabla: proveedores
-- Proveedores de materiales e insumos
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre_empresa` VARCHAR(150) NOT NULL,
    `nombre_contacto` VARCHAR(100) DEFAULT NULL,
    `telefono` VARCHAR(20) DEFAULT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `direccion` TEXT DEFAULT NULL,
    `notas` TEXT DEFAULT NULL,
    `activo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: inventario
-- Inventario de materiales e insumos
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `inventario`;
CREATE TABLE `inventario` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_medicamento` INT(11) UNSIGNED DEFAULT NULL,
    `id_proveedor` INT(11) UNSIGNED DEFAULT NULL,
    `lote` VARCHAR(50) DEFAULT NULL,
    `cantidad` INT(11) DEFAULT 0,
    `cantidad_minima` INT(11) DEFAULT 10,
    `fecha_vencimiento` DATE DEFAULT NULL,
    `precio_compra` DECIMAL(10,2) DEFAULT 0.00,
    `ubicacion` VARCHAR(100) DEFAULT NULL,
    `notas` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_inventario_medicamento` (`id_medicamento`),
    KEY `idx_inventario_proveedor` (`id_proveedor`),
    CONSTRAINT `fk_inventario_medicamento` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamentos` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_inventario_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: movimientos_inventario
-- Registro de movimientos de inventario
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `movimientos_inventario`;
CREATE TABLE `movimientos_inventario` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_inventario` INT(11) UNSIGNED NOT NULL,
    `tipo_movimiento` ENUM('entrada', 'salida', 'ajuste') NOT NULL,
    `cantidad` INT(11) NOT NULL,
    `motivo` TEXT DEFAULT NULL,
    `id_usuario` INT(11) UNSIGNED DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_movimiento_inventario` (`id_inventario`),
    CONSTRAINT `fk_movimientos_inventario` FOREIGN KEY (`id_inventario`) REFERENCES `inventario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: alertas_inventario
-- Alertas de inventario (stock bajo, vencimiento, etc.)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `alertas_inventario`;
CREATE TABLE `alertas_inventario` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_inventario` INT(11) UNSIGNED NOT NULL,
    `id_medicamento` INT(11) UNSIGNED NOT NULL,
    `tipo_alerta` ENUM('stock_bajo', 'stock_alto', 'proximo_vencimiento', 'vencido', 'custom') NOT NULL,
    `prioridad` ENUM('baja', 'media', 'alta', 'critica') NOT NULL DEFAULT 'media',
    `mensaje` TEXT DEFAULT NULL,
    `estado` ENUM('activa', 'resuelta', 'ignorada') NOT NULL DEFAULT 'activa',
    `fecha_alerta` DATETIME DEFAULT NULL,
    `fecha_resuelta` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_alerta_inventario` (`id_inventario`),
    KEY `idx_alerta_medicamento` (`id_medicamento`),
    CONSTRAINT `fk_alertas_inventario` FOREIGN KEY (`id_inventario`) REFERENCES `inventario` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_alertas_medicamento` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLAS DE CONFIGURACIÓN ADICIONAL
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Tabla: doctor_schedules
-- Horarios de doctores
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `doctor_schedules`;
CREATE TABLE `doctor_schedules` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_usuario` INT(11) UNSIGNED NOT NULL,
    `dia_semana` TINYINT(1) NOT NULL COMMENT '0=Domingo, 1=Lunes, ..., 6=Sábado',
    `hora_inicio` TIME NOT NULL,
    `hora_fin` TIME NOT NULL,
    `activo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_schedule_usuario` (`id_usuario`),
    CONSTRAINT `fk_schedule_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: doctor_exceptions
-- Excepciones en horarios (vacaciones, días especiales)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `doctor_exceptions`;
CREATE TABLE `doctor_exceptions` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_usuario` INT(11) UNSIGNED NOT NULL,
    `fecha` DATE NOT NULL,
    `tipo` ENUM('no_disponible', 'horario_especial') NOT NULL,
    `hora_inicio` TIME DEFAULT NULL,
    `hora_fin` TIME DEFAULT NULL,
    `motivo` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_exception_usuario` (`id_usuario`),
    CONSTRAINT `fk_exception_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: doctor_preferences
-- Preferencias de doctores
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `doctor_preferences`;
CREATE TABLE `doctor_preferences` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_usuario` INT(11) UNSIGNED NOT NULL,
    `duracion_cita_default` INT(11) DEFAULT 30,
    `intervalo_citas` INT(11) DEFAULT 15,
    `max_citas_dia` INT(11) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_doctor_preferences` (`id_usuario`),
    CONSTRAINT `fk_preferences_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabla: migrations
-- Control de migraciones de CodeIgniter
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `version` VARCHAR(255) NOT NULL,
    `class` VARCHAR(255) NOT NULL,
    `group` VARCHAR(255) NOT NULL,
    `namespace` VARCHAR(255) NOT NULL,
    `time` INT(11) NOT NULL,
    `batch` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- FIN DEL SCHEMA
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 1;

-- Mensaje de confirmación
SELECT 'Schema de DentalMX creado exitosamente' AS mensaje;

