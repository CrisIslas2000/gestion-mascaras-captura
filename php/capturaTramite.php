<?php
    require('../db/connection.php');
    // Establece la zona horaria a México
    date_default_timezone_set('America/Mexico_City');
    // Obtiene la fecha y hora actual en México
    $fecha_actual = date('Y-m-d');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $operacion = $_POST['operacion'];
        $dataSelect = array();
        if ( $operacion == 'llenarSelectNivel' ) {
            try {
                $id_estructura = $_POST['id_estructura'];
                //Validar que el nombre de variable ya exista en algun otro registro
                $querySelect = "SELECT * FROM cat_estructuras ce WHERE ce.estructura_padre = ($1) AND ce.status = 1 AND ce.id_cat_estructura <> 1;";
                $resultSelect  = pg_query_params($connection, $querySelect , array( $id_estructura ));
                if (!$resultSelect ) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }
                while ($rowSelect = pg_fetch_array($resultSelect)) {
                    $dataSelect[] = array(
                        'id_estructura' => $rowSelect['id_cat_estructura'],
                        'nombre_estructura' => $rowSelect['nombre'], 
                    );
                }
                $countRowsSelect = count($dataSelect);
                if ($countRowsSelect <= 0) {
                    header('Content-Type: application/json');
                    $data = array(
                        'msg' => 'No existen registros',
                        'status' => '404',
                        'data' => $dataSelect
                    );
                    echo json_encode($data);
                    return;
                }
            
                header('Content-Type: application/json');
                $data = array(
                    'data' => $dataSelect
                );
                echo json_encode($data);
                return;
            } catch (Exception $e) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Error al consultar los datos',
                    'error' => $e->getMessage()
                );
                echo json_encode($data);
                return;
            }
        } elseif ( $operacion == 'llenarSelectTramite' ) {
            try {
                $id_estructura = $_POST['id_estructura'];
                //Validar que el nombre de variable ya exista en algun otro registro
                $querySelect = "SELECT * FROM cat_tramites_formulario ct WHERE ct.id_cat_estructura = ($1) AND ct.status = 1;";
                $resultSelect  = pg_query_params($connection, $querySelect , array( $id_estructura ));
                if (!$resultSelect ) {
                    throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                }
                while ($rowSelect = pg_fetch_array($resultSelect)) {
                    $dataSelect[] = array(
                        'id_tramite' => $rowSelect['id_cat_tramite_formulario'],
                        'nombre_tramite' => $rowSelect['descripcion_tramite'], 
                    );
                }
                $countRowsSelect = count($dataSelect);
                if ($countRowsSelect <= 0) {
                    header('Content-Type: application/json');
                    $data = array(
                        'msg' => 'No existen registros',
                        'status' => '404',
                        'data' => $dataSelect
                    );
                    echo json_encode($data);
                    return;
                }
            
                header('Content-Type: application/json');
                $data = array(
                    'data' => $dataSelect
                );
                echo json_encode($data);
                return;
            } catch (Exception $e) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Error al consultar los datos',
                    'error' => $e->getMessage()
                );
                echo json_encode($data);
                return;
            }
        }
        //     try {
        //         try {
        //         $id_tramite = $_POST['id_tramite'];

        //         //Validar que el nombre de variable ya exista en algun otro registro
        //         $querySelect = "SELECT * FROM cat_tramites ct WHERE ct.id_cat_estructura = ($1) AND ct.status = 1;";
        //         $resultSelect  = pg_query_params($connection, $querySelect , array( $id_estructura ));
        //         if (!$resultSelect ) {
        //             throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
        //         }
        //         while ($rowSelect = pg_fetch_array($resultSelect)) {
        //             var_dump($rowSelect);
        //             $dataSelect[] = array(
        //                 'id_tramite' => $rowSelect['id_cat_tramite'],
        //                 'nombre_tramite' => $rowSelect['descripcion_tramite'], 
        //             );
        //         }
        //         $countRowsSelect = count($dataSelect);
        //         if ($countRowsSelect <= 0) {
        //             header('Content-Type: application/json');
        //             $data = array(
        //                 'msg' => 'No existen registros',
        //                 'status' => '404',
        //                 'data' => $dataSelect
        //             );
        //             echo json_encode($data);
        //             return;
        //         }
            
        //         header('Content-Type: application/json');
        //         $data = array(
        //             'data' => $dataSelect
        //         );
        //         echo json_encode($data);
        //         return;
        //     } catch (Exception $e) {
        //         header('Content-Type: application/json');
        //         $data = array(
        //             'msg' => 'Error al consultar los datos',
        //             'error' => $e->getMessage()
        //         );
        //         echo json_encode($data);
        //         return;
        //     }
    
    }
?>