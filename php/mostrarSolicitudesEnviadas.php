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
            $id_user = $_SESSION['id_cat_user'];

            $dataRs = array();
            try {
  
                $query = "SELECT rst.id_reg_solicitudes, rst.asunto, ce.nombre as direccion, rst.fec_registro, cs.nombre_status from reg_solicitudes_tramite rst
                inner join cat_status cs on cs.id_cat_status = rst.id_cat_status 
                inner join cat_estructuras ce on ce.id_cat_estructura = rst.id_cat_estructura 
                where rst.id_cat_user = $1
                order by rst.fec_registro";
                $result = pg_query_params($connection, $query, array($id_user));
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
//     else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $operacionPOST = $_POST['operacion'];
//     if ($operacionPOST === 'eliminarCampo') {

//         $data = array();
//         try {
//             $id_campo = $_POST['id_campo'];
//             /* Obtener datos para llenar el selector de las mascaras */
//             $query = "DELETE FROM datoscampos WHERE id_datos_campos = ($1)";
//             $result = pg_query_params($connection, $query, array($id_campo));

//             if (!$result) {
//                 throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
//             } else {
//                 /* Enviar resultado */
//                 header('Content-Type: application/json');
//                 $data = array(
//                     'msg' => 'Información eliminada correctamente',
//                     'icon' => 'success',
//                     'status' => 'Hecho!',
//                 );
//             }
//             echo json_encode($data);
//             return;
//         } catch (Exception $e) {
//             header('Content-Type: application/json');
//             $data = array(
//                 'msg' => 'Error al eliminar información',
//                 'icon' => 'error',
//                 'status' => 'Error!',
//             );
//             echo json_encode($data);
//             return;
//         }
//     } else if ($operacionPOST === 'mostrarCampo') {

//         $data = array();
//         try {
//             $id_campo = $_POST['id_campo'];
//             /* Obtener datos para llenar el selector de las mascaras */
//             $query = "SELECT * FROM datoscampos WHERE id_datos_campos = ($1)";
//             $result = pg_query_params($connection, $query, array($id_campo));

//             if (!$result) {
//                 throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
//             } 

//             while ($row = pg_fetch_array($result)) {
//                 $data = array(
//                     'id_datos_campos' => $row['id_datos_campos'],
//                     'titulo_campo' => $row['titulo_campo'],
//                     'id_tags_campos' => $row['id_tags_campos'],
//                     'nombre_campo' => $row['nombre_campo'],
//                     'id_css_columnas' => $row['id_css_columnas'],
//                     'id_nombre_catalogo_datos' => $row['id_nombre_catalogo_datos'],
//                 );
//             }
//             $countRows = count($data);
//             if ($countRows <= 0) {
//                 header('Content-Type: application/json');
//                 $data = array(
//                     'vacio' => 'si',
//                     'msg' => 'No hay datos de la columna para mostrar'
//                 );
//                 echo json_encode($data);
//                 return;
//             }
    
//             /* Enviar resultado */
//             header('Content-Type: application/json');
//             $data = array(
//                 'vacio' => 'no',
//                 'data' => $data
//             );
//             echo json_encode($data);
//             return;
//         } catch (Exception $e) {
//             header('Content-Type: application/json');
//             $data = array(
//                 'msg' => 'Error al editar información',
//                 'icon' => 'error',
//                 'status' => 'Error!',
//             );
//             echo json_encode($data);
//             return;
//         }
//     } else if ($operacionPOST === 'editarCampo') {

//         $data = array();
//         try {
//             $id_campo = $_POST['id_campo'];
//             /* Obtener datos para llenar el selector de las mascaras */
//             $query = "SELECT * FROM datoscampos WHERE id_datos_campos = ($1)";
//             $result = pg_query_params($connection, $query, array($id_campo));

//             if (!$result) {
//                 throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
//             } else {
//                 /* Enviar resultado */
//                 header('Content-Type: application/json');
//                 $data = array(
//                     'msg' => 'Información editada correctamente',
//                     'icon' => 'success',
//                     'status' => 'Hecho!',
//                 );
//             }
//             echo json_encode($data);
//             return;
//         } catch (Exception $e) {
//             header('Content-Type: application/json');
//             $data = array(
//                 'msg' => 'Error al editar información',
//                 'icon' => 'error',
//                 'status' => 'Error!',
//             );
//             echo json_encode($data);
//             return;
//         }
//     }
// }
