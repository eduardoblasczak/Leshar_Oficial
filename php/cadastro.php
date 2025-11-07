<?php
include_once("conexao.php");

$retorno = [
    "status" => "",
    "mensagem" => ""
];

if (isset($_POST['CadastroNome']) && isset($_POST['CadastroEmail']) && isset($_POST['CadastroSenha'])) {

    $nome = $_POST['CadastroNome'];
    $email = $_POST['CadastroEmail'];
    $senha = password_hash($_POST['CadastroSenha'], PASSWORD_DEFAULT);

    // Verifica se e-mail já existe
    $sql = $conexao->prepare("SELECT id FROM usuario WHERE email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $resultado = $sql->get_result();

    if ($resultado->num_rows > 0) {
        $retorno['status'] = "erro";
        $retorno['mensagem'] = 'Email já cadastrado.';
    } else {
        // Inserir novo usuário
        $sql = $conexao->prepare("INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)");
        $sql->bind_param("sss", $nome, $email, $senha);

        if ($sql->execute()) {
            $retorno['status'] = "ok";
            $retorno['mensagem'] = "Usuário cadastrado com sucesso.";
        } else {
            $retorno['status'] = "erro";
            $retorno['mensagem'] = "Erro ao cadastrar usuário.";
        }
    }
} else {
    $retorno['status'] = "erro";
    $retorno['mensagem'] = "Dados de cadastro não enviados.";
}

echo json_encode($retorno);
?>
