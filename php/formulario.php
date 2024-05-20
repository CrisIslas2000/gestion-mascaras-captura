<?php
session_start();
require('../db/connection.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataRs = array();

    $operacionPOST = $_POST['operacion'];

    if ($operacionPOST === 'eliminarFormulario') {

        $data = array();
        try {
            $id_formulario = $_POST['id_formulario'];
            /* Obtener datos para llenar el selector de las mascaras */
            $query = "UPDATE cat_tramites_formulario SET borrado = $1 WHERE id_cat_tramite_formulario = $2;";
            $result = pg_query_params($connection, $query, array('1', $id_formulario));

            if (!$result) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            } else {
                /* Enviar resultado */
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Información eliminada correctamente',
                    'icon' => 'success',
                    'status' => 'Hecho!',
                );
            }
            echo json_encode($data);
            return;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            $data = array(
                'msg' => 'Error al eliminar información',
                'icon' => 'error',
                'status' => 'Error!',
            );
            echo json_encode($data);
            return;
        }
    } else if ($operacionPOST === 'insertarFormulario') {
        try {
            // Iniciar la transacción
            pg_query($connection, "BEGIN");
        
            $nombreFormulario = $_POST["nombre-formulario"];
            $variablFormulario = $_POST["variable-formulario"];
            $camposVacios = array();
        
            // Verificar cada campo
            foreach ($_POST as $campo => $valor) {
                if (empty($valor)) {
                    $camposVacios[] = $campo;
                }
            }
        
            if (!empty($camposVacios)) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Faltan campos por llenar: ',
                    'campos' => $camposVacios,
                    'icon' => "warning",
                    'status' => "Error"
                );
                echo json_encode($data);
        
                // Hacer rollback
                pg_query($connection, "ROLLBACK");
                return;
            }
            
            //Validar que el nombre de variable ya exista en algun otro registro
            $queryName = "SELECT tf.name_tramite FROM cat_tramites_formulario tf WHERE tf.name_tramite = $1";
            $resultName = pg_query_params($connection, $queryName, array($variablFormulario));
            if (!$resultName) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            }
            $dataName = array();
            while ($rowName = pg_fetch_array($resultName)) {
                $dataName = $rowName;
            }
            $countRowsName = count($dataName);
            if ($countRowsName > 0) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Ya existe variable de formulario ' . $variablFormulario ,
                    'icon' => 'error',
                    'status' => 'Intente con otra variable de formulario!',
                );
                echo json_encode($data);
        
                // Hacer rollback
                pg_query($connection, "ROLLBACK");
                return;
            }
        
            /* Insertar datos en la base de datos */
            $query = "INSERT INTO cat_tramites_formulario ( name_tramite, id_cat_estructura, descripcion_tramite, id_cat_user ) VALUES ( $1, $2, $3, $4 );";
            $result = pg_query_params($connection, $query, array($variablFormulario, $_SESSION['id_cat_estructura'], $nombreFormulario, $_SESSION['id_cat_user'])); //Se queda el valor de 3 por defecto en pruebas
        
            if (!$result) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            }
        
            // Hacer commit
            pg_query($connection, "COMMIT");
        
            header('Content-Type: application/json');
            $data = array(
                'msg' => 'Información guardada correctamente',
                'icon' => 'success',
                'status' => 'Hecho!',
            );
            echo json_encode($data);
            return;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            $data = array(
                'msg' => 'Error al insertar los datos',
                'error' => $e->getMessage()
            );
            echo json_encode($data);
        
            // Hacer rollback en caso de error
            pg_query($connection, "ROLLBACK");
            return;
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $operacionGET = $_GET['operacion'];  
    if ( $operacionGET === 'mostrarTramites' ) {
        try {
            $dataRs = array();

            $query = "SELECT * FROM cat_tramites_formulario ct WHERE ct.id_cat_estructura = $1 AND borrado = '0' ORDER BY ct.id_cat_tramite_formulario ASC";
            $result = pg_query_params($connection, $query, array($_SESSION['id_cat_estructura']));
            if (!$result) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            }

            while ($row = pg_fetch_array($result)) {
                $dataRs[] = array(
                    'id_tipo_formulario' => $row['id_cat_tramite_formulario'],
                    'nombre_formulario' => $row['name_tramite'],
                    'texto' => $row['descripcion_tramite'],
                );
                // $dataRs[] = $row;
            }

            $countRows = count($dataRs);
            if ($countRows <= 0) {
                header('Content-Type: application/json');
                $data = array(
                    'vacio' => 'si',
                    'msg' => 'No hay datos de la columna para mostrar',
                    'data' => $dataRs
                );
                echo json_encode($data);
                return;
            }

            /* Enviar resultado */
            header('Content-Type: application/json');
            $data = array(
                'vacio' => 'no',
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
    } 
} 