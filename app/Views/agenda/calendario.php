<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">📅 Calendario</h1>
        <div class="ds-page__actions">
            <a href="<?= base_url('/agenda'); ?>" class="ds-btn ds-btn--secondary">
                <span class="ds-btn__icon ds-btn__icon--left">⬅️</span>
                Volver
            </a>
        </div>
    </div>

    <div class="ds-card">
        <div class="ds-card__header">
            <h2 class="ds-card__title">Vista Semanal</h2>
        </div>
        <div class="ds-card__body">
            <!-- Navegación del calendario -->
            <div class="ds-flex ds-justify-between ds-align-center ds-mb-4">
                <button type="button" class="ds-btn ds-btn--secondary ds-btn--sm" onclick="cambiarSemana(-1)">
                    ⬅️ Semana Anterior
                </button>
                <h3 class="ds-text-lg" id="semanaActual"></h3>
                <button type="button" class="ds-btn ds-btn--secondary ds-btn--sm" onclick="cambiarSemana(1)">
                    Semana Siguiente ➡️
                </button>
            </div>

            <!-- Grilla del calendario -->
            <div class="ds-table-responsive">
                <table class="ds-table ds-table--bordered" id="calendarioTabla">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Lunes</th>
                            <th>Martes</th>
                            <th>Miércoles</th>
                            <th>Jueves</th>
                            <th>Viernes</th>
                            <th>Sábado</th>
                            <th>Domingo</th>
                        </tr>
                    </thead>
                    <tbody id="calendarioBody">
                        <!-- Se genera dinámicamente -->
                    </tbody>
                </table>
            </div>

            <!-- Leyenda -->
            <div class="ds-flex ds-justify-center ds-gap-4 ds-mt-4">
                <div class="ds-flex ds-align-center ds-gap-2">
                    <span class="legend-box legend-box--available"></span>
                    <span class="ds-text-sm">Disponible</span>
                </div>
                <div class="ds-flex ds-align-center ds-gap-2">
                    <span class="legend-box legend-box--unavailable"></span>
                    <span class="ds-text-sm">No disponible</span>
                </div>
                <div class="ds-flex ds-align-center ds-gap-2">
                    <span class="legend-box legend-box--exception"></span>
                    <span class="ds-text-sm">Excepción</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.calendar-slot {
    height: 40px;
    cursor: pointer;
    transition: background-color 0.2s;
}
.calendar-slot:hover {
    opacity: 0.8;
}
.calendar-slot--available {
    background-color: var(--color-success-light);
}
.calendar-slot--unavailable {
    background-color: var(--color-gray-200);
}
.calendar-slot--exception {
    background-color: var(--color-danger-light);
}
</style>

<style>
.legend-box {
    width: 20px;
    height: 20px;
    border-radius: var(--radius);
    display: inline-block;
}
.legend-box--available { background: var(--color-success-light); }
.legend-box--unavailable { background: var(--color-gray-200); }
.legend-box--exception { background: var(--color-danger-light); }
</style>

<script>
// Datos de horarios desde PHP
const horarios = <?= json_encode($horarios ?? []); ?>;
const excepciones = <?= json_encode($excepciones ?? []); ?>;

let fechaBase = new Date();

function generarCalendario() {
    const tbody = document.getElementById('calendarioBody');
    tbody.innerHTML = '';

    // Obtener inicio de la semana
    const inicioSemana = new Date(fechaBase);
    inicioSemana.setDate(inicioSemana.getDate() - inicioSemana.getDay() + 1);

    // Actualizar título
    const finSemana = new Date(inicioSemana);
    finSemana.setDate(finSemana.getDate() + 6);
    document.getElementById('semanaActual').textContent = 
        `${formatearFecha(inicioSemana)} - ${formatearFecha(finSemana)}`;

    // Generar filas de horas (8:00 - 20:00)
    for (let hora = 8; hora <= 20; hora++) {
        const tr = document.createElement('tr');
        const horaStr = `${hora.toString().padStart(2, '0')}:00`;
        
        // Celda de hora
        const tdHora = document.createElement('td');
        tdHora.innerHTML = `<strong>${horaStr}</strong>`;
        tdHora.classList.add('ds-text-center');
        tr.appendChild(tdHora);

        // Celdas de días
        for (let dia = 1; dia <= 7; dia++) {
            const td = document.createElement('td');
            td.className = 'calendar-slot';
            
            const fechaDia = new Date(inicioSemana);
            fechaDia.setDate(fechaDia.getDate() + dia - 1);
            const fechaStr = fechaDia.toISOString().split('T')[0];

            // Verificar si hay excepción
            const hayExcepcion = excepciones.some(e => e.fecha === fechaStr);
            
            // Verificar horario del día
            const horarioDia = horarios[dia];
            let disponible = false;
            
            if (horarioDia && horarioDia.activo) {
                const horaInicio = parseInt(horarioDia.hora_inicio.split(':')[0]);
                const horaFin = parseInt(horarioDia.hora_fin.split(':')[0]);
                disponible = hora >= horaInicio && hora < horaFin;
            }

            if (hayExcepcion) {
                td.classList.add('calendar-slot--exception');
            } else if (disponible) {
                td.classList.add('calendar-slot--available');
            } else {
                td.classList.add('calendar-slot--unavailable');
            }

            tr.appendChild(td);
        }
        
        tbody.appendChild(tr);
    }
}

function cambiarSemana(offset) {
    fechaBase.setDate(fechaBase.getDate() + (offset * 7));
    generarCalendario();
}

function formatearFecha(fecha) {
    const dia = fecha.getDate().toString().padStart(2, '0');
    const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
    return `${dia}/${mes}`;
}

// Generar calendario al cargar
document.addEventListener('DOMContentLoaded', generarCalendario);
</script>
<?= $this->endSection(); ?>
