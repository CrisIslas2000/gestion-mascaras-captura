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
    let select_turnar = document.getElementById('select-turnar');

    let form_content = document.getElementById('form-content');
    let vista_turnos = document.getElementById('mostrarTurnos');

    let btn_turnar = document.getElementById('btnTurnar-solicitud');
    let btn_regresar = document.getElementById('btnRegresar-solicitud');
    let btn_aprobar = document.getElementById('btnAprobar-solicitud');

    $.ajax({
        method: 'GET',
        url: '/mascarasCaptura/php/seguimientoTramiteRecibidas.php',
        data: { id_tramite: id_tramite, operacion: 'mostrarTramite' }
    }).done(function (data) {
        txt_status.innerText = data.data.name_status;
        txt_fec_vencimiento.innerText = data.data.fec_vencimiento;
        txt_usuario.innerText = data.data.nombre_user;
        txt_direccion.innerText = data.data.nombre_estructura;
        txt_fec_envio.innerText = data.data.fec_registro;
        txt_asunto.innerText = data.data.asunto;
        txt_comentario.value = data.data.observaciones;

        // Des-habilitar el boton de regresar si ya esta turnada la solicitud
        if (txt_status.innerText === 'Turnado') {
            btn_regresar.style.display = 'none';
        }

        $.ajax({
            method: 'GET',
            url: '/mascarasCaptura/php/seguimientoTramiteRecibidas.php',
            data: { operacion: 'mostrarFormulario', id_tramite: id_tramite }
        }).done(function (dataFormulario) {

            let contenidoHtml = '';

            dataFormulario.data.forEach((campo) => {
                contenidoHtml += campo.htmlTag;
            });
            form_content.innerHTML = contenidoHtml;

            $.ajax({
                method: 'GET',
                url: '/mascarasCaptura/php/seguimientoTramiteRecibidas.php',
                data: { operacion: 'datosRegistro', id_tramite: id_tramite }
            }).done(function (dataRegistros) {
                // Asignar a los campos del formulario 
                for (let clave in dataRegistros.data) {
                    if (dataRegistros.data.hasOwnProperty(clave)) {
                        // Buscar elementos del formulario con el mismo nombre en el atributo name que la clave del JSON 
                        let campo = form_content.querySelector(`[name="${clave}"]`);
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
                            }
                        } else if (campo && campo.type !== 'file') {
                            campo.value = dataRegistros.data[clave];
                        } else if (campo && campo.type == 'file') {
                            let enlaceDescarga = document.createElement('a');
                            let cadena = dataRegistros.data[clave];
                            // Reemplazar un punto seguido de otro punto por una cadena vacía
                            cadena = cadena.replace(/\.(?=\.)/g, '');
                            enlaceDescarga.href = cadena; // Establecer el enlace de descarga
                            enlaceDescarga.textContent = 'Visualizar archivo'; // Texto del enlace de descarga
                            enlaceDescarga.target = '_blank';
                            // Insertar el enlace después del campo de archivo
                            campo.parentNode.insertBefore(enlaceDescarga, campo.nextSibling);
                        }
                    }
                }
            });
        });
    });

    btn_regresar.addEventListener('click', (e) => {
        e.preventDefault();
        Swal.fire({
            title: 'Devolver Solicitud',
            text: '¿Seguro(a) que desea devolver la solicitud al tramitante?',
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
                    method: 'POST',
                    url: '/mascarasCaptura/php/seguimientoTramiteRecibidas.php',
                    data: { id_tramite: id_tramite, observaciones: txt_comentario.value, operacion: 'regresarTramite' }
                }).done(function (data) {
                    Swal.fire({
                        title: data.status,
                        text: data.msg,
                        icon: data.icon,
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                method: 'GET',
                                url: '/mascarasCaptura/php/seguimientoTramiteRecibidas.php',
                                data: { operacion: 'enviarCorreoDevuelto', id_tramite: id_tramite }
                            }).done(function (data) {
                                if (data.icon == 'success') {
                                    // Recargar la página
                                    window.location.reload();
                                }
                            });
                        }
                    });
                    return;
                });
            }
        })
        return;
    });

    btn_aprobar.addEventListener('click', (e) => {
        select_turnar.innerHTML = "";
        e.preventDefault();
        $.ajax({
            method: 'GET',
            url: '/mascarasCaptura/php/seguimientoTramiteRecibidas.php',
            data: { id_tramite: id_tramite, operacion: 'mostrarSelect' }
        }).done(function (dataSelect) {
            dataSelect.data.forEach((select) => {
                select_turnar.innerHTML += `<option class="text-center" value="${select.id_user}">${select.nombre}</option>`;
            });

            $.ajax({
                method: 'GET',
                url: '/mascarasCaptura/php/seguimientoTramiteRecibidas.php',
                data: { id_tramite: id_tramite, operacion: 'mostrarTurnos' }
            }).done(function (dataDatos) {
                // Generar el contenido HTML
                if (dataDatos.data.length !== 0) {
                    vista_turnos.innerHTML = '';
                    dataDatos.data.forEach(datos => {
                        const nuevoDiv = document.createElement("div");
                        nuevoDiv.id = datos.id_seguimiento;
                        nuevoDiv.classList.add("border", "rounded", "col-12", "m-1"); // Si necesitas añadir más clases adicionales
                        nuevoDiv.innerHTML = `
                            <p>${datos.nombre_completo}</p>
                        `;

                        vista_turnos.appendChild(nuevoDiv);
                    });

                    // Seleccionar todos los divs hijos de la vista previa
                    let divsEnVistaTurnos = vista_turnos.querySelectorAll('div');
                    clickCampos(divsEnVistaTurnos);

                } else if (dataDatos.data.length === 0) {
                    // sin_datos.innerText = 'No hay datos para mostrar.';
                    let contenidoHTML = `
                            <div id="sinDatos" class="col-12 m-1">
                                <h5>No hay turnos para mostrar.</h5>
                            </div>
                        `;
                    vista_turnos.innerHTML = contenidoHTML;
                }
            });

        });
    });

    btn_turnar.addEventListener('click', (e) => {
        e.preventDefault();
        $.ajax({
            method: 'POST',
            url: '/mascarasCaptura/php/seguimientoTramiteRecibidas.php',
            data: { id_tramite: id_tramite, id_user: select_turnar.value, operacion: 'insertarTurno' }
        }).done(function (data) {
            if (data.icon === 'error') {
                Swal.fire({
                    title: data.status,
                    text: data.msg,
                    icon: data.icon,
                    confirmButtonText: 'Ok'
                });
                return;
            } else if (data.icon === 'success') {
                Swal.fire({
                    title: data.status,
                    text: data.msg,
                    icon: data.icon,
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            method: 'GET',
                            url: '/mascarasCaptura/php/seguimientoTramiteRecibidas.php',
                            data: { operacion: 'enviarCorreoTurno', id_tramite: id_tramite, turno: data.data.id_turno }
                        }).done(function (data) {
                            if (data.icon == 'success') {
                                // Recargar la página
                                window.location.reload();
                            }
                        });

                        // Verificar si el elemento existe en caso de que sea el primer turnado
                        if (document.getElementById('sinDatos')) {
                            // Si existe, eliminar el elemento
                            document.getElementById('sinDatos').remove();
                        }

                        let contenidoHTML = vista_turnos.innerHTML;
                        let nuevoContenidoHTML = `
                            <div id="${data.data.id_turno}" class="col-12 border rounded m-1">
                                <p>${data.data.nombre_completo}</p>
                            </div>
                        `;



                        // Asignar el contenido al div
                        vista_turnos.innerHTML = contenidoHTML + nuevoContenidoHTML;

                        // Seleccionar todos los divs hijos de la vista previa
                        let divsEnVistaTurnos = vista_turnos.querySelectorAll('div');
                        clickCampos(divsEnVistaTurnos);

                        // Cambiar status a turnado en la pantalla principal
                        txt_status.innerText = 'Turnado';
                    }
                });
                return;
            }
        });
    });

    function clickCampos(divsEnVistaTurnos) {
        divsEnVistaTurnos.forEach(function (div) {
            // Agregar un evento de clic a cada div
            div.addEventListener('click', function () {
                Swal.fire({
                    title: 'Eliminar Turno',
                    text: '¿Seguro(a) que desea elimnar el turno?',
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
                        // Acciones que deseas realizar cuando se hace clic en el div
                        id_turno = div.id;
                        $.ajax({
                            method: 'POST',
                            url: '/mascarasCaptura/php/seguimientoTramiteRecibidas.php',
                            data: { id_turno: id_turno, operacion: 'eliminarTurno' }
                        }).done(function (data) {
                            let divAEliminar = document.getElementById(data.id_turno); // Obtener el div por su ID
                            if (divAEliminar) { // Verificar si se encontró un div con el ID proporcionado
                                divAEliminar.parentNode.removeChild(divAEliminar); // Eliminar el div del DOM
                            }
                        })
                    }
                });
                return;
            });
        });
    }

});
