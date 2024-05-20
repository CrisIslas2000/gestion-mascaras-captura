document.addEventListener('DOMContentLoaded', () => {

    // let form_content = document.getElementById('form-content');
    let select_estructura = document.getElementById('select-estructura');
    let select_dependencia = document.getElementById('select-dependencia');

    /* Solicitud para obtener las opciones del select */
    $.ajax({
        method: 'GET',
        url: '/mascarasCaptura/php/registrar_user.php',
        data: { operacion: 'obtenerDependencia' }
    }).done(function (dataSelect) {
        /* Imprimir las opciones */
        dataSelect.dependencia.forEach((dependencia) => {
            select_dependencia.innerHTML += `<option class="text-center" value="${dependencia.id_cat_estructura}">${dependencia.nombre_estructura}</option>`;
        });
        select_dependencia.addEventListener('change', (e) => {
            let id_estructura = e.target.value;
            $.ajax({
                method: 'GET',
                url: '/mascarasCaptura/php/registrar_user.php',
                data: { operacion: 'obtenerEstructura', id_estructura: id_estructura }
            }).done(function (dataSelect) {
                // Imprimir las opciones y vaciar el select si tiene datos
                select_estructura.innerHTML = '';
                select_estructura.innerHTML = '<option default value=0>Seleccione una opción</option>';
                dataSelect.area.forEach((area) => {
                    select_estructura.innerHTML += `<option class="text-center" value="${area.id_cat_estructura}">${area.nombre_estructura}</option>`;
                });
            });
        });
    });

    document.getElementById('btnRegistrar-user').addEventListener("click", (e) => {
        e.preventDefault();

        // Obtener el elemento select
        let id_estructura = document.getElementById("select-estructura");
        let rol = document.getElementById("select-rol");
        let direccion = document.getElementById("select-dependencia");

        let formData = new FormData(document.getElementById('form-registrar'));
        formData.append("id_estructura", id_estructura.value);
        formData.append("rol", rol.value);
        formData.append("id_direccion", direccion.value);

        // formData.forEach((data, index) => {
        //     console.log(index, ': ', data);
        // });

        $.ajax({
            type: 'POST',
            url: '/mascarasCaptura/php/registrar_user.php',
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
                        window.location.href = './login.php';
                    }
                });
            } else if (data.icon === 'error') {
                Swal.fire({
                    title: data.status,
                    text: data.msg,
                    icon: data.icon,
                    confirmButtonText: 'Ok'
                })
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