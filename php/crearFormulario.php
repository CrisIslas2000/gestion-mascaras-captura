<?php
require('../db/connection.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataRs = array();
    try {

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
            return;
        }
        
        //Validar que el nombre de variable ya exista en algun otro registro
        $queryName = "SELECT tf.nombre_formulario FROM tipoformulario tf WHERE tf.nombre_formulario = ($1);";
        $resultName = pg_query_params($connection, $queryName, array( $variablFormulario ));
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
            return;
        }
        /* Insertar datos en la base de datos */
        $query = "INSERT INTO tipoformulario (nombre_formulario, texto) VALUES ( $1, $2 )";
        $result = pg_query_params($connection, $query, array( $nombreFormulario, $variablFormulario )); //Se queda el valor de 3 por defecto en pruebas

        if (!$result) {
            throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
        }

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
        return;
    }
}