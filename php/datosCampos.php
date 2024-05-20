<?php
require('../db/connection.php');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $operacionGET = $_GET['operacion'];
    if ($operacionGET === 'mostrarSelect') {

        try {
            /* Obtener datos para llenar el selector de las mascaras */
            $queryTags = "SELECT tf.id_tags_campos, tf.texto FROM tagscampos tf ORDER BY tf.id_tags_campos ASC";
            $resultTags = pg_query($connection, $queryTags);
            if (!$resultTags) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            }
            while ($rowTags = pg_fetch_array($resultTags)) {
                $dataTags[] = array(
                    'id_tags_campos' => $rowTags['id_tags_campos'],
                    'texto' => $rowTags['texto']
                );
            }
            $countRowsTags = count($dataTags);
            if ($countRowsTags <= 0) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'No hay datos de los tags para mostrar'
                );
                echo json_encode($data);
                return;
            }

            $queryColumn = "SELECT tf.id_css_columnas, tf.texto FROM csscolumnas tf ORDER BY tf.id_css_columnas ASC";
            $resultColumn = pg_query($connection, $queryColumn);
            if (!$resultColumn) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            }

            while ($rowColumn = pg_fetch_array($resultColumn)) {
                $dataColumn[] = array(
                    'id_css_columnas' => $rowColumn['id_css_columnas'],
                    'texto' => $rowColumn['texto']
                );
            }
            $countRowsColumn = count($dataColumn);
            if ($countRowsColumn <= 0) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'No hay datos de la columna para mostrar'
                );
                echo json_encode($data);
                return;
            }

            $queryCatalogo = "SELECT ncd.id_nombre_catalogo_datos, ncd.nombre_catalogo FROM cat_catalogos ncd ORDER BY ncd.id_nombre_catalogo_datos ASC;";
            $resultCatalogo = pg_query($connection, $queryCatalogo);
            if (!$resultCatalogo) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            }

            while ($rowCatalogo = pg_fetch_array($resultCatalogo)) {
                $dataCatalogo[] = array(
                    'id_nombre_catalogo_datos' => $rowCatalogo['id_nombre_catalogo_datos'],
                    'nombre_catalogo' => $rowCatalogo['nombre_catalogo']
                );
            }
            $countRowsColumn = count($dataColumn);
            if ($countRowsColumn <= 0) {
                header('Content-Type: application/json');
                $data = array(
                    'msg' => 'No hay datos de la columna para mostrar'
                );
                echo json_encode($data);
                return;
            }
            /* Enviar resultado */
            header('Content-Type: application/json');
            $data = array(
                'dataTags' => $dataTags,
                'dataColumn' => $dataColumn,
                'dataCatalogo' => $dataCatalogo,
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
    } else if ($operacionGET === 'mostrarDatos') {
        $id_form = $_GET['id_form'];
        $data = array();
        try {
            $query = "SELECT dc.id_datos_campos, dc.titulo_campo, tc.texto AS texto_tag, cc.cssclass AS clase_css, cc.texto AS texto_columnas
                FROM reg_tramite_campos dc
                LEFT JOIN tagscampos tc ON dc.id_tags_campos = tc.id_tags_campos
                LEFT JOIN csscolumnas cc ON dc.id_css_columnas = cc.id_css_columnas 
                WHERE dc.id_cat_tramite_formulario = $id_form AND borrado = '0' ORDER BY dc.id_datos_campos ASC;";
            $result = pg_query($connection, $query);
            if (!$result) {
                throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
            }

            while ($row = pg_fetch_array($result)) {
                $data[] = array(
                    'id_datos_campos' => $row['id_datos_campos'],
                    'titulo_campo' => $row['titulo_campo'],
                    'texto_tag' => $row['texto_tag'],
                    'clase_css' => $row['clase_css'],
                    'texto_columnas' => $row['texto_columnas']
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
                'msg' => 'Error al realizar la consulta',
                'error' => $e->getMessage()
            );
            return;
        }
    }
}
