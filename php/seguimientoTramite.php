<?php
    require('../db/connection.php');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $operacionGET = $_GET['operacion'];
        if ($operacionGET = 'mostrarTramite') {
            $id_estructura = $_GET['id_estructura'];
            $id_tramite = $_GET['id_tramite'];

            $dataRs = array();
            try {
                /* Verificar si ya existe una tabla para la mascara de captura */
                $sqlCheckTable = "SELECT name_estructura from cat_estructuras where id_cat_estructura = $1;";
                $resultSqlCheckTable = pg_query_params($connection, $sqlCheckTable, array($id_estructura));

                if (!$resultSqlCheckTable) {
                    throw new Exception("Error al verificar la tabla existente: " . pg_last_error($connection));
                    exit;
                } 
                
                /* Si la tabla no existe, CREARLA */
                if (pg_num_rows($resultSqlCheckTable) == 0) {
                    header('Content-Type: application/json');
                    echo json_encode(
                        array(
                            'status' => 404,
                            'msg' => 'No existe esta mascara de captura',
                            'data' => []
                        )
                    );
                    return;  
                } elseif (pg_num_rows($resultSqlCheckTable) != 0) {
                    $name_estructura = pg_fetch_assoc($resultSqlCheckTable)['name_estructura'];
                }
                
                $sqlRegistrosMascara = "SELECT * FROM $name_estructura;";
                $resultSqlRegistrosMascara = pg_query($connection, $sqlRegistrosMascara);
                if (!$resultSqlRegistrosMascara) {
                    throw new Exception("Error al obtener el nombre los registros de $nombreMascara: " . pg_last_error($connection));
                    exit;
                }

                /* Si la tabla no existe, CREARLA */
                if (pg_num_rows($resultSqlRegistrosMascara) == 0) {
                    header('Content-Type: application/json');
                    echo json_encode(
                        array(
                            'status' => 404,
                            'msg' => 'No existe esta mascara de captura',
                            'data' => []
                        )
                    );
                    return;  
                }

                $query = "SELECT t.id_$name_estructura AS id_solicitud_tramite, t.txtfechavencimiento AS fec_vencimiento, t.txtasunto AS asunto, t.txtfechaVencimiento, t.fec_registro, cs.nombre_status AS name_status, ce.nombre AS nombre_estructura  
                    FROM $name_estructura t
                    inner join cat_estructuras ce on ce.id_cat_estructura = t.id_cat_estructura 
                    inner join cat_status cs on cs.id_cat_status = t.id_cat_status  
                    WHERE t.id_$name_estructura = $id_tramite
                    ORDER BY t.id_$name_estructura DESC;";
                $result = pg_query($connection, $query);
                if (!$result) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }

                while ($row = pg_fetch_array($result)) {
                    $dataRs = array(
                        'asunto' => $row['asunto'],
                        'fec_vencimiento' => $row['fec_vencimiento'],
                        'fec_registro' => $row['fec_registro'],
                        'nombre_estructura' => $row['nombre_estructura'],
                        'name_status' => $row['name_status'],
                        'id_solicitud_tramite' => $row['id_solicitud_tramite']
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
        } else {
            # code...
        }
    }
?>