document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('fullCalendar');
    
    var userId = document.body.getAttribute('data-user-id');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto', 
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        editable: false, 
        timeZone: 'local',
        events: {
            url: 'index.php?ruta=calendario/eventos',
            method: 'GET',
            failure: function() {
                alert('Hubo un error al cargar los eventos del calendario desde el servidor.');
            }
        },
        
        eventClick: function(info) {
            var props = info.event.extendedProps;
            alert('📅 Detalle de Reserva:\n' + 
                  'Título: ' + info.event.title + 
                  '\nServicio: ' + props.servicio_titulo + 
                  '\nUbicación: ' + props.ubicacion + 
                  '\nCliente/Proveedor: ' + props.otra_persona_nombre + 
                  '\nFecha y Hora: ' + info.event.start.toLocaleDateString() + ' a las ' + 
                  info.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
        },
        
        eventDidMount: function(info) {
            info.el.style.cursor = 'pointer';
        }
    });

    calendar.render();
});