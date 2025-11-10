<?php
    session_start();
    session_unset();
    session_destroy();

    $retorno = [
        "status" => "ok",
        "mensagem" => "Sessão encerrada com sucesso.",
        "data" => []
    ];
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($retorno);