<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('content') ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">❓ Centro de Ayuda</h1>
        <p class="ds-page__subtitle">Documentación y preguntas frecuentes</p>
    </div>

    <!-- Búsqueda -->
    <div class="ds-card ds-mb-4">
        <div class="ds-card__body">
            <div class="ds-form-group ds-mb-0">
                <div class="ds-flex ds-gap-3">
                    <input type="text" class="ds-input ds-flex-1" id="searchHelp" 
                           placeholder="🔍 Buscar en la ayuda...">
                    <button class="ds-btn ds-btn--primary">Buscar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Categorías de Ayuda -->
    <div class="ds-grid ds-grid--3">
        <div class="ds-card ds-card--hoverable">
            <div class="ds-card__body ds-text-center">
                <div class="ds-empty-state__icon ds-text-3xl ds-mb-4">👥</div>
                <h3 class="ds-card__title">Pacientes</h3>
                <p class="ds-text-muted">Cómo registrar y gestionar pacientes</p>
            </div>
        </div>
        
        <div class="ds-card ds-card--hoverable">
            <div class="ds-card__body ds-text-center">
                <div class="ds-empty-state__icon ds-text-3xl ds-mb-4">📅</div>
                <h3 class="ds-card__title">Citas</h3>
                <p class="ds-text-muted">Programación y gestión de citas</p>
            </div>
        </div>
        
        <div class="ds-card ds-card--hoverable">
            <div class="ds-card__body ds-text-center">
                <div class="ds-empty-state__icon ds-text-3xl ds-mb-4">📋</div>
                <h3 class="ds-card__title">Recetas</h3>
                <p class="ds-text-muted">Crear e imprimir recetas médicas</p>
            </div>
        </div>
        
        <div class="ds-card ds-card--hoverable">
            <div class="ds-card__body ds-text-center">
                <div class="ds-empty-state__icon ds-text-3xl ds-mb-4">💵</div>
                <h3 class="ds-card__title">Presupuestos</h3>
                <p class="ds-text-muted">Generar cotizaciones para tratamientos</p>
            </div>
        </div>
        
        <div class="ds-card ds-card--hoverable">
            <div class="ds-card__body ds-text-center">
                <div class="ds-empty-state__icon ds-text-3xl ds-mb-4">💊</div>
                <h3 class="ds-card__title">Medicamentos</h3>
                <p class="ds-text-muted">Catálogo y gestión de medicamentos</p>
            </div>
        </div>
        
        <div class="ds-card ds-card--hoverable">
            <div class="ds-card__body ds-text-center">
                <div class="ds-empty-state__icon ds-text-3xl ds-mb-4">⚙️</div>
                <h3 class="ds-card__title">Configuración</h3>
                <p class="ds-text-muted">Ajustes del sistema</p>
            </div>
        </div>
    </div>

    <!-- Preguntas Frecuentes -->
    <div class="ds-card ds-mt-4">
        <div class="ds-card__header">
            <h2 class="ds-card__title">📚 Preguntas Frecuentes</h2>
        </div>
        <div class="ds-card__body">
            <div class="ds-accordion">
                <details class="ds-accordion__item">
                    <summary class="ds-accordion__header">
                        <span>¿Cómo registro un nuevo paciente?</span>
                        <span class="ds-accordion__icon">▼</span>
                    </summary>
                    <div class="ds-accordion__content">
                        <p>Para registrar un nuevo paciente:</p>
                        <ol>
                            <li>Ve a la sección <strong>Pacientes</strong> en el menú lateral</li>
                            <li>Haz clic en el botón <strong>Nuevo Paciente</strong></li>
                            <li>Completa el formulario con los datos del paciente</li>
                            <li>Haz clic en <strong>Guardar</strong></li>
                        </ol>
                    </div>
                </details>
                
                <details class="ds-accordion__item">
                    <summary class="ds-accordion__header">
                        <span>¿Cómo programo una cita?</span>
                        <span class="ds-accordion__icon">▼</span>
                    </summary>
                    <div class="ds-accordion__content">
                        <p>Para programar una nueva cita:</p>
                        <ol>
                            <li>Ve a <strong>Citas</strong> en el menú lateral</li>
                            <li>Selecciona <strong>Nueva Cita</strong></li>
                            <li>Elige el paciente, fecha y hora</li>
                            <li>Guarda la cita</li>
                        </ol>
                    </div>
                </details>
                
                <details class="ds-accordion__item">
                    <summary class="ds-accordion__header">
                        <span>¿Cómo genero una receta?</span>
                        <span class="ds-accordion__icon">▼</span>
                    </summary>
                    <div class="ds-accordion__content">
                        <p>Para generar una receta médica:</p>
                        <ol>
                            <li>Selecciona un paciente desde la lista de pacientes</li>
                            <li>Ve a la sección de <strong>Recetas</strong></li>
                            <li>Agrega los medicamentos necesarios</li>
                            <li>Imprime o guarda la receta</li>
                        </ol>
                    </div>
                </details>
                
                <details class="ds-accordion__item">
                    <summary class="ds-accordion__header">
                        <span>¿Cómo creo un presupuesto?</span>
                        <span class="ds-accordion__icon">▼</span>
                    </summary>
                    <div class="ds-accordion__content">
                        <p>Para crear un presupuesto:</p>
                        <ol>
                            <li>Selecciona un paciente</li>
                            <li>Ve a <strong>Presupuestos</strong></li>
                            <li>Agrega los servicios y tratamientos</li>
                            <li>Genera el documento PDF</li>
                        </ol>
                    </div>
                </details>
            </div>
        </div>
    </div>

    <!-- Contacto -->
    <div class="ds-card ds-mt-4">
        <div class="ds-card__header">
            <h2 class="ds-card__title">📞 ¿Necesitas más ayuda?</h2>
        </div>
        <div class="ds-card__body">
            <div class="ds-grid ds-grid--2">
                <div>
                    <h4>📧 Soporte por Email</h4>
                    <p class="ds-text-muted">soporte@dentalmx.com</p>
                    <p class="ds-text-muted">Tiempo de respuesta: 24-48 horas</p>
                </div>
                <div>
                    <h4>📱 Soporte Telefónico</h4>
                    <p class="ds-text-muted">+52 (55) 1234-5678</p>
                    <p class="ds-text-muted">Lunes a Viernes: 9:00 - 18:00</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.ds-accordion__item {
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius);
    margin-bottom: var(--space-2);
}

.ds-accordion__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-4);
    cursor: pointer;
    font-weight: var(--font-weight-medium);
}

.ds-accordion__header:hover {
    background-color: var(--color-gray-50);
}

.ds-accordion__icon {
    transition: transform 0.3s;
}

details[open] .ds-accordion__icon {
    transform: rotate(180deg);
}

.ds-accordion__content {
    padding: var(--space-4);
    border-top: 1px solid var(--color-gray-200);
    background-color: var(--color-gray-50);
}

.ds-accordion__content ol {
    margin: var(--space-2) 0;
    padding-left: var(--space-6);
}

.ds-accordion__content li {
    margin-bottom: var(--space-2);
}
</style>
<?= $this->endSection() ?>
