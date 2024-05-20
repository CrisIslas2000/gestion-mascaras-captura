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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
    <title>Editar formulario</title>
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
                <a class="navbar-brand bi bi-arrow-left" href="formularios.php" style="font-size: 1.6rem;"></a>
                <label class="text-black" id="<?php echo $_SESSION["id_cat_user"]; ?>"><?php echo $_SESSION["nombre_completo"]; ?></label>
                <label class="text-black"><?php echo $_SESSION["nombre_estructura"]; ?></label>
            </div>
        </div>
        <h5 class="text-center text-uppercase form-label fw-bold">Campos</h5>
        <p></p>
        <div class="d-flex justify-content-center">
            <div class="d-flex flex-column justify-content-center">
                <button type="button" class="form-control btn-secondary btn" data-bs-toggle="modal" data-bs-target="#modalFormulario" id="detalle-formulario">Agregar campo</button>
            </div>
        </div>
        <table id="tablaCampos" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Tipo campo</th>
                    <th>Titulo campo</th>
                    <th>Nombre campo</th>
                    <th>Cantidad espacios</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <!-- Modal para crear el formulario -->
        <div class="modal fade" id="modalFormulario" tabindex="-1" aria-labelledby="modalFormularioLabel" aria-hidden="true">
            <div class="modal-dialog  modal-xl">
                <div class="modal-content text-dark">
                    <div class="modal-header">
                        <h1 class="modal-title fs-4 text-center" id="modalFormularioLabel">Creación de campos</h1>
                        <button id="" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row ">
                            <div class="col-lg-8 col-md-8 col-sm-12 border-end">
                                <div class="row rounded">
                                    <h5 class="text-center fw-bold">Estructura de los campos</h5>
                                    <p id="sinDatos"></p>
                                    <div class="row ms-1 me-1" id="vistaPrevia">

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="row fw-bold">
                                    <input type="text" class="form-control" id="id_campo" name="id_campo" hidden>
                                    <form id="crear_campo">
                                        <h5 class="text-center fw-bold mb-1">Llene los datos a requerir</h5>
                                        <div class="mb-2">
                                            <label for="select-tipo" class="col-form-label">Tipo de campo:</label>
                                            <select id="select-tipo" name="select-tipo" class="form-select">
                                                <option class="text-center" default value="0">Seleccione una opción</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label for="titulo_campo" class="col-form-label">Titulo de campo:</label>
                                            <input type="text" class="form-control" id="titulo_campo" name="titulo_campo">
                                        </div>
                                        <div class="mb-2">
                                            <label for="variable_campo" class="col-form-label">Nombre de la variable del campo:</label>
                                            <input type="text" class="form-control text-lowercase" name="variable_campo" id="variable_campo">
                                        </div>
                                        <div class="mb-2">
                                            <label for="select-catalogo" class="col-form-label">Catalogo:</label>
                                            <select id="select-catalogo" name="select-catalogo" class="form-select">
                                                <option class="text-center" default value="0">Seleccione una opción</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label for="select-columnas" class="col-form-label">Cantidad de espacio a ocupar:</label>
                                            <select id="select-columnas" name="select-columnas" class="form-select">
                                                <option class="text-center" default value="0">Seleccione una opción</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <div class="row justify-content-center">
                                                <button type="submit" class="btn btn-secondary  col-4 col-lg-4 col-sm-12 m-1" id="agregar-campo">Agregar</button>

                                                <button class="btn btn-secondary col-4 col-lg-4 col-sm-12 m-1" id="editar-campo" style="display: none;">Editar</button>

                                                <button class="btn btn-secondary bt-color-secondary col-4 col-lg-4 col-sm-12 m-1" id="eliminar-campo" style="display: none;">Eliminar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="./jquery/jquery-3.7.1.min.js"></script>
    <script src="./bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./sweetAlert/sweetalert2@11.js"></script>
    <script src="./dataTables/datatables.min.js"></script>
    <script src="./Js/campos-dinamicos.js"></script>
</body>

</html>