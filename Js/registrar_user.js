document.addEventListener('DOMContentLoaded', () => {

    // let form_content = document.getElementById('form-content');
    let select_estructura = document.getElementById('select-estructura');

    /* Solicitud para obtener las opciones del select */
    $.ajax({
        method: 'GET',
        url: '/mascarasCaptura/php/mostrarSolicitudes.php',
        data: { operacion: 'dataSelect' }
    }).done(function (dataSelect) {
        /* Imprimir las opciones */
        dataSelect.data.forEach((select) => {
            select_estructura.innerHTML += `<option class="text-center" value="${select.id_cat_estructura}">${select.nombre_estructura}</option>`;
        });

    });

    document.getElementById('btnRegistrar-user').addEventListener("click", (e) => {
        e.preventDefault();

        // Obtener el elemento select
        let id_estructura = document.getElementById("select-estructura");

        let formData = new FormData(document.getElementById('form-registrar'));
        formData.append("id_estructura", id_estructura.value);;

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