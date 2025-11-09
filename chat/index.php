<?php
session_start();

// Redireciona usuário deslogado para a tela de login
if (!isset($_SESSION['user_id'])) {
    // envia URL atual para que o login possa redirecionar de volta
    $current = $_SERVER['REQUEST_URI'];
    header('Location: ../login/index.html?redirect=' . urlencode($current));
    exit;
}

// Supondo que o ID do usuário logado (remetente) está na sessão
$remetente_id = (int) $_SESSION['user_id'];
// Supondo que o ID da pessoa com quem ele está conversando (destinatário) venha da URL
$destinatario_id = isset($_GET['chat_with']) ? (int) $_GET['chat_with'] : 2;

// Garante que o usuário logado e o destinatário sejam diferentes
if ($remetente_id == $destinatario_id) {
    echo "Você não pode conversar consigo mesmo!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Chat Privado PHP + MySQL</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 20px auto; }
        #chat-window { border: 1px solid #ccc; height: 300px; overflow-y: scroll; padding: 10px; margin-bottom: 10px; background-color: #f9f9f9; }
        .message-box { margin-bottom: 5px; padding: 5px; border-radius: 5px; clear: both; }
        /* Sua mensagem à direita */
        .my-message { background-color: #d1e7dd; float: right; max-width: 70%; }
        /* Mensagem do outro à esquerda */
        .other-message { background-color: #f8d7da; float: left; max-width: 70%; }
        .message-box::after { content: ""; display: table; clear: both; }
    </style>
</head>
<body>

    <h2>Chat Privado (ID <?php echo $remetente_id; ?> conversando com ID <?php echo $destinatario_id; ?>)</h2>

    <div id="chat-window">
        </div>

    <form id="chat-form">
        <input type="hidden" id="remetente_id" value="<?php echo $remetente_id; ?>">
        <input type="hidden" id="destinatario_id" value="<?php echo $destinatario_id; ?>">
        <input type="text" id="message" placeholder="Digite sua mensagem..." style="width: 80%;" required>
        <button type="submit">Enviar</button>
    </form>

    <script>
        const chatWindow = document.getElementById('chat-window');
        const chatForm = document.getElementById('chat-form');
        const remetenteId = document.getElementById('remetente_id').value;
        const destinatarioId = document.getElementById('destinatario_id').value;
        const messageInput = document.getElementById('message');

        function scrollToBottom() {
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        // --- 1. FUNÇÃO DE BUSCAR MENSAGENS (POLLING) ---
        async function fetchMessages() {
            try {
                // Envia os IDs da conversa para o handler
                const response = await fetch(`chat_handler.php?action=get&remetente=${remetenteId}&destinatario=${destinatarioId}`, {
                    method: 'GET'
                });
                const messages = await response.json();

                chatWindow.innerHTML = '';
                messages.forEach(msg => {
                    const msgDiv = document.createElement('div');
                    msgDiv.classList.add('message-box');
                    
                    // Verifica se o ID do remetente é o ID do usuário logado
                    if (msg.remetente_id == remetenteId) {
                        msgDiv.classList.add('my-message');
                    } else {
                        msgDiv.classList.add('other-message');
                    }
                    
                    const time = new Date(msg.data_envio).toLocaleTimeString();
                    msgDiv.innerHTML = `<strong>(${msg.remetente_nome} - ${time})</strong><br>${msg.mensagem}`;
                    
                    chatWindow.appendChild(msgDiv);
                });

                scrollToBottom();

            } catch (error) {
                console.error('Erro ao buscar mensagens:', error);
            }
        }

        // --- 2. FUNÇÃO DE ENVIAR MENSAGENS ---
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const text = messageInput.value.trim();

            if (!text) return;

            try {
                // Envia todos os dados necessários (remetente, destinatário, texto)
                await fetch('chat_handler.php?action=send', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `remetente_id=${remetenteId}&destinatario_id=${destinatarioId}&text=${encodeURIComponent(text)}`
                });

                messageInput.value = '';
                fetchMessages(); // Atualiza após o envio

            } catch (error) {
                console.error('Erro ao enviar mensagem:', error);
            }
        });

        // Polling: Busca mensagens a cada 2 segundos
        setInterval(fetchMessages, 2000); 
        fetchMessages(); 
    </script>
</body>
</html>