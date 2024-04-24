document.addEventListener('DOMContentLoaded', () => {

    // Obtener la cadena de consulta (query string) de la URL actual
    let queryString = window.location.search;
    // Crear un nuevo objeto URLSearchParams con la cadena de consulta
    let params = new URLSearchParams(queryString);
    // Obtener el valor del parámetro "parametro"
    const id_form = params.get('id_form');

    const btnAgregar_campo = document.getElementById('agregar-campo');
    const btnEditar_campo = document.getElementById('editar-campo');
    const btnEliminar_campo = document.getElementById('eliminar-campo');
    const detalle_formulario = document.getElementById('detalle-formulario');
    //  Seleccionar el div para meter los datos
    const vista_previa = document.getElementById('vistaPrevia');
    let select_tipo = document.getElementById('select-tipo');
    let select_columnas = document.getElementById('select-columnas');
    let select_catologos = document.getElementById('select-catalogo');
    let titulo_campo = document.getElementById('titulo_campo');
    let variable_campo = document.getElementById('variable_campo');
    let sin_datos = document.getElementById('sinDatos');
    let id_datos_campo = document.getElementById('id_campo');

    inicializarTabla();

    $('#tablaCampos tbody').on('click', '.btnEditar', function (e) {
        e.preventDefault();

        mostrarFormulario();
        btnEliminar_campo.style.display = 'block';
        btnEditar_campo.style.display = 'block';

        // Obtener el id de la fila
        mostrarCampo($(this).data('id'));
    });

    $('#tablaCampos').on('click', '.btnEliminar', function (e) {
        e.preventDefault();
        // Obtener el id de la fila
        eliminarCampo($(this).data('id'));

    });

    detalle_formulario.addEventListener('click', (e) => {
        e.preventDefault(); // Evita que se envíe el formulario de manera convencional
        btnEliminar_campo.style.display = 'none';
        btnEditar_campo.style.display = 'none';
        btnAgregar_campo.style.display = 'block';
        mostrarFormulario();
    });

    btnAgregar_campo.addEventListener('click', (e) => {
        e.preventDefault(); // Evita que se envíe el formulario de manera convencional
        insertarCampo();
    });

    // Agregar evento de clic al botón de eliminar fuera del bucle forEach
    btnEliminar_campo.addEventListener('click', (e) => {
        e.preventDefault();
        eliminarCampo(id_datos_campo);
        let divAEliminar = document.getElementById(id_datos_campo); // Obtener el div por su ID
        if (divAEliminar) { // Verificar si se encontró un div con el ID proporcionado
            divAEliminar.parentNode.removeChild(divAEliminar); // Eliminar el div del DOM
        }
    });
    
    btnEditar_campo.addEventListener('click', (e) => {
        e.preventDefault();
        editarCampo(id_datos_campo);
        // mostrarCampo(ultimoDivClickeado);
        inicializarTabla();
    });

    select_tipo.addEventListener('change', function () {
        // Verificar si el select-tipo tiene un valor seleccionado
        if (select_tipo.value != 7) {
            // Si tiene un valor, establecer el valor del select-catalogo en default
            select_catologos.value = 0;
        }
    });

    // Escuchar el evento de cambio en el select-catalogo
    select_catologos.addEventListener('change', function () {
        // Verificar si el select-catalogo tiene un valor seleccionado
        if (select_catologos.value != 0) {
            // Si tiene un valor, establecer el valor del select-tipo en default
            select_tipo.value = 7;
        }
    });

    function mostrarFormulario() {
        // Vaciar los datos por si tiene algo lleno
        select_columnas.innerHTML = '';
        select_tipo.innerHTML = '';
        select_catologos.innerHTML = '';
        titulo_campo.value = '';
        variable_campo.value = '';
        //Llenar los option por defecto
        select_columnas.innerHTML = '<option class="text-center" default value="0">Seleccione una opción</option>';
        select_tipo.innerHTML = '<option class="text-center" default value="0">Seleccione una opción</option>';
        select_catologos.innerHTML = '<option class="text-center" default value="0">Seleccione una opción</option>'
        // Solicitud para obtener las opciones del select
        // Escuchar el evento de cambio en el select-tipo

        $.ajax({
            method: 'GET',
            url: '/mascarasCaptura/php/datosCampos.php',
            data: { operacion: 'mostrarSelect' }
        }).done(function (dataSelect) {
            //Imprimir las opciones en los select correspondientes
            dataSelect.dataTags.forEach((select) => {
                select_tipo.innerHTML += `<option class="text-center" value="${select.id_tags_campos}">${select.texto}</option>`;
            });
            dataSelect.dataColumn.forEach((select) => {
                select_columnas.innerHTML += `<option class="text-center" value="${select.id_css_columnas}">${select.texto}</option>`;
            });
            dataSelect.dataCatalogo.forEach((select) => {
                select_catologos.innerHTML += `<option class="text-center" value="${select.id_nombre_catalogo_datos}">${select.nombre_catalogo}</option>`;
            });
            $.ajax({
                method: 'GET',
                url: '/mascarasCaptura/php/datosCampos.php',
                data: { operacion: 'mostrarDatos', id_form: id_form }
            }).done(function (dataDatos) {
                // Generar el contenido HTML
                if (dataDatos.vacio === 'no') {
                    vista_previa.innerHTML = '';
                    dataDatos.data.forEach(datos => {
                        const nuevoDiv = document.createElement("div");
                        nuevoDiv.id = datos.id_datos_campos;
                        const clases = datos.clase_css.split(" "); // Separar las clases por espacios
                        clases.forEach(clase => {
                            nuevoDiv.classList.add(clase); // Agregar cada clase individualmente
                        });
                        nuevoDiv.classList.add("border", "rounded"); // Si necesitas añadir más clases adicionales

                        nuevoDiv.innerHTML = `
                            <h4>${datos.titulo_campo}</h4>
                            <p>${datos.texto_tag}</p>
                            <p>${datos.texto_columnas}</p>
                        `;

                        vista_previa.appendChild(nuevoDiv);
                    });

                    // Seleccionar todos los divs hijos de la vista previa
                    let divsEnVistaPrevia = vista_previa.querySelectorAll('div');
                    clickCampos(divsEnVistaPrevia);

                } else if (dataDatos.vacio === 'si') {
                    sin_datos.innerText = 'No hay datos para mostrar.';
                }
            });
        });
    }

    function inicializarTabla() {
        //Destruimos la tabla si es que existe
        $('#tablaCampos').DataTable().destroy();
        //Creamos nuevamente la tabla
        $('#tablaCampos ').DataTable({
            "ajax": {
                "url": "/mascarasCaptura/php/mostrarCamposTabla.php",
                "type": "GET", // Especifica el método GET
                "data": function(d) {
                    // Agrega parámetros GET aquí si es necesario
                    d.id_form = id_form; // Ejemplo de parámetro
                }
            },
            "language": {
                "url": "./Datatables/es-MX.json",
            },
            "columns": [
                { "data": "tipo_campo" },
                { "data": "titulo_campo" },
                { "data": "nombre_campo" },
                { "data": "texto_columnas" },
                { "data": "id_datos_campos" }
            ],
            "columnDefs": [
                {
                    "targets": -1, // Última columna
                    "render": function (data, type, row) {
                        return `
                            <button class="btn btn-primary btnEditar" data-id="${data}" data-bs-toggle="modal" data-bs-target="#modalFormulario">Editar</button>
                            <button class="btn btn-danger btnEliminar" data-id="${data}">Eliminar</button>
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

    function insertarCampo() {
        sin_datos.innerText = '';
        // Obtiene los datos del formulario
        let formData = new FormData($("#crear_campo")[0]);
        formData.append('id_form', id_form);

        //Enviar datos del formulario al script PHP
        $.ajax({
            type: 'POST',
            url: '/mascarasCaptura/php/insertarCampo.php',
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
                        let contenidoHTML = vista_previa.innerHTML;
                        let nuevoContenidoHTML = `
                            <div id="${data.data[0].id_campo}" class="${data.data[0].clase_css} border rounded">
                                <h4>${data.data[0].titulo_campo}</h4>
                                <p>${data.data[0].texto_tag}</p>
                                <p>${data.data[0].texto_columnas}</p>
                            </div>
                        `;
                        // Asignar el valor del id en el div 
                        id_datos_campo = data.data[0].id_campo;

                        // //  Asignar el contenido al div
                        vista_previa.innerHTML = contenidoHTML + nuevoContenidoHTML;

                        // Seleccionar todos los divs hijos de la vista previa
                        let divsEnVistaPrevia = vista_previa.querySelectorAll('div');
                        clickCampos(divsEnVistaPrevia);

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

    function editarCampo(id_campo) {

        // Obtiene los datos del formulario
        let formData = new FormData($("#crear_campo")[0]);
        // Agregar id_campo y id_fomr al objeto formData
        formData.append('id_campo', id_campo);
        formData.append('id_form', id_form);

        //Enviar datos del formulario al script PHP
        $.ajax({
            type: 'POST',
            url: '/mascarasCaptura/php/editarCampo.php',
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
                        // Obtener el ID dinámico del div
                        let idDivDinamico = data.data.id_campo;

                        // Obtener el div dinámico por su ID
                        let divDinamico = document.getElementById(idDivDinamico);
                        // Actualizar la clase del div
                        divDinamico.className = data.data.clase_css + ' border rounded';
                        // Actualizar el contenido del div con los datos
                        divDinamico.innerHTML = `
                            <h4>${data.data.titulo_campo}</h4>
                            <p>${data.data.texto_tag}</p>
                            <p>${data.data.texto_columnas}</p>
                        `;

                        // Seleccionar todos los divs hijos de la vista previa
                        let divsEnVistaPrevia = vista_previa.querySelectorAll('div');
                        clickCampos(divsEnVistaPrevia);

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

    function eliminarCampo(id_campo) {
        $.ajax({
            method: 'POST',
            url: '/mascarasCaptura/php/mostrarCamposTabla.php',
            data: { operacion: 'eliminarCampo', id_campo: id_campo }
        }).done(function (data) {
            if (data.msg == 'Error') {
                Swal.fire({
                    title: data.status,
                    text: data.msg,
                    icon: data.icon,
                    confirmButtonText: 'Ok'
                })
            } else {
                Swal.fire({
                    title: data.status,
                    text: data.msg,
                    icon: data.icon,
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    if (result.isConfirmed) {
                        //Removerlo de la tabla
                        let tabla = $('#tablaCampos').DataTable();
                        tabla.rows().every(function () { // Itera sobre todas las filas de la tabla
                            let datosFila = this.data(); // Obtiene los datos de la fila
                            if (datosFila['id_datos_campos'] == id_campo) { // Compara el valor de la segunda columna con el valor deseado
                                inicializarTabla();
                            }
                        });

                    }
                });
            }
        });
    }

    function mostrarCampo(id_campo) {
        $.ajax({
            method: 'POST',
            url: '/mascarasCaptura/php/mostrarCamposTabla.php',
            data: { operacion: 'mostrarCampo', id_campo: id_campo }
        }).done(function (data) {
            //Asignamos el valor por defecto en base a campo seleccionado
            Array.from(select_tipo.options).forEach(function (option) {
                // Verificar si el valor de la opción actual coincide con el valor deseado
                if (option.value == data.data.id_tags_campos) {
                    // Establecer el atributo selected en la opción actual
                    option.selected = true;
                }
            });
            //Asignamos el valor por defecto en base a campo seleccionado
            Array.from(select_columnas.options).forEach(function (option) {
                // Verificar si el valor de la opción actual coincide con el valor deseado
                if (option.value == data.data.id_css_columnas) {
                    // Establecer el atributo selected en la opción actual
                    option.selected = true;
                }
            });
            //Asignamos el valor por defecto en base a campo seleccionado
            if (data.data.id_nombre_catalogo_datos === null) {
                // Obtener referencia al elemento option que deseas seleccionar por defecto
                let optionSelect = select_catologos.querySelector('option[value="0"]');
                // Establecer el atributo selected del elemento option
                optionSelect.selected = true;
            } else {
                Array.from(select_catologos.options).forEach(function (option) {
                    // Verificar si el valor de la opción actual coincide con el valor deseado
                    if (option.value == data.data.id_nombre_catalogo_datos) {
                        // Establecer el atributo selected en la opción actual
                        option.selected = true;
                    }
                });
            }

            variable_campo.value = data.data.nombre_campo;
            titulo_campo.value = data.data.titulo_campo;
            id_campo.value = id_campo;

        });
    }

    function clickCampos(divsEnVistaPrevia) {
        // Iterar sobre los divs y agregarles un evento
        divsEnVistaPrevia.forEach(function (div) {
            // Agregar un evento de clic a cada div
            div.addEventListener('click', function () {
                // Acciones que deseas realizar cuando se hace clic en el div
                btnEliminar_campo.style.display = 'block';
                btnEditar_campo.style.display = 'block';
                id_datos_campo = div.id;
                //Mostramos el dato la informacion del dato seleccionado
                mostrarCampo(id_datos_campo);
            });
        });
    }
});