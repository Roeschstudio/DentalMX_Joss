<?php
/**
 * Modal Templates - Design System
 * Dental MX
 * 
 * Templates reutilizables para modales comunes
 */
?>

<!-- Modal: Confirmar Eliminación -->
<div class="ds-modal-overlay" id="modalConfirmDelete" onclick="if(event.target===this)closeModal('modalConfirmDelete')">
    <div class="ds-modal ds-modal--sm ds-modal--centered">
        <div class="ds-modal__body">
            <div class="ds-modal__icon ds-modal__icon--danger">⚠️</div>
            <h4 class="ds-modal__title" id="deleteModalTitle">¿Eliminar este registro?</h4>
            <p class="ds-modal__text" id="deleteModalMessage">Esta acción no se puede deshacer.</p>
        </div>
        <div class="ds-modal__footer ds-modal__footer--center">
            <button class="ds-btn ds-btn--light" onclick="closeModal('modalConfirmDelete')">Cancelar</button>
            <button class="ds-btn ds-btn--danger" id="deleteModalConfirm">Eliminar</button>
        </div>
    </div>
</div>

<!-- Modal: Éxito -->
<div class="ds-modal-overlay" id="modalSuccess" onclick="if(event.target===this)closeModal('modalSuccess')">
    <div class="ds-modal ds-modal--sm ds-modal--centered">
        <div class="ds-modal__body">
            <div class="ds-modal__icon ds-modal__icon--success">✅</div>
            <h4 class="ds-modal__title" id="successModalTitle">¡Operación exitosa!</h4>
            <p class="ds-modal__text" id="successModalMessage">Los cambios se han guardado correctamente.</p>
        </div>
        <div class="ds-modal__footer ds-modal__footer--center">
            <button class="ds-btn ds-btn--primary" onclick="closeModal('modalSuccess')">Aceptar</button>
        </div>
    </div>
</div>

<!-- Modal: Error -->
<div class="ds-modal-overlay" id="modalError" onclick="if(event.target===this)closeModal('modalError')">
    <div class="ds-modal ds-modal--sm ds-modal--centered">
        <div class="ds-modal__body">
            <div class="ds-modal__icon ds-modal__icon--danger">❌</div>
            <h4 class="ds-modal__title" id="errorModalTitle">Error</h4>
            <p class="ds-modal__text" id="errorModalMessage">Ha ocurrido un error. Por favor, intente nuevamente.</p>
        </div>
        <div class="ds-modal__footer ds-modal__footer--center">
            <button class="ds-btn ds-btn--secondary" onclick="closeModal('modalError')">Cerrar</button>
        </div>
    </div>
</div>

<!-- Modal: Cargando -->
<div class="ds-modal-overlay" id="modalLoading">
    <div class="ds-modal ds-modal--sm ds-modal--centered">
        <div class="ds-modal__body">
            <div class="ds-loading-spinner"></div>
            <h4 class="ds-modal__title" id="loadingModalTitle">Procesando...</h4>
            <p class="ds-modal__text" id="loadingModalMessage">Por favor espere.</p>
        </div>
    </div>
</div>

<script>
/**
 * Funciones helper para modales de confirmación
 */

// Mostrar modal de confirmación de eliminación
function confirmDelete(title, message, callback) {
    document.getElementById('deleteModalTitle').textContent = title || '¿Eliminar este registro?';
    document.getElementById('deleteModalMessage').textContent = message || 'Esta acción no se puede deshacer.';
    
    const confirmBtn = document.getElementById('deleteModalConfirm');
    confirmBtn.onclick = function() {
        closeModal('modalConfirmDelete');
        if (typeof callback === 'function') {
            callback();
        }
    };
    
    openModal('modalConfirmDelete');
}

// Mostrar modal de éxito
function showSuccessModal(title, message, callback) {
    document.getElementById('successModalTitle').textContent = title || '¡Operación exitosa!';
    document.getElementById('successModalMessage').textContent = message || 'Los cambios se han guardado correctamente.';
    
    openModal('modalSuccess');
    
    if (typeof callback === 'function') {
        const modal = document.getElementById('modalSuccess');
        modal.querySelector('.ds-btn--primary').onclick = function() {
            closeModal('modalSuccess');
            callback();
        };
    }
}

// Mostrar modal de error
function showErrorModal(title, message) {
    document.getElementById('errorModalTitle').textContent = title || 'Error';
    document.getElementById('errorModalMessage').textContent = message || 'Ha ocurrido un error. Por favor, intente nuevamente.';
    openModal('modalError');
}

// Mostrar modal de carga
function showLoadingModal(title, message) {
    document.getElementById('loadingModalTitle').textContent = title || 'Procesando...';
    document.getElementById('loadingModalMessage').textContent = message || 'Por favor espere.';
    openModal('modalLoading');
}

// Cerrar modal de carga
function hideLoadingModal() {
    closeModal('modalLoading');
}

// Confirmación antes de eliminar con formulario
function confirmDeleteForm(formId, title, message) {
    confirmDelete(title, message, function() {
        document.getElementById(formId).submit();
    });
}

// Confirmación antes de eliminar con URL
function confirmDeleteUrl(url, title, message) {
    confirmDelete(title, message, function() {
        window.location.href = url;
    });
}

// Confirmación antes de eliminar con AJAX
function confirmDeleteAjax(url, title, message, successCallback) {
    confirmDelete(title, message, function() {
        showLoadingModal('Eliminando...', 'Por favor espere.');
        
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoadingModal();
            if (data.success) {
                showToast('success', 'Eliminado', data.message || 'Registro eliminado correctamente');
                if (typeof successCallback === 'function') {
                    successCallback(data);
                }
            } else {
                showErrorModal('Error', data.message || 'No se pudo eliminar el registro');
            }
        })
        .catch(error => {
            hideLoadingModal();
            showErrorModal('Error', 'Error de conexión. Intente nuevamente.');
            console.error('Error:', error);
        });
    });
}
</script>

<style>
/* Loading Spinner */
.ds-loading-spinner {
    width: 48px;
    height: 48px;
    margin: 0 auto var(--space-4);
    border: 4px solid var(--color-gray-200);
    border-top-color: var(--color-primary);
    border-radius: 50%;
    animation: ds-spin 1s linear infinite;
}

@keyframes ds-spin {
    to {
        transform: rotate(360deg);
    }
}

/* Empty State */
.ds-empty-state {
    text-align: center;
    padding: var(--space-12) var(--space-6);
    color: var(--color-gray-500);
}

.ds-empty-state__icon {
    font-size: 64px;
    margin-bottom: var(--space-4);
    opacity: 0.5;
}

.ds-empty-state__text {
    font-size: var(--font-size-lg);
    margin-bottom: var(--space-4);
}

.ds-empty-state__action {
    margin-top: var(--space-4);
}
</style>
