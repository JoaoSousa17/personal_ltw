document.addEventListener("DOMContentLoaded", () => {
  const scrollToBottom = (chatWindow) => {
    const messagesContainer = chatWindow.querySelector('.chat-messages');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  };

  // Mostrar conversa ao clicar
  document.querySelectorAll('.conversation-item').forEach(item => {
    item.addEventListener('click', () => {
      const userId = item.getAttribute('data-user-id');

      // Esconde todas as janelas
      document.querySelectorAll('.chat-window').forEach(win => win.style.display = 'none');

      // Mostra a janela clicada
      const chatWindow = document.getElementById('chat-with-' + userId);
      if (chatWindow) {
        chatWindow.style.display = 'flex';
        scrollToBottom(chatWindow);
      }

      // Marcar conversa ativa
      document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove('active'));
      item.classList.add('active');
    });
  });

  // Seleciona a primeira conversa automaticamente
  const firstConv = document.querySelector('.conversation-item');
  if (firstConv) {
    firstConv.click();
    setTimeout(() => {
      const firstUserId = firstConv.getAttribute('data-user-id');
      const firstWindow = document.getElementById('chat-with-' + firstUserId);
      if (firstWindow) scrollToBottom(firstWindow);
    }, 100);
  }

  // Envio de mensagens
  document.querySelectorAll(".message-form").forEach(form => {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const input = form.querySelector("input[name='message']");
      const message = input.value.trim();
      const receiverId = form.dataset.receiverId;

      if (message === "") return;

      try {
        const response = await fetch("/Views/Actions/send_message.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            receiver_id: receiverId,
            message: message
          })
        });

        const result = await response.json();

        if (result.success) {
          const chatWindow = form.closest(".chat-window");
          const chatMessages = chatWindow.querySelector(".chat-messages");

          const now = new Date();
          const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

          const messageDiv = document.createElement("div");
          messageDiv.className = "message sent";
          messageDiv.innerHTML = `
            ${message}
            <span class="message-time">${time}</span>
          `;

          chatMessages.appendChild(messageDiv);
          chatMessages.scrollTop = chatMessages.scrollHeight;

          input.value = "";
        } else {
          alert("Erro ao enviar mensagem: " + (result.error || "desconhecido."));
        }
      } catch (err) {
        console.error("Erro ao enviar mensagem:", err);
        alert("Erro ao enviar mensagem.");
      }
    });
  });

  // Atualizações em tempo real (polling)
  function startPolling() {
    setInterval(async () => {
      const activeConv = document.querySelector('.conversation-item.active');
      if (!activeConv) return;

      const userId = activeConv.getAttribute('data-user-id');
      const chatWindow = document.getElementById('chat-with-' + userId);
      if (!chatWindow) return;

      const chatMessages = chatWindow.querySelector('.chat-messages');

      try {
        const response = await fetch(`/Views/Actions/get_messages.php?receiver_id=${userId}`);
        const data = await response.json();

        if (data.messages && Array.isArray(data.messages)) {
          data.messages.forEach(msg => {
            const messageDiv = document.createElement("div");
            const isSent = msg.sender_id == loggedInUserId ? "sent" : "received";
            messageDiv.className = `message ${isSent}`;
            messageDiv.innerHTML = `
              ${msg.body_}
              <span class="message-time">${msg.time_.slice(0, 5)}</span>
            `;
            chatMessages.appendChild(messageDiv);
          });

          if (data.messages.length > 0) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
          }
        }
      } catch (error) {
        console.error("Erro ao buscar mensagens:", error);
      }
    }, 3000);
  }

  startPolling();
});
