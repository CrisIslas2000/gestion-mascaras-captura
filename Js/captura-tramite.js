document.addEventListener('DOMContentLoaded', () => {
    let txtAsunto = document.getElementById('txtAsunto');
    let txtfechaVencimiento = document.getElementById('txtfechaVencimiento');
    let select_tramite = document.getElementById('select-tramite');
    let select_nivel_1 = document.getElementById('select-nivel-1');
    let select_nivel_2 = document.getElementById('select-nivel-2');
    let select_nivel_3 = document.getElementById('select-nivel-3');
    let select_nivel_4 = document.getElementById('select-nivel-4');
    let select_nivel_5 = document.getElementById('select-nivel-5');
    let select_nivel_6 = document.getElementById('select-nivel-6');
    let form_content = document.getElementById('form-content');
    const btnEnviar_tramite = document.getElementById('btnEnviar_tramite');

    let optionDefault = `<option default value="N/A">Seleccione una opción</option>`;
    let noAplica = `<option default value="N/A">N/A</option>`;

    // // Mostramos el primer select 
    // mostrarPrimerSelect();

    // Mostramos los primeros selct por default
    mostrarSelectNivel(select_nivel_1.value, 2);
    //Mostramos los tramites de acuerdo id_estructura
    mostrarSelectTramite(select_nivel_1.value);

    // Opciones para llenar los select de forma dinamica de acuerdo a la eleccion
    select_nivel_2.addEventListener('change', (e) => {
        deshabilitarSelect(3);
        let id_estructura = e.target.value;
        llenarSelectNivel(id_estructura, 3);
        llenarSelectTramite(id_estructura, select_nivel_1.value);
    });

    select_nivel_3.addEventListener('change', (e) => {
        deshabilitarSelect(4);
        let id_estructura = e.target.value;
        llenarSelectNivel(id_estructura, 4);
        llenarSelectTramite(id_estructura, select_nivel_2.value);
    });

    select_nivel_4.addEventListener('change', (e) => {
        deshabilitarSelect(5);
        let id_estructura = e.target.value;
        llenarSelectNivel(id_estructura, 5);
        llenarSelectTramite(id_estructura, select_nivel_3.value);
    });

    select_nivel_5.addEventListener('change', (e) => {
        deshabilitarSelect(6);
        let id_estructura = e.target.value;
        llenarSelectNivel(id_estructura, 6);
        llenarSelectTramite(id_estructura, select_nivel_4.value);
    });

    select_nivel_6.addEventListener('change', (e) => {
        let id_estructura = e.target.value;
        llenarSelectTramite(id_estructura, select_nivel_5.value);
    });

    let selectValue;
    let nombreTramite;
    /* Obtener el valor de la opción seleccionada */
    select_tramite.addEventListener('change', (e) => {
        selectValue = e.target.value;

        let selectedOption = Array.from(select_tramite.options).find(option => option.value === selectValue);
  
        // Obtener el texto de la opción seleccionada
        nombreTramite = selectedOption ? selectedOption.text : "";
        
        /* Vaciar formulario */
        form_content.innerHTML = '';

        if (selectValue === 0 || selectValue === '0') {
            form_content.innerHTML = '';
            return;
        }

        /* Petición para obtener y mostrar el formulario (mascara de captura) desde la base de datos */
        $.ajax({
            method: 'GET',
            url: '/mascarasCaptura/php/imprimirFormulario.php',
            data: { operacion: 'mostrarFormulario', selectValue: selectValue }
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
                <div class="row mb-3 justify-content-center">
                    <button id="btnEnviar_tramite" class="col-lg-3 col-md-4 col-sm-6 col-4 btn btn-secondary" type="submit">Enviar</button>
                </div>
                `;

            document.getElementById('btnEnviar_tramite').addEventListener("click", (e) => {
                e.preventDefault();
                Swal.fire({
                    title: 'Enviar Trámite',
                    text: '¿Seguro(a) que desea enviar el trámite?',
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
                        let formData = new FormData(document.getElementById('form-content'));
                        let asunto = document.getElementById("txtAsunto").value;
                        let fecha_vencimiento = document.getElementById("txtfechaVencimiento").value;

                        formData.append("txtAsunto", asunto);
                        formData.append("txtfechaVencimiento", fecha_vencimiento);
                        formData.append("mascaraCaptura", selectValue);
                        formData.append("select_nivel_1", select_nivel_1.value);
                        formData.append("select_nivel_2", select_nivel_2.value);
                        formData.append("select_nivel_3", select_nivel_3.value);
                        formData.append("select_nivel_4", select_nivel_4.value);
                        formData.append("select_nivel_5", select_nivel_5.value);
                        formData.append("select_nivel_6", select_nivel_6.value);

                        // formData.forEach((data, index) => {
                        //     console.log(index, ': ', data);
                        // });

                        $.ajax({
                            type: 'POST',
                            url: '/mascarasCaptura/php/insertarDatos.php',
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
                                        $.ajax({
                                            method: 'GET',
                                            url: '/mascarasCaptura/php/insertarDatos.php',
                                            data: { operacion: 'enviarCorreo', form: selectValue , mensaje: asunto, asunto: nombreTramite }
                                        }).done(function (data) {
                                            if (data.icon == 'success') {
                                                // Recargar la página
                                                window.location.reload();   
                                            }
                                        });
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
                    }
                });
                return;
            });
        });
    });

    function mostrarSelectNivel(id_estructura, nivel) {
        if (nivel == 3) {// Enviar datos mediante AJAX para rellenar los select con las estructuras de las dependencias
            $.ajax({
                type: 'POST',
                url: '/mascarasCaptura/php/capturaTramite.php',
                data: { operacion: 'llenarSelectNivel3', id_estructura: id_estructura },
            }).done(function (dataSelect) {
                if (dataSelect.data.length === 0) {
                    document.getElementById(`select-nivel-${nivel}`).disabled = true;
                    document.getElementById(`select-nivel-${nivel}`).innerHTML = noAplica;
                } else {
                    dataSelect.data.forEach((select) => {
                        document.getElementById(`select-nivel-${nivel}`).innerHTML += `<option value="${select.id_estructura}">${select.nombre_estructura}</option>`;
                    });
                }
            });
        } else {
            // Enviar datos mediante AJAX para rellenar los select con las estructuras de las dependencias
            $.ajax({
                type: 'POST',
                url: '/mascarasCaptura/php/capturaTramite.php',
                data: { operacion: 'llenarSelectNivel', id_estructura: id_estructura },
            }).done(function (dataSelect) {
                if (dataSelect.data.length === 0) {
                    document.getElementById(`select-nivel-${nivel}`).disabled = true;
                    document.getElementById(`select-nivel-${nivel}`).innerHTML = noAplica;
                } else {
                    dataSelect.data.forEach((select) => {
                        document.getElementById(`select-nivel-${nivel}`).innerHTML += `<option value="${select.id_estructura}">${select.nombre_estructura}</option>`;
                    });
                }
            });
        }
    }

    function llenarSelectNivel(id_estructura, count) {
        let nivel = count;
        if (id_estructura === 'N/A') {
            for (let i = count; i <= 6; i++) {
                document.getElementById(`select-nivel-${i}`).disabled = true;
                document.getElementById(`select-nivel-${i}`).innerHTML = noAplica;
            }
        } else if (id_estructura !== 'N/A') {
            document.getElementById(`select-nivel-${count}`).innerHTML = optionDefault;
            mostrarSelectNivel(id_estructura, nivel);
            document.getElementById(`select-nivel-${count}`).disabled = false;
        }
    }

    function mostrarSelectTramite(id_estructura) {
        // Enviar datos mediante AJAX
        $.ajax({
            type: 'POST',
            url: '/mascarasCaptura/php/capturaTramite.php',
            data: { operacion: 'llenarSelectTramite', id_estructura: id_estructura },
        }).done(function (dataSelect) {
            if (dataSelect.data.length === 0) {
                select_tramite.innerHTML = noAplica;
            } else {
                select_tramite.innerHTML = optionDefault;
                dataSelect.data.forEach((select) => {
                    select_tramite.innerHTML += `<option value="${select.id_tramite}">${select.nombre_tramite}</option>`;
                });
            }
        });
    }

    function llenarSelectTramite(id_estructura, valorAnterior) {
        if (id_estructura === "N/A") {
            mostrarSelectTramite(valorAnterior);
        } else {
            mostrarSelectTramite(id_estructura);
        }
    }

    function deshabilitarSelect(count) {
        for (let i = count; i <= 6; i++) {
            document.getElementById(`select-nivel-${i}`).disabled = true;
            document.getElementById(`select-nivel-${i}`).innerHTML = noAplica;
        }
    }

});