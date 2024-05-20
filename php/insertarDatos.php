<?php
require('../db/connection.php');

date_default_timezone_set('America/Mexico_City');
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $camposVacios = array();
    // Verificar cada campo y el guardar los que esten vacios
    foreach ($_POST as $campo => $valor) {
        if (($campo !== 'select_nivel_2' && $campo !== 'select_nivel_3'
            && $campo !== 'select_nivel_4' && $campo !== 'select_nivel_5'
            && $campo !== 'select_nivel_6')) {
            if ($valor === 'N/A' || empty($valor)) {
                $camposVacios[] = $campo;
            }
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

    // Define un arreglo con los nombres de las variables $_POST que quieres recorrer
    $selectNivel = array(
        "select_nivel_6", "select_nivel_5", "select_nivel_4",
        "select_nivel_3", "select_nivel_2", "select_nivel_1"
    );

    // Foreach para ignorar los select que tengan un valor de "N/A" y tomar el valor del select anterior 
    foreach ($selectNivel as $nivel) {
        // Verifica si la variable $_POST existe y su valor es diferente de "0"
        if (isset($_POST[$nivel]) && $_POST[$nivel] !== "N/A") {
            // Si el valor es diferente de "0", lo asigna a una nueva variable y rompe el bucle
            $id_estructura = $_POST[$nivel];
            break;
        }
    }


    // Agregamos el id de las estructuras y de usuario al POST
    $_POST['id_cat_estructura'] = $id_estructura;

    // Eliminamos los select-nivel, ya que los obtuvimos
    foreach ($selectNivel as $nivel) {
        unset($_POST[$nivel]);
    }

    // Función para determinar el tipo de dato
    function obtenerTipoDato($valor)
    {
        // Verificar si el valor es un número entero
        if (ctype_digit($valor)) {
            return 'INT';
        }
        // Si no es un número, se asume que es texto
        else {
            return 'TEXT';
        }
    }

    try {

        // Obtener el id de la estructura
        if ($_SESSION['id_cat_user']) {
            $id_user = $_SESSION['id_cat_user'];
        }
        $mascaraCaptura = $_POST['mascaraCaptura'];
        $fec_registro = date("Y-m-d");
        $fec_vencimiento = $_POST['txtfechaVencimiento'];
        $asunto = $_POST['txtAsunto'];
        $id_cat_estructura = $_POST['id_cat_estructura'];

        /* Buscar que exista en la tabla de tipoFormulario */
        $sqlCheckTipoFormulario = "SELECT name_tramite from cat_tramites_formulario where id_cat_tramite_formulario = $1;";
        $resultCheckTipoFormulario = pg_query_params($connection, $sqlCheckTipoFormulario, array($mascaraCaptura));

        /* Si no existe mandar respuesta */
        if (pg_num_rows($resultCheckTipoFormulario) == 0) {
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'status' => 404,
                    'msg' => 'No existe esta mascara de captura'
                )
            );
            return;
        }

        /* Obtener el nombre del formulario */
        $nombre_mascara = pg_fetch_assoc($resultCheckTipoFormulario)['name_tramite'];

        $sqlInsertSol = "INSERT INTO reg_solicitudes_tramite (
            id_cat_user, id_cat_tramite_formulario, fec_registro, fec_vencimiento, 
            asunto, id_cat_estructura, referencia_tabla) 
            VALUES ( $1, $2, $3, $4, $5, $6, $7) RETURNING id_reg_solicitudes;";
        $resultInsertSol = pg_query_params($connection, $sqlInsertSol, array(
            $id_user, $mascaraCaptura, $fec_registro, $fec_vencimiento, $asunto, $id_cat_estructura, $nombre_mascara
        ));

        if (!$resultInsertSol) {
            throw new Exception("Error al consltar ultimo id insertado: " . pg_last_error($connection));
            exit;
        }
        $rowInsertoSol = pg_fetch_assoc($resultInsertSol); // Obtenemos el resultado de la consulta
        $ultimoId_solicitud = $rowInsertoSol['id_reg_solicitudes']; // Cambiado a id_reg_solicitudes

        unset($_POST['txtfechaVencimiento']);
        unset($_POST['txtAsunto']);
        unset($_POST['id_cat_estructura']);

        /* Verificar si ya existe una tabla para la mascara de captura */
        $sqlCheckTable = "SELECT table_name from information_schema.tables where table_name = $1";
        $resultSqlCheckTable = pg_query_params($connection, $sqlCheckTable, array($nombre_mascara));

        if (!$resultSqlCheckTable) {
            throw new Exception("Error al verificar la tabla existente: " . pg_last_error($connection));
            exit;
        }
        /* Si la tabla no existe, CREARLA */
        if (pg_num_rows($resultSqlCheckTable) == 0) {
            $sqlCreateTable = "CREATE TABLE $nombre_mascara (
                id SERIAL PRIMARY KEY, id_cat_tramite_formulario int, 
                foreign key (id_cat_tramite_formulario) references cat_tramites_formulario (id_cat_tramite_formulario))";
            $resultSqlCreateTable = pg_query($connection, $sqlCreateTable);
            if (!$resultSqlCreateTable) {
                throw new Exception("Error al crear la tabla: " . pg_last_error($connection));
                exit;
            }
        }

        /* Recibir TODOS los datos del formulario */
        $registrosMascara = $_POST;

        // Verificar si se han subido archivos
        foreach ($_FILES as $nombreCampo => $campoArchivo) {
            if ($campoArchivo['error'] === UPLOAD_ERR_OK) {
                // Procesar el archivo subido
                $nombreArchivo = $campoArchivo['name'];
                $rutaArchivo = $campoArchivo['tmp_name'];

                // Mover el archivo subido a una ubicación deseada
                $rutaDestino = "../uploads/$nombreArchivo";
                move_uploaded_file($rutaArchivo, $rutaDestino);

                // Crear una columna dinámica para el archivo si aún no se ha creado
                $nombreColumnaArchivo = "$nombreCampo"; // Nombre de la columna dinámica
                $sqlAgregarColumnaArchivo = "ALTER TABLE $nombre_mascara ADD COLUMN IF NOT EXISTS $nombreColumnaArchivo TEXT";
                $resultSqlAgregarColumnaArchivo = pg_query($connection, $sqlAgregarColumnaArchivo);
                if (!$resultSqlAgregarColumnaArchivo) {
                    throw new Exception("Error al agregar la columna $nombreColumnaArchivo: " . pg_last_error($connection));
                    exit;
                }

                $registrosMascara[$nombreColumnaArchivo] = $rutaDestino;
            }
        }

        /* Verificar si las columnas ya existen en la tabla */
        $sqlCheckColumns = "SELECT column_name FROM information_schema.columns WHERE table_name = $1";
        $resultSqlCheckColumns = pg_query_params($connection, $sqlCheckColumns, array($nombre_mascara));
        if (!$resultSqlCheckColumns) {
            throw new Exception("Error al verificar las columnas existentes: " . pg_last_error($connection));
            exit;
        }

        /* Obtener los nombres de las columnas existentes */
        $columnasExistentes = array();
        while ($row = pg_fetch_assoc($resultSqlCheckColumns)) {
            $columnasExistentes[] = $row['column_name'];
        }

        /* Agregar las columnas dinámicamente si NO existen */
        foreach ($registrosMascara as $clave => $valor) {
            if ($clave !== 'mascaraCaptura') {
                $nombreColumna = strtolower(pg_escape_string($clave));
            }
            /* Verificar si la columna existe en la tabla */
            if (!in_array($nombreColumna, $columnasExistentes)) {
                /* Obtener tipo de dato para la columna */
                $tipoDato = obtenerTipoDato($valor);
                /* Si no existe agregarla a la tabla */
                $sqlAgregarColumna = "ALTER TABLE $nombre_mascara ADD COLUMN $nombreColumna $tipoDato";
                $resultSqlAgregarColumna = pg_query($connection, $sqlAgregarColumna);
                if (!$resultSqlAgregarColumna) {
                    throw new Exception("Error al agregar la columna $nombreColumna: " . pg_last_error($connection));
                    exit;
                }
                /* Agregar la columna recién creada al array de columnas existentes para evitar crearla otra vez */
                $columnasExistentes[] = $nombreColumna;
            }
        }
        /* Insertar datos en la tabla correspondiente */
        $sqlInsert = "INSERT INTO $nombre_mascara (id_cat_tramite_formulario, ";
        foreach ($registrosMascara as $clave => $valor) {
            if ($clave !== 'mascaraCaptura') { // Evita agregar 'id_cat_estructura' dos veces
                $sqlInsert .= pg_escape_string($clave) . ",";
            }
        }
        $sqlInsert = rtrim($sqlInsert, ",") . ") VALUES(";

        /* Agregar el valor correspondiente al id_cat_tramite_formulario */
        $sqlInsert .= "'" . pg_escape_string($registrosMascara['mascaraCaptura']) . "',";
        /* Agregar los valores a los demás campos */
        foreach ($registrosMascara as $clave => $valor) {
            if ($clave !== 'mascaraCaptura') { // Evita agregar 'id_cat_estructura' dos veces
                $sqlInsert .= "'" . pg_escape_string($valor) . "',";
            }
        }
        $sqlInsert = rtrim($sqlInsert, ",") . ") RETURNING id";
        $resultSqlInsert = pg_query($connection, $sqlInsert);
        if (!$resultSqlInsert) {
            throw new Exception("Error al guardar los datos: " . pg_last_error($connection));
            exit;
        }
        $rowInsert = pg_fetch_assoc($resultSqlInsert); // Obtenemos el resultado de la consulta
        $ultimoId_tramite = $rowInsert["id"]; // Cambiado a id_reg_solicitudes

        // Actualizar los registros
        $sqlUpdate = "UPDATE reg_solicitudes_tramite SET no_tramite_referencia = ($1) WHERE id_reg_solicitudes = ($2)";
        $resultUpdate = pg_query_params($connection, $sqlUpdate, array($ultimoId_tramite, $ultimoId_solicitud));

        if (!$resultUpdate) {
            // Manejar el error si la actualización falla
            throw new Exception("Error al agregar la columna $nombreColumna: " . pg_last_error($connection));
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode(
            array(
                //'status' => 200,
                'msg' => 'Información guardada correctamente',
                'icon' => 'success',
                'status' => '¡Hecho!'
            )
        );

        /* Cerrar conexión para liberar memoria */
        pg_close($connection);
        return;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'msg' => 'Error al insertar los datos',
                'icon' => 'error',
                'status' => 'Oops..',
                // 'status' => 500,
                'error' => $e->getMessage()
            )
        );
        return;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $operacionGET = $_GET['operacion'];
    if ($operacionGET === 'enviarCorreo') {
        $dataRs = array();
        try {

            // Incluye el archivo enviar_correo.php
            require('envioCorreo.php');

            $asunto = "Solicitud " . $_GET['asunto'];
            $mensaje = 'Se ha devuelto la solicitud con el siguiente aunto: <br>' . $_GET['mensaje'];
            $form = $_GET['form'];

            $sql = "SELECT cu.email_user from cat_tramites_formulario ctf
                inner join  cat_user cu on cu.id_cat_user = ctf.id_cat_user 
                where ctf.id_cat_tramite_formulario = $1;";
            $result = pg_query_params($connection, $sql, array($form));
            if (!$result) {
                throw new Exception("Error al consultar correo: " . pg_last_error($connection));
                exit;
            }

            // Obtener el email 
            $row = pg_fetch_assoc($result);
            $destinatario = $row['email_user']; 
    

            $correoEnviado = enviarCorreo($destinatario, $asunto, $mensaje);
            // Envía el correo
            if ($correoEnviado) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Información guardada correctamente',
                    'icon' => 'success',
                    'status' => '¡Hecho!',
                );
                echo json_encode($data);
                return;
            } else {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Correo no enviado',
                    'icon' => 'error',
                    'status' => 'No se pudo enviar el correo!',
                );
                echo json_encode($data);
                return;
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            $data = array(
                'msg' => 'Error al insertar los datos',
                'error' => $e->getMessage()
            );
            echo json_encode($data);
            return;
        }
    }
}
