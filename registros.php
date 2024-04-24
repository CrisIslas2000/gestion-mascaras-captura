<?php
    session_start(); // Iniciar la sesión

    // Verificar si el usuario está autenticado
    if (!isset($_SESSION["nombre_completo"])) {
        header("Location: login.php"); // Redirigir al formulario de inicio de sesión si no está autenticado
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./bootstrap/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
    <title>Registros</title>
</head>

<body class="">
    <nav class="navbar navbar-expand-sm navbar-light bg-body-tertiary shadow-lg">
        <div class="container-fluid">
            <a class="navbar-brand bi bi-caret-left-fill" href="formularios.php"></a>
            <img src="./img/logo_oficialia.jpg" alt="logo_oficilia_mayor" class="navbar-brand" width="20%">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0"> <!-- "ms-auto" para alinear a la derecha en tamaños de pantalla grandes -->
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="formularios.php">Formularios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="solicitudes.php">Solicitud</a>
                    </li>
                </ul>
            </div>
            <a class="btn btn-secondary" href="logout.php">Cerrar Sesión</a>
        </div>
    </nav>
    <div class="container p-3 mt-2 fw-bold">

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
                <div class="modal-content text-bg-dark">
                    <!-- bg-secondary -->
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Editar registros</h1>
                        <button type="button" class="btn-close text-bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex justify-content-center">
                        <!-- Aqui se mostraran las mascaras de captura dinamicamente -->
                        <form id="form-content" class="row mt-3 container"></form>
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
    <script src="./Js/registrarTratimte.js"></script>
</body>

</html>