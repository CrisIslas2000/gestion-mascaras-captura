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
    <title>Mascaras captura</title>
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
    <div class="container  p-3 mt-2 fw-bold">
        <div class='row mb-3'>

            <div class="d-flex gap-5 justify-content-around bg-dark bg-body-tertiary rounded-3">
                <label id="nombreUsuario" class="text-black">Nombre de usuario</label>
                <label id="dependenciaUsuario" class="text-black">Dependencia</label>
            </div>

            <div class="d-flex flex-column align-items-center p-2 mt-3">
                <div class="col-lg-5 col-md-5 col-sm-12 mb-2 d-flex flex-column">
                    <label for="txtAsunto" id="" class="form-label text-center">Asunto</label>
                    <textarea rows="4" name="txtAsunto" id="txtAsunto" class="form-control rounded-4"></textarea>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 mb-2 d-flex flex-column">
                    <label for="txtfechaVencimiento" id="" class="form-label text-center">Fecha de vencimiento</label>
                    <input type="date" id="txtfechaVencimiento" name="txtfechaVencimiento" class="form-control text-center" />
                </div>
            </div>
        </div>

        <h1 class="text-center text-uppercase">Mascara de captura</h1>
        <p></p>

        <div class="d-flex justify-content-center">
            <div class="d-flex flex-column justify-content-center">
                <label for="select-formulario" class="text-center">Selecciona una opción</label>
                <select id="select-formulario" class="form-select">
                    <option class="text-center" default value=0>Selecciona un formulario</option>
                </select>
            </div>
        </div>

        <!-- Aqui se mostraran las mascaras de captura dinamicamente -->
        <form id="form-content" class=" row mt-3 "></form>
        <!-- form-control text-center -->

    </div>
    <script src="./bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./sweetAlert/sweetalert2@11.js"></script>
    <script src="./jquery/jquery-3.7.1.min.js"></script>
    <script src="./Js/mascaras-dinamicas.js"></script>
</body>

</html>