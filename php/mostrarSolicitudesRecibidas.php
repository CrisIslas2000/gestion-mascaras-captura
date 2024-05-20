<?php
    session_start();
    require('../db/connection.php');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $operacionGET = $_GET['operacion'];
        if ($operacionGET === 'dataSelect') {
            $dataSelect = array();
            try {
                /* Obtener datos para llenar el selector de las mascaras */
                $query = "SELECT * FROM sp_obtenerEstructura( $1 );";
                $result = pg_query_params($connection, $query, array($_SESSION['id_cat_estructura']));
                if (!$result) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }
    
                while ($row = pg_fetch_array($result)) {
                    $dataSelect[] = array(
                        'id_cat_estructura' => $row['id_cat_estructura_result'],
                        'nombre_estructura' => $row['nombre_result']
                    );
                }
                $countRows = count($dataSelect);
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
                    'data' => $dataSelect
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
        } else if ($operacionGET === 'mostrarSolicitudes') {
            // Mover esta dependencia para que sea dinamica y poner por default oficialia mayor
            //$id_estructura = $_GET['id_estructura'];
            $id_estructura = $_SESSION['id_cat_estructura'];

            $dataRs = array();
            try {
  
                $query = "SELECT rst.id_reg_solicitudes, rst.asunto, ce.nombre as direccion, 
                    rst.fec_registro, rst.fec_vencimiento, cs.nombre_status, cu.nombre_completo as usuario
                    from reg_solicitudes_tramite rst
                    inner join cat_user cu on cu.id_cat_user = rst.id_cat_user 
                    inner join cat_status cs on cs.id_cat_status = rst.id_cat_status 
                    inner join cat_estructuras ce on ce.id_cat_estructura = cu.id_cat_estructura 
                    where rst.borrado = $1 AND rst.id_cat_estructura = $2
                    order by rst.fec_registro;";
                $result = pg_query_params($connection, $query, array('0', $id_estructura));
                if (!$result) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }

                while ($row = pg_fetch_array($result)) {
                    $dataRs[] = array(
                        'asunto' => $row['asunto'], 
                        'fec_registro' => date('d/m/Y', strtotime($row['fec_registro'])),
                        'nombre_estructura' => $row['direccion'],
                        'name_status' => $row['nombre_status'],
                        'id_solicitud_tramite' => $row['id_reg_solicitudes']
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
                    'msg' => 'Error al consultar los datos',
                    'icon' => 'error',
                    'status' => 'Oops..',
                    'error' => $e->getMessage()
                );
                echo json_encode($data);
                return;
            }

        }
    }

