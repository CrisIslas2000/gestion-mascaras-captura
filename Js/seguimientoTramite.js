document.addEventListener('DOMContentLoaded', () => {
    // Obtener la cadena de consulta (query string) de la URL actual
    let queryString = window.location.search;
    // Crear un nuevo objeto URLSearchParams con la cadena de consulta
    let params = new URLSearchParams(queryString);
    // Obtener el valor del parámetro "parametro"
    const id_tramite = params.get('solicitud');
    const id_estructura = params.get('estructura');

    let txt_status = document.getElementById('txt_status');
    let txt_fec_vencimiento = document.getElementById('txt_fec_vencimiento');
    let txt_usuario = document.getElementById('txt_usuario');
    let txt_direccion = document.getElementById('txt_direccion');
    let txt_fec_envio = document.getElementById('txt_fec_envio');
    let txt_asunto = document.getElementById('txt_asunto');
    let txt_comentario = document.getElementById('txt_comentario');

    $.ajax({
        method: 'GET',
        url: '/mascarasCaptura/php/seguimientoTramite.php',
        data: { id_tramite : id_tramite, id_estructura : id_estructura , operacion : 'mostrarTramite' }
    }).done(function (data) {
        console.log(data);
        txt_status.innerText = data.data.name_status;
        txt_fec_vencimiento.innerText = data.data.fec_vencimiento;
        // txt_usuario.innerText = data.data.nombre_envio;
        txt_direccion.innerText = data.data.nombre_estructura;
        txt_fec_envio.innerText = data.data.fec_registro;
        txt_asunto.innerText = data.data.asunto;
    });

})