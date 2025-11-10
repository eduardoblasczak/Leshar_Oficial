async function loadConversations() {
  try {
    // ajuste o caminho se necessário; aqui assume que home/index.html está em /home/
    const res = await fetch('../chat/conversations.php', { credentials: 'same-origin' });
    if (!res.ok) {
      console.warn('Não autenticado ou erro ao buscar conversas');
      return;
    }

    const convs = await res.json();
    const list = document.getElementById('conversations-list');
    list.innerHTML = '';

    if (!convs.length) {
      list.innerHTML = '<div>Nenhuma conversa recente.</div>';
      return;
    }

    convs.forEach(c => {
      const div = document.createElement('div');
      div.className = 'conv';
      div.dataset.id = c.partner_id;

      const time = new Date(c.data_envio).toLocaleString([], {
        hour: '2-digit',
        minute: '2-digit',
        day: '2-digit',
        month: '2-digit'
      });

      div.innerHTML = `
        <div>
          <span class="name">${escapeHtml(c.partner_name)}</span>
          <span class="time">${time}</span>
        </div>
        <div class="last">${escapeHtml(c.last_message)}</div>
      `;

      div.addEventListener('click', () => {
        window.location.href = `../chat/index.php?chat_with=${c.partner_id}`;
      });

      list.appendChild(div);
    });
  } catch (err) {
    console.error('Erro ao carregar conversas', err);
  }
}

// função mínima para escapar HTML na exibição
function escapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

loadConversations();
setInterval(loadConversations, 5000);
