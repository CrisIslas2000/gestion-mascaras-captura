<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* print_r($_FILES); */

    /* Validar el si se selecciono algún archivo */
    if (!isset($_FILES['subirArchivo']) || $_FILES['subirArchivo']['error'] === 4) {
        header('Content-Type: application/json');
        $data = array(
            'msg' => "No se ha mandado nungun archivo",
            'icon' => "warning",
            'status' => "Error"
        );
        echo json_encode($data);
        return;
    } else if ($_FILES['subirArchivo']['error'] === 1) { /* Validar el tamaño del archivo */
        header('Content-Type: application/json');
        $data = array(
            'msg' => "El archivo seleccionado no puede pesar mas de 2 megabytes",
            'icon' => "warning",
            'status' => "Error"
        );
        echo json_encode($data);
        return;
    }

    /* Obtener las propiedades del archivo */
    $fileName = $_FILES['subirArchivo']['name'];
    $fileTmpName = $_FILES['subirArchivo']['tmp_name'];
    $carpetaDestino = '../uploads/';

    /* Guardar el archivo */
    if (move_uploaded_file($fileTmpName, $carpetaDestino . $fileName)) {
        $tipoFormulario = $_POST["tipoFormulario"];

        if ($tipoFormulario === 'conExterna') {
            $nombreSolicitante = $_POST["nombreSolicitante"];
            $cargo = $_POST["cargo"];
            $correoElectronico = $_POST["correoElectronico"];
            $telCelular = $_POST["telCelular"];
            $telOficinaExt = $_POST["telOficinaExt"];

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
                    'msg' => 'Faltan campos por llenar',
                    'campos' => $camposVacios,
                    'icon' => "warning",
                    'status' => "Error"
                );
                echo json_encode($data);
                return;
            }

            header('Content-Type: application/json');
            $data = array(
                'nombreSolicitante' => $nombreSolicitante,
                'cargo' => $cargo,
                'correoElectronico' => $correoElectronico,
                'telCelular' => $telCelular,
                'telOficinaExt' => $telOficinaExt,
                'icon' => "success",
                'msg' => 'Datos y archivo enviados correctamente',
                'status' => "Hecho"
            );
            echo json_encode($data);
            return;
        } else if ($tipoFormulario === 'extGobierno') {
            $nombreSolicitante = $_POST["nombreSolicitante"];
            $cargo = $_POST["cargo"];
            $ubicacion = $_POST["ubicacion"];
            $correoInstitucional = $_POST["correoInstitucional"];

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
                    'msg' => 'Faltan campos por llenar',
                    'campos' => $camposVacios,
                    'icon' => "warning",
                    'status' => "Error"
                );
                echo json_encode($data);
                return;
            }

            header('Content-Type: application/json');
            $data = array(
                'nombreSolicitante' => $nombreSolicitante,
                'cargo' => $cargo,
                'ubicacion' => $ubicacion,
                'correoInstitucional' => $correoInstitucional,
                'icon' => "success",
                'msg' => 'Datos y archivo enviados correctamente',
                'status' => "Hecho"
            );
            echo json_encode($data);
            return;
        }
    } else {
        header('Content-Type: application/json');
        $data = array(
            'msg' => "Error al subir el archivo",
            'icon' => "error",
            'status' => "Error"
        );
    }
} else {
    header('Content-Type: application/json');
    $data = array(
        'msg' => 'No se ha recibido una solicitud POST',
        'icon' => "error",
        'status' => "Error"
    );
    echo json_encode($data);
    return;
}