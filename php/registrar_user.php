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

                    $sqlInsert = "INSERT INTO cat_user ( email_user, password_user, id_cat_estructura, fec_registro, nombre_completo ) VALUES ( $1, $2, $3, $4, $5 );";
                    $resultInsert = pg_query_params($connection, $sqlInsert, array( $email, $hashPassword, $id_estructura, $fecha_actual, $nombre ));

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