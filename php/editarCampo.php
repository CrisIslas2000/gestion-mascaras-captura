<?php
require('../db/connection.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        $camposVacios = array();
        // Verificar cada campo
        if ($_POST['select-tipo'] === '7' && $_POST['select-catalogo'] === '0'){
            $_POST['select-catalogo'] = null;
        } elseif($_POST['select-tipo'] !== '7' && $_POST['select-catalogo'] === '0'){
            $_POST['select-catalogo'] = 'null';
        }
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
        $id_campo = $_POST['id_campo'];
        $tipoCampo = $_POST["select-tipo"];
        $tituloCampo = $_POST["titulo_campo"];
        $variableCampo = $_POST["variable_campo"];
        $numColumnas = $_POST["select-columnas"];
        $tipoCatalogo = $_POST['select-catalogo'];

        //Validar que no tenga el mismo nombre de variable que algun otro campo
        $queryName = "SELECT * FROM datoscampos dc WHERE dc.nombre_campo = ($1) AND dc.id_datos_campos != ($2) AND dc.id_tipo_formulario = $id_form;";
        $resultName = pg_query_params($connection, $queryName, array($variableCampo, $id_campo));
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
                'msg' => 'Ya existe un nombre de campo ' . $variableCampo,
                'icon' => 'error',
                'status' => 'Intente con otro nombre de campo!',
            );
            echo json_encode($data);
            return;
        }

        $tipoCatalogo = ($tipoCatalogo === 'null') ? null : intval($tipoCatalogo);
        //si no existe algun registro con el nombre de variable ya actualizamos el registro
        $queryUpdate = 'UPDATE datoscampos SET titulo_campo = ($1), nombre_campo = ($2), id_tags_campos = ($3), id_css_columnas = ($4), id_nombre_catalogo_datos = ($5) WHERE id_datos_campos = ($6);';
        $resultUpdate = pg_query_params($connection, $queryUpdate, array($tituloCampo, $variableCampo, $tipoCampo, $numColumnas, $tipoCatalogo, $id_campo));
        if (!$resultUpdate) {
            throw new Exception("No se pudo realizar la consulta", pg_errormessage());
        }
        //Obetenemos los datos para poder actualizar en patalla la vista previa
        $queryCSS = "SELECT datoscampos.id_datos_campos AS id_campo, 
        datoscampos.titulo_campo, 
        tagscampos.texto AS texto_tag, 
        csscolumnas.cssclass AS clase_css, 
        csscolumnas.texto AS texto_columnas,
        nombrecatalogodatos.nombre_catalogo as texto_catalogo
        FROM datoscampos
        LEFT JOIN tagscampos ON datoscampos.id_tags_campos = tagscampos.id_tags_campos
        LEFT JOIN csscolumnas ON datoscampos.id_css_columnas = csscolumnas.id_css_columnas 
        LEFT JOIN nombrecatalogodatos ON datoscampos.id_nombre_catalogo_datos  = nombrecatalogodatos.id_nombre_catalogo_datos
        WHERE datoscampos.id_tipo_formulario = $id_form AND datoscampos.id_datos_campos =  ( $1 );";
        $resultCSS = pg_query_params($connection, $queryCSS, array($id_campo)); //Se queda el valor de 3 por defecto en pruebas
        if (!$resultCSS) {
            throw new Exception('No se pudo realizar la consulta' . pg_errormessage());
        }
        $dataCSS = array();
        while ($rowCSS = pg_fetch_array($resultCSS)) {
            $dataCSS = array(
                'titulo_campo' => $rowCSS['titulo_campo'],
                'texto_tag' => $rowCSS['texto_tag'],
                'clase_css' => $rowCSS['clase_css'],
                'texto_columnas' =>$rowCSS['texto_columnas'],
                'nombre_campo' => $variableCampo,
                'id_campo' => $rowCSS['id_campo'],
                'nombre_catalogo' => $rowCSS['texto_catalogo']
            );
        }

        header('Content-Type: application/json');
        $data = array(
            'msg' => 'Actualizado correctamente',
            'icon' => 'success',
            'status' => 'Hecho!',
            'data' => $dataCSS
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
