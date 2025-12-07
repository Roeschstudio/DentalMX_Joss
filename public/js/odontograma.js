/**
 * ============================================
 * DENTAL MX - ODONTOGRAMA INTERACTIVO
 * ============================================
 * JavaScript para la interactividad del odontograma
 * Sistema FDI (Federación Dental Internacional)
 * ============================================
 */

class Odontograma {
    constructor(options = {}) {
        this.containerId = options.containerId || 'odontograma-container';
        this.pacienteId = options.pacienteId || null;
        this.readonly = options.readonly || false;
        this.onUpdate = options.onUpdate || null;
        
        this.container = null;
        this.modal = null;
        this.selectedDiente = null;
        this.selectedSuperficie = null;
        this.dientes = {};
        this.estados = {};
        this.colores = {};
        
        // Superficies por orden de dibujo
        this.superficies = ['oclusal', 'vestibular', 'lingual', 'mesial', 'distal'];
        
        // Mapeo de superficie a campo de BD
        this.superficieToField = {
            'oclusal': 'sup_oclusal',
            'vestibular': 'sup_vestibular',
            'lingual': 'sup_lingual',
            'mesial': 'sup_mesial',
            'distal': 'sup_distal'
        };
        
        this.init();
    }
    
    async init() {
        this.container = document.getElementById(this.containerId);
        if (!this.container) {
            console.error('Contenedor del odontograma no encontrado:', this.containerId);
            return;
        }
        
        // Cargar estados disponibles
        await this.cargarEstados();
        
        // Cargar datos del odontograma si hay paciente
        if (this.pacienteId) {
            await this.cargarOdontograma();
        }
        
        // Inicializar eventos
        this.initEventListeners();
        
        // Crear modal de edición
        this.createModal();
    }
    
    async cargarEstados() {
        try {
            const response = await fetch('/odontograma/api/estados');
            const data = await response.json();
            
            if (data.success) {
                this.estados = data.data.estados;
                this.colores = data.data.colores;
                this.estadosDiente = data.data.estadosDiente;
            }
        } catch (error) {
            console.error('Error cargando estados:', error);
        }
    }
    
    async cargarOdontograma() {
        try {
            this.showLoading();
            
            const response = await fetch(`/odontograma/api/get/${this.pacienteId}`);
            const data = await response.json();
            
            if (data.success) {
                this.dientes = data.data.dientes || {};
                this.colores = data.data.colores || this.colores;
                this.actualizarVisualizacion();
            }
            
            this.hideLoading();
        } catch (error) {
            console.error('Error cargando odontograma:', error);
            this.hideLoading();
            this.showError('Error al cargar el odontograma');
        }
    }
    
    initEventListeners() {
        // Click en superficies
        this.container.addEventListener('click', (e) => {
            const superficie = e.target.closest('.superficie');
            if (superficie && !this.readonly) {
                this.handleSuperficieClick(superficie);
            }
            
            // Click en diente completo (para ver/editar estado general)
            const diente = e.target.closest('.odontograma-diente');
            if (diente && e.target.classList.contains('odontograma-diente__numero')) {
                this.handleDienteClick(diente);
            }
        });
        
        // Doble click en diente para abrir modal de edición completa
        this.container.addEventListener('dblclick', (e) => {
            const diente = e.target.closest('.odontograma-diente');
            if (diente && !this.readonly) {
                this.openEditModal(diente);
            }
        });
    }
    
    handleSuperficieClick(superficie) {
        const dienteElement = superficie.closest('.odontograma-diente');
        if (!dienteElement) return;
        
        const numeroDiente = dienteElement.dataset.diente;
        const superficieNombre = superficie.dataset.superficie;
        
        // Guardar selección
        this.selectedDiente = numeroDiente;
        this.selectedSuperficie = superficieNombre;
        
        // Mostrar modal de selección de estado
        this.openEstadoSelector(numeroDiente, superficieNombre);
    }
    
    handleDienteClick(dienteElement) {
        const numeroDiente = dienteElement.dataset.diente;
        this.selectedDiente = numeroDiente;
        this.openDienteInfo(numeroDiente);
    }
    
    openEstadoSelector(numeroDiente, superficie) {
        const modal = this.getModal();
        const content = modal.querySelector('.modal-body');
        
        // Obtener estado actual
        const diente = this.dientes[numeroDiente] || {};
        const estadoActual = diente[this.superficieToField[superficie]] || 'S001';
        
        let html = `
            <div class="superficie-selector">
                <div class="superficie-selector__info">
                    <div class="superficie-selector__nombre">Diente ${numeroDiente}</div>
                    <div class="superficie-selector__descripcion">Superficie ${this.getNombreSuperficie(superficie)}</div>
                </div>
                
                <div class="odontograma-modal__estados mt-4">
        `;
        
        for (const estado of this.estados) {
            const isActive = estadoActual === estado.codigo;
            html += `
                <button type="button" 
                        class="odontograma-modal__estado-btn ${isActive ? 'odontograma-modal__estado-btn--active' : ''}"
                        data-estado="${estado.codigo}">
                    <span class="odontograma-modal__estado-color" style="background-color: ${estado.color_hex}"></span>
                    <span>${estado.nombre}</span>
                </button>
            `;
        }
        
        html += `
                </div>
            </div>
        `;
        
        content.innerHTML = html;
        
        // Agregar eventos a los botones de estado
        content.querySelectorAll('.odontograma-modal__estado-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const estado = btn.dataset.estado;
                this.actualizarSuperficie(numeroDiente, superficie, estado);
                this.closeModal();
            });
        });
        
        // Mostrar modal
        modal.querySelector('.ds-modal__title').textContent = 'Seleccionar Estado';
        this.showModal();
    }
    
    openDienteInfo(numeroDiente) {
        // Cargar información del diente desde la API
        fetch(`/odontograma/api/diente/${this.pacienteId}/${numeroDiente}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showDienteInfo(data.data);
                }
            })
            .catch(error => {
                console.error('Error cargando info del diente:', error);
            });
    }
    
    showDienteInfo(data) {
        const modal = this.getModal();
        const content = modal.querySelector('.modal-body');
        const diente = data.diente;
        const historial = data.historial || [];
        
        let html = `
            <div class="ds-card mb-3">
                <div class="ds-card__body">
                    <h5 class="ds-card__title">${data.nombre}</h5>
                    <p class="mb-2">
                        <strong>Estado:</strong> 
                        <span class="ds-badge ds-badge--${this.getEstadoBadgeClass(diente.estado)}">${data.estadosDiente[diente.estado] || diente.estado}</span>
                    </p>
                    
                    <div class="ds-grid ds-grid--2 mt-3">
                        ${this.superficies.map(sup => {
                            const campo = this.superficieToField[sup];
                            const codigo = diente[campo] || 'S001';
                            const estado = data.estadosSuperficie[codigo] || {};
                            return `
                                <div class="ds-info-list__item">
                                    <span class="ds-info-list__label">${this.getNombreSuperficie(sup)}</span>
                                    <span class="ds-info-list__value">
                                        <span class="odontograma-leyenda__color" style="background-color: ${estado.color || '#4CAF50'}"></span>
                                        ${estado.nombre || 'Sano'}
                                    </span>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            </div>
            
            ${diente.notas ? `
                <div class="ds-alert ds-alert--info mb-3">
                    <strong>Notas:</strong> ${diente.notas}
                </div>
            ` : ''}
            
            <div class="ds-card">
                <div class="ds-card__header">
                    <h6 class="ds-card__title mb-0">
                        <i class="fas fa-history"></i> Historial de cambios
                    </h6>
                </div>
                <div class="ds-card__body">
                    <div class="odontograma-historial">
                        ${historial.length > 0 ? historial.map(h => `
                            <div class="odontograma-historial__item">
                                <span class="odontograma-historial__fecha">${h.fecha}</span>
                                <span class="odontograma-historial__descripcion">
                                    ${h.campo}: 
                                    <span class="odontograma-historial__valor odontograma-historial__valor--anterior">${h.anterior || 'N/A'}</span>
                                    →
                                    <span class="odontograma-historial__valor odontograma-historial__valor--nuevo">${h.nuevo}</span>
                                </span>
                            </div>
                        `).join('') : '<p class="text-muted text-center">Sin historial de cambios</p>'}
                    </div>
                </div>
            </div>
        `;
        
        content.innerHTML = html;
        modal.querySelector('.ds-modal__title').textContent = `Diente ${diente.numero_diente}`;
        
        // Agregar botón de edición al footer si no es readonly
        const footer = modal.querySelector('.modal-footer');
        if (!this.readonly) {
            footer.innerHTML = `
                <button type="button" class="ds-btn ds-btn--secondary" onclick="closeModal('modal-odontograma')">Cerrar</button>
                <button type="button" class="ds-btn ds-btn--primary" id="btn-editar-diente">
                    ✏️ Editar
                </button>
            `;
            
            footer.querySelector('#btn-editar-diente').addEventListener('click', () => {
                this.closeModal();
                const dienteElement = this.container.querySelector(`[data-diente="${diente.numero_diente}"]`);
                if (dienteElement) {
                    this.openEditModal(dienteElement);
                }
            });
        }
        
        this.showModal();
    }
    
    openEditModal(dienteElement) {
        const numeroDiente = dienteElement.dataset.diente;
        const diente = this.dientes[numeroDiente] || {};
        
        const modal = this.getModal();
        const content = modal.querySelector('.modal-body');
        
        let html = `
            <form id="form-editar-diente">
                <input type="hidden" name="numero_diente" value="${numeroDiente}">
                
                <div class="ds-form__group">
                    <label class="ds-form__label">Estado del diente</label>
                    <select class="ds-form__select" name="estado">
                        ${Object.entries(this.estadosDiente || {}).map(([key, label]) => `
                            <option value="${key}" ${diente.estado === key ? 'selected' : ''}>${label}</option>
                        `).join('')}
                    </select>
                </div>
                
                <hr class="ds-divider">
                
                <h6 class="mb-3">Superficies</h6>
                <div class="ds-grid ds-grid--2">
                    ${this.superficies.map(sup => {
                        const campo = this.superficieToField[sup];
                        const valorActual = diente[campo] || 'S001';
                        return `
                            <div class="ds-form__group">
                                <label class="ds-form__label">${this.getNombreSuperficie(sup)}</label>
                                <select class="ds-form__select" name="${campo}">
                                    ${this.estados.map(estado => `
                                        <option value="${estado.codigo}" ${valorActual === estado.codigo ? 'selected' : ''}>
                                            ${estado.nombre}
                                        </option>
                                    `).join('')}
                                </select>
                            </div>
                        `;
                    }).join('')}
                </div>
                
                <hr class="ds-divider">
                
                <div class="ds-form__group">
                    <label class="ds-form__label">Notas</label>
                    <textarea class="ds-form__textarea" name="notas" rows="3">${diente.notas || ''}</textarea>
                </div>
            </form>
        `;
        
        content.innerHTML = html;
        modal.querySelector('.ds-modal__title').textContent = `Editar Diente ${numeroDiente}`;
        
        // Agregar botones al footer
        const footer = modal.querySelector('.modal-footer');
        footer.innerHTML = `
            <button type="button" class="ds-btn ds-btn--secondary" onclick="closeModal('modal-odontograma')">Cancelar</button>
            <button type="button" class="ds-btn ds-btn--primary" id="btn-guardar-diente">
                💾 Guardar
            </button>
        `;
        
        footer.querySelector('#btn-guardar-diente').addEventListener('click', () => {
            this.guardarDiente(numeroDiente);
        });
        
        this.showModal();
    }
    
    async actualizarSuperficie(numeroDiente, superficie, estado) {
        try {
            const formData = new FormData();
            formData.append('id_paciente', this.pacienteId);
            formData.append('numero_diente', numeroDiente);
            formData.append('superficie', superficie);
            formData.append('estado', estado);
            
            const response = await fetch('/odontograma/api/superficie', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Actualizar datos locales
                if (!this.dientes[numeroDiente]) {
                    this.dientes[numeroDiente] = {};
                }
                this.dientes[numeroDiente][this.superficieToField[superficie]] = estado;
                
                // Actualizar visualización
                this.actualizarSuperficieVisual(numeroDiente, superficie, estado);
                
                // Notificar
                this.showToast('Superficie actualizada correctamente', 'success');
                
                // Callback
                if (this.onUpdate) {
                    this.onUpdate({ tipo: 'superficie', numeroDiente, superficie, estado });
                }
            } else {
                this.showToast(data.message || 'Error al actualizar', 'error');
            }
        } catch (error) {
            console.error('Error actualizando superficie:', error);
            this.showToast('Error al actualizar la superficie', 'error');
        }
    }
    
    async guardarDiente(numeroDiente) {
        const form = document.getElementById('form-editar-diente');
        if (!form) return;
        
        const formData = new FormData(form);
        const datos = {};
        formData.forEach((value, key) => {
            if (key !== 'numero_diente') {
                datos[key] = value;
            }
        });
        
        try {
            const response = await fetch('/odontograma/api/diente', {
                method: 'POST',
                body: new URLSearchParams({
                    id_paciente: this.pacienteId,
                    numero_diente: numeroDiente,
                    datos: JSON.stringify(datos)
                }),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Actualizar datos locales
                this.dientes[numeroDiente] = data.data;
                
                // Actualizar visualización
                this.actualizarDienteVisual(numeroDiente);
                
                // Cerrar modal
                this.closeModal();
                
                // Notificar
                this.showToast('Diente actualizado correctamente', 'success');
                
                // Callback
                if (this.onUpdate) {
                    this.onUpdate({ tipo: 'diente', numeroDiente, datos: data.data });
                }
            } else {
                this.showToast(data.message || 'Error al guardar', 'error');
            }
        } catch (error) {
            console.error('Error guardando diente:', error);
            this.showToast('Error al guardar el diente', 'error');
        }
    }
    
    actualizarVisualizacion() {
        Object.keys(this.dientes).forEach(numeroDiente => {
            this.actualizarDienteVisual(numeroDiente);
        });
    }
    
    actualizarDienteVisual(numeroDiente) {
        const diente = this.dientes[numeroDiente];
        if (!diente) return;
        
        const dienteElement = this.container.querySelector(`[data-diente="${numeroDiente}"]`);
        if (!dienteElement) return;
        
        // Actualizar clases de estado
        dienteElement.classList.remove('odontograma-diente--ausente');
        if (['ausente', 'extraido'].includes(diente.estado)) {
            dienteElement.classList.add('odontograma-diente--ausente');
        }
        
        // Actualizar cada superficie
        this.superficies.forEach(superficie => {
            const campo = this.superficieToField[superficie];
            const estado = diente[campo] || 'S001';
            this.actualizarSuperficieVisual(numeroDiente, superficie, estado);
        });
    }
    
    actualizarSuperficieVisual(numeroDiente, superficie, estado) {
        const dienteElement = this.container.querySelector(`[data-diente="${numeroDiente}"]`);
        if (!dienteElement) return;
        
        const superficieElement = dienteElement.querySelector(`[data-superficie="${superficie}"]`);
        if (!superficieElement) return;
        
        // Obtener color del estado
        const colorInfo = this.colores[estado] || { color: '#4CAF50' };
        const color = colorInfo.color || '#4CAF50';
        
        // Aplicar color
        superficieElement.style.fill = color;
        
        // Actualizar clase (usar setAttribute para compatibilidad con SVG)
        superficieElement.setAttribute('class', `superficie superficie-${estado}`);
    }
    
    // Utilidades
    getNombreSuperficie(superficie) {
        const nombres = {
            'oclusal': 'Oclusal/Incisal',
            'vestibular': 'Vestibular',
            'lingual': 'Lingual/Palatino',
            'mesial': 'Mesial',
            'distal': 'Distal'
        };
        return nombres[superficie] || superficie;
    }
    
    getEstadoBadgeClass(estado) {
        const clases = {
            'presente': 'success',
            'ausente': 'danger',
            'extraido': 'danger',
            'impactado': 'warning',
            'erupcion': 'info',
            'corona': 'primary',
            'implante': 'info',
            'endodoncia': 'warning',
            'protesis': 'secondary'
        };
        return clases[estado] || 'secondary';
    }
    
    // Modal - Usando Design System de Dental MX
    createModal() {
        const modalId = 'modal-odontograma';
        
        // Verificar si ya existe
        if (document.getElementById(modalId)) {
            return;
        }
        
        const modalHtml = `
            <div class="ds-modal-overlay" id="${modalId}" onclick="closeModalOnOverlayClick(event, '${modalId}')">
                <div class="ds-modal ds-modal--lg">
                    <div class="ds-modal__header">
                        <h2 class="ds-modal__title">Odontograma</h2>
                        <button type="button" class="ds-modal__close" onclick="closeModal('${modalId}')">×</button>
                    </div>
                    <div class="ds-modal__body modal-body"></div>
                    <div class="ds-modal__footer modal-footer">
                        <button type="button" class="ds-btn ds-btn--secondary" onclick="closeModal('${modalId}')">Cerrar</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    
    getModal() {
        return document.getElementById('modal-odontograma');
    }
    
    showModal() {
        openModal('modal-odontograma');
    }
    
    closeModal() {
        closeModal('modal-odontograma');
    }
    
    // Loading y errores
    showLoading() {
        const loadingHtml = `
            <div class="odontograma-loading" id="odontograma-loading">
                <div class="odontograma-loading__spinner"></div>
                <p class="odontograma-loading__texto">Cargando odontograma...</p>
            </div>
        `;
        this.container.insertAdjacentHTML('afterbegin', loadingHtml);
    }
    
    hideLoading() {
        const loading = document.getElementById('odontograma-loading');
        if (loading) {
            loading.remove();
        }
    }
    
    showError(message) {
        this.showToast(message, 'error');
    }
    
    showToast(message, type = 'info') {
        // Usar el sistema de toast del proyecto si existe
        if (typeof DentalMX !== 'undefined' && DentalMX.toast) {
            DentalMX.toast(message, type);
            return;
        }
        
        // Fallback simple
        const toast = document.createElement('div');
        toast.className = `ds-alert ds-alert--${type} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
        toast.innerHTML = `
            <span>${message}</span>
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.remove(), 5000);
    }
    
    // Métodos públicos
    refresh() {
        if (this.pacienteId) {
            this.cargarOdontograma();
        }
    }
    
    setReadonly(readonly) {
        this.readonly = readonly;
    }
    
    setPaciente(pacienteId) {
        this.pacienteId = pacienteId;
        this.cargarOdontograma();
    }
    
    getDientes() {
        return this.dientes;
    }
}

// Exportar para uso global
window.Odontograma = Odontograma;

// Inicialización automática si hay contenedor
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('odontograma-container');
    if (container && container.dataset.paciente) {
        window.odontogramaInstance = new Odontograma({
            containerId: 'odontograma-container',
            pacienteId: container.dataset.paciente
        });
    }
});
