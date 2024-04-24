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
    <link href="./dataTables/datatables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
    <title>Editar formulario</title>
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
    <div class="container p-3 mt-2 fw-bold ">
        <h1 class="text-center text-uppercase form-label">Formularios</h1>
        <p></p>
        <div class="d-flex justify-content-center">
            <div class="d-flex flex-column justify-content-center">
                <button type="button" class="form-control" data-bs-toggle="modal" data-bs-target="#modalFormulario" id="detalle-formulario">Agregar campo</button>
            </div>
        </div>
        <table id="tablaCampos" class="display" style="width:90%">
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
            <div class="modal-dialog modal-xl">
                <div class="modal-content text-dark">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5 " id="modalFormularioLabel">Creación de campos</h1>
                        <button id="" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row m-1">
                            <div class="col-lg-8 col-md-8 col-sm-12 border-end">
                                <div class="row rounded">
                                    <h4>Estructura de los campos</h4>
                                    <h5 id="sinDatos"></h5>
                                    <div class="row" id="vistaPrevia">

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="row">
                                    <input type="text" class="form-control" id="id_campo" name="id_campo" hidden>
                                    <form id="crear_campo">
                                        <h4>LLene los datos a requerir</h4>
                                        <div class="mb-3">
                                            <label for="select-tipo" class="col-form-label">Tipo de campo:</label>
                                            <select id="select-tipo" name="select-tipo" class="form-select">
                                                <option class="text-center" default value="0">Seleccione una opción</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="titulo_campo" class="col-form-label">Titulo de campo:</label>
                                            <input type="text" class="form-control" id="titulo_campo" name="titulo_campo">
                                        </div>
                                        <div class="mb-3">
                                            <label for="variable_campo" class="col-form-label">Nombre de la variable del campo:</label>
                                            <input type="text" class="form-control text-lowercase" name="variable_campo" id="variable_campo">
                                        </div>
                                        <div class="mb-3">
                                            <label for="select-catalogo" class="col-form-label">Catalogo:</label>
                                            <select id="select-catalogo" name="select-catalogo" class="form-select">
                                                <option class="text-center" default value="0">Seleccione una opción</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="select-columnas" class="col-form-label">Cantidad de espacio a ocupar:</label>
                                            <select id="select-columnas" name="select-columnas" class="form-select">
                                                <option class="text-center" default value="0">Seleccione una opción</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 ">
                                            <button type="submit" class="btn btn-primary" id="agregar-campo">Agregar campo</button>
                                            <button class="btn btn-primary" id="editar-campo" style="display: none;">Editar campo</button>
                                            <button class="btn btn-danger" id="eliminar-campo" style="display: none;">Eliminar campo</button>
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