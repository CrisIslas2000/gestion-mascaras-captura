<?php
    session_start(); // Iniciar la sesión

    // Verificar si el usuario está autenticado
    if (isset($_SESSION["nombre_completo"])) {
        header("Location: solicitudes.php"); // Redirigir al formulario de inicio de sesión si no está autenticado
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
    <link href="./dataTables/datatables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
    <title>Mascaras captura</title>
</head>

<header>
    <nav class="navbar navbar-expand-sm navbar-light bg-body-tertiary shadow-lg">
        <div class="container-fluid">
            <img src="./img/logo_oficialia.jpg" alt="logo_oficilia_mayor" class="navbar-brand" width="20%">
            <h1>Gestión</h1>
        </div>
    </nav>
</header>

<body class="bg-secondary">
    <div class=" align-self-center"></div>
    <div class="container text-center fw-bolder">
        <div class="row justify-content-center ">
            <div class="col-lg-5 col-md-8 col-sm-10 col-11 mt-5 ">
                <div class="bg-white shadow rounded">
                    <div class="col-12 ">
                        <div class=" py-5 px-5">
                            <form id="form-sesion" class="row g-4">
                                <div class="col-12 mb-2">
                                    <label>Iniciar Sesión</label>
                                </div>
                                <div class="col-12">
                                    <label for="email">Correo:<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="bi bi-person-fill"></i></div>
                                        <input type="email" id="email" name="email" class="form-control" placeholder="Ingresar correo institucional">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="password">Contraseña:<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="bi bi-lock-fill"></i></div>
                                        <input type="password" id="password" name="password" class="form-control" placeholder="Ingresar contraseña">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <a href="registrar_user.php">Registrarse</a>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-secondary px-4 float-center mt-4" id='btn-ingresar'>Ingresar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./sweetAlert/sweetalert2@11.js"></script>
    <script src="./jquery/jquery-3.7.1.min.js"></script>
    <script src="./dataTables/datatables.min.js"></script>
    <script src="./Js/login.js"></script>
</body>

</html>