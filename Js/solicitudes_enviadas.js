document.addEventListener('DOMContentLoaded', () => {
    // Inicializar tablas
    inicializarTabla();

    function inicializarTabla() {
        //Destruimos la tabla si es que existe
        $('#tablaSolicitudes').DataTable().destroy();
        //Creamos nuevamente la tabla
        $('#tablaSolicitudes ').DataTable({
            "ajax": {
                "url": "/mascarasCaptura/php/mostrarSolicitudesEnviadas.php",
                "type": "GET", // Especifica el método GET
                "data": function (d) {
                    // Agrega parámetros GET aquí si es necesario
                    d.operacion = 'mostrarSolicitudes';
                }
            },
            "pagingType": "full_numbers",
            "language": {
                "url": "./Datatables/es-MX.json",
                "paginate": {
                    "first": "‹",
                    "previous": "«",
                    "next": "»",
                    "last": "›"
                }
            },
            "lengthMenu": [
                [10, 15, 25, -1], [10, 15, 25, 'Todos']
            ],
            "columns": [
                { "data": "asunto" },
                { "data": "nombre_estructura" },
                { "data": "fec_registro" },
                { "data": "name_status" },
                { "data": "id_solicitud_tramite" }
            ],
            "columnDefs": [
                {
                    "targets": -1, // Última columna
                    "render": function (data, type, row) {
                        return `
                            <a class="btn btn-secondary " data-id="${data}" href="seguimiento_enviadas.php?solicitud=${data}" >Ver</a>
                        `;
                    },
                    "orderable": false // Evita que se ordene por esta columna
                }
            ],
            "order": [
                [4, 'desc'] // Ordenar por la columna 'id_datos_campos' en orden ascendente
            ]
        });
    }



});
