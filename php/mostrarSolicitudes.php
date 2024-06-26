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
        }
        else if ($operacionGET === 'mostrarSolicitudes') {
            // Mover esta dependencia para que sea dinamica y poner por default oficialia mayor
            $id_estructura = $_GET['id_estructura'];
            $id_user = $_SESSION['id_cat_user'];

            $dataRs = array();
            try {

                /* Verificar si ya existe una tabla para la mascara de captura */
                $sqlCheckTable = "SELECT name_tramite from cat_tramites_formulario where id_cat_estructura = $1;";
                $resultSqlCheckTable = pg_query_params($connection, $sqlCheckTable, array($id_estructura));

                if (!$resultSqlCheckTable) {
                    throw new Exception("Error al verificar la tabla existente: " . pg_last_error($connection));
                    exit;
                } 
                
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
                }elseif (pg_num_rows($resultSqlCheckTable) != 0) {
                    while($rowEstructura = pg_fetch_array($resultSqlCheckTable)['name_tramite'])
                    $name_estructura[] = $rowEstructura;
                }
                
                var_dump($name_estructura);

                // Consulta para verificar si la tabla existe en la base de datos
                $query = "SELECT EXISTS (
                    SELECT 1
                    FROM   pg_catalog.pg_tables
                    WHERE  schemaname = 'public't
                    AND    tablename = $1
                )";

                // Ejecutar la consulta con pg_query_params para evitar la inyección de SQL
                $resultado = pg_query_params($connection, $query, array($name_estructura));

                // Verificar si la consulta fue exitosa
                if ($resultado) {
                    // Obtener el resultado de la consulta
                    $fila = pg_fetch_assoc($resultado);
                    
                    // Obtener el valor de la columna EXISTS (será "t" si la tabla existe, "f" si no existe)
                    $tabla_existe = $fila['exists'];
                    
                    // Verificar el valor de $tabla_existe
                    if ($tabla_existe === 't') {
                        $sqlRegistrosMascara = "SELECT * FROM $name_estructura WHERE id_cat_user = id_user;";
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

                        $query = "SELECT t.id_$name_estructura AS id_solicitud_tramite ,t.txtasunto AS asunto, t.txtfechaVencimiento, t.fec_registro, cs.nombre_status AS name_status, ce.nombre AS nombre_estructura  
                                FROM $name_estructura t
                                inner join cat_estructuras ce on ce.id_cat_estructura = t.id_cat_estructura 
                                inner join cat_status cs on cs.id_cat_status = t.id_cat_status  
                                ORDER BY t.id_$name_estructura DESC;";
                            $result = pg_query($connection, $query);
                            if (!$result) {
                                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
                            }

                            while ($row = pg_fetch_array($result)) {
                                $dataRs[] = array(
                                    'asunto' => $row['asunto'], 
                                    'fec_registro' => date('d/m/Y', strtotime($row['fec_registro'])),
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
                        
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(
                            array(
                                'status' => 404,
                                'msg' => 'No existe esta tabla ' . $name_estructura,
                                'data' => []
                            )
                        );
                        return; 
                    }
                } else {
                    throw new Exception("Error al verificar la tabla existente: " . pg_last_error($connection));
                    exit;
                }
                
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
