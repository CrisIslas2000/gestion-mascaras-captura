<?php
require ('../db/connection.php');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $operacionGET = $_GET['operacion'];
    if ($operacionGET === 'dataSelect') {
        $dataSelect = array();
        try {
            /* Obtener datos para llenar el selector de las mascaras */
            $query = "select tf.id_cat_tramite_formulario, tf.descripcion_tramite from cat_tramites_formulario tf order by tf.id_cat_tramite_formulario asc";
            $result = pg_query($connection, $query);
            if (!$result) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            }

            while ($row = pg_fetch_array($result)) {
                $dataSelect[] = array(
                    'id_tipo_formulario' => $row['id_cat_tramite_formulario'],
                    'texto' => $row['descripcion_tramite']
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
    } else if ($operacionGET === 'mostrarFormulario') {
        $dataFormulario = array();
        $param = $_GET['selectValue'];

        try {
            /* Llamar al procedimiento almacenado para mostrar los campos necesarios para cada mascara */
            $query = "select * from sp_MostrarFormulario($1)";
            $result = pg_query_params($connection, $query, array($param));
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
    }
}