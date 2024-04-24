document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('btn-ingresar').addEventListener("click", (e) => {
        e.preventDefault();

        let formData = new FormData(document.getElementById('form-sesion'));

        $.ajax({
            type: 'POST',
            url: '/mascarasCaptura/php/login.php',
            data: formData,
            processData: false,
            contentType: false
        }).done(function (data) {
            if (data.icon === 'success') {
                // Redireccionar a una solicitud
                window.location.href = "./solicitudes.php";
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