document.addEventListener('DOMContentLoaded', () => {
    const btnAgregar_formulario = document.getElementById('agregar-formulario');

    inicializarTabla();

    btnAgregar_formulario.addEventListener('click', (e) => {
        e.preventDefault();
        insertarFormulario();
    });

    $('#tablaFormularios tbody').on('click', '.btnEditar', function (e) {
        e.preventDefault();
        // Obtener el id de la fila
        editarFormulario($(this).data('id'));
    });

    $('#tablaFormularios tbody').on('click', '.btnEliminar', function (e) {
        e.preventDefault();
        // Obtener el id de la fila
        eliminarFormulario($(this).data('id'));
    });

    // $('#tablaFormularios').on('click', '.btnRegistros', function (e) {
    //     e.preventDefault();
    //     registrosFormulario($(this).data('id'));
    // });

    // $('#tablaFormularios').on('click', '.btnVer', function (e) {
    //     e.preventDefault();
    //     verFormulario($(this).data('id'));
    // });

    function insertarFormulario() {
        let formData = new FormData($("#crear-formulario")[0]);
        formData.append("operacion", "insertarFormulario");

        //Enviar datos del formulario al script PHP
        $.ajax({
            type: 'POST',
            url: '/mascarasCaptura/php/formulario.php',
            data: formData,
            processData: false,
            contentType: false,
        }).done(function (data) {
            if (data.icon != 'error') {
                Swal.fire({
                    title: data.status,
                    text: data.msg,
                    icon: data.icon,
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    if (result.isConfirmed) {
                        inicializarTabla();
                    }
                });
            } else {
                Swal.fire({
                    title: data.status,
                    text: data.msg,
                    icon: data.icon,
                    confirmButtonText: 'Ok'
                })
            }

        });
    }

    function inicializarTabla() {
        //Destruimos la tabla si es que existe
        $('#tablaFormularios').DataTable().destroy();
        //Creamos nuevamente la tabla
        $('#tablaFormularios ').DataTable({
            "ajax": {
                "url": "/mascarasCaptura/php/formulario.php",
                "type": "GET", // Especifica el método GET
                "data": function(d) {
                    // Agregar parámetros personalizados al objeto de datos
                    d.operacion = 'mostrarTramites'; // Aquí debes especificar el valor del ID del formulario que deseas enviar
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
                [5, 10, 15, 25, -1], [5, 10, 15, 25, 'Todos']
            ],
            "columns": [
                { "data": "id_tipo_formulario" },
                { "data": "texto" },
                { "data": "nombre_formulario" },
                { "data": "id_tipo_formulario" },
            ],
            "columnDefs": [
                {
                    "targets": -1, // Última columna
                    "render": function (data, type, row) {
                        return `
                            <button class="btn btn-secondary btnEditar" data-id="${data}">Editar</button>
                            <button class="btn btn-secondary btnEliminar bt-color-secondary" data-id="${data}">Eliminar</button>
                        `;
                        /*return `
                            <button class="btn btn-primary btnEditar" data-id="${data}">Editar</button>
                            <button class="btn btn-primary btnVer" data-id="${data}">Ver</button>
                            <button class="btn btn-primary btnRegistros" data-id="${data}">Registros</button>
                        `; */
                    },
                    "orderable": false // Evita que se ordene por esta columna
                }
            ],
            "order": [
                [4, 'desc'] // Ordenar por la columna 'id_datos_campos' en orden ascendente
            ]
        });
    }

    function editarFormulario(id_formulario) {
        // Redireccionar a otra página en la misma carpeta
        window.location.href = "crear-campos.php?form=" + id_formulario;
    }

    function eliminarFormulario(id_formulario) {
        Swal.fire({
            title: 'Eliminar Formulario',
            text: '¿Seguro(a) que desea eliminar el formulario?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#E0E0E0',
            cancelButtonColor: '#6E6E6E',
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            customClass: {
                confirmButton: 'text-black' // Agrega la clase personalizada al botón de cancelar
            }
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type: 'POST',
                    url: '/mascarasCaptura/php/formulario.php',
                    data: { operacion: 'eliminarFormulario', id_formulario: id_formulario },
                }).done(function (data) {
                    if (data.icon != 'error') {
                        Swal.fire({
                            title: data.status,
                            text: data.msg,
                            icon: data.icon,
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                inicializarTabla();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: data.status,
                            text: data.msg,
                            icon: data.icon,
                            confirmButtonText: 'Ok'
                        })
                    }
                })
            }
        });
        return;
    }

});