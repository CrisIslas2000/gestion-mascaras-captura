<?php
require('../db/connection.php');
session_start(); // Iniciar la sesión
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $operacionGET = $_GET['operacion'];
    if ($operacionGET === 'mostrarTramite') {
        $id_tramite = $_GET['id_tramite'];

        $dataRs = array();
        try {

            $query = "SELECT rst.asunto, ce.nombre as direccion, rst.fec_registro, rst.fec_vencimiento, cs.nombre_status, cu.nombre_completo as nombre_user, rst.observaciones
                    from reg_solicitudes_tramite rst
                    inner join cat_user cu on cu.id_cat_user = rst.id_cat_user 
                    inner join cat_status cs on cs.id_cat_status = rst.id_cat_status 
                    inner join cat_estructuras ce on ce.id_cat_estructura = rst.id_cat_estructura 
                    where rst.id_reg_solicitudes = $id_tramite;";
            $result = pg_query($connection, $query);
            if (!$result) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            }

            while ($row = pg_fetch_array($result)) {
                $dataRs = array(
                    'asunto' => $row['asunto'],
                    'fec_vencimiento' => date('d/m/Y', strtotime($row['fec_vencimiento'])),
                    'fec_registro' => date('d/m/Y', strtotime($row['fec_registro'])),
                    'nombre_estructura' => $row['direccion'],
                    'name_status' => $row['nombre_status'],
                    'nombre_user' => $row['nombre_user'],
                    'observaciones' => $row['observaciones']
                );
            }
            $countRows = count($dataRs);
            if ($countRows <= 0) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'No hay datos de la columna para mostrar',
                    'data' => $dataRs
                );
                echo json_encode($data);
                return;
            }

            /* Enviar resultado */
            header('Content-Type: application/json');
            $data = array(
                'data' => $dataRs
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
    } elseif ($operacionGET === 'mostrarFormulario') {
        $dataFormulario = array();
        $id_tramite = $_GET['id_tramite'];
        try {
            $query = "SELECT rst.id_cat_tramite_formulario from reg_solicitudes_tramite rst where id_reg_solicitudes = $1;";
            $result = pg_query_params($connection, $query, array($id_tramite));
            if (!$result) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            }
            while ($row = pg_fetch_array($result)) {
                $id_cat_tramite_formulario = $row['id_cat_tramite_formulario'];
            }

            if (empty($id_cat_tramite_formulario)) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'No hay datos para mostrar'
                );
                echo json_encode($data);
                return;
            }

            /* Llamar al procedimiento almacenado para mostrar los campos necesarios para cada mascara */
            $query = "SELECT * from sp_mostrarformulario($1)";
            $result = pg_query_params($connection, $query, array($id_cat_tramite_formulario));
            if (!$result) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            }
            while ($row = pg_fetch_array($result)) {
                $dataFormulario[] = array(
                    'htmlTag' => $row['htmltag']
                );
            }

            $countRows = count($dataFormulario);
            if ($countRows <= 0) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'No hay datos para mostrar'
                );
                echo json_encode($data);
                return;
            }

            /* Retornar el código HTML */
            header('Content-Type: application/json');
            $data = array(
                'data' => $dataFormulario
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
    } elseif ($operacionGET === 'datosRegistro') {
        try {
            $id_tramite = $_GET['id_tramite'];

            /* Variable para guardar los registros */
            $data = array();

            $query = "SELECT rst.id_cat_tramite_formulario, rst.no_tramite_referencia from reg_solicitudes_tramite rst where id_reg_solicitudes = $1;";
            $result = pg_query_params($connection, $query, array($id_tramite));
            if (!$result) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            }
            while ($row = pg_fetch_array($result)) {
                $id_cat_tramite_formulario = $row['id_cat_tramite_formulario'];
                $no_tramite_referencia = $row['no_tramite_referencia'];
            }

            if (empty($id_cat_tramite_formulario)) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'No hay datos para mostrar'
                );
                echo json_encode($data);
                return;
            }

            $sqlNombreMascara = "SELECT name_tramite FROM cat_tramites_formulario WHERE id_cat_tramite_formulario = $1";
            $resultSqlNombreMascara = pg_query_params($connection, $sqlNombreMascara, array($id_cat_tramite_formulario));
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

            $sqlRegistrosMascara = "SELECT $selectedoClumnsString FROM $nombreMascara WHERE id = $no_tramite_referencia";
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
    } elseif ($operacionGET === 'mostrarTurnos') {
        $id_tramite = $_GET['id_tramite'];
        $dataRs = array();
        try {
            $sql = "SELECT rt.fec_seguimiento, cs.nombre_status, rt.seguimiento, cu.nombre_completo  from reg_seguimiento_tramite rt 
                    inner join cat_status cs on cs.id_cat_status  = rt.id_cat_status
                    inner join cat_user cu on cu.id_cat_user  = rt.id_cat_user 
                    where rt.id_reg_solicitudes = $1 and rt.borrado = $2;";
            $result = pg_query_params($connection, $sql, array($id_tramite, '0'));

            if (!$result) {
                throw new Exception("Error al obtrener seguimiento turnos:", pg_last_error($connection));
                exit;
            }
            while ($row = pg_fetch_array($result)) {
                $dataRs[] = array(
                    'nombre_status' => $row['nombre_status'],
                    'fec_seguimiento' => date('d/m/Y', strtotime($row['fec_seguimiento'])),
                    'seguimiento' => $row['seguimiento'],
                    'nombre_completo' => $row['nombre_completo']
                );
            }

            $countRows = count($dataRs);
            if ($countRows <= 0) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'No hay datos de la columna para mostrar',
                    'data' => $dataRs
                );
                echo json_encode($data);
                return;
            }

            /* Retornar el código HTML */
            header('Content-Type: application/json');
            $data = array(
                'data' => $dataRs
            );
            echo json_encode($data);
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
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $id_tramite = $_POST['id_registro'];
        try {
            $query = "SELECT rst.id_cat_tramite_formulario, rst.no_tramite_referencia from reg_solicitudes_tramite rst where id_reg_solicitudes = $1;";
            $result = pg_query_params($connection, $query, array($id_tramite));
            if (!$result) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            }
            while ($row = pg_fetch_array($result)) {
                $id_cat_tramite_formulario = $row['id_cat_tramite_formulario'];
                $id_referencia = $row['no_tramite_referencia'];
            }

            if (empty($id_cat_tramite_formulario)) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'No hay datos para mostrar'
                );
                echo json_encode($data);
                return;
            }

            //Obtener el id de mascara de captura
            $mascaraCaptura = $id_cat_tramite_formulario;

            /* Id registro */
            $id_registro = $_POST['id_registro'];

            /* Buscar que exista en la tabla de tipoFormulario */
            $sqlCheckTipoFormulario = "SELECT name_tramite from cat_tramites_formulario where id_cat_tramite_formulario = $1;";
            $resultCheckTipoFormulario = pg_query_params($connection, $sqlCheckTipoFormulario, array($mascaraCaptura));

            /* Si no existe mandar respuesta */
            if (pg_num_rows($resultCheckTipoFormulario) == 0) {
                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        'status' => 'error',
                        'msg' => 'No existe esta mascara de captura'
                    )
                );
                return;
            }

            /* Obtener el nombre del formulario */
            $nombre_mascara = pg_fetch_assoc($resultCheckTipoFormulario)['name_tramite'];

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

            /* Construir la consulta de actualización */
            $sqlUpdate = "UPDATE $nombre_mascara SET ";
            foreach ($registrosMascara as $clave => $valor) {
                // Omitir campos innecesarios
                if ($clave !== 'id' && $clave !== 'id_registro' && $clave !== 'operacion' && $clave !== 'tipo_formulario' && !is_array($valor)) {
                    $sqlUpdate .= "$clave = '" . pg_escape_string($valor) . "', ";
                }
            }

            // Eliminar la coma adicional al final de la cadena
            $sqlUpdate = rtrim($sqlUpdate, ", ");
            // Agregar la condición WHERE para el ID del registro
            $sqlUpdate .= "  WHERE id = $id_referencia;";

            $resultSqlUpdate = pg_query($connection, $sqlUpdate);
            if (!$resultSqlUpdate) {
                throw new Exception("Error al actualizar los datos: " . pg_last_error($connection));
                exit;
            }

            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'status' => '¡Hecho!',
                    'msg' => 'Datos actualizados correctamente',
                    'icon' => 'success'
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
