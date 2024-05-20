document.addEventListener('DOMContentLoaded', () => {
    // Obtener la cadena de consulta (query string) de la URL actual
    let queryString = window.location.search;
    // Crear un nuevo objeto URLSearchParams con la cadena de consulta
    let params = new URLSearchParams(queryString);
    // Obtener el valor del parámetro "parametro"
    const id_tramite = params.get('solicitud');

    let txt_status = document.getElementById('txt_status');
    let txt_fec_vencimiento = document.getElementById('txt_fec_vencimiento');
    let txt_usuario = document.getElementById('txt_usuario');
    let txt_direccion = document.getElementById('txt_direccion');
    let txt_fec_envio = document.getElementById('txt_fec_envio');
    let txt_asunto = document.getElementById('txt_asunto');
    let txt_comentario = document.getElementById('txt_comentario');
    let form_content = document.getElementById('form-content');
    let btnReenviar_solicitud = document.getElementById('btnReenviar-solicitud');

    mostrarDatos();
    // Inicializar tabla
    inicializarTabla();

    function mostrarDatos() {
        $.ajax({
            method: 'GET',
            url: '/mascarasCaptura/php/seguimientoTramiteEnviado.php',
            data: { id_tramite: id_tramite, operacion: 'mostrarTramite' }
        }).done(function (data) {
            txt_status.innerText = data.data.name_status;
            txt_fec_vencimiento.innerText = data.data.fec_vencimiento;
            txt_usuario.innerText = data.data.nombre_user;
            txt_direccion.innerText = data.data.nombre_estructura;
            txt_fec_envio.innerText = data.data.fec_registro;
            txt_asunto.innerText = data.data.asunto;
            txt_comentario.value = data.data.observaciones;

            if (txt_status.textContent == 'Devuelto') {
                document.getElementById('datosTramite').style.display = 'block';
                $.ajax({
                    method: 'GET',
                    url: '/mascarasCaptura/php/seguimientoTramiteEnviado.php',
                    data: { operacion: 'mostrarFormulario', id_tramite: id_tramite }
                }).done(function (dataFormulario) {
                    let contenidoHtml = '';

                    dataFormulario.data.forEach((campo) => {
                        contenidoHtml += campo.htmlTag;
                    });
                    form_content.innerHTML = contenidoHtml;

                    $.ajax({
                        method: 'GET',
                        url: '/mascarasCaptura/php/seguimientoTramiteEnviado.php',
                        data: { operacion: 'datosRegistro', id_tramite: id_tramite }
                    }).done(function (dataRegistros) {
                        // Asignar a los campos del formulario 
                        for (let clave in dataRegistros.data) {
                            if (dataRegistros.data.hasOwnProperty(clave)) {
                                // Buscar elementos del formulario con el mismo nombre en el atributo name que la clave del JSON 
                                let campo = form_content.querySelector(`[name="${clave}"]`);
                                // console.log('Es ');
                                if (campo && campo.tagName === 'SELECT') {
                                    // Buscar la opción con el valor correspondiente y establecerla como seleccionada
                                    let opciones = campo.options;
                                    let encontrado = false;
                                    for (let i = 0; i < opciones.length; i++) {
                                        if (opciones[i].value === dataRegistros.data[clave]) {
                                            // if (opciones[i].textContent === dataRegistros.data[clave]) {
                                            opciones[i].selected = true;
                                            encontrado = true;
                                            break; // Salir del bucle una vez que se haya encontrado la opción
                                        }
                                    }
                                    if (!encontrado) {
                                        let opcionNinguno = document.createElement('option');
                                        opcionNinguno.text = 'N/A';
                                        opcionNinguno.value = null;
                                        opcionNinguno.selected = true;
                                        campo.add(opcionNinguno);
                                        opciones.disabled = true;
                                        campo.disabled = true;// Deshabilitar el select
                                    }
                                } else if (campo && campo.type !== 'file') {
                                    campo.value = dataRegistros.data[clave];
                                } else if (campo && campo.type == 'file') {
                                    let enlaceDescarga = document.createElement('a');
                                    let cadena = dataRegistros.data[clave];
                                    // Reemplazar un punto seguido de otro punto por una cadena vacía
                                    cadena = cadena.replace(/\.(?=\.)/g, '');
                                    enlaceDescarga.href = cadena; // Establecer el enlace de descarga
                                    enlaceDescarga.textContent = 'Vizualizar archivo enviado'; // Texto del enlace de descarga
                                    enlaceDescarga.target = '_blank';
                                    // Insertar el enlace después del campo de archivo
                                    campo.parentNode.insertBefore(enlaceDescarga, campo.nextSibling);

                                }
                            }
                        }
                    });
                });
            }
        });
    }

    function inicializarTabla() {
        //Destruimos la tabla si es que existe
        $('#tablaTurnos').DataTable().destroy();
        //Creamos nuevamente la tabla

        $('#tablaTurnos').DataTable({
            "ajax": {
                "url": "/mascarasCaptura/php/seguimientoTramiteEnviado.php",
                "type": "GET",
                "data": function (d) {
                    d.operacion = 'mostrarTurnos';
                    d.id_tramite = id_tramite;
                },
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
                { "data": "fec_seguimiento" },
                { "data": "nombre_status" },
                { "data": "nombre_completo" },
                { "data": "seguimiento" }
            ],
            "order": [
                [0, 'desc'] // Ordenar por la segunda columna (índice 1) de forma descendente
            ]
        });
    }

    btnReenviar_solicitud.addEventListener('click', (e) => {
        e.preventDefault();
        Swal.fire({
            title: 'Actualizar Trámite',
            text: '¿Seguro(a) que desea actualizar el trámite?',
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
                let formData = new FormData(document.getElementById("form-content"));
                formData.append("id_registro", id_tramite);
                formData.append("operacion", 'actualizarRegistro');
        
                $.ajax({
                    method: 'POST',
                    url: '/mascarasCaptura/php/seguimientoTramiteEnviado.php',
                    data: formData,
                    processData: false,
                    contentType: false
                }).done(function (data) {
                    if (data.icon === 'success') {
                        Swal.fire({
                            title: data.status,
                            text: data.msg,
                            icon: data.icon,
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Recargar la página
                                window.location.reload();
                            }
                        });
                    } else if (data.icon === 'error') {
                        Swal.fire({
                            title: data.status,
                            text: data.msg,
                            icon: data.icon,
                            confirmButtonText: 'Ok'
                        })
                    }
                });
            }
        });
        return;
    });
});