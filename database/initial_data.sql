-- ============================================================================
-- DentalMX - Datos Iniciales
-- Versión: 2.0.0
-- Fecha: 2024-12-05
-- Descripción: Datos iniciales para la instalación del sistema
-- IMPORTANTE: Ejecutar DESPUÉS de schema.sql
-- ============================================================================

USE engsigne_magic_dental;

-- ============================================================================
-- USUARIO ADMINISTRADOR PRINCIPAL
-- Email: admin@dentalmx.com
-- Password: Admin123! (hash bcrypt)
-- ============================================================================

INSERT INTO usuarios (
    nombre, 
    apellido, 
    email, 
    password, 
    rol, 
    telefono, 
    direccion, 
    foto_perfil, 
    activo, 
    created_at, 
    updated_at
) VALUES (
    'Administrador',
    'Sistema',
    'admin@dentalmx.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: Admin123!
    'administrador',
    '5551234567',
    'Consultorio Dental Principal',
    NULL,
    1,
    NOW(),
    NOW()
);

-- ============================================================================
-- DOCTOR DE EJEMPLO
-- ============================================================================

INSERT INTO usuarios (
    nombre, 
    apellido, 
    email, 
    password, 
    rol, 
    telefono, 
    direccion, 
    foto_perfil, 
    activo, 
    created_at, 
    updated_at
) VALUES (
    'Dr. Juan',
    'Pérez García',
    'doctor@dentalmx.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: Admin123!
    'doctor',
    '5559876543',
    'Consultorio 1',
    NULL,
    1,
    NOW(),
    NOW()
);

-- ============================================================================
-- SERVICIOS DENTALES BÁSICOS
-- ============================================================================

INSERT INTO servicios (nombre, descripcion, precio_base, created_at, updated_at) VALUES
('Consulta General', 'Revisión dental completa con diagnóstico', 500.00, NOW(), NOW()),
('Limpieza Dental', 'Limpieza dental profesional con ultrasonido', 800.00, NOW(), NOW()),
('Extracción Simple', 'Extracción de pieza dental sin complicaciones', 1000.00, NOW(), NOW()),
('Extracción Quirúrgica', 'Extracción de pieza dental con procedimiento quirúrgico', 2500.00, NOW(), NOW()),
('Resina Dental', 'Restauración con resina compuesta', 1200.00, NOW(), NOW()),
('Amalgama', 'Restauración con amalgama de plata', 800.00, NOW(), NOW()),
('Endodoncia Unirradicular', 'Tratamiento de conductos en diente de una raíz', 3500.00, NOW(), NOW()),
('Endodoncia Birradicular', 'Tratamiento de conductos en diente de dos raíces', 4500.00, NOW(), NOW()),
('Endodoncia Multirradicular', 'Tratamiento de conductos en diente de múltiples raíces', 5500.00, NOW(), NOW()),
('Corona de Porcelana', 'Corona dental de porcelana sobre metal', 6000.00, NOW(), NOW()),
('Corona de Zirconia', 'Corona dental de zirconia monolítica', 8000.00, NOW(), NOW()),
('Puente Dental (por pieza)', 'Puente fijo dental por unidad', 5000.00, NOW(), NOW()),
('Implante Dental', 'Colocación de implante de titanio', 15000.00, NOW(), NOW()),
('Prótesis Total', 'Prótesis dental completa removible', 8000.00, NOW(), NOW()),
('Prótesis Parcial', 'Prótesis dental parcial removible', 5000.00, NOW(), NOW()),
('Blanqueamiento Dental', 'Blanqueamiento dental profesional en consultorio', 3000.00, NOW(), NOW()),
('Ortodoncia (mensualidad)', 'Control mensual de tratamiento ortodóntico', 800.00, NOW(), NOW()),
('Brackets Metálicos', 'Colocación de brackets metálicos convencionales', 18000.00, NOW(), NOW()),
('Brackets Estéticos', 'Colocación de brackets de cerámica o zafiro', 25000.00, NOW(), NOW()),
('Férula de Descarga', 'Guarda oclusal para bruxismo', 2500.00, NOW(), NOW()),
('Radiografía Periapical', 'Radiografía dental simple', 150.00, NOW(), NOW()),
('Radiografía Panorámica', 'Radiografía panorámica digital', 500.00, NOW(), NOW()),
('Aplicación de Flúor', 'Aplicación tópica de flúor', 300.00, NOW(), NOW()),
('Sellador de Fosetas', 'Sellador dental preventivo por diente', 400.00, NOW(), NOW()),
('Cirugía de Encías', 'Procedimiento periodontal quirúrgico', 3500.00, NOW(), NOW());

-- ============================================================================
-- MEDICAMENTOS BÁSICOS
-- ============================================================================

INSERT INTO medicamentos (nombre_comercial, sustancia_activa, presentacion, indicaciones_base, stock, created_at, updated_at) VALUES
('Ibuprofeno 400mg', 'Ibuprofeno', 'Caja con 20 tabletas', 'Tomar 1 tableta cada 8 horas después de los alimentos. No exceder 3 tabletas al día.', 100, NOW(), NOW()),
('Ibuprofeno 600mg', 'Ibuprofeno', 'Caja con 20 tabletas', 'Tomar 1 tableta cada 8 horas después de los alimentos. No exceder 3 tabletas al día.', 80, NOW(), NOW()),
('Paracetamol 500mg', 'Paracetamol', 'Caja con 20 tabletas', 'Tomar 1-2 tabletas cada 6-8 horas. No exceder 4g al día.', 150, NOW(), NOW()),
('Ketorolaco 10mg', 'Ketorolaco trometamina', 'Caja con 10 tabletas', 'Tomar 1 tableta cada 8 horas por máximo 5 días.', 60, NOW(), NOW()),
('Ketorolaco Sublingual 30mg', 'Ketorolaco trometamina', 'Caja con 4 tabletas sublinguales', 'Colocar 1 tableta debajo de la lengua cada 8 horas por máximo 2 días.', 40, NOW(), NOW()),
('Nimesulida 100mg', 'Nimesulida', 'Caja con 14 tabletas', 'Tomar 1 tableta cada 12 horas después de los alimentos.', 50, NOW(), NOW()),
('Naproxeno 550mg', 'Naproxeno sódico', 'Caja con 12 tabletas', 'Tomar 1 tableta cada 12 horas con alimentos.', 70, NOW(), NOW()),
('Amoxicilina 500mg', 'Amoxicilina', 'Caja con 21 cápsulas', 'Tomar 1 cápsula cada 8 horas por 7 días. Completar el tratamiento.', 100, NOW(), NOW()),
('Amoxicilina 875mg / Ácido Clavulánico 125mg', 'Amoxicilina con ácido clavulánico', 'Caja con 14 tabletas', 'Tomar 1 tableta cada 12 horas por 7 días.', 50, NOW(), NOW()),
('Clindamicina 300mg', 'Clindamicina', 'Caja con 16 cápsulas', 'Tomar 1 cápsula cada 8 horas por 7 días.', 40, NOW(), NOW()),
('Azitromicina 500mg', 'Azitromicina', 'Caja con 3 tabletas', 'Tomar 1 tableta cada 24 horas por 3 días.', 60, NOW(), NOW()),
('Metronidazol 500mg', 'Metronidazol', 'Caja con 30 tabletas', 'Tomar 1 tableta cada 8 horas por 7 días. No consumir alcohol.', 80, NOW(), NOW()),
('Dexametasona 4mg', 'Dexametasona', 'Caja con 10 tabletas', 'Tomar según indicación médica.', 30, NOW(), NOW()),
('Betametasona 0.5mg', 'Betametasona', 'Caja con 20 tabletas', 'Tomar según indicación médica.', 25, NOW(), NOW()),
('Omeprazol 20mg', 'Omeprazol', 'Caja con 14 cápsulas', 'Tomar 1 cápsula en ayunas.', 100, NOW(), NOW()),
('Clorhexidina 0.12%', 'Gluconato de clorhexidina', 'Frasco 250ml', 'Realizar enjuagues de 30 segundos después del cepillado, 2 veces al día.', 50, NOW(), NOW()),
('Lidocaína + Epinefrina 2%', 'Lidocaína con epinefrina', 'Caja con 50 cartuchos', 'Uso exclusivo profesional para anestesia local.', 200, NOW(), NOW()),
('Articaína 4%', 'Articaína clorhidrato', 'Caja con 50 cartuchos', 'Uso exclusivo profesional para anestesia local.', 150, NOW(), NOW()),
('Mepivacaína 3%', 'Mepivacaína', 'Caja con 50 cartuchos', 'Uso exclusivo profesional para anestesia local sin vasoconstrictor.', 100, NOW(), NOW()),
('Tramadol 50mg', 'Tramadol clorhidrato', 'Caja con 10 cápsulas', 'Tomar 1 cápsula cada 8 horas en caso de dolor severo. Medicamento controlado.', 20, NOW(), NOW());

-- ============================================================================
-- CONFIGURACIÓN INICIAL DE LA CLÍNICA
-- ============================================================================

INSERT INTO configuracion_clinica (
    nombre_clinica,
    logo,
    telefono,
    email,
    direccion,
    horario_atencion,
    vigencia_presupuestos,
    mensaje_bienvenida,
    mail_host,
    mail_port,
    mail_username,
    mail_password,
    mail_encryption,
    mail_from_email,
    mail_from_name,
    created_at,
    updated_at
) VALUES (
    'DentalMX Clínica',
    NULL,
    '555-123-4567',
    'contacto@dentalmx.com',
    'Av. Principal #123, Col. Centro, Ciudad de México, CP 06600',
    'Lunes a Viernes: 9:00 AM - 7:00 PM, Sábado: 9:00 AM - 2:00 PM',
    30,
    'Bienvenido a DentalMX, su salud dental es nuestra prioridad.',
    'smtp.gmail.com',
    587,
    '',
    '',
    'tls',
    'contacto@dentalmx.com',
    'DentalMX Clínica',
    NOW(),
    NOW()
);

-- ============================================================================
-- PREFERENCIAS DE USUARIO PARA ADMIN
-- ============================================================================

INSERT INTO preferencias_usuario (
    id_usuario,
    tema,
    idioma,
    notificaciones_email,
    notificaciones_sistema,
    formato_fecha,
    created_at,
    updated_at
) VALUES (
    1,
    'light',
    'es',
    1,
    1,
    'd/m/Y',
    NOW(),
    NOW()
);

-- ============================================================================
-- HORARIOS DEL DOCTOR (Doctor ID = 2)
-- ============================================================================

INSERT INTO doctor_schedules (usuario_id, dia_semana, hora_inicio, hora_fin, activo, created_at, updated_at) VALUES
(2, 'lunes', '09:00:00', '14:00:00', 1, NOW(), NOW()),
(2, 'lunes', '16:00:00', '19:00:00', 1, NOW(), NOW()),
(2, 'martes', '09:00:00', '14:00:00', 1, NOW(), NOW()),
(2, 'martes', '16:00:00', '19:00:00', 1, NOW(), NOW()),
(2, 'miercoles', '09:00:00', '14:00:00', 1, NOW(), NOW()),
(2, 'miercoles', '16:00:00', '19:00:00', 1, NOW(), NOW()),
(2, 'jueves', '09:00:00', '14:00:00', 1, NOW(), NOW()),
(2, 'jueves', '16:00:00', '19:00:00', 1, NOW(), NOW()),
(2, 'viernes', '09:00:00', '14:00:00', 1, NOW(), NOW()),
(2, 'viernes', '16:00:00', '19:00:00', 1, NOW(), NOW()),
(2, 'sabado', '09:00:00', '14:00:00', 1, NOW(), NOW());

-- ============================================================================
-- PREFERENCIAS DEL DOCTOR
-- ============================================================================

INSERT INTO doctor_preferences (
    usuario_id,
    duracion_cita,
    tiempo_descanso,
    citas_simultaneas,
    created_at,
    updated_at
) VALUES (
    2,
    30,
    10,
    1,
    NOW(),
    NOW()
);

-- ============================================================================
-- PACIENTE DE EJEMPLO
-- ============================================================================

INSERT INTO pacientes (
    nombre,
    primer_apellido,
    segundo_apellido,
    fecha_nacimiento,
    nacionalidad,
    domicilio,
    telefono,
    celular,
    email,
    created_at,
    updated_at
) VALUES (
    'María',
    'González',
    'López',
    '1985-06-15',
    'Mexicana',
    'Calle Reforma #456, Col. Centro, CDMX',
    '5551112222',
    '5559998888',
    'maria.gonzalez@email.com',
    NOW(),
    NOW()
);

-- ============================================================================
-- DATOS GENERALES DEL PACIENTE DE EJEMPLO
-- ============================================================================

INSERT INTO datos_generales (
    id_paciente,
    edad,
    sexo,
    peso,
    tipo_sangre,
    fecha_nacimiento,
    estado_civil,
    ocupacion,
    lugar_trabajo,
    cuenta_seguro,
    seguro,
    nombre_contacto_emergencia,
    embarazo,
    meses_embarazo,
    ginecologo,
    telefono_ginecologo,
    lugar_control,
    created_at,
    updated_at
) VALUES (
    1,
    39,
    'Femenino',
    65.00,
    'O+',
    '1985-06-15',
    'Casada',
    'Contadora',
    'Despacho Contable ABC',
    1,
    'GNP Seguros',
    'Juan González - 5557778888',
    0,
    NULL,
    NULL,
    NULL,
    NULL,
    NOW(),
    NOW()
);

-- ============================================================================
-- FIN DE DATOS INICIALES
-- ============================================================================

-- Verificación de datos insertados
SELECT 'Usuarios creados:' AS info, COUNT(*) AS total FROM usuarios;
SELECT 'Servicios creados:' AS info, COUNT(*) AS total FROM servicios;
SELECT 'Medicamentos creados:' AS info, COUNT(*) AS total FROM medicamentos;
SELECT 'Configuración creada:' AS info, COUNT(*) AS total FROM configuracion_clinica;
