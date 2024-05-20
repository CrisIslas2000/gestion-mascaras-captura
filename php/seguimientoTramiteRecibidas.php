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
        } else if ($operacionGET === 'mostrarSelect') {
            $id_tramite = $_GET['id_tramite'];

            $dataRs = array();
            try {

                $query = "SELECT rst.id_cat_estructura as direccion
                    from reg_solicitudes_tramite rst
                    where rst.id_reg_solicitudes = $1;";
                $result = pg_query_params($connection, $query, array($id_tramite));
                if (!$result) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }

                while ($row = pg_fetch_array($result)) {
                        $id_estructura = $row['direccion'];
                }

                $querySelect = "SELECT cu.id_cat_user, cu.nombre_completo 
                    from cat_user cu
                    where cu.id_cat_estructura = $1 ;";
                $resultSelect = pg_query_params($connection, $querySelect, array($id_estructura));
                if (!$resultSelect) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }

                while ($rowSelect = pg_fetch_array($resultSelect)) {
                    $dataRs[] = array(
                        'id_user' => $rowSelect['id_cat_user'],
                        'nombre' => $rowSelect['nombre_completo']
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
        } else if ($operacionGET === 'mostrarTurnos') {
            $dataRs = array();
            $id_tramite = $_GET['id_tramite'];
            try {
                $query = "SELECT rst.id_reg_seguimiento_tramite, cu.nombre_completo 
                    FROM reg_seguimiento_tramite rst
                    INNER JOIN cat_user cu ON rst.id_cat_user = cu.id_cat_user
                    WHERE rst.id_reg_solicitudes = $1 AND rst.borrado = '0' 
                    ORDER BY id_reg_seguimiento_tramite ASC;";
                $result = pg_query_params($connection, $query, array($id_tramite));
                if (!$result) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }

                while ($row = pg_fetch_array($result)) {
                    $dataRs[] = array(
                        'id_seguimiento' => $row['id_reg_seguimiento_tramite'],
                        'nombre_completo' => $row['nombre_completo'],
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
        } else if ($operacionGET === 'enviarCorreoDevuelto') {
            $dataRs = array();
            try {
                // Incluye el archivo enviar_correo.php
                require('envioCorreo.php');

                $id_tramite = $_GET['id_tramite'];
    
                $sql = "SELECT cu.email_user, rst.asunto, ctf.descripcion_tramite  as tramite, rst.observaciones 
                from  reg_solicitudes_tramite rst 
                inner join cat_user cu on cu.id_cat_user = rst.id_cat_user 
                inner join cat_tramites_formulario ctf on ctf.id_cat_tramite_formulario = rst.id_cat_tramite_formulario 
                 where id_reg_solicitudes = $1;";
                $result = pg_query_params($connection, $sql, array($id_tramite));
                if (!$result) {
                    throw new Exception("Error al consultar correo: " . pg_last_error($connection));
                    exit;
                }

                while ($row = pg_fetch_array($result)) {
                    $dataRs = array(
                        'asunto' => $row['asunto'],
                        'tramite' => $row['tramite'],
                        'email_user' => $row['email_user'],
                        'observaciones' => $row['observaciones'],
                    );
                }
    
                // Obtener el email 
                $row = pg_fetch_assoc($result);
                $destinatario = $dataRs['email_user']; 

                $asunto = "Solicitud " . $dataRs['tramite'];
                $mensaje = 'Se ha devuelto la solicitud con el siguiente aunto: ' . $dataRs['asunto'] .
                '<br> Que tiene como observación: ' . $dataRs['observaciones'];
                $tramite = $dataRs['email_user'];
    
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
        } else if ($operacionGET === 'enviarCorreoTurno') {
            $dataRs = array();
            $dataTurno = array();
            try {
                // Incluye el archivo enviar_correo.php
                require('envioCorreo.php');

                $id_tramite = $_GET['id_tramite'];
                $id_turno = $_GET['turno'];
    
                $sqlSolicitud = "SELECT cu.email_user, rst.asunto, ctf.descripcion_tramite  as tramite, rst.observaciones 
                from  reg_solicitudes_tramite rst 
                inner join cat_user cu on cu.id_cat_user = rst.id_cat_user 
                inner join cat_tramites_formulario ctf on ctf.id_cat_tramite_formulario = rst.id_cat_tramite_formulario 
                 where id_reg_solicitudes = $1;";
                $resultSolicitud  = pg_query_params($connection, $sqlSolicitud , array($id_tramite));
                if (!$resultSolicitud) {
                    throw new Exception("Error al consultar correo: " . pg_last_error($connection));
                    exit;
                }

                while ($row = pg_fetch_array($resultSolicitud )) {
                    $dataRs = array(
                        'asunto' => $row['asunto'],
                        'tramite' => $row['tramite'],
                    );
                }

                $sqlTurno = "SELECT cu.email_user  from reg_turnos rt 
                inner join cat_user cu on cu.id_cat_user = rt.id_cat_user 
                where rt.id_reg_turnos = $1;";
                $resultTurno  = pg_query_params($connection, $sqlTurno, array($id_turno));
                if (!$resultTurno) {
                    throw new Exception("Error al consultar correo: " . pg_last_error($connection));
                    exit;
                }

                // Obtener el email 
                $row = pg_fetch_assoc($resultTurno);
                $destinatario = $row['email_user']; 

                $asunto = "Solicitud " . $dataRs['tramite'];
                $mensaje = 'Se te ha turnado la solicitud con el siguiente aunto: ' . $dataRs['asunto'];
    
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

    } else if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $operacionPost = $_POST['operacion'];
        if ($operacionPost === 'regresarTramite'){
            $id_tramite = $_POST['id_tramite'];
            $observaciones = $_POST['observaciones'];
            try {
                $sqlUpdate = "UPDATE reg_solicitudes_tramite SET id_cat_status = $1, observaciones = $2 WHERE id_reg_solicitudes = $3; ";
                $resultUpdate = pg_query_params($connection, $sqlUpdate, array(5, $observaciones, $id_tramite));

                if (!$resultUpdate) {
                    throw new Exception("Error al obtener el nombre de la mascara: " . pg_last_error($connection));
                    exit;
                }

                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        //'status' => 200,
                        'msg' => 'Información devuelta correctamente',
                        'icon' => 'success',
                        'status' => '¡Hecho!'
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
        } else if ($operacionPost === 'insertarTurno'){
            try{
                $dataRs = array();
                $id_tramite = $_POST['id_tramite'];
                $id_user = $_POST['id_user'];

                $query = "SELECT * FROM reg_seguimiento_tramite rst
                    WHERE rst.id_reg_solicitudes = $1 AND rst.borrado = '0' AND rst.id_cat_user = $2
                    ORDER BY id_reg_seguimiento_tramite ASC;";
                $result = pg_query_params($connection, $query, array($id_tramite, $id_user));
                if (!$result) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }

                while ($row = pg_fetch_assoc($result)) {
                    $dataRs[] = $row;
                }
                
                $countRows = count($dataRs);
                if ($countRows > 0) {
                    header('Content-Type: application/json');
                    $data = array(
                        'msg' => 'Usuario ya asignado',
                        'icon' => 'error',
                        'status' => '¡Error!'
                    );
                    echo json_encode($data);
                    return;
                }

                $sqlTurnos = "INSERT INTO reg_turnos ( id_cat_user, id_reg_solicitudes ) VALUES ( $1, $2 ) RETURNING id_reg_turnos;";
                $resultTurnos = pg_query_params( $connection, $sqlTurnos, array( $id_user, $id_tramite ));

                if (!$resultTurnos) {
                    throw new Exception("Error al insertar registro de turnos: " . pg_last_error($connection));
                    exit;
                }
                $rowTurnos = pg_fetch_assoc($resultTurnos); // Obtenemos el resultado de la consulta
                $ultimoIdTurnos = $rowTurnos["id_reg_turnos"];

                $sqlSeguimiento = "INSERT INTO reg_seguimiento_tramite ( id_reg_solicitudes, id_cat_status, fec_seguimiento, id_cat_user ) VALUES ( $1, $2, $3, $4 ) RETURNING id_reg_seguimiento_tramite;";
                $resultSeguimiento = pg_query_params($connection, $sqlSeguimiento, array($id_tramite, 6, $fecha_actual, $id_user));
                
                if (!$resultSeguimiento) {
                    throw new Exception("Error al insertar registro de seguinmiento: " . pg_last_error($connection));
                    exit;
                }
                $rowSeguimiento = pg_fetch_assoc($resultSeguimiento); // Obtenemos el resultado de la consulta
                $ultimoIdSeguimiento = $rowSeguimiento["id_reg_seguimiento_tramite"];

                $sqlUpdate = "UPDATE reg_turnos SET id_reg_seguimiento_tramite = $1 WHERE id_reg_turnos = $2;";
                $resultUpdate = pg_query_params($connection, $sqlUpdate, array($ultimoIdSeguimiento, $ultimoIdTurnos));

                if (!$resultUpdate) {
                    throw new Exception("Error al actualizar registro: ".pg_last_error($connection));
                }
                // Aqui

                $sqlUpdate = "UPDATE reg_solicitudes_tramite SET id_cat_status = $1 WHERE id_reg_solicitudes = $2;";
                $resultUpdate = pg_query_params($connection, $sqlUpdate, array(6, $id_tramite));

                if (!$resultUpdate) {
                    throw new Exception("Error al actualizar registro: ".pg_last_error($connection));
                }
                
                $query = "SELECT rt.id_reg_seguimiento_tramite, cu.nombre_completo 
                    FROM reg_turnos rt
                    INNER JOIN cat_user cu ON rt.id_cat_user = cu.id_cat_user
                    WHERE rt.id_reg_turnos = $1 AND rt.borrado = '0'
                    ORDER BY id_reg_seguimiento_tramite ASC;";
                $result = pg_query_params($connection, $query, array($ultimoIdTurnos));
                if (!$result) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }

                while ($row = pg_fetch_array($result)) {
                    $dataTurno = array(
                        'id_turno' => $row['id_reg_seguimiento_tramite'],
                        'nombre_completo' => $row['nombre_completo'],
                    );
                }

                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        //'status' => 200,
                        'msg' => 'Turnado correctamente',
                        'icon' => 'success',
                        'status' => '¡Hecho!',
                        'data' => $dataTurno
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
        } else if ($operacionPost === 'eliminarTurno') {
            $id_turno = $_POST['id_turno'];
            try{
                $queryUpdate = "UPDATE reg_seguimiento_tramite SET borrado = $1
                    WHERE id_reg_seguimiento_tramite = $2;";
                $resultUpdate = pg_query_params($connection, $queryUpdate, array('1', $id_turno));
                if (!$resultUpdate) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }

                $query = "UPDATE reg_turnos SET borrado = $1
                WHERE id_reg_seguimiento_tramite = $2;";
                $result = pg_query_params($connection, $query, array('1', $id_turno));
                if (!$result) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }

                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        //'status' => 200,
                        'msg' => 'Turno eliminado correctamente',
                        'icon' => 'success',
                        'status' => '¡Hecho!',
                        'id_turno' => $id_turno
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