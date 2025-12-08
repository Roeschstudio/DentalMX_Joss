-- ============================================================================
-- DENTALMX - DATOS INICIALES
-- ============================================================================
-- Este archivo contiene los datos mínimos necesarios para iniciar el sistema
-- Ejecutar DESPUÉS de importar schema.sql
-- NOTA: La base de datos debe ser seleccionada previamente por el instalador
-- ============================================================================

-- ============================================================================
-- USUARIO ADMINISTRADOR
-- ============================================================================
-- Email: admin@dentalmx.com
-- Contraseña: admin123 (CAMBIAR después del primer login)
-- Hash generado con password_hash('admin123', PASSWORD_DEFAULT)

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `rol`, `activo`, `created_at`) 
VALUES 
(1, 'Administrador', 'Sistema', 'admin@dentalmx.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NOW());

-- ============================================================================
-- CONFIGURACIÓN INICIAL DE LA CLÍNICA
-- ============================================================================

INSERT INTO `configuracion_clinica` (`id`, `nombre_clinica`, `telefono`, `email`, `horario_atencion`, `vigencia_presupuestos`, `mensaje_bienvenida`, `created_at`) 
VALUES 
(1, 'Clínica Dental', '555-123-4567', 'contacto@clinicadental.com', 'Lunes a Viernes 9:00 - 18:00, Sábados 9:00 - 14:00', 30, 'Bienvenido a nuestra clínica dental. Su salud bucal es nuestra prioridad.', NOW());

-- ============================================================================
-- CATÁLOGO DE SERVICIOS BÁSICOS
-- ============================================================================

INSERT INTO `servicios` (`nombre`, `descripcion`, `precio_base`, `created_at`) VALUES
('Consulta General', 'Revisión dental general y diagnóstico', 300.00, NOW()),
('Limpieza Dental', 'Profilaxis y limpieza profunda', 500.00, NOW()),
('Extracción Simple', 'Extracción de pieza dental simple', 800.00, NOW()),
('Extracción Compleja', 'Extracción quirúrgica de pieza dental', 1500.00, NOW()),
('Resina Compuesta', 'Restauración con resina compuesta', 600.00, NOW()),
('Amalgama', 'Restauración con amalgama dental', 450.00, NOW()),
('Endodoncia Unirradicular', 'Tratamiento de conductos - 1 raíz', 2000.00, NOW()),
('Endodoncia Birradicular', 'Tratamiento de conductos - 2 raíces', 2500.00, NOW()),
('Endodoncia Multirradicular', 'Tratamiento de conductos - 3+ raíces', 3000.00, NOW()),
('Corona Porcelana', 'Corona dental de porcelana', 4000.00, NOW()),
('Corona Metal-Porcelana', 'Corona dental metal-porcelana', 3500.00, NOW()),
('Blanqueamiento Dental', 'Blanqueamiento dental completo', 2000.00, NOW()),
('Ortodoncia (Mensual)', 'Pago mensual de ortodoncia', 1200.00, NOW()),
('Implante Dental', 'Colocación de implante dental', 8000.00, NOW()),
('Prótesis Removible', 'Prótesis dental removible', 5000.00, NOW()),
('Prótesis Fija', 'Prótesis dental fija', 8000.00, NOW()),
('Radiografía Periapical', 'Radiografía dental periapical', 150.00, NOW()),
('Radiografía Panorámica', 'Radiografía panorámica', 400.00, NOW()),
('Aplicación de Flúor', 'Aplicación de flúor tópico', 200.00, NOW()),
('Sellador de Fosetas', 'Sellador de fosetas y fisuras', 300.00, NOW());

-- ============================================================================
-- CATÁLOGO DE MEDICAMENTOS BÁSICOS
-- ============================================================================

INSERT INTO `medicamentos` (`nombre_comercial`, `sustancia_activa`, `presentacion`, `indicaciones_base`, `stock`, `created_at`) VALUES
('Ibuprofeno 400mg', 'Ibuprofeno', 'Tabletas', 'Tomar 1 tableta cada 8 horas después de los alimentos', 100, NOW()),
('Ibuprofeno 600mg', 'Ibuprofeno', 'Tabletas', 'Tomar 1 tableta cada 8 horas después de los alimentos', 100, NOW()),
('Paracetamol 500mg', 'Paracetamol', 'Tabletas', 'Tomar 1-2 tabletas cada 6-8 horas', 100, NOW()),
('Amoxicilina 500mg', 'Amoxicilina', 'Cápsulas', 'Tomar 1 cápsula cada 8 horas durante 7 días', 50, NOW()),
('Amoxicilina 875mg + Ác. Clavulánico', 'Amoxicilina/Ácido Clavulánico', 'Tabletas', 'Tomar 1 tableta cada 12 horas durante 7 días', 50, NOW()),
('Clindamicina 300mg', 'Clindamicina', 'Cápsulas', 'Tomar 1 cápsula cada 8 horas durante 7 días', 50, NOW()),
('Ketorolaco 10mg', 'Ketorolaco', 'Tabletas', 'Tomar 1 tableta cada 8 horas por máximo 5 días', 50, NOW()),
('Nimesulida 100mg', 'Nimesulida', 'Tabletas', 'Tomar 1 tableta cada 12 horas después de los alimentos', 50, NOW()),
('Naproxeno 550mg', 'Naproxeno', 'Tabletas', 'Tomar 1 tableta cada 12 horas', 50, NOW()),
('Metronidazol 500mg', 'Metronidazol', 'Tabletas', 'Tomar 1 tableta cada 8 horas durante 7 días', 50, NOW()),
('Clorhexidina 0.12%', 'Clorhexidina', 'Enjuague bucal 250ml', 'Realizar enjuagues 2 veces al día por 30 segundos', 30, NOW()),
('Lidocaína 2% c/Epinefrina', 'Lidocaína/Epinefrina', 'Cartuchos 1.8ml', 'Uso exclusivo del profesional dental', 100, NOW()),
('Articaína 4% c/Epinefrina', 'Articaína/Epinefrina', 'Cartuchos 1.7ml', 'Uso exclusivo del profesional dental', 100, NOW()),
('Dexametasona 8mg', 'Dexametasona', 'Ampolletas', 'Aplicar según indicación médica', 20, NOW()),
('Tramadol 50mg', 'Tramadol', 'Cápsulas', 'Tomar 1 cápsula cada 8 horas en caso de dolor intenso', 30, NOW());

-- ============================================================================
-- CATÁLOGOS ODONTOLÓGICOS PARA ODONTOGRAMA
-- ============================================================================

-- Estados de superficie dental
INSERT INTO `catalogos_odontologicos` (`codigo`, `tipo`, `nombre`, `descripcion`, `color_hex`, `activo`, `orden`) VALUES
('S001', 'superficie_estado', 'Sano', 'Superficie sin patología', '#FFFFFF', 1, 1),
('S002', 'superficie_estado', 'Caries', 'Presencia de caries dental', '#FF0000', 1, 2),
('S003', 'superficie_estado', 'Obturación Resina', 'Restauración con resina compuesta', '#0000FF', 1, 3),
('S004', 'superficie_estado', 'Obturación Amalgama', 'Restauración con amalgama', '#808080', 1, 4),
('S005', 'superficie_estado', 'Fractura', 'Superficie fracturada', '#FFA500', 1, 5),
('S006', 'superficie_estado', 'Abrasión', 'Desgaste por abrasión', '#FFD700', 1, 6),
('S007', 'superficie_estado', 'Erosión', 'Desgaste por erosión', '#DAA520', 1, 7);

-- Diagnósticos odontológicos
INSERT INTO `catalogos_odontologicos` (`codigo`, `tipo`, `nombre`, `descripcion`, `color_hex`, `activo`, `orden`) VALUES
('D001', 'diagnostico', 'Caries Incipiente', 'Caries en etapa inicial (mancha blanca)', '#FFFF00', 1, 1),
('D002', 'diagnostico', 'Caries de Esmalte', 'Caries limitada al esmalte', '#FFD700', 1, 2),
('D003', 'diagnostico', 'Caries de Dentina', 'Caries que afecta la dentina', '#FFA500', 1, 3),
('D004', 'diagnostico', 'Caries Profunda', 'Caries cercana a pulpa dental', '#FF4500', 1, 4),
('D005', 'diagnostico', 'Pulpitis Reversible', 'Inflamación pulpar reversible', '#FF6347', 1, 5),
('D006', 'diagnostico', 'Pulpitis Irreversible', 'Inflamación pulpar irreversible', '#FF0000', 1, 6),
('D007', 'diagnostico', 'Necrosis Pulpar', 'Muerte del tejido pulpar', '#333333', 1, 7),
('D008', 'diagnostico', 'Absceso Periapical', 'Infección periapical aguda', '#8B0000', 1, 8),
('D009', 'diagnostico', 'Periodontitis Leve', 'Enfermedad periodontal leve', '#9370DB', 1, 9),
('D010', 'diagnostico', 'Periodontitis Moderada', 'Enfermedad periodontal moderada', '#8A2BE2', 1, 10),
('D011', 'diagnostico', 'Periodontitis Severa', 'Enfermedad periodontal severa', '#800080', 1, 11),
('D012', 'diagnostico', 'Gingivitis', 'Inflamación de encías', '#FF69B4', 1, 12),
('D013', 'diagnostico', 'Fractura Dental', 'Fractura de pieza dental', '#CD853F', 1, 13),
('D014', 'diagnostico', 'Movilidad Dental', 'Movilidad anormal del diente', '#D2691E', 1, 14);

-- Tratamientos odontológicos
INSERT INTO `catalogos_odontologicos` (`codigo`, `tipo`, `nombre`, `descripcion`, `color_hex`, `activo`, `orden`) VALUES
('T001', 'tratamiento', 'Resina Compuesta', 'Restauración con resina', '#00BFFF', 1, 1),
('T002', 'tratamiento', 'Amalgama', 'Restauración con amalgama', '#A9A9A9', 1, 2),
('T003', 'tratamiento', 'Ionómero de Vidrio', 'Restauración con ionómero', '#87CEEB', 1, 3),
('T004', 'tratamiento', 'Endodoncia', 'Tratamiento de conductos', '#FF1493', 1, 4),
('T005', 'tratamiento', 'Extracción', 'Extracción dental', '#DC143C', 1, 5),
('T006', 'tratamiento', 'Corona', 'Colocación de corona', '#FFD700', 1, 6),
('T007', 'tratamiento', 'Puente Fijo', 'Prótesis fija tipo puente', '#DAA520', 1, 7),
('T008', 'tratamiento', 'Implante', 'Colocación de implante', '#4169E1', 1, 8),
('T009', 'tratamiento', 'Profilaxis', 'Limpieza dental profesional', '#32CD32', 1, 9),
('T010', 'tratamiento', 'Sellador', 'Sellador de fosetas y fisuras', '#00FA9A', 1, 10),
('T011', 'tratamiento', 'Aplicación Flúor', 'Aplicación tópica de flúor', '#7CFC00', 1, 11),
('T012', 'tratamiento', 'Raspado y Alisado', 'Raspado y alisado radicular', '#9932CC', 1, 12),
('T013', 'tratamiento', 'Cirugía Periodontal', 'Procedimiento quirúrgico periodontal', '#8B008B', 1, 13),
('T014', 'tratamiento', 'Blanqueamiento', 'Blanqueamiento dental', '#FFFACD', 1, 14),
('T015', 'tratamiento', 'Carilla', 'Carilla dental estética', '#FFEFD5', 1, 15);

-- Condiciones especiales
INSERT INTO `catalogos_odontologicos` (`codigo`, `tipo`, `nombre`, `descripcion`, `color_hex`, `activo`, `orden`) VALUES
('C001', 'condicion', 'Ausente', 'Diente ausente', '#808080', 1, 1),
('C002', 'condicion', 'Implante Presente', 'Implante dental colocado', '#4169E1', 1, 2),
('C003', 'condicion', 'Corona Presente', 'Corona dental existente', '#FFD700', 1, 3),
('C004', 'condicion', 'Puente (Pilar)', 'Diente pilar de puente', '#DAA520', 1, 4),
('C005', 'condicion', 'Puente (Póntico)', 'Póntico de puente fijo', '#B8860B', 1, 5),
('C006', 'condicion', 'Prótesis Removible', 'Forma parte de prótesis removible', '#9370DB', 1, 6),
('C007', 'condicion', 'Ortodoncia', 'Con aparato ortodóntico', '#00CED1', 1, 7),
('C008', 'condicion', 'Retenedor', 'Con retenedor ortodóntico', '#20B2AA', 1, 8),
('C009', 'condicion', 'Diente Temporal', 'Diente de leche', '#FFC0CB', 1, 9),
('C010', 'condicion', 'En Erupción', 'Diente en proceso de erupción', '#98FB98', 1, 10),
('C011', 'condicion', 'Impactado', 'Diente impactado', '#CD853F', 1, 11),
('C012', 'condicion', 'Supernumerario', 'Diente supernumerario', '#DDA0DD', 1, 12);

-- Hallazgos clínicos
INSERT INTO `catalogos_odontologicos` (`codigo`, `tipo`, `nombre`, `descripcion`, `color_hex`, `activo`, `orden`) VALUES
('H001', 'hallazgo', 'Placa Bacteriana', 'Presencia de placa dental', '#F0E68C', 1, 1),
('H002', 'hallazgo', 'Sarro/Cálculo', 'Presencia de sarro dental', '#BDB76B', 1, 2),
('H003', 'hallazgo', 'Sangrado al Sondeo', 'Sangrado gingival al sondeo', '#FF6B6B', 1, 3),
('H004', 'hallazgo', 'Retracción Gingival', 'Recesión de encías', '#FFB6C1', 1, 4),
('H005', 'hallazgo', 'Bolsa Periodontal', 'Presencia de bolsa periodontal', '#DDA0DD', 1, 5),
('H006', 'hallazgo', 'Sensibilidad', 'Hipersensibilidad dental', '#87CEFA', 1, 6),
('H007', 'hallazgo', 'Bruxismo', 'Signos de bruxismo', '#F4A460', 1, 7),
('H008', 'hallazgo', 'Pigmentación', 'Manchas o pigmentación', '#D2B48C', 1, 8),
('H009', 'hallazgo', 'Fisura', 'Línea de fisura en esmalte', '#C0C0C0', 1, 9),
('H010', 'hallazgo', 'Lesión Periapical', 'Lesión visible en radiografía', '#A52A2A', 1, 10);

-- ============================================================================
-- PREFERENCIAS DEL USUARIO ADMIN
-- ============================================================================

INSERT INTO `preferencias_usuario` (`id_usuario`, `tema`, `idioma`, `notificaciones_email`, `notificaciones_sistema`, `formato_fecha`, `created_at`)
VALUES (1, 'light', 'es', 1, 1, 'd/m/Y', NOW());

-- ============================================================================
-- HORARIO DE TRABAJO DEL ADMIN (Ejemplo)
-- ============================================================================

INSERT INTO `doctor_schedules` (`id_usuario`, `dia_semana`, `hora_inicio`, `hora_fin`, `activo`, `created_at`) VALUES
(1, 1, '09:00:00', '18:00:00', 1, NOW()),  -- Lunes
(1, 2, '09:00:00', '18:00:00', 1, NOW()),  -- Martes
(1, 3, '09:00:00', '18:00:00', 1, NOW()),  -- Miércoles
(1, 4, '09:00:00', '18:00:00', 1, NOW()),  -- Jueves
(1, 5, '09:00:00', '18:00:00', 1, NOW()),  -- Viernes
(1, 6, '09:00:00', '14:00:00', 1, NOW());  -- Sábado

-- ============================================================================
-- PREFERENCIAS DE CITAS DEL ADMIN
-- ============================================================================

INSERT INTO `doctor_preferences` (`id_usuario`, `duracion_cita_default`, `intervalo_citas`, `max_citas_dia`, `created_at`)
VALUES (1, 30, 15, 20, NOW());

-- ============================================================================
-- NOTAS IMPORTANTES
-- ============================================================================
-- 
-- CREDENCIALES DE ACCESO:
-- Email: admin@dentalmx.com
-- Contraseña: admin123
--
-- ¡IMPORTANTE! Cambie la contraseña inmediatamente después del primer login.
--
-- Los precios de servicios son ejemplos y deben ajustarse según su clínica.
-- Los medicamentos son ejemplos comunes en odontología.
-- El catálogo odontológico puede ampliarse según necesidades.
--
-- ============================================================================

-- Verificación de datos
SELECT 'Datos iniciales de DentalMX cargados exitosamente' AS mensaje;
SELECT CONCAT('Usuarios creados: ', COUNT(*)) AS info FROM usuarios;
SELECT CONCAT('Servicios creados: ', COUNT(*)) AS info FROM servicios;
SELECT CONCAT('Medicamentos creados: ', COUNT(*)) AS info FROM medicamentos;
SELECT CONCAT('Catálogos odontológicos: ', COUNT(*)) AS info FROM catalogos_odontologicos;
