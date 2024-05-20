<?php
require('../db/connection.php');
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $dataRs = array();
    try {
        $query = "SELECT * FROM cat_tramites_formulario ct WHERE ct.id_cat_estructura = $1 WHERE borrado = ORDER BY ct.id_cat_tramite_formulario ASC";
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
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $operacionPOST = $_POST['operacion'];
    if ($operacionPOST === 'eliminarCampo') {

        $data = array();
        try {
            $id_campo = $_POST['id_campo'];
            /* Obtener datos para llenar el selector de las mascaras */
            $query = "UPDATE datoscampos SET borrado = '1' WHERE id_datos_campos = ($1)";
            $result = pg_query_params($connection, $query, array($id_campo));

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
    } else if ($operacionPOST === 'mostrarCampo') {

        $data = array();
        try {
            $id_campo = $_POST['id_campo'];
            /* Obtener datos para llenar el selector de las mascaras */
            $query = "SELECT * FROM datoscampos WHERE id_datos_campos = ($1)";
            $result = pg_query_params($connection, $query, array($id_campo));

            if (!$result) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            } 

            while ($row = pg_fetch_array($result)) {
                $data = array(
                    'id_datos_campos' => $row['id_datos_campos'],
                    'titulo_campo' => $row['titulo_campo'],
                    'id_tags_campos' => $row['id_tags_campos'],
                    'nombre_campo' => $row['nombre_campo'],
                    'id_css_columnas' => $row['id_css_columnas'],
                    'id_nombre_catalogo_datos' => $row['id_nombre_catalogo_datos'],
                );
            }
            $countRows = count($data);
            if ($countRows <= 0) {
                header('Content-Type: application/json');
                $data = array(
                    'vacio' => 'si',
                    'msg' => 'No hay datos de la columna para mostrar'
                );
                echo json_encode($data);
                return;
            }
    
            /* Enviar resultado */
            header('Content-Type: application/json');
            $data = array(
                'vacio' => 'no',
                'data' => $data
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
    } else if ($operacionPOST === 'editarCampo') {

        $data = array();
        try {
            $id_campo = $_POST['id_campo'];
            /* Obtener datos para llenar el selector de las mascaras */
            $query = "SELECT * FROM datoscampos WHERE id_datos_campos = ($1)";
            $result = pg_query_params($connection, $query, array($id_campo));

            if (!$result) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            } else {
                /* Enviar resultado */
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'Información editada correctamente',
                    'icon' => 'success',
                    'status' => 'Hecho!',
                );
            }
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
    }
}
