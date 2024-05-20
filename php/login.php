<?php
    require( '../db/connection.php' );
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        try {

            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Sanitizar el correo electrónico
            $password = $_POST['password'];
            $camposVacios = array();

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

            // Consulta para obtener el hash de la contraseña asociado al usuario
            $sql = "SELECT cu.id_cat_user, cu.password_user, cu.nombre_completo, cu.id_cat_estructura, ce.nombre AS nombre_estructura, cu.rol_user, cu.direccion_padre
            FROM cat_user cu
            INNER JOIN cat_estructuras ce ON ce.id_cat_estructura = cu.id_cat_estructura
            WHERE email_user = $1";
            $result = pg_query_params($connection, $sql, array($email));

            if ($row = pg_fetch_assoc($result)) {
                // Hash de contraseña almacenado en la base de datos
                $passwordHash = $row['password_user'];

                // Verificar si la contraseña proporcionada coincide con el hash almacenado
                if (password_verify($password, $passwordHash)) {
                    session_start();
                    // Guardar la información en las variables 
                    $_SESSION['nombre_completo'] = $row['nombre_completo'];
                    $_SESSION['id_cat_estructura'] = $row['id_cat_estructura'];
                    $_SESSION['nombre_estructura'] = $row['nombre_estructura'];
                    $_SESSION['id_cat_user'] = $row['id_cat_user'];
                    $_SESSION['email'] = $email;
                    $_SESSION['rol'] = $row['rol_user'] ;
                    $_SESSION['direccion_padre'] = $row['direccion_padre'];

                    // Configurar la cookie de sesión con atributos de seguridad
                    $session_name = session_name();
                    setcookie($session_name, session_id(), null, '/', null, true, true); // HTTPOnly y Secure

                    header('Content-Type: application/json');
                    $data = array(
                        'icon' => "success"
                    );
                    echo json_encode($data);
                    return;
                } else {
                    // Contraseña incorrecta
                    header('Content-Type: application/json');
                    $data = array(
                        'msg' => 'Nombre de usuario o contraseña incorrectos.',
                        'icon' => "warning",
                        'status' => "Intente de nuevo."
                    );
                    echo json_encode($data);
                    return;
                }
            } else {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Correo no registrado.',
                    'icon' => "warning",
                    'status' => "Verificar correo electrónico."
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
?>