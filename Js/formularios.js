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

    $('#tablaFormularios').on('click', '.btnRegistros', function (e) {
        e.preventDefault();
        registrosFormulario($(this).data('id'));
    });

    $('#tablaFormularios').on('click', '.btnVer', function (e) {
        e.preventDefault();
        verFormulario($(this).data('id'));
    });

    function insertarFormulario() {
        let formData = new FormData($("#crear-formulario")[0]);

        //Enviar datos del formulario al script PHP
        $.ajax({
            type: 'POST',
            url: '/mascarasCaptura/php/crearFormulario.php',
            data: formData,
            processData: false,
            contentType: false,
        }).done(function (data) {
            console.log(data);
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
                "url": "/mascarasCaptura/php/mostrarFormularios.php",
                "type": "GET", // Especifica el método GET
            },
            "language": {
                "url": "./Datatables/es-MX.json",
            },
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
                        /* return `
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
        window.location.href = "crear-campos.php?id_form=" + id_formulario;
    }

    function verFormulario(id_formulario) {
        // Redireccionar a otra página en la misma carpeta
        window.location.href = "captura-formulario.php?id_form=" + id_formulario;
    }

    function registrosFormulario(id_formulario) {
        // Redireccionar a otra página en la misma carpeta
        window.location.href = "registros.php?id_form=" + id_formulario;
    }
    
});