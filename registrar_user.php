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
    <nav class="navbar navbar-expand-sm navbar-light bg-body-tertiary shadow-lg bg-navbar">
        <div class="container-fluid">
            <img src="./img/logo_oficialia.jpg" alt="logo_oficilia_mayor" class="navbar-brand" width="20%">
            <h1>Gestión</h1>
        </div>
    </nav>

    <div class="container p-3 mt-2 fw-bold">
        <form id="form-registrar">
            <div class="row mb-3 ">
                <div class="d-flex justify-content-around rounded-3">
                    <a class="navbar-brand bi bi-arrow-left" href="login.php" style="font-size: 1.6rem;"></a>
                    <h4>Llene los datos a requerir</h4>
                </div>
                <div class="mb-3 col-xl-8 col-md-8 col-sm-10">
                    <label for="txt_nombre" class="col-form-label">Nombre completo:</label>
                    <input type="text" class="form-control" id="txt_nombre" name="txt_nombre">
                </div>
                <div class="mb-3 col-xl-6 col-md-6 col-sm-10">
                    <label for="select-dependencia" class="text-center col-form-label">Dirección:</label>
                    <select id="select-dependencia" class="form-select justify-content-start">
                        <option class="text-center" default value=0>Seleccione una opción</option>
                    </select>
                </div>
                <div class="mb-3 col-xl-6 col-md-6 col-sm-10">
                    <label for="select-estructura" class="text-center col-form-label">Área:</label>
                    <select id="select-estructura" class="form-select">
                        <option class="text-center" default value=0>Seleccione una opción</option>
                    </select>
                </div>
                <div class="mb-3 col-xl-6 col-md-6 col-sm-10">
                    <label for="txt_email" class="col-form-label">Correo electrónico:</label>
                    <input type="text" class="form-control" id="txt_email" name="txt_email">
                </div>
                <div class="mb-3 col-xl-6 col-md-6 col-sm-10">
                    <label for="select-rol" class="col-form-label">Rol:</label>
                    <select id="select-rol" class="form-select">
                        <option class="text-center" default value=0>Seleccione un rol</option>
                        <option class="text-center" value='Administrador'>Administrador</option>
                        <option class="text-center" value='Solicitante'>Solicitante</option>
                    </select>
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