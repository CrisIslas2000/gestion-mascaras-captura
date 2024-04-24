document.addEventListener('DOMContentLoaded', () => {
    // Obtener la cadena de consulta (query string) de la URL actual
    let queryString = window.location.search;
    // Crear un nuevo objeto URLSearchParams con la cadena de consulta
    let params = new URLSearchParams(queryString);
    // Obtener el valor del parámetro "parametro"
    const id_form = params.get('id_form');

    let form_content = document.getElementById('form-content');
    let select_formulario = document.getElementById('select-formulario');


    let selectValue;
    /* Obtener el valor de la opción seleccionada */
    select_formulario.addEventListener('change', (e) => {
        selectValue = e.target.value;

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
                    <div class="row">
                        <div class="d-flex flex-column align-items-center">
                            <button type="submit" id="btnEnviar" class="btn btn-secondary mt-2">Enviar</button>
                        </div>
                    </div>
                `;

            document.getElementById('btnEnviar').addEventListener("click", (e) => {
                e.preventDefault();

                let formData = new FormData(document.getElementById('form-content'));
                let asunto = document.getElementById("txtAsunto").value;
                let fecha_vencimiento = document.getElementById("txtfechaVencimiento").value;

                formData.append("asunto", asunto);
                formData.append("fecha_vencimiento", fecha_vencimiento);
                formData.append("mascaraCaptura", selectValue);

                /* formData.forEach((data, index) => {
                    console.log(index, ': ', data);
                }); */

                $.ajax({
                    type: 'POST',
                    url: '/mascarasCaptura/php/insertarDatos.php',
                    data: formData,
                    processData: false,
                    contentType: false
                }).done(function (response) {
                    console.log(response)
                });
            });
        });
    });


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
                        <button type="submit" id="btnEnviar" class="btn btn-secondary mt-2">Enviar</button>
                    </div>
                </div>
            `;

            document.getElementById('btnEnviar').addEventListener("click", (e) => {
                e.preventDefault();

                let formData = new FormData(document.getElementById('form-content'));
                let asunto = document.getElementById("txtAsunto").value;
                let fecha_vencimiento = document.getElementById("txtfechaVencimiento").value;

                formData.append("asunto", asunto);
                formData.append("fecha_vencimiento", fecha_vencimiento);
                formData.append("mascaraCaptura", id_form);

                /* formData.forEach((data, index) => {
                    console.log(index, ': ', data);
                }); */

                $.ajax({
                    type: 'POST',
                    url: '/mascarasCaptura/php/insertarDatos.php',
                    data: formData,
                    processData: false,
                    contentType: false
                }).done(function (response) {
                    console.log(response)
                });
            });
        });
    });
});