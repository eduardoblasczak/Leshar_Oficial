<?php
include_once("conexao.php");
session_start();

$retorno = [
    "status" => "",
    "mensagem" => "",
    "data" => []
];

// Verifica se campos foram enviados
if (!isset($_POST["LoginEmail"]) || !isset($_POST["LoginSenha"])) {
    $retorno["status"] = "erro";
    $retorno["mensagem"] = "Dados de login não enviados.";
    echo json_encode($retorno);
    exit;
}

$email = $_POST["LoginEmail"];
$senha = $_POST["LoginSenha"];

$stmt = $conexao->prepare("SELECT * FROM usuario WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();

    if (password_verify($senha, $usuario['senha'])) {
        $_SESSION["usuario"] = $usuario;
        $retorno = [
            "status" => "ok",
            "mensagem" => "Login realizado com sucesso.",
            "data" => $usuario
        ];
    } else {
        $retorno["status"] = "erro";
        $retorno["mensagem"] = "Senha incorreta.";
    }
} else {
    $retorno["status"] = "erro";
    $retorno["mensagem"] = "Email não cadastrado.";
}

$stmt->close();
$conexao->close();

header("Content-Type: application/json");
echo json_encode($retorno);
?>
