<?= $this->extend('layout/ds_layout') ?>
<?= $this->section('content') ?>

<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">Calendario de Citas</h1>
    </div>
    
    <div class="ds-card">
        <div class="ds-card__body">
            <div class="ds-embed-container">
                <iframe src="https://calendar.google.com/calendar/embed?src=armonybyjossprogramming%40gmail.com&ctz=America%2FMexico_City" class="ds-embed" frameborder="0" scrolling="no"></iframe>
            </div>
        </div>
    </div>
</div>
<!--
<div class="modal fade" id="eventoModal" tabindex="-1" aria-labelledby="eventoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p><strong>Paciente:</strong> <span id="modalTitulo"></span></p>
                <p><strong>Fecha:</strong> <span id="modalFecha"></span></p>
            </div>
        </div>
    </div>
</div>!-->
<?= $this->endSection() ?>

<?= $this->section('scripts')?>
<script>
    /*document.getElementById('toggleSidebar').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('mainContent').classList.toggle('collapsed');
        });
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: '2025-06-07',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [{
                        "title": "Consulta: Juan Pérez",
                        "start": "2025-06-20T10:00:00"
                    },
                    {
                        "title": "Chequeo: María López",
                        "start": "2025-06-20T12:30:00"
                    },
                    {
                        "title": "Revisión: Ana Torres",
                        "start": "2025-06-21T09:00:00"
                    },
                    {
                        "title": "Consulta: Luis Ramírez",
                        "start": "2025-06-21T11:15:00"
                    },
                    {
                        "title": "Urgencia: Pedro Hernández",
                        "start": "2025-06-21T17:45:00"
                    },
                    {
                        "title": "Control: Laura Sánchez",
                        "start": "2025-06-22T08:30:00"
                    },
                    {
                        "title": "Evaluación: Diego Morales",
                        "start": "2025-06-22T14:00:00"
                    },
                    {
                        "title": "Psicología: Sofía Castillo",
                        "start": "2025-06-23T10:45:00"
                    },
                    {
                        "title": "Consulta: Alberto Gómez",
                        "start": "2025-06-23T16:00:00"
                    },
                    {
                        "title": "Seguimiento: Carla Ríos",
                        "start": "2025-06-24T13:30:00"
                    }
                ],
                eventClick: function(info) {
                    document.getElementById('modalTitulo').textContent = info.event.title;
                    document.getElementById('modalFecha').textContent = info.event.start.toLocaleString('es-MX');
                    new bootstrap.Modal(document.getElementById('eventoModal')).show();
                }
            });

            calendar.render();

            let table = new DataTable('#myTable');
        });*/
</script>
<?= $this->endSection() ?>