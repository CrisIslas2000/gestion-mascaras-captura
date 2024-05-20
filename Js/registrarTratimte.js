document.addEventListener("DOMContentLoaded", () => {
    // Obtener la cadena de consulta (query string) de la URL actual
    let queryString = window.location.search;
    // Crear un nuevo objeto URLSearchParams con la cadena de consulta
    let params = new URLSearchParams(queryString);
    // Obtener el valor del parámetro "parametro"
    const id_form = params.get('id_form');

    let select_formulario = document.getElementById('select-formulario');
    let tabla_registros = document.getElementById("tabla-registros");
    let form_content = document.getElementById('form-content');
    

    /* Solicitud para obtener las opciones del select */
    $.ajax({
        method: 'GET',
        url: '/mascarasCaptura/php/imprimirFormulario.php',
        data: { operacion: 'dataSelect' }
    }).done(function (dataSelect) {
        /* Imprimir las opciones */
        dataSelect.data.forEach((select) => {
            select_formulario.innerHTML += `<option class="text-center" value="${select.id_tipo_formulario}">${select.texto}</option>`;
        });

        //Seleccionar por defecto el valor que recibimos por el parametro
        Array.from(select_formulario.options).forEach(function (option) {
            // Verificar si el valor de la opción actual coincide con el valor deseado
            if (option.value == id_form) {
                // Establecer el atributo selected en la opción actual
                option.selected = true;
            }
        });

        /* Petición para obtener y mostrar los registros en la tabla */
        $.ajax({
            method: 'GET',
            url: '/mascarasCaptura/php/operacionesRegistros.php',
            data: { tipo_formulario: id_form, operacion: 'datosTabla' }
        }).done(function (responseTabla) {
            tabla_registros.innerHTML = ''; // Limpiar el contenido actual de la tabla

            // Crear el contenido HTML de la tabla
            let contenidoTabla = '<thead><tr>';

            // Agregar cabecera de la tabla
            Object.keys(responseTabla.data[0]).forEach(function (nombreColumna) {
                contenidoTabla += '<th>' + nombreColumna + '</th>';
            });

            // Agregar columna adicional para el botón
            contenidoTabla += '<th>Acciones</th>';

            contenidoTabla += '</tr></thead>';
            contenidoTabla += '<tbody>';

            // Agregar filas de datos a la tabla
            responseTabla.data.forEach(function (registro) {
                contenidoTabla += '<tr>';
                Object.values(registro).forEach(function (valorColumna) {
                    contenidoTabla += '<td>' + valorColumna + '</td>';
                });
                console.log(registro);
                // Agregar botón a cada fila
                contenidoTabla += '<td><button id="' + registro.id + '" class="btn btn-primary btnSeleccion" data-bs-toggle="modal" data-bs-target="#modalEditar">Editar</button></td>';

                contenidoTabla += '</tr>';
            });

            contenidoTabla += '</tbody>';

            // Asignar el contenido HTML de la tabla al elemento en el HTML
            tabla_registros.innerHTML = contenidoTabla;

            /* Petición para obtener y mostrar el formulario (mascara de captura) desde la base de datos */
            $.ajax({
                method: 'GET',
                url: '/mascarasCaptura/php/imprimirFormulario.php',
                data: { operacion: 'mostrarFormulario', selectValue: id_form }
            }).done(function (dataFormulario) {
                if (dataFormulario.status === '404') {
                    Swal.fire({
                        title: 'Ups',
                        text: dataFormulario.msg,
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }
                if (dataFormulario.status === '500') {
                    Swal.fire({
                        title: 'Error',
                        text: dataFormulario.msg,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }
                let contenidoHtml = '';

                dataFormulario.data.forEach((campo) => {
                    contenidoHtml += campo.htmlTag;
                });
                form_content.innerHTML = contenidoHtml;

                form_content.innerHTML += `
                    <div class="row">
                        <div class="d-flex flex-column align-items-center">
                            <button type="button" id="btnEditar" class="btn btn-primary mt-2">Guardar</button>
                        </div>
                    </div>
                `;
                let id_registro = 0;
                /* Evento para enviar y actualizar datos */
                let arrayBtnSeleccion = document.querySelectorAll('.btnSeleccion');
                arrayBtnSeleccion.forEach((btnSeleccion) => {
                    btnSeleccion.addEventListener("click", (e) => {
                        e.preventDefault();
                        const id = e.target.id;
                        id_registro = id;

                        /* Peticion para obtener los datos seleccionados */
                        $.ajax({
                            method: 'GET',
                            url: '/mascarasCaptura/php/operacionesRegistros.php',
                            data: { tipo_formulario: id_form, operacion: 'datosRegistro', id: id }
                        }).done(function (dataRegistros) {
                            /* Asignar a los campos del formulario */
                            for (let clave in dataRegistros.data) {
                                if (dataRegistros.data.hasOwnProperty(clave)) {
                                    /* Buscar elementos del formulario con el mismo nombre en el atributo name que la clave del JSON */
                                    let campo = form_content.querySelector(`[name="${clave}"]`);
                                    if (campo && campo.type !== 'file') {
                                        campo.value = dataRegistros.data[clave];
                                    }
                                }
                            }
                        });
                    });
                });
                /* Evento para editar el registro */
                document.getElementById('btnEditar').addEventListener('click', (e) => {
                    e.preventDefault();
                    let formData = new FormData(document.getElementById("form-content"));
                    formData.append("tipo_formulario", id_form);
                    formData.append("id_registro", id_registro);
                    formData.append("operacion", 'actualizarRegistro');

                    /* formData.forEach((valor, clave) => {
                        console.log(clave, ': ', valor);
                    }) */

                    $.ajax({
                        method: 'POST',
                        url: '/mascarasCaptura/php/operacionesRegistros.php',
                        data: formData,
                        processData: false,
                        contentType: false
                    }).done(function (responseEditar) {
                        console.log(responseEditar);
                    });
                });
            });
        });
    });
});