<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/custom.css">
    <link rel="stylesheet" href="./bootstrap/bootstrap.min.css">
    <title>Registros</title>
</head>
<body class="bg-secondary">
    <div class="container text-bg-dark p-3 mt-2 fw-bold d-flex flex-column justify-content-center">
        
        <div class='row mb-3'>
            <div class="d-flex gap-5 justify-content-around bg-dark bg-body-tertiary rounded-3">
                <label id="nombreUsuario" class="text-black">Nombre de usuario</label>
                <label id="dependenciaUsuario" class="text-black">Dependencia</label>
            </div>
        </div>

        <h1 class="text-center text-uppercase">Registros</h1>
        <p></p>
        <div class="d-flex justify-content-center">
            <div class="d-flex flex-column justify-content-center">
                <label for="select-formulario" class="text-center">Selecciona una opción</label>
                <select id="select-formulario" class="form-select">
                    <option class="text-center" default value=0>Selecciona un formulario</option>
                </select>
            </div>
        </div>
        <div class="table-responsive mt-3">
            <table id="tabla-registros" class="table">
            </table>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content text-bg-dark"> <!-- bg-secondary -->
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Editar registros</h1>
                        <button type="button" class="btn-close text-bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex justify-content-center">
                        <!-- Aqui se mostraran las mascaras de captura dinamicamente -->
                        <form id="form-content" class="row mt-3 container"></form>

                        <iframe src="./uploads/examen-egel-marzo-2023.pdf" style="width: 100%; height: 600px;"></iframe>';
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="./Bootstrap/bootstrap.bundle.min.js"></script>
<script src="./sweetAlert/sweetalert2@11.js"></script>
<script src="./jquery/jquery-3.7.1.min.js"></script>
<script src="./operacionesRegistros.js"></script>
</body>
</html>