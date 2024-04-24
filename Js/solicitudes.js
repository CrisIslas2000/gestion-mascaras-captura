document.addEventListener('DOMContentLoaded', () => {

    // let form_content = document.getElementById('form-content');
    let select_formulario = document.getElementById('select-formulario');

    // Inicializar tablas
    inicializarTabla(-1);

    /* Solicitud para obtener las opciones del select */
    $.ajax({
        method: 'GET',
        url: '/mascarasCaptura/php/mostrarSolicitudes.php',
        data: { operacion: 'dataSelect' }
    }).done(function (dataSelect) {
        /* Imprimir las opciones */
        dataSelect.data.forEach((select) => {
            select_formulario.innerHTML += `<option class="text-center" value="${select.id_cat_estructura}">${select.nombre_estructura}</option>`;
        });
    });

    let selectValue;
    /* Obtener el valor de la opción seleccionada */
    select_formulario.addEventListener('change', (e) => {
        selectValue = e.target.value;

        if (selectValue === 0 || selectValue === '0') {
            inicializarTabla(-1);
            return;
        }

        inicializarTabla(selectValue);

    });

    function inicializarTabla(id_estructura) {
        //Destruimos la tabla si es que existe
        $('#tablaSolicitudes').DataTable().destroy();
        //Creamos nuevamente la tabla
        $('#tablaSolicitudes ').DataTable({
            "ajax": {
                "url": "/mascarasCaptura/php/mostrarSolicitudes.php",
                "type": "GET", // Especifica el método GET
                "data": function (d) {
                    // Agrega parámetros GET aquí si es necesario
                    d.operacion = 'mostrarSolicitudes';
                    d.id_estructura = id_estructura; // Ejemplo de parámetro
                }
            },
            "language": {
                "url": "./Datatables/es-MX.json",
            },
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
                            <a class="btn btn-secondary " data-id="${data}" href="seguimiento.php?solicitud=${data}&estructura=${id_estructura}" >Ver</a>
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
