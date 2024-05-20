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
    <link rel="stylesheet" href="./styles/style_modal.css">
    <link rel="stylesheet" href="./styles/custom.css">
    <link href="./dataTables/datatables.min.css" rel="stylesheet">
    <title>Formularios dinámicos</title>
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
    <div class="container p-3 mt-2  ">
        <div class='row mb-3 fw-bold'>
            <div class="d-flex justify-content-around rounded-3">
                <label class="text-black" id="<?php echo $_SESSION["id_cat_user"]; ?>"><?php echo $_SESSION["nombre_completo"]; ?></label>
                <label class="text-black"><?php echo $_SESSION["nombre_estructura"]; ?></label>
            </div>
        </div>
        <h5 class="text-center text-uppercase form-label fw-bold">Formularios</h5>
        <div class="d-flex justify-content-center fw-bold">
            <form class="row g-3" id="crear-formulario">
                <div class="col-auto">
                    <label for="nombre-formulario" class="form-label">Nombre fromulario</label>
                    <input type="text" class="form-control" id="nombre-formulario" name="nombre-formulario" required>
                </div>
                <div class="col-auto">
                    <label for="variable-formulario" class="form-label">Variable formulario</label>
                    <input type="text" class="form-control" id="variable-formulario" name="variable-formulario" required>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-secondary" id="agregar-formulario" type="submit">Agregar formulario</button>
                </div>
            </form>
        </div>
        <table id="tablaFormularios" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nombre formulario</th>
                    <th>Variable formulario</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <script src="./jquery/jquery-3.7.1.min.js"></script>
    <script src="./bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./sweetAlert/sweetalert2@11.js"></script>
    <script src="./dataTables/datatables.min.js"></script>
    <script src="./Js/formularios.js"></script>
</body>

</html>