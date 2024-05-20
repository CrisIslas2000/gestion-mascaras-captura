<?php
require ('../db/connection.php');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $operacion = $_GET['operacion'];
    if ($operacion == 'datosTabla') {
        try {
            $tipo_formulario = $_GET['tipo_formulario'];

            /* Variable para guardar los registros */
            $data = array();

            $sqlNombreMascara = "SELECT name_tramite from cat_tramites_formulario where id_cat_tramite_formulario = $1";
            $resultSqlNombreMascara = pg_query_params($connection, $sqlNombreMascara, array($tipo_formulario));
            if (!$resultSqlNombreMascara) {
                throw new Exception("Error al obtener el nombre de la mascara: " . pg_last_error($connection));
                exit;
            }

            $nombreMascara = pg_fetch_assoc($resultSqlNombreMascara)['name_tramite'];

            $sqlRegistrosMascara = "SELECT * FROM $nombreMascara";
            $resultSqlRegistrosMascara = pg_query($connection, $sqlRegistrosMascara);
            if (!$resultSqlRegistrosMascara) {
                throw new Exception("Error al obtener el nombre los registros de $nombreMascara: " . pg_last_error($connection));
                exit;
            }

            if (pg_num_rows($resultSqlRegistrosMascara) == 0) {
                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        'status' => 'error',
                        'msg' => 'No hay datos para mostrar'
                    )
                );
                return;
            }

            while ($row = pg_fetch_assoc($resultSqlRegistrosMascara)) {
                $data[] = $row;
            }

            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'status' => 'success',
                    'data' => $data
                )
            );
            return;

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
    if ($operacion == 'datosRegistro') {
        try {
            $tipo_formulario = $_GET['tipo_formulario'];
            $id = $_GET['id'];

            /* Variable para guardar los registros */
            $data = array();

            $sqlNombreMascara = "SELECT name_tramite FROM cat_tramites_formulario WHERE id_cat_tramite_formulario = $1";
            $resultSqlNombreMascara = pg_query_params($connection, $sqlNombreMascara, array($tipo_formulario));
            if (!$resultSqlNombreMascara) {
                throw new Exception("Error al obtener el nombre de la mascara: " . pg_last_error($connection));
                exit;
            }

            $nombreMascara = pg_fetch_assoc($resultSqlNombreMascara)['name_tramite'];

            // Obtener los nombres de todas las columnas de la tabla
            $queryGetColumns = "SELECT column_name FROM information_schema.columns WHERE table_name = '$nombreMascara'";
            $resultGetColumns = pg_query($connection, $queryGetColumns);

            // Almacenar los nombres de las columnas en un array
            $columns = array();
            while ($row = pg_fetch_assoc($resultGetColumns)) {
                $columns[] = $row['column_name'];
            }

            // Excluir columnas no deseadas
            $excludedColumns = array('id', 'id_cat_tramites_formulario');
            $selectedColumns = array_diff($columns, $excludedColumns);

            // Construir la lista de columnas seleccionadas
            $selectedoClumnsString = implode(', ', $selectedColumns);

            $sqlRegistrosMascara = "SELECT $selectedoClumnsString FROM $nombreMascara WHERE id = $id";
            $resultSqlRegistrosMascara = pg_query($connection, $sqlRegistrosMascara);
            if (!$resultSqlRegistrosMascara) {
                throw new Exception("Error al obtener el nombre los registros de $nombreMascara: " . pg_last_error($connection));
                exit;
            }

            $data = pg_fetch_assoc($resultSqlRegistrosMascara);

            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'data' => $data
                )
            );
            return;
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
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $operacion = $_POST['operacion'];
    if ($operacion == 'actualizarRegistro') {
        try {

            //Obtener el id de mascara de captura
            $mascaraCaptura = $_POST['tipo_formulario'];

            /* Id registro */
            $id_registro = $_POST['id_registro'];

            /* Buscar que exista en la tabla de tipoFormulario */
            $sqlCheckTipoFormulario = "select nombre_formulario from cat_tramites_formulario where id_tipo_formulario = $1";
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
            $nombre_mascara = pg_fetch_assoc($resultCheckTipoFormulario)['nombre_formulario'];

            /* Recibir TODOS los datos del formulario */
            $registrosMascara = $_POST;

            // Verificar si se han subido archivos
            foreach ($_FILES as $nombreCampo => $campoArchivo) {
                if ($campoArchivo['error'] === UPLOAD_ERR_OK) {
                    // Procesar el archivo subido
                    $nombreArchivo = $campoArchivo['name'];
                    $rutaArchivo = $campoArchivo['tmp_name'];

                    // Mover el archivo subido a una ubicación deseada
                    $nombreColumnaArchivo = "$nombreCampo";
                    $rutaDestino = "../uploads/$nombreArchivo";
                    move_uploaded_file($rutaArchivo, $rutaDestino);

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
                if ($clave !== 'mascaraCaptura' && $clave !== 'tipo_formulario' && $clave !== 'id_registro' && $clave !== 'operacion') {
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


            /* Construir la consulta de actualización */
            $sqlUpdate = "UPDATE $nombre_mascara SET ";
            foreach ($registrosMascara as $clave => $valor) {
                // Omitir campos innecesarios
                if ($clave !== 'id_registro' && $clave !== 'operacion' && $clave !== 'tipo_formulario' && !is_array($valor)) {
                    $sqlUpdate .= "$clave = '" . pg_escape_string($valor) . "', ";
                }
            }

            // Eliminar la coma adicional al final de la cadena
            $sqlUpdate = rtrim($sqlUpdate, ", ");
            // Agregar la condición WHERE para el ID del registro
            $sqlUpdate .= " WHERE id = $id_registro";
            $resultSqlUpdate = pg_query($connection, $sqlUpdate);
            if (!$resultSqlUpdate) {
                throw new Exception("Error al actualizar los datos: " . pg_last_error($connection));
                exit;
            }

            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'status' => 200,
                    'msg' => 'Datos actualizados correctamente'
                )
            );

            // Cerrar conexión para liberar memoria
            pg_close($connection);
            return;

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
}