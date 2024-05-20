<?php
require('../db/connection.php');
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataRs = array();
    try {

        $camposVacios = array();
        // echo $_POST['select-tipo'] . ' : ' . $_POST['select-catalogo']. ', ';
        if ($_POST['select-tipo'] === '7' && $_POST['select-catalogo'] === '0'){
            $_POST['select-catalogo'] = null;
        } elseif($_POST['select-tipo'] !== '7' && $_POST['select-catalogo'] === '0'){
            $_POST['select-catalogo'] = 'null';
        }

        // Verificar cada campo
        foreach ($_POST as $campo => $valor) {
            if (empty($valor)) {
                $camposVacios[] = $campo;
            }
        }

        if (!empty($camposVacios)) {
            header('Content-Type: application/json');
            $data = array(
                'msg' => 'Faltan campos por llenar',
                'campos' => $camposVacios,
                'icon' => "warning",
                'status' => "Error"
            );
            echo json_encode($data);
            return;
        }
        
        $id_form = $_POST['id_form'];
        $tipoCampo = $_POST["select-tipo"];
        $tituloCampo = $_POST["titulo_campo"];
        $variableCampo = $_POST["variable_campo"];
        $numColumnas = $_POST["select-columnas"];
        $tipoCatalogo = $_POST['select-catalogo'];
        
        //Validar que el nombre de variable ya exista en algun otro registro
        $queryName = "SELECT * FROM reg_tramite_campos dc WHERE dc.nombre_campo = ($1) AND dc.id_cat_tramite_formulario = $id_form;";
        $resultName = pg_query_params($connection, $queryName, array( $variableCampo ));
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
                'msg' => 'Ya existe un nombre de campo ' . $variableCampo ,
                'icon' => 'error',
                'status' => 'Intente con otro nombre de campo!',
            );
            echo json_encode($data);
            return;
        }
        $tipoCatalogo = ($tipoCatalogo === 'null') ? null : intval($tipoCatalogo);
        /* Insertar datos en la base de datos */
        $queryText = "SELECT * FROM sp_insertar_datos_campo($1, $2, $3, $4, $5, $6, $7)";
        $result = pg_query_params($connection, $queryText, array( $tituloCampo, $variableCampo, $tipoCampo, $numColumnas, $id_form, $tipoCatalogo, $_SESSION["id_cat_user"])); //Se queda el valor de 3 por defecto en pruebas

        if (!$result) {
            throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
        }
        while ($row = pg_fetch_array($result)) {
            $dataRs[] = array(
                'titulo_campo' => $row['titulo_campo'],
                'texto_tag' => $row['texto_tag'],
                'clase_css' => $row['clase_css'],
                'texto_columnas' =>$row['texto_columnas'],
                'nombre_campo' => $variableCampo,
                'id_campo' => $row['id_campo'],
                'nombre_catalogo' => $row['nombre_catalogo']
            );
        }

        $countRows = count($dataRs);
        if ($countRows <= 0) {
            header('Content-Type: application/json');
            $data = array(
                'msg' => 'No se puede insertar campo',
                'icon' => 'error',
                'status' => 'Intente de  nuevo!',
            );
            echo json_encode($data);
            return;
        } else {
            header('Content-Type: application/json');
            $data = array(
                'msg' => 'Información guardada correctamente',
                'icon' => 'success',
                'status' => '¡Hecho!',
                'data' => $dataRs
            );
            echo json_encode($data);
            return;
        }
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        $data = array(
            'msg' => 'Error al insertar los datos',
            'error' => $e->getMessage()
        );
        echo json_encode($data);
        return;
    }
} 
