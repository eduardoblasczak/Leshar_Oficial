<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado.']);
    exit;
}

require_once __DIR__ . '/../php/conexao.php';

$user_id = (int) $_SESSION['user_id'];

$sql = "
  SELECT
    partner.partner_id,
    u.nome AS partner_name,
    partner.mensagem AS last_message,
    partner.data_envio
  FROM (
    SELECT
      IF(m.remetente_id = ?, m.destinatario_id, m.remetente_id) AS partner_id,
      m.mensagem,
      m.data_envio
    FROM chat_message m
    WHERE m.remetente_id = ? OR m.destinatario_id = ?
    ORDER BY m.data_envio DESC
  ) AS partner
  LEFT JOIN usuario u ON u.id = partner.partner_id
  GROUP BY partner.partner_id
  ORDER BY partner.data_envio DESC
";

$stmt = $conexao->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao preparar consulta', 'details' => $conexao->error]);
    exit;
}

$stmt->bind_param("iii", $user_id, $user_id, $user_id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao executar consulta', 'details' => $stmt->error]);
    $stmt->close();
    exit;
}

$result = $stmt->get_result();
$conversations = [];

while ($row = $result->fetch_assoc()) {
    $conversations[] = [
        'partner_id'   => (int) $row['partner_id'],
        'partner_name' => $row['partner_name'] ?? 'Usuário',
        'last_message' => $row['last_message'],
        'data_envio'   => $row['data_envio'],
    ];
}

$stmt->close();
echo json_encode($conversations);