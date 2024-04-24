<?php
try{
    $connString = "host=localhost, port=5432 dbname=mascaras_capturas user=postgres password=12345";
    $connection = pg_connect($connString);
    if(!$connection){
        throw new Exception('No se puede conectar a la base de datos' . pg_errormessage());
    }
    return $connection;
}catch(Exception $e){
    header('Content-Type: application/json');
    $data = array(
        'msg' => 'Error al conectar a base de datos',
        'error' => $e->getMessage()
    );
    echo json_encode($data);
    return;
}