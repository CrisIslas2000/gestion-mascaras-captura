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
            <a class="navbar-brand bi bi-caret-left-fill" href="solicitudes.php"></a>
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
        <!-- <form id="form-solicitud"> -->
            <div class='row mb-3'>
                <div class="d-flex justify-content-around bg-dark bg-body-tertiary rounded-3">
                    <label id="nombreUsuario" class="text-black">Nombre de usuario</label>
                    <label id="dependenciaUsuario" class="text-black">Dependencia</label>
                </div>
            </div>
            <div class="row mb-3 justify-content-center">
                <div class="col-lg-6 col-md-8 col-sm-10 ">
                    <label for="txtAsunto" id="" class="form-label text-center">Asunto</label>
                    <textarea rows="3" name="txtAsunto" id="txtAsunto" class="form-control rounded-4"></textarea>
                </div>
            </div>
            <div class="row mb-3 justify-content-center">
                <div class="col-lg-5 col-md-7 col-sm-8 ">
                    <label for="txtfechaVencimiento" id="" class="form-label text-center">Fecha de vencimiento</label>
                    <input type="date" id="txtfechaVencimiento" name="txtfechaVencimiento" class="form-control text-center" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <label for="select-nivel-1" class="text-center">Nivel 1:</label>
                    <select name="select-nivel-1" id="select-nivel-1" class="form-select">
                        <option default value=1>Gobierno del Estado de Hidalgo</option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <label for="select-nivel-2" class="text-center">Nivel 2:</label>
                    <select name="select-nivel-2" id="select-nivel-2" class="form-select">
                        <option default value="N/A">Seleccione una opción</option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <label for="select-nivel-3" class="text-center">Nivel 3:</label>
                    <select name="select-nivel-3" id="select-nivel-3" class="form-select" disabled>
                        <option default value="N/A">N/A</option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <label for="select-nivel-4" class="text-center">Nivel 4:</label>
                    <select name="select-nivel-4" id="select-nivel-4" class="form-select" disabled>
                        <option default value="N/A">N/A</option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <label for="select-nivel-5" class="text-center">Nivel 5:</label>
                    <select name="select-nivel-5" id="select-nivel-5" class="form-select" disabled>
                        <option default value="N/A">N/A</option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <label for="select-nivel-6" class="text-center">Nivel 6:</label>
                    <select name="select-nivel-6" id="select-nivel-6" class="form-select" disabled>
                        <option default value="N/A">N/A</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <label for="select-tramite" class="text-center">Trámite:</label>
                    <select name="select-tramite" id="select-tramite" class="form-select">
                        <option default value=0>Selecione una opción</option>
                    </select>
                </div>
            </div>
        <!-- </form> -->
        <!-- Aqui se mostraran las mascaras de captura dinamicamente -->
        <form id="form-content" class=" row mt-3 "></form>
        <!-- form-control text-center -->

    </div>
    <script src="./bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./sweetAlert/sweetalert2@11.js"></script>
    <script src="./jquery/jquery-3.7.1.min.js"></script>
    <script src="./Js/captura-tramite.js"></script>
</body>

</html>