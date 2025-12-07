-- ========================================
-- DATOS INICIALES - DENTALMX
-- ========================================
-- Este archivo contiene los datos mínimos necesarios para iniciar el sistema
-- Ejecutar después de importar schema.sql

-- ========================================
-- USUARIO ADMINISTRADOR
-- ========================================
-- Usuario: admin
-- Contraseña: admin123 (CAMBIAR después del primer login)

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `username`, `password`, `rol`, `activo`, `fecha_creacion`) 
VALUES 
(1, 'Administrador', 'Sistema', 'admin@dentalmx.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NOW());

-- ========================================
-- CATÁLOGO DE SERVICIOS BÁSICOS
-- ========================================

INSERT INTO `servicios` (`nombre`, `descripcion`, `precio`, `duracion_minutos`, `activo`) VALUES
('Consulta General', 'Revisión dental general y diagnóstico', 300.00, 30, 1),
('Limpieza Dental', 'Profilaxis y limpieza profunda', 500.00, 45, 1),
('Extracción Simple', 'Extracción de pieza dental simple', 800.00, 30, 1),
('Extracción Compleja', 'Extracción quirúrgica de pieza dental', 1500.00, 60, 1),
('Resina (Amalgama)', 'Restauración con resina compuesta', 600.00, 45, 1),
('Endodoncia', 'Tratamiento de conductos', 2500.00, 90, 1),
('Corona Porcelana', 'Corona dental de porcelana', 4000.00, 60, 1),
('Blanqueamiento', 'Blanqueamiento dental completo', 2000.00, 60, 1),
('Ortodoncia (Mensual)', 'Pago mensual de ortodoncia', 1200.00, 30, 1),
('Implante Dental', 'Colocación de implante dental', 8000.00, 120, 1);

-- ========================================
-- ESTADOS Y CONFIGURACIÓN
-- ========================================

-- Los estados de citas, presupuestos y otros elementos
-- se manejan desde la aplicación mediante constantes

-- ========================================
-- NOTAS IMPORTANTES
-- ========================================
-- 1. CAMBIAR la contraseña del usuario admin después del primer login
-- 2. Los precios son ejemplos, ajustar según su clínica
-- 3. Duración de servicios en minutos, ajustar según necesidades
-- 4. Para agregar más servicios, usar la interfaz web después de instalar

-- ========================================
-- VERIFICACIÓN
-- ========================================
-- Verificar que los datos se importaron correctamente:
-- SELECT * FROM usuarios WHERE username = 'admin';
-- SELECT COUNT(*) FROM servicios;
