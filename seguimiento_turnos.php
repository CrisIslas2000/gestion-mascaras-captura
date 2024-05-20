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
    <link rel="stylesheet" href="./styles/style_modal.css">
    <link rel="stylesheet" href="./bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="./styles/custom.css">
    <link href="./dataTables/datatables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
    <title>Mascaras captura</title>
</head>

<body class="">
    <nav class="navbar navbar-expand-sm navbar-light bg-body-tertiary shadow-lg  bg-navbar">
        <div class="container-fluid">
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
                        <a class="nav-link active" aria-current="page" href="solicitudes_enviadas.php">Enviados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="solicitudes_recibidas.php">Recibidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="solicitudes_turnadas.php">Turnos</a>
                    </li>
                    <li class="nav-item">
                        <a class=" nav-link active " aria-current="page" href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container p-3 mt-2 fw-bold">
        <div class='row mb-3'>
            <div class="d-flex justify-content-around rounded-3">
                <a class="navbar-brand bi bi-arrow-left" href="solicitudes_turnadas.php" style="font-size: 1.6rem;"></a>
                <label class="text-black"><?php echo $_SESSION["nombre_completo"]; ?></label>
                <label class="text-black"><?php echo $_SESSION["nombre_estructura"]; ?></label>
            </div>
        </div>
        <div class="row justify-content-center ">
            <div class="row mb-2 text-center">
                <div class="col-12 ">
                    <h5 class="fw-bolder ">Datos generales</h5>
                </div>
            </div>
            <div class="col-12 col-lg-3 col-md-4 col-sm-4">
                <div class="row mb-3">
                    <label class="fw-bolder ">
                        Estatus:
                        <label class="fw-normal" id="txt_status"></label>
                    </label>
                </div>
                <div class="row mb-3 ">
                    <label class="fw-bolder ">
                        Fecha vencimiento:
                        <label class="fw-normal" id="txt_fec_vencimiento"></label>
                    </label>
                </div>
                <div class="row mb-3 ">
                    <label class="fw-bolder ">
                        Usuario:
                        <label class="fw-normal" id="txt_usuario"></label>
                    </label>
                </div>
            </div>
            <div class="col-12 col-lg-3 col-md-4 col-sm-4">
                <div class="row mb-3">
                    <label class="fw-bolder">
                        Dirección:
                        <label class="fw-normal" id="txt_direccion"></label>
                    </label>
                </div>
                <div class="row mb-3">
                    <label class="fw-bolder ">
                        Fecha envío:
                        <label class="fw-normal" id="txt_fec_envio"></label>
                    </label>
                </div>
                <div class="row mb-3">
                    <label class="fw-bolder ">
                        Asunto:
                        <label class="fw-normal" id="txt_asunto"></label>
                    </label>
                </div>
            </div>
            <div class="col-12 col-lg-3 col-md-4 col-sm-4">
                <div class="row mb-3">
                    <label for="txt_comentario" class="form-label fw-bolder">Comentarios: </label>
                    <textarea class="form-control" name="txt_comentario" id="txt_comentario" cols="8" disabled></textarea>
                </div>
            </div>
            <div class="row " id="datosTramite">
                <div class="col-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="row mb-3 text-center">
                        <h5 class="fw-bolder ">Datos formulario trámite</h5>
                    </div>
                    <div class="row mb-3">
                        <form id="form-content" class="row container"></form>
                    </div>
                </div>
            </div>
        </div>


        <div class="row  mb-3">
            <form action="" id="form-turno">
                <div class="col-12 text-center">
                    <h5 class="fw-bolder">Seguimiento turnado</h5>
                </div>
                <div class="row justify-content-center text-center">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3 ">
                        <label for="select_status" class="form-label fw-bolder">Status: </label>
                        <select class="form-select" name="select_status" id="select_status"></select>
                    </div>
                </div>
                <div class="row justify-content-center text-center">
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12 mb-3 ">
                        <label for="txt_comentarioTurno" class="form-label fw-bolder">Comentarios: </label>
                        <textarea class="form-control" name="txt_comentarioTurno" id="txt_comentarioTurno" cols="8"></textarea>
                    </div>
                </div>
                <div class="row justify-content-center text-center">
                    <div class="col-xl-3 col-md-5 col-sm-4 d-flex justify-content-center">
                        <!-- <button type="submit" class="btn btn-secondary" id="btnAprobar-solicitud" data-bs-toggle="modal" data-bs-target="#modal-turnar">Aprobar</button> -->
                        <button type="submit" class="btn btn-secondary" id="btnActualizar_turno">Actualizar turno</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal para turnar -->
    <div class="modal fade" id="modal-turnar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Turnar solicitud</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-body myModal">
                        <div class="container-fluid">
                            <div class="row">
                                <div class=" col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <div class="row mb-3" id="mostrarTurnos">

                                    </div>
                                </div>
                                <div class=" col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <div class="row mb-3">
                                        <div class="col-sm-12 col-md-10 col-lg-10">
                                            <label for="select-turnar" class="text-center">Persona a turnar:</label>
                                            <select name="select-turnar" id="select-turnar" class="form-select">
                                                <option default value=0>Selecione persona a turnar</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-12 col-md-10 col-lg-10">
                                            <button type="button" class="btn btn-secondary" id="btnTurnar-solicitud">Turnar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <script src="./bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./sweetAlert/sweetalert2@11.js"></script>
    <script src="./jquery/jquery-3.7.1.min.js"></script>
    <script src="./dataTables/datatables.min.js"></script>
    <script src="./Js/seguimientoTramiteTurnado.js"></script>
</body>

</html>