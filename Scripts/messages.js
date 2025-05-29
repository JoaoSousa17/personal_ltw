document.addEventListener("DOMContentLoaded", () => {
  const scrollToBottom = (chatWindow) => {
    const messagesContainer = chatWindow.querySelector('.chat-messages');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  };

  // === Mostrar conversa ao clicar ===
  document.querySelectorAll('.conversation-item').forEach(item => {
    item.addEventListener('click', () => {
      const userId = item.getAttribute('data-user-id');

      document.querySelectorAll('.chat-window').forEach(win => win.style.display = 'none');
      const chatWindow = document.getElementById('chat-with-' + userId);
      if (chatWindow) {
        chatWindow.style.display = 'flex';
        scrollToBottom(chatWindow);
      }

      document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove('active'));
      item.classList.add('active');
    });
  });

  // === Seleciona a primeira conversa automaticamente ===
  const firstConv = document.querySelector('.conversation-item');
  if (firstConv) {
    firstConv.click();
    setTimeout(() => {
      const firstUserId = firstConv.getAttribute('data-user-id');
      const firstWindow = document.getElementById('chat-with-' + firstUserId);
      if (firstWindow) scrollToBottom(firstWindow);
    }, 100);
  }

  // === Envio de mensagens ===
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
          body: JSON.stringify({ receiver_id: receiverId, message })
        });

        const result = await response.json();

        if (result.success) {
          const chatWindow = form.closest(".chat-window");
          const chatMessages = chatWindow.querySelector(".chat-messages");

          const now = new Date();
          const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

          const messageDiv = document.createElement("div");
          messageDiv.className = "message sent";
          messageDiv.innerHTML = `${message}<span class="message-time">${time}</span>`;

          chatMessages.appendChild(messageDiv);
          scrollToBottom(chatWindow);
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

  // === Atualizações em tempo real (Polling) ===
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
            const isSent = msg.sender_id == loggedInUserId ? "sent" : "received";

            const messageDiv = document.createElement("div");
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

  // === Modais de Pedido ===
  document.querySelectorAll('.request-modal').forEach(modal => {
    const closeBtn = modal.querySelector('.closeRequest');
    const sendBtn = modal.querySelector('.sendRequest');
    const priceInput = modal.querySelector('.newPrice');

    closeBtn.addEventListener('click', () => {
      modal.style.display = 'none';
      modal.removeAttribute('data-active');
    });

    sendBtn.addEventListener('click', async () => {
  const newPrice = priceInput.value;
  if (!newPrice || isNaN(newPrice) || newPrice <= 0) {
    alert('Insira um preço válido.');
    return;
  }

  const userId = modal.closest('.chat-window')?.id?.split('-').pop();

  // Pega o serviço selecionado
  const serviceSelect = modal.querySelector('.serviceSelect');
  const selectedServiceId = serviceSelect.value;

  // Aqui tens que definir duração, por exemplo um input que falta no modal, ou pode ser fixo (exemplo: 1 hora)
  const duration = 1; // Ideal criar input para duração no modal, ou pegar de outro lugar.

  try {
    const response = await fetch('/Views/Actions/send_request.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        freelancer_id: userId,
        service_id: selectedServiceId,
        price: parseFloat(newPrice),
        duration: duration
      }),
    });

    const result = await response.json();

    if (result.success) {
      alert('Pedido enviado com sucesso!');
      modal.style.display = 'none';
      modal.removeAttribute('data-active');
      priceInput.value = "";
    } else {
      alert('Erro ao enviar pedido: ' + (result.error || 'desconhecido.'));
    }
  } catch (err) {
    console.error('Erro ao enviar pedido:', err);
    alert('Erro ao enviar pedido.');
  }
});

  });

  // === Abrir modal via botões ===
  document.querySelectorAll('.request-button').forEach(button => {
    button.addEventListener('click', async () => {
      const freelancerId = button.getAttribute('data-freelancer-id');
      const chatWindow = button.closest('.chat-window');
      const modal = chatWindow.querySelector('.request-modal');
      const serviceSelect = modal.querySelector('.serviceSelect');
      const hourlyRateEl = modal.querySelector('.hourlyRate');

      try {
        console.log("Freelancer ID:", freelancerId);
        const response = await fetch(`/Views/Actions/get_service.php?freelancer_id=${freelancerId}`);
        const services = await response.json();
        console.log("Serviços recebidos:", services);

        if (services.error) {
          alert("Erro: " + services.error);
          return;
        }

        // Verifica se services é um array antes de usar forEach
        if (!Array.isArray(services)) {
          alert("Erro: resposta do serviço não é uma lista válida.");
          console.error("Resposta inválida de serviços:", services);
          return;
        }

        // Limpa e insere opções
        serviceSelect.innerHTML = "";
        services.forEach(service => {
          const option = document.createElement("option");
          option.value = service.id;

          // Ajusta o nome do campo conforme a propriedade real do JSON
          const serviceName = service.name_ !== undefined ? service.name_ : service.name || "Sem nome";

          option.textContent = `${serviceName} (€${parseFloat(service.price_per_hour).toFixed(2)}/h)`;
          option.dataset.price = service.price_per_hour;
          serviceSelect.appendChild(option);
        });

        if (services.length > 0) {
          hourlyRateEl.textContent = parseFloat(services[0].price_per_hour).toFixed(2);
        } else {
          hourlyRateEl.textContent = "--";
        }

        // Remove event listener anterior para evitar múltiplos anexos
        serviceSelect.onchange = null;

        // Atualiza preço ao mudar serviço
        serviceSelect.addEventListener("change", (e) => {
          const selectedOption = e.target.selectedOptions[0];
          hourlyRateEl.textContent = parseFloat(selectedOption.dataset.price).toFixed(2);
        });

        modal.style.display = 'flex';
        modal.setAttribute('data-active', 'true');
      } catch (err) {
        console.error("Erro ao buscar serviço:", err);
        alert("Erro ao buscar serviço.");
      }
    });
  });
});
