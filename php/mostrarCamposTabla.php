<?php
require('../db/connection.php');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = array();
    try {
        $id_form = $_GET['id_form'];
        $query = "SELECT dc.id_datos_campos, tc.texto AS tipo_campo,  dc.nombre_campo, dc.titulo_campo, cc.texto AS texto_columnas
            FROM reg_tramite_campos dc
            LEFT JOIN tagscampos tc ON dc.id_tags_campos = tc.id_tags_campos
            LEFT JOIN csscolumnas cc ON dc.id_css_columnas = cc.id_css_columnas 
            WHERE dc.id_cat_tramite_formulario = $id_form;";
        $result = pg_query($connection, $query);
        if (!$result) {
            throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
        }

        while ($row = pg_fetch_array($result)) {
            $data[] = array(
                'id_datos_campos' => $row['id_datos_campos'],
                'titulo_campo' => $row['titulo_campo'],
                'tipo_campo' => $row['tipo_campo'],
                'nombre_campo' => $row['nombre_campo'],
                'texto_columnas' => $row['texto_columnas']
            );
        }
        $countRows = count($data);
        if ($countRows <= 0) {
            header('Content-Type: application/json');
            $data = array(
                'vacio' => 'si',
                'msg' => 'No hay datos de la columna para mostrar',
                'data' => $data
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
            $query = "DELETE FROM datoscampos WHERE id_datos_campos = ($1)";
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
            $query = "SELECT * FROM reg_tramite_campos WHERE id_datos_campos = ($1)";
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
            $query = "SELECT * FROM reg_tramite_campos WHERE id_datos_campos = ($1)";
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
