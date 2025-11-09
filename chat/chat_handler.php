<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require '../php/conexao.php'; 

header('Content-Type: application/json');

if (!isset($_GET['action'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Ação não especificada.']);
    exit;
}

$action = $_GET['action'];

switch ($action) {
    case 'get':
        $u1_id = (int) ($_GET['remetente'] ?? 0);
        $u2_id = (int) ($_GET['destinatario'] ?? 0);

        if ($u1_id === 0 || $u2_id === 0) {
             http_response_code(400);
             echo json_encode(['error' => 'IDs de conversa inválidos.']);
             exit;
        }

        $sql = "SELECT 
                    m.remetente_id, 
                    m.mensagem, 
                    m.data_envio,
                    u.nome AS remetente_nome
                FROM 
                    chat_message m
                JOIN
                    usuario u ON m.remetente_id = u.id
                WHERE
                    (m.remetente_id = ? AND m.destinatario_id = ?)
                    OR 
                    (m.remetente_id = ? AND m.destinatario_id = ?)
                ORDER BY 
                    m.data_envio ASC";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iiii", $u1_id, $u2_id, $u2_id, $u1_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $messages = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        echo json_encode($messages);
        break;

    case 'send':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido.']);
            exit;
        }

        $remetente_id = (int) ($_POST['remetente_id'] ?? 0);
        $destinatario_id = (int) ($_POST['destinatario_id'] ?? 0);
        $text = trim($_POST['text'] ?? '');

        if ($remetente_id === 0 || $destinatario_id === 0 || $text === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Dados de envio incompletos.']);
            exit;
        }

        $sql = "INSERT INTO chat_message (remetente_id, destinatario_id, mensagem) 
                VALUES (?, ?, ?)";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iis", $remetente_id, $destinatario_id, $text);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            echo json_encode(['success' => true, 'message' => 'Mensagem salva.']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao salvar mensagem.']);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Ação desconhecida.']);
        break;
}
?>