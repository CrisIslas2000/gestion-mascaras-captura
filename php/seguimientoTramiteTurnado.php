<?php
    // Establece la zona horaria a México
    date_default_timezone_set('America/Mexico_City');
    // Obtiene la fecha y hora actual en México
    $fecha_actual = date('Y-m-d');
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
        } else if ($operacionGET === 'mostrarFormulario') {
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
                $query = "SELECT * from sp_mostrarformularioDisabled($1)";
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
        } else if ($operacionGET === 'datosRegistro') {
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

        } else if ($operacionGET === 'mostrarSeguimientoTurno'){
            $id_tramite = $_GET['id_tramite'];
            $dataRs = array();
            $dataStatus = array();
            try {
                $sqlTurno = "SELECT rt.id_reg_seguimiento_tramite from reg_turnos rt 
                where rt.id_reg_solicitudes = $1 and rt.id_cat_user = $2 and rt.borrado = $3;";
                $resultTurno = pg_query_params($connection, $sqlTurno, array($id_tramite, $_SESSION['id_cat_user'], '0'));

                if (!$resultTurno) {
                    throw new Exception("Error al obtener solicitud: " . pg_last_error($connection));
                    exit;
                } 

                $rowSeguimiento = pg_fetch_assoc($resultTurno);
                // $id_seguimiento_tramite = pg_fetch_assoc($resultTurno);
                $id_seguimiento_tramite = $rowSeguimiento['id_reg_seguimiento_tramite'];

                $sqlSeguimiento = "SELECT rst.seguimiento, rst.id_cat_status from reg_seguimiento_tramite rst where rst.id_reg_seguimiento_tramite = $1 and rst.borrado = $2;";
                $resultSeguimiento = pg_query_params($connection, $sqlSeguimiento, array($id_seguimiento_tramite, '0'));

                if (!$resultSeguimiento) {
                    throw new Exception("Error al obtener solicitud: " . pg_last_error($connection));
                    exit;
                } 

                while($row = pg_fetch_array($resultSeguimiento)){
                    $dataRs = array(
                        'comentarios' => $row['seguimiento'],
                        'id_status' => $row['id_cat_status']
                    );
                }

                $sqlStatus = "SELECT * FROM cat_status cs 
                    ORDER BY 
                        CASE 
                            WHEN cs.id_cat_status = $1 THEN 0 
                            ELSE 1 
                        END, cs.id_cat_status;";
                $resultStatus = pg_query_params($connection, $sqlStatus, array($dataRs['id_status']));

                if (!$resultStatus) {
                    throw new Exception("Error al obtener solicitud: " . pg_last_error($connection));
                    exit;
                } 

                while($rowStatus = pg_fetch_array($resultStatus)){
                    $dataStatus[] = array(
                        'nombre_status' => $rowStatus['nombre_status'],
                        'id_status' => $rowStatus['id_cat_status']
                    );
                }

                // Enviar resultado 
                header('Content-Type: application/json');
                $data = array(
                    'data' => $dataRs,
                    'status' => $dataStatus
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
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $operacionPost = $_POST['operacion'];
        if ($operacionPost === 'actualizarTurno') {
            try {
                $camposVacios = array();
                $dataStatus = array();
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

                $id_tramite = $_POST['id_tramite'];
                $seguimiento = $_POST['txt_comentarioTurno'];
                $id_status = $_POST['select_status'];

                $sqlTurno = "SELECT rt.id_reg_seguimiento_tramite from reg_turnos rt 
                where rt.id_reg_solicitudes = $1 and rt.id_cat_user = $2 and rt.borrado = $3;";
                $resultTurno = pg_query_params($connection, $sqlTurno, array($id_tramite, $_SESSION['id_cat_user'], '0'));

                if (!$resultTurno) {
                    throw new Exception("Error al obtener solicitud: " . pg_last_error($connection));
                    exit;
                } 

                $rowSeguimiento = pg_fetch_assoc($resultTurno);
                // $id_seguimiento_tramite = pg_fetch_assoc($resultTurno);
                $id_seguimiento_tramite = $rowSeguimiento['id_reg_seguimiento_tramite'];

                $sqlUpdate = "UPDATE reg_seguimiento_tramite SET seguimiento = $1, id_cat_status = $2 WHERE id_reg_seguimiento_tramite = $3;";
                $resultUpdate = pg_query_params($connection, $sqlUpdate, array($seguimiento, $id_status, $id_seguimiento_tramite));

                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        //'status' => 200,
                        'msg' => 'Información guardada correctamente',
                        'icon' => 'success',
                        'status' => 'Hecho!'
                    )
                );
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
?>