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
    let txt_comentarioTurno = document.getElementById('txt_comentarioTurno');
    let select_status = document.getElementById('select_status');

    let form_content = document.getElementById('form-content');

    let btnActualizar_turno = document.getElementById('btnActualizar_turno');

    $.ajax({
        method: 'GET',
        url: '/mascarasCaptura/php/seguimientoTramiteTurnado.php',
        data: { id_tramite: id_tramite, operacion: 'mostrarTramite' }
    }).done(function (data) {
        txt_status.innerText = data.data.name_status;
        txt_fec_vencimiento.innerText = data.data.fec_vencimiento;
        txt_usuario.innerText = data.data.nombre_user;
        txt_direccion.innerText = data.data.nombre_estructura;
        txt_fec_envio.innerText = data.data.fec_registro;
        txt_asunto.innerText = data.data.asunto;
        txt_comentario.value = data.data.observaciones;

        $.ajax({
            method: 'GET',
            url: '/mascarasCaptura/php/seguimientoTramiteTurnado.php',
            data: { operacion: 'mostrarFormulario', id_tramite: id_tramite }
        }).done(function (dataFormulario) {

            let contenidoHtml = '';

            dataFormulario.data.forEach((campo) => {
                contenidoHtml += campo.htmlTag;
            });
            form_content.innerHTML = contenidoHtml;

            $.ajax({
                method: 'GET',
                url: '/mascarasCaptura/php/seguimientoTramiteTurnado.php',
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

            $.ajax({
                method: 'GET',
                url: '/mascarasCaptura/php/seguimientoTramiteTurnado.php',
                data: { operacion: 'mostrarSeguimientoTurno', id_tramite: id_tramite }
            }).done(function (data) {
                txt_comentarioTurno.value = data.data.comentarios;
                data.status.forEach(status => {
                    select_status.innerHTML += `<option class="text-center" value="${status.id_status}">${status.nombre_status}</option>`;
                });
            })
        });
    });

    btnActualizar_turno.addEventListener('click', (e) => {
        e.preventDefault();
        let formData = new FormData(document.getElementById('form-turno'));
        formData.append("operacion", 'actualizarTurno');
        formData.append("id_tramite", id_tramite);

        // formData.forEach((data, index) => {
        //     console.log(index, ': ', data);
        // });

        $.ajax({
            type: 'POST',
            url: '/mascarasCaptura/php/seguimientoTramiteTurnado.php',
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
            } else if (data.icon === 'warning') {
                Swal.fire({
                    title: data.status,
                    text: data.msg,
                    icon: data.icon,
                    confirmButtonText: 'Ok'
                })
            }
        });
    });
});
