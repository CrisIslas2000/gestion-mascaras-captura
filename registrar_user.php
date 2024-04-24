
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

<body class="">
    <nav class="navbar navbar-expand-sm navbar-light bg-body-tertiary shadow-lg">
        <div class="container-fluid">
            <img src="./img/logo_oficialia.jpg" alt="logo_oficilia_mayor" class="navbar-brand" width="20%">
            <!--            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="formularios.php">Formularios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="solicitudes.php">Solicitud</a>
            </li>
            </ul>
            </div> -->
        </div>
    </nav>

    <div class="container p-3 mt-2 fw-bold">

        <form id="form-registrar">
            <div class="row mb-3 d-flex justify-content-center">
                <h4>LLene los datos a requerir</h4>
                <div class="mb-3 col-xl-6 col-md-6 col-sm-10">
                    <label for="select-estructura" class="text-center col-form-label">Selecciona una área:</label>
                    <select id="select-estructura" class="form-select">
                        <option class="text-center" default value=0>Selecciona un formulario</option>
                    </select>
                </div>
                <div class="mb-3 col-xl-8 col-md-8 col-sm-10">
                    <label for="txt_nombre" class="col-form-label">Nombre completo:</label>
                    <input type="text" class="form-control" id="txt_nombre" name="txt_nombre">
                </div>
                <div class="mb-3 col-xl-6 col-md-6 col-sm-10">
                    <label for="txt_email" class="col-form-label">Correo electrónico:</label>
                    <input type="text" class="form-control" id="txt_email" name="txt_email">
                </div>
                <div class="mb-3 col-xl-6 col-md-6 col-sm-10">
                    <label for="txt_password" class="col-form-label">Contraseña:</label>
                    <input type="password" class="form-control" id="txt_password" name="txt_password">
                </div>
                <div class="mb-3 col-xl-6 col-md-6 col-sm-10">
                    <label for="txt_password2" class="col-form-label">Confirmar contraseña:</label>
                    <input type="password" class="form-control" id="txt_password2" name="txt_password2">
                </div>
                <div class="mb-3 col-xl-6 col-md-6 col-sm-10">
                    <button type="submit" class="btn btn-secondary" id="btnRegistrar-user">Registrar</button>
                </div>
            </div>
        </form>

    </div>
    <script src="./bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./sweetAlert/sweetalert2@11.js"></script>
    <script src="./jquery/jquery-3.7.1.min.js"></script>
    <script src="./dataTables/datatables.min.js"></script>
    <script src="./Js/registrar_user.js"></script>
</body>

</html>