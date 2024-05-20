<?php
    require('../db/connection.php');
    // Establece la zona horaria a México
    date_default_timezone_set('America/Mexico_City');
    // Obtiene la fecha y hora actual en México
    $fecha_actual = date('Y-m-d');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dataRs = array();
        try {

            // Verificar cada campo y el guardar los que esten vacios
            foreach ($_POST as $campo => $valor) {
                if (empty($valor)) {
                    $camposVacios[] = $campo;
                }
            }

            if (!empty($camposVacios)) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Faltan campos por llenar',
                    'campos' => $camposVacios,
                    'icon' => "warning",
                    'status' => "Error"
                );
                echo json_encode($data);
                return;
            }

            $id_estructura = $_POST['id_estructura'];
            $nombre = $_POST['txt_nombre'];
            $email = $_POST['txt_email'];
            $password = $_POST['txt_password'];
            $password2 = $_POST['txt_password2'];
            $rol = $_POST['rol'];
            $id_direcion = $_POST['id_direccion'];

            $sql = "SELECT * FROM cat_user WHERE email_user = $1 ;";
            $result = pg_query_params($connection, $sql, array($email));

            if (!$result){
                throw new Exception("Error al insertar datos" . pg_last_error($connection));
                exit;
            }

            while ($row = pg_fetch_array($result)) {
                $dataRs[] = $row;
            }

            $countRows = count($dataRs);
            if ($countRows != 0) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Ya existe cuenta con el correo proporcionado.',
                    'icon' => "warning",
                    'status' => "Error."
                );
                echo json_encode($data);
                return;
            }

            // Validamos que el dominio del correo sea correcto
            if (validarCorreo($email)) {
                // Validamos que la contraseña conicida
                if (validarContra($password, $password2)){

                    // Generar el hash de la contraseña usando bcrypt
                    $hashPassword = password_hash($password, PASSWORD_DEFAULT);

                    $sqlInsert = "INSERT INTO cat_user ( email_user, password_user, id_cat_estructura, fec_registro, nombre_completo, rol_user, direccion_padre ) VALUES ( $1, $2, $3, $4, $5, $6, $7 );";
                    $resultInsert = pg_query_params($connection, $sqlInsert, array( $email, $hashPassword, $id_estructura, $fecha_actual, $nombre, $rol, $id_direcion ));

                    if (!$resultInsert){
                        throw new Exception("Error al insertar datos" . pg_last_error($connection));
                        exit;
                    }

                    // Ejecutamos la insercion
                    header('Content-Type: application/json');
                    $data = array(
                        'msg' => 'Registro guardado exitosamente' ,
                        'icon' => 'success',
                        'status' => 'Exitosamente guardado',
                    );
                    echo json_encode($data);
                    return;
                } else {
                    header('Content-Type: application/json');
                    $data = array(
                        'msg' => 'Verifique que las contraseñas coincidan.' ,
                        'icon' => 'warning',
                        'status' => 'Error de contraseña',
                    );
                    echo json_encode($data);
                    return;
                }
            } else {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Verifique el dominio del correo "@hidalgo.gob.mx" ' ,
                    'icon' => 'warning',
                    'status' => 'Error de correo',
                );
                echo json_encode($data);
                return;
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'status' => 'error',
                    'msg' => 'Error al obtener los datos',
                    'error' => $e->getMessage()
                )
            );
            return;
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET'){
        $operacionGET = $_GET['operacion'];
        if ($operacionGET === 'obtenerDependencia') {
            $dataDependencia = array();
            try {
                /* Obtener datos para llenar el selector de las mascaras */
                $query = "SELECT * FROM cat_estructuras where nivel = $1 or nivel = $2 order by id_cat_estructura ASC;";
                $result = pg_query_params($connection, $query, array( 12, 13 ));
                if (!$result) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }
    
                while ($row = pg_fetch_array($result)) {
                    $dataDependencia[] = array(
                        'id_cat_estructura' => $row['id_cat_estructura'],
                        'nombre_estructura' => $row['nombre']
                    );
                }
                $countRows = count($dataDependencia);
                if ($countRows <= 0) {
                    header('Content-Type: application/json');
                    $data = array(
                        'msg' => 'No hay datos para mostrar'
                    );
                    echo json_encode($data);
                    return;
                }

                /* Enviar resultado */
                header('Content-Type: application/json');
                $data = array(
                    'dependencia' => $dataDependencia
                );
                echo json_encode($data);
                return;
            } catch (Exception $e) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Error al realizar la consulta',
                    'error' => $e->getMessage()
                );
                return;
            }
        } elseif ($operacionGET === 'obtenerEstructura') {
            try{
                $id_estructura = $_GET['id_estructura'];
                $dataArea = array();
                /* Obtener datos para llenar el selector de las mascaras */
                $query = "WITH RECURSIVE estructura_recursiva AS (
                    select id_cat_estructura, estructura_padre, nivel, nombre
                    from cat_estructuras
                    where id_cat_estructura = $1
                    UNION ALL
                    select ce.id_cat_estructura, ce.estructura_padre, ce.nivel, ce.nombre 
                    from cat_estructuras ce
                    join estructura_recursiva r ON r.id_cat_estructura = ce.estructura_padre
                    where ce.nivel < $2
                )
                select id_cat_estructura, estructura_padre, nivel, nombre 
                from estructura_recursiva order by nivel desc;";
                $result = pg_query_params($connection, $query, array($id_estructura, 14));
                if (!$result) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }
    
                while ($row = pg_fetch_array($result)) {
                    $dataArea[] = array(
                        'id_cat_estructura' => $row['id_cat_estructura'],
                        'nombre_estructura' => $row['nombre']
                    );
                }
                $countRows = count($dataArea);
                if ($countRows <= 0) {
                    header('Content-Type: application/json');
                    $data = array(
                        'msg' => 'No hay datos para mostrar'
                    );
                    echo json_encode($data);
                    return;
                }
            
                /* Enviar resultado */
                header('Content-Type: application/json');
                $data = array(
                    'area' => $dataArea
                );
                echo json_encode($data);
                return;
            } catch (Exception $e) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Error al realizar la consulta',
                    'error' => $e->getMessage()
                );
                return;
            }
        }
    }
    
    
    function validarContra($password, $password2){
        if($password === $password2){
            return true;
        } else if ($password !== $password2){
            return false;
        }
    }

    function validarCorreo($email){
        // Verificar si el correo contiene '@' y al menos un punto después de '@'
        if (strpos($email, '@') !== false && strpos($email, '.', strpos($email, '@')) !== false) {
            // Obtener el dominio del correo
            $dominio = substr($email, strpos($email, '@') + 1);
    
            // Verificar si el dominio es 'hidalgo.gob.mx'
            if (substr($dominio, 0, strlen('hidalgo.gob.mx')) === 'hidalgo.gob.mx') {
                return true; // El correo es válido
            }
        }
        return false; // El correo no es válido
    }
     
?>