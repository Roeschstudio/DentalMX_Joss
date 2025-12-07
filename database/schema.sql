-- ========================================
-- SCHEMA DE BASE DE DATOS - DENTALMX
-- Sistema de Gestión Dental Integral
-- ========================================
-- Versión: 1.0.0
-- Fecha: 2024-12-07
-- Charset: utf8mb4
-- Collation: utf8mb4_unicode_ci
-- ========================================
-- INSTRUCCIONES DE INSTALACIÓN:
-- 1. Crear base de datos: CREATE DATABASE dentalmx CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 2. Seleccionar base de datos: USE dentalmx;
-- 3. Ejecutar este archivo: SOURCE schema.sql;
-- 4. Luego ejecutar: SOURCE initial_data.sql;
-- ========================================

-- Configuración inicial
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ========================================
-- TABLAS BASE (sin dependencias)
-- ========================================

-- Tabla: usuarios
-- Modelo: UsuariosModel
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','medico','asistente') NOT NULL DEFAULT 'medico',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: pacientes
-- Modelo: PacientesModel
DROP TABLE IF EXISTS `pacientes`;
CREATE TABLE `pacientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `primer_apellido` varchar(100) NOT NULL,
  `segundo_apellido` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `nacionalidad` varchar(50) DEFAULT NULL,
  `domicilio` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: servicios
-- Modelo: ServiciosModel
DROP TABLE IF EXISTS `servicios`;
CREATE TABLE `servicios` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_base` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: medicamentos
-- Modelo: MedicamentosModel
DROP TABLE IF EXISTS `medicamentos`;
CREATE TABLE `medicamentos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_comercial` varchar(150) NOT NULL,
  `sustancia_activa` varchar(150) DEFAULT NULL,
  `presentacion` varchar(100) DEFAULT NULL,
  `indicaciones_base` text DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: proveedores
DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_empresa` varchar(150) NOT NULL,
  `nombre_contacto` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `telefono_secundario` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `email_secundario` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `estado_provincia` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `pais` varchar(50) NOT NULL DEFAULT 'México',
  `rfc` varchar(13) DEFAULT NULL,
  `tipo_proveedor` enum('medicamentos','insumos_medicos','ambos') NOT NULL DEFAULT 'medicamentos',
  `dias_credito` int(3) NOT NULL DEFAULT 0,
  `limite_credito` decimal(12,2) NOT NULL DEFAULT 0.00,
  `descuento_base` decimal(5,2) NOT NULL DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  `estado` enum('activo','inactivo','suspendido') NOT NULL DEFAULT 'activo',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nombre_empresa` (`nombre_empresa`),
  KEY `estado` (`estado`),
  KEY `tipo_proveedor` (`tipo_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: configuracion_clinica
-- Modelo: ConfiguracionClinicaModel
DROP TABLE IF EXISTS `configuracion_clinica`;
CREATE TABLE `configuracion_clinica` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_clinica` varchar(150) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `horario_atencion` varchar(255) DEFAULT NULL,
  `vigencia_presupuestos` int(11) NOT NULL DEFAULT 30,
  `mensaje_bienvenida` text DEFAULT NULL,
  `mail_host` varchar(255) DEFAULT NULL,
  `mail_port` int(11) DEFAULT NULL,
  `mail_username` varchar(255) DEFAULT NULL,
  `mail_password` varchar(255) DEFAULT NULL,
  `mail_encryption` varchar(10) DEFAULT NULL,
  `mail_from_email` varchar(255) DEFAULT NULL,
  `mail_from_name` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: catalogos_odontologicos
DROP TABLE IF EXISTS `catalogos_odontologicos`;
CREATE TABLE `catalogos_odontologicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `tipo` enum('diagnostico','tratamiento','condicion','hallazgo','superficie_estado') NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `color_hex` varchar(7) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(3) DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_codigo` (`codigo`),
  KEY `idx_catalogo_tipo` (`tipo`),
  KEY `idx_catalogo_activo` (`activo`),
  KEY `idx_catalogo_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: migrations (para CodeIgniter)
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLAS CON DEPENDENCIAS DE USUARIOS
-- ========================================

-- Tabla: preferencias_usuario
-- Modelo: PreferenciasUsuarioModel
DROP TABLE IF EXISTS `preferencias_usuario`;
CREATE TABLE `preferencias_usuario` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned NOT NULL,
  `tema` enum('light','dark','auto') NOT NULL DEFAULT 'light',
  `idioma` varchar(5) NOT NULL DEFAULT 'es',
  `notificaciones_email` tinyint(1) NOT NULL DEFAULT 1,
  `notificaciones_sistema` tinyint(1) NOT NULL DEFAULT 1,
  `formato_fecha` varchar(20) NOT NULL DEFAULT 'd/m/Y',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `preferencias_usuario_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: doctor_schedules
-- Modelo: DoctorScheduleModel
DROP TABLE IF EXISTS `doctor_schedules`;
CREATE TABLE `doctor_schedules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) unsigned NOT NULL COMMENT 'ID del doctor/usuario',
  `dia_semana` tinyint(1) NOT NULL COMMENT '1=Lunes, 2=Martes, ..., 7=Domingo',
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `usuario_id_dia_semana` (`usuario_id`,`dia_semana`),
  CONSTRAINT `doctor_schedules_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: doctor_exceptions
-- Modelo: DoctorExceptionModel
DROP TABLE IF EXISTS `doctor_exceptions`;
CREATE TABLE `doctor_exceptions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) unsigned NOT NULL COMMENT 'ID del doctor/usuario',
  `fecha` date NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `todo_el_dia` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Todo el día, 0=Parcial',
  `hora_inicio` time DEFAULT NULL COMMENT 'Solo si es parcial',
  `hora_fin` time DEFAULT NULL COMMENT 'Solo si es parcial',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `usuario_id_fecha` (`usuario_id`,`fecha`),
  CONSTRAINT `doctor_exceptions_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: doctor_preferences
-- Modelo: DoctorPreferenceModel
DROP TABLE IF EXISTS `doctor_preferences`;
CREATE TABLE `doctor_preferences` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) unsigned NOT NULL COMMENT 'ID del doctor/usuario',
  `duracion_cita` int(11) NOT NULL DEFAULT 30 COMMENT 'Duración de cita en minutos',
  `tiempo_descanso` int(11) NOT NULL DEFAULT 15 COMMENT 'Descanso entre citas en minutos',
  `citas_simultaneas` int(11) NOT NULL DEFAULT 1 COMMENT 'Número máximo de citas simultáneas',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `doctor_preferences_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLAS CON DEPENDENCIAS DE PACIENTES
-- ========================================

-- Tabla: datos_generales
-- Modelo: DatosGeneralesModel
DROP TABLE IF EXISTS `datos_generales`;
CREATE TABLE `datos_generales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `edad` int(11) DEFAULT NULL,
  `sexo` enum('M','F') DEFAULT NULL,
  `peso` varchar(50) DEFAULT NULL,
  `tipo_sangre` varchar(10) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `ocupacion` varchar(100) DEFAULT NULL,
  `lugar_trabajo` varchar(200) DEFAULT NULL,
  `cuenta_seguro` tinyint(1) DEFAULT NULL,
  `seguro` varchar(100) DEFAULT NULL,
  `nombre_contacto_emergencia` varchar(150) DEFAULT NULL,
  `embarazo` tinyint(1) DEFAULT NULL,
  `meses_embarazo` int(2) DEFAULT NULL,
  `ginecologo` varchar(150) DEFAULT NULL,
  `telefono_ginecologo` varchar(20) DEFAULT NULL,
  `lugar_control` varchar(200) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_paciente` (`id_paciente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: antecedentes_familiares
-- Modelo: AntecedentesFamiliaresModel
DROP TABLE IF EXISTS `antecedentes_familiares`;
CREATE TABLE `antecedentes_familiares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `integrante_padece` text DEFAULT NULL,
  `cual_enfermedad` text DEFAULT NULL,
  `padre_alive` tinyint(1) DEFAULT 1,
  `razon_padre` text DEFAULT NULL,
  `madre_alive` tinyint(1) DEFAULT 1,
  `razon_madre` text DEFAULT NULL,
  `hermano_alive` tinyint(1) DEFAULT 1,
  `razon_hermano` text DEFAULT NULL,
  `hermana_alive` tinyint(1) DEFAULT 1,
  `razon_hermana` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_paciente` (`id_paciente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: antecedentes_patologicos
-- Modelo: AntecedentesPatologicosModel
DROP TABLE IF EXISTS `antecedentes_patologicos`;
CREATE TABLE `antecedentes_patologicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `tratamiento` text DEFAULT NULL,
  `tipo_tratamiento` varchar(100) DEFAULT NULL,
  `sustancia` text DEFAULT NULL,
  `tipo_sustancia` varchar(100) DEFAULT NULL,
  `hospitalizado` tinyint(1) DEFAULT NULL,
  `motivo_hospitalizado` text DEFAULT NULL,
  `alergico` tinyint(1) DEFAULT NULL,
  `sustancia_alergia` text DEFAULT NULL,
  `anestesiado` tinyint(1) DEFAULT NULL,
  `anestesia_reaccion` text DEFAULT NULL,
  `chk_hipertension` tinyint(1) DEFAULT NULL,
  `chk_cardiopatia` tinyint(1) DEFAULT NULL,
  `chk_hepatica` tinyint(1) DEFAULT NULL,
  `chk_pulmonar` tinyint(1) DEFAULT NULL,
  `chk_digestivas` tinyint(1) DEFAULT NULL,
  `chk_diabetes` tinyint(1) DEFAULT NULL,
  `chk_asma` tinyint(1) DEFAULT NULL,
  `chk_transtornos` tinyint(1) DEFAULT NULL,
  `chk_vih` tinyint(1) DEFAULT NULL,
  `chk_epilepcia` tinyint(1) DEFAULT NULL,
  `chk_respiratorias` tinyint(1) DEFAULT NULL,
  `chk_nerviosa` tinyint(1) DEFAULT NULL,
  `txt_otra_enfermedad` text DEFAULT NULL,
  `tiroides` tinyint(1) DEFAULT NULL,
  `reumatica` tinyint(1) DEFAULT NULL,
  `alcoholicas` tinyint(1) DEFAULT NULL,
  `alcoholicas_frecuencia` varchar(50) DEFAULT NULL,
  `cigarrillos` tinyint(1) DEFAULT NULL,
  `cigarrillos_frecuencia` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_paciente` (`id_paciente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: historia_bucodental
-- Modelo: HistorialBucodentalModel (NOTA: el modelo usa 'historia_bucodental', no 'historial_bucodental')
DROP TABLE IF EXISTS `historia_bucodental`;
CREATE TABLE `historia_bucodental` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `primera_vez_dentista` varchar(100) DEFAULT NULL,
  `cuanto_ultima_vez` varchar(100) DEFAULT NULL,
  `tratamiento_realizaron` text DEFAULT NULL,
  `tomaron_radiografia` tinyint(1) DEFAULT NULL,
  `movilidad_dientes` tinyint(1) DEFAULT NULL,
  `sangran_encias` tinyint(1) DEFAULT NULL,
  `frecuencia_sangran_encias` varchar(100) DEFAULT NULL,
  `mal_sabor_boca` tinyint(1) DEFAULT NULL,
  `resequedad_boca` tinyint(1) DEFAULT NULL,
  `tenido_infeccion_dientes` tinyint(1) DEFAULT NULL,
  `cuanto_tiempo_infeccion_dientes` varchar(100) DEFAULT NULL,
  `rechinan_dientes` tinyint(1) DEFAULT NULL,
  `dolor_cabeza` tinyint(1) DEFAULT NULL,
  `frecuencia_dolor_cabeza` varchar(100) DEFAULT NULL,
  `veces_cepilla_al_dia` varchar(50) DEFAULT NULL,
  `cuando_cambia_cepillo` varchar(100) DEFAULT NULL,
  `usa_hilo_dental` tinyint(1) DEFAULT NULL,
  `enjuages_bucales` tinyint(1) DEFAULT NULL,
  `motivo_consulta` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_paciente` (`id_paciente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: notas_evolucion
-- Modelo: NotasEvolucionModel
DROP TABLE IF EXISTS `notas_evolucion`;
CREATE TABLE `notas_evolucion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `tratamiento_realizado` text DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `abono` decimal(10,2) DEFAULT NULL,
  `saldo` decimal(10,2) DEFAULT NULL,
  `firma` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_paciente` (`id_paciente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: odontogramas
-- Modelo: OdontogramaModel
DROP TABLE IF EXISTS `odontogramas`;
CREATE TABLE `odontogramas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `tipo_dentadura` enum('permanente','decidua','mixta') NOT NULL DEFAULT 'permanente',
  `observaciones_generales` text DEFAULT NULL,
  `estado_general` enum('bueno','regular','malo','excelente','critico') NOT NULL DEFAULT 'bueno',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_paciente_odontograma` (`id_paciente`),
  KEY `idx_odontograma_paciente` (`id_paciente`),
  CONSTRAINT `fk_odontograma_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: odontograma_dientes
-- Modelo: OdontogramaDienteModel
DROP TABLE IF EXISTS `odontograma_dientes`;
CREATE TABLE `odontograma_dientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_odontograma` int(11) NOT NULL,
  `numero_diente` int(2) NOT NULL,
  `estado` enum('presente','ausente','extraido','impactado','erupcion','supernumerario','corona','implante','endodoncia','protesis') NOT NULL DEFAULT 'presente',
  `sup_oclusal` varchar(10) DEFAULT 'S001',
  `sup_vestibular` varchar(10) DEFAULT 'S001',
  `sup_lingual` varchar(10) DEFAULT 'S001',
  `sup_mesial` varchar(10) DEFAULT 'S001',
  `sup_distal` varchar(10) DEFAULT 'S001',
  `movilidad` enum('0','1','2','3') DEFAULT '0',
  `sensibilidad` enum('normal','leve','moderada','severa','sin_respuesta') DEFAULT 'normal',
  `diagnosticos` JSON DEFAULT NULL,
  `tratamientos_realizados` JSON DEFAULT NULL,
  `tratamientos_pendientes` JSON DEFAULT NULL,
  `condiciones_especiales` JSON DEFAULT NULL,
  `hallazgos` JSON DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_odontograma_diente` (`id_odontograma`,`numero_diente`),
  KEY `idx_diente_numero` (`numero_diente`),
  KEY `idx_diente_estado` (`estado`),
  CONSTRAINT `fk_diente_odontograma` FOREIGN KEY (`id_odontograma`) REFERENCES `odontogramas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: odontograma_historial
-- Modelo: OdontogramaHistorialModel
DROP TABLE IF EXISTS `odontograma_historial`;
CREATE TABLE `odontograma_historial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_odontograma` int(11) NOT NULL,
  `numero_diente` int(2) DEFAULT NULL,
  `tipo_accion` enum('creacion','modificacion','modificacion_superficie','modificacion_estado','eliminacion','tratamiento','diagnostico') NOT NULL DEFAULT 'modificacion',
  `campo_modificado` varchar(100) DEFAULT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `descripcion_cambio` text DEFAULT NULL,
  `usuario_modificacion` varchar(100) DEFAULT 'Sistema',
  `fecha_modificacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_historial_odontograma` (`id_odontograma`),
  KEY `idx_historial_diente` (`numero_diente`),
  KEY `idx_historial_fecha` (`fecha_modificacion`),
  KEY `idx_historial_accion` (`tipo_accion`),
  CONSTRAINT `fk_historial_odontograma` FOREIGN KEY (`id_odontograma`) REFERENCES `odontogramas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLAS CON DEPENDENCIAS MÚLTIPLES
-- ========================================

-- Tabla: citas
-- Modelo: CitasModel
DROP TABLE IF EXISTS `citas`;
CREATE TABLE `citas` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `id_usuario` int(11) unsigned NOT NULL,
  `id_servicio` int(11) unsigned DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `estado` enum('programada','confirmada','en_progreso','completada','cancelada') NOT NULL DEFAULT 'programada',
  `tipo_cita` enum('consulta','tratamiento','revision','urgencia') NOT NULL DEFAULT 'consulta',
  `color` varchar(7) NOT NULL DEFAULT '#5ccdde',
  `notas` text DEFAULT NULL,
  `recordatorio_enviado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_fecha` (`id_usuario`,`fecha_inicio`),
  KEY `idx_paciente_fecha` (`id_paciente`,`fecha_inicio`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: presupuestos
-- Modelo: PresupuestosModel
DROP TABLE IF EXISTS `presupuestos`;
CREATE TABLE `presupuestos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) unsigned NOT NULL,
  `id_usuario` int(11) unsigned NOT NULL,
  `folio` varchar(20) NOT NULL,
  `fecha_emision` datetime NOT NULL,
  `fecha_vigencia` date NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `iva` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('borrador','pendiente','aprobado','rechazado','convertido') NOT NULL DEFAULT 'borrador',
  `observaciones` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `folio` (`folio`),
  KEY `idx_paciente` (`id_paciente`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: presupuestos_detalles
-- Modelo: PresupuestosDetallesModel
DROP TABLE IF EXISTS `presupuestos_detalles`;
CREATE TABLE `presupuestos_detalles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_presupuesto` int(11) unsigned NOT NULL,
  `id_servicio` int(11) unsigned NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT 1.00,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento_porcentaje` decimal(5,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_presupuesto` (`id_presupuesto`),
  KEY `idx_servicio` (`id_servicio`),
  CONSTRAINT `presupuestos_detalles_id_presupuesto_foreign` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuestos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `presupuestos_detalles_id_servicio_foreign` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: cotizaciones
-- Modelo: CotizacionesModel
DROP TABLE IF EXISTS `cotizaciones`;
CREATE TABLE `cotizaciones` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) unsigned NOT NULL,
  `id_usuario` int(11) unsigned NOT NULL,
  `fecha_emision` datetime DEFAULT NULL,
  `fecha_vigencia` date DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','aceptada','rechazada') NOT NULL DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: cotizaciones_detalles
-- Modelo: CotizacionesDetallesModel
DROP TABLE IF EXISTS `cotizaciones_detalles`;
CREATE TABLE `cotizaciones_detalles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_cotizacion` int(11) unsigned NOT NULL,
  `id_servicio` int(11) unsigned NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: recetas
-- Modelo: RecetasModel
DROP TABLE IF EXISTS `recetas`;
CREATE TABLE `recetas` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) unsigned NOT NULL,
  `id_usuario` int(11) unsigned NOT NULL,
  `folio` varchar(20) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `notas_adicionales` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recetas_id_paciente_foreign` (`id_paciente`),
  KEY `recetas_id_usuario_foreign` (`id_usuario`),
  CONSTRAINT `recetas_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: recetas_detalles
-- Modelo: RecetasDetallesModel
DROP TABLE IF EXISTS `recetas_detalles`;
CREATE TABLE `recetas_detalles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_receta` int(11) unsigned NOT NULL,
  `id_medicamento` int(11) unsigned NOT NULL,
  `dosis` varchar(150) NOT NULL,
  `duracion` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recetas_detalles_id_receta_foreign` (`id_receta`),
  KEY `recetas_detalles_id_medicamento_foreign` (`id_medicamento`),
  CONSTRAINT `recetas_detalles_id_medicamento_foreign` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `recetas_detalles_id_receta_foreign` FOREIGN KEY (`id_receta`) REFERENCES `recetas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: tratamientos_realizados
-- Modelo: TratamientosRealizadosModel
DROP TABLE IF EXISTS `tratamientos_realizados`;
CREATE TABLE `tratamientos_realizados` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `diente` varchar(5) DEFAULT NULL,
  `superficie` enum('vestibular','lingual','oclusal','mesial','distal','incisal','palatino','bucal') DEFAULT NULL,
  `estado` enum('iniciado','en_progreso','completado','suspendido','cancelado') NOT NULL DEFAULT 'iniciado',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  `pagado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tratamiento_paciente` (`id_paciente`),
  KEY `idx_tratamiento_servicio` (`id_servicio`),
  KEY `idx_tratamiento_usuario` (`id_usuario`),
  KEY `idx_tratamiento_estado` (`estado`),
  KEY `idx_tratamiento_fecha` (`fecha_inicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: historial_actividades
-- Modelo: HistorialActividadesModel
DROP TABLE IF EXISTS `historial_actividades`;
CREATE TABLE `historial_actividades` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_paciente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo_actividad` enum('cita','receta','presupuesto','cotizacion','nota_evolucion','tratamiento','pago','odontograma') NOT NULL,
  `id_referencia` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_actividad` datetime NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_paciente_fecha` (`id_paciente`,`fecha_actividad`),
  KEY `idx_usuario` (`id_usuario`),
  KEY `idx_tipo` (`tipo_actividad`),
  KEY `idx_referencia` (`id_referencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: historial_adjuntos
-- Modelo: HistorialAdjuntosModel
DROP TABLE IF EXISTS `historial_adjuntos`;
CREATE TABLE `historial_adjuntos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_historial_actividad` int(11) unsigned NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta_archivo` varchar(500) NOT NULL,
  `tipo_archivo` varchar(100) NOT NULL,
  `tamanio_archivo` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_adjunto_historial` (`id_historial_actividad`),
  KEY `idx_adjunto_tipo` (`tipo_archivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLAS DE INVENTARIO
-- ========================================

-- Tabla: inventario
DROP TABLE IF EXISTS `inventario`;
CREATE TABLE `inventario` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_medicamento` int(11) unsigned NOT NULL,
  `id_proveedor` int(11) unsigned NOT NULL,
  `lote` varchar(50) NOT NULL COMMENT 'Número de lote del fabricante',
  `fecha_fabricacion` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `stock_inicial` int(11) NOT NULL DEFAULT 0,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 5,
  `stock_maximo` int(11) NOT NULL DEFAULT 100,
  `ubicacion` varchar(100) DEFAULT NULL,
  `precio_compra_lote` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_venta_lote` decimal(10,2) NOT NULL DEFAULT 0.00,
  `alerta_vencimiento` tinyint(1) NOT NULL DEFAULT 0,
  `estado` enum('activo','inactivo','agotado','vencido') NOT NULL DEFAULT 'activo',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_medicamento` (`id_medicamento`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `estado` (`estado`),
  KEY `fecha_vencimiento` (`fecha_vencimiento`),
  KEY `alerta_vencimiento` (`alerta_vencimiento`),
  CONSTRAINT `inventario_id_medicamento_foreign` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventario_id_proveedor_foreign` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: movimientos_inventario
DROP TABLE IF EXISTS `movimientos_inventario`;
CREATE TABLE `movimientos_inventario` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_inventario` int(11) unsigned NOT NULL,
  `id_medicamento` int(11) unsigned NOT NULL,
  `tipo_movimiento` enum('entrada','salida','ajuste','merma','devolucion') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `fecha_movimiento` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_inventario` (`id_inventario`),
  KEY `id_medicamento` (`id_medicamento`),
  KEY `fecha_movimiento` (`fecha_movimiento`),
  CONSTRAINT `movimientos_inventario_id_inventario_foreign` FOREIGN KEY (`id_inventario`) REFERENCES `inventario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `movimientos_inventario_id_medicamento_foreign` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: alertas_inventario
DROP TABLE IF EXISTS `alertas_inventario`;
CREATE TABLE `alertas_inventario` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_inventario` int(11) unsigned NOT NULL,
  `id_medicamento` int(11) unsigned NOT NULL,
  `tipo_alerta` enum('stock_bajo','stock_alto','proximo_vencimiento','vencido','custom') NOT NULL,
  `prioridad` enum('baja','media','alta','critica') NOT NULL DEFAULT 'media',
  `mensaje` text DEFAULT NULL,
  `estado` enum('activa','resuelta','ignorada') NOT NULL DEFAULT 'activa',
  `fecha_alerta` datetime DEFAULT NULL,
  `fecha_resuelta` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_inventario` (`id_inventario`),
  KEY `id_medicamento` (`id_medicamento`),
  KEY `tipo_alerta` (`tipo_alerta`),
  KEY `estado` (`estado`),
  KEY `fecha_alerta` (`fecha_alerta`),
  CONSTRAINT `alertas_inventario_id_inventario_foreign` FOREIGN KEY (`id_inventario`) REFERENCES `inventario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `alertas_inventario_id_medicamento_foreign` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- RESTAURAR CONFIGURACIÓN
-- ========================================
SET FOREIGN_KEY_CHECKS = 1;

-- ========================================
-- FIN DEL SCHEMA
-- ========================================

