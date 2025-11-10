<?php
    session_start();
    if (isset($_SESSION['usuario'])){
        $status = "ok";
    } else{
        $status = "erro";
    }

    $retorno = [
        "status" => $status,
        "mensagem" => "",
        "data" => []
    ];
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($retorno);
?>