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
    <link rel="stylesheet" href="./styles/custom.css">
    <link href="./dataTables/datatables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
    <title>Mascaras captura</title>
</head>

<body class="">
    <?php if ( $_SESSION['rol'] === 'Administrador' ) { ?>
        <nav class="navbar navbar-expand-sm navbar-light bg-body-tertiary shadow-lg bg-navbar">
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
    <?php } elseif ( $_SESSION['rol'] === 'Solicitante' ) {?>
        <nav class="navbar navbar-expand-sm navbar-light bg-body-tertiary shadow-lg bg-navbar">
            <div class="container-fluid">
                <img src="./img/logo_oficialia.jpg" alt="logo_oficilia_mayor" class="navbar-brand" width="20%">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0"> <!-- "ms-auto" para alinear a la derecha en tamaños de pantalla grandes -->
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
    <?php } ?>

    <div class="container p-3 mt-2 ">
        <div class="fw-bold">
            <div class='row mb-3 fw-bold'>
                <div class="d-flex justify-content-around rounded-3">
                    <label class="text-black" id="<?php echo $_SESSION["id_cat_user"]; ?>"><?php echo $_SESSION["nombre_completo"]; ?></label>
                    <label class="text-black"><?php echo $_SESSION["nombre_estructura"]; ?></label>
                </div>
            </div>
            <div class="row mb-3 fw-bold">
                <div class="col-9  justify-content-center">
                    <h1>Tickets enviados</h1>
                </div>
                <div class="col-3 justify-content-end ">
                    <button class="btn btn-outline-secondary bi bi-bar-chart-line shadow"></button>
                </div>
            </div>
            <div class="row mb-3 justify-content-evenly">
                <div class="col-md-3 ms-md-auto ">
                    <a class="text-secondary" href="captura-solicitud.php">+ Nuevo</a>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <table id="tablaSolicitudes" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Asunto</th>
                        <th>Dirección</th>
                        <th>Fecha</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>
    <script src="./bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./sweetAlert/sweetalert2@11.js"></script>
    <script src="./jquery/jquery-3.7.1.min.js"></script>
    <script src="./dataTables/datatables.min.js"></script>
    <script src="./Js/solicitudes_enviadas.js"></script>
</body>

</html>