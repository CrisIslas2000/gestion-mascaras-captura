<?php
    session_start();
    require('../db/connection.php');
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $operacionGET = $_GET['operacion'];
        if ($operacionGET === 'mostrarDatos') {
            $id_user = $_SESSION['id_cat_user'];
            $dataRs = array();
            try{
                $sql = "SELECT rt.id_reg_solicitudes, rs.asunto, ce.nombre, cs.nombre_status, rt.fec_seguimiento 
                    from reg_seguimiento_tramite rt 
                    inner join reg_solicitudes_tramite rs on rs.id_reg_solicitudes = rt.id_reg_solicitudes 
                    inner join cat_user cu on cu.id_cat_user = rs.id_cat_user 
                    inner join cat_estructuras ce on ce.id_cat_estructura = cu.id_cat_estructura 
                    inner join cat_status cs on cs.id_cat_status  = rt.id_cat_status 
                                where rt.borrado = $1 and rt.id_cat_user  = $2;";
                $result = pg_query_params($connection, $sql, array('0', $id_user));

                if (!$result) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                    return;
                }

                while ($row = pg_fetch_array($result)) {
                    $dataRs[] = array(
                        'asunto' => $row['asunto'], 
                        'fec_registro' => date('d/m/Y', strtotime($row['fec_seguimiento'])),
                        'nombre_estructura' => $row['nombre'],
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
                    'msg' => 'Error al editar información',
                    'icon' => 'error',
                    'status' => 'Error!',
                );
                echo json_encode($data);
                return;
            }
        } elseif ($operacionGET === '') {
            
        }
    }
?>