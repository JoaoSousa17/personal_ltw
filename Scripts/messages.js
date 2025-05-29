/*======================================
   JAVASCRIPT PARA SISTEMA DE MENSAGENS
======================================*/

// Aguardar carregamento do DOM
document.addEventListener("DOMContentLoaded", () => {
    
  /*------------------
  Utilitários de Scroll
  ------------------*/
  const scrollToBottom = (chatWindow) => {
      const messagesContainer = chatWindow.querySelector('.chat-messages');
      if (messagesContainer) {
          messagesContainer.scrollTop = messagesContainer.scrollHeight;
      }
  };

  /*------------------
  Gestão de Conversas - Página Principal
  ------------------*/
  function initConversationHandlers() {
      // Mostrar conversa ao clicar
      document.querySelectorAll('.conversation-item').forEach(item => {
          item.addEventListener('click', () => {
              const userId = item.getAttribute('data-user-id');

              // Esconder todas as janelas de chat
              document.querySelectorAll('.chat-window').forEach(win => win.style.display = 'none');
              
              // Mostrar janela de chat selecionada
              const chatWindow = document.getElementById('chat-with-' + userId);
              if (chatWindow) {
                  chatWindow.style.display = 'flex';
                  scrollToBottom(chatWindow);
              }

              // Atualizar item ativo
              document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove('active'));
              item.classList.add('active');
          });
      });

      // Selecionar primeira conversa automaticamente
      const firstConv = document.querySelector('.conversation-item');
      if (firstConv) {
          firstConv.click();
          setTimeout(() => {
              const firstUserId = firstConv.getAttribute('data-user-id');
              const firstWindow = document.getElementById('chat-with-' + firstUserId);
              if (firstWindow) scrollToBottom(firstWindow);
          }, 100);
      }
  }

  /*------------------
  Envio de Mensagens - Página Principal
  ------------------*/
  function initMessageSending() {
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
  }

  /*------------------
  Polling para Atualizações em Tempo Real
  ------------------*/
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

  /*------------------
  Gestão de Modais de Pedido
  ------------------*/
  function initRequestModals() {
      document.querySelectorAll('.request-modal').forEach(modal => {
          const closeBtn = modal.querySelector('.closeRequest');
          const sendBtn = modal.querySelector('.sendRequest');
          const priceInput = modal.querySelector('.newPrice');

          // Fechar modal
          if (closeBtn) {
              closeBtn.addEventListener('click', () => {
                  modal.style.display = 'none';
                  modal.removeAttribute('data-active');
              });
          }

          // Enviar pedido
          if (sendBtn) {
              sendBtn.addEventListener('click', async () => {
                  const newPrice = priceInput.value;
                  if (!newPrice || isNaN(newPrice) || newPrice <= 0) {
                      alert('Insira um preço válido.');
                      return;
                  }

                  const userId = modal.closest('.chat-window')?.id?.split('-').pop();
                  const serviceSelect = modal.querySelector('.serviceSelect');
                  const selectedServiceId = serviceSelect.value;
                  const duration = 1; // Duração padrão (idealmente deveria ser um input)

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
          }
      });
  }

  /*------------------
  Botões de Abertura de Modal
  ------------------*/
  function initRequestButtons() {
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

                  if (!Array.isArray(services)) {
                      alert("Erro: resposta do serviço não é uma lista válida.");
                      console.error("Resposta inválida de serviços:", services);
                      return;
                  }

                  // Limpar e inserir opções
                  serviceSelect.innerHTML = "";
                  services.forEach(service => {
                      const option = document.createElement("option");
                      option.value = service.id;

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

                  // Remover event listener anterior
                  serviceSelect.onchange = null;

                  // Atualizar preço ao mudar serviço
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
  }

  /*------------------
  POPUP DE MENSAGENS (para outras páginas)
  ------------------*/
  
  // Variáveis globais para o popup
  window.receiverId = null;
  window.currentUserId = null;
  window.serviceId = null;

  // Abrir popup
  window.openPopup = function() {
      const popup = document.getElementById("messagePopup");
      if (popup) {
          popup.classList.remove("hidden");
          if (window.receiverId) {
              carregarMensagens(window.receiverId);
          }
      }
  };

  // Fechar popup
  window.closePopup = function() {
      const popup = document.getElementById("messagePopup");
      if (popup) {
          popup.classList.add("hidden");
      }
  };

  // Minimizar popup
  window.minimizePopup = function(event) {
      if (event) event.stopPropagation();
      
      const popup = document.getElementById("messagePopup");
      if (popup) {
          popup.classList.add("minimized");
      }
  };

  // Toggle popup (expandir/minimizar)
  window.togglePopup = function() {
      const popup = document.getElementById("messagePopup");
      if (popup && popup.classList.contains("minimized")) {
          popup.classList.remove("minimized");
      }
  };

  // Enviar mensagem via popup
  window.sendMessage = async function() {
      const textInput = document.getElementById("messageText");
      const text = textInput.value.trim();
      
      if (!text) return;

      try {
          const response = await fetch("/Views/Actions/send_message.php", {
              method: "POST",
              headers: {
                  "Content-Type": "application/json"
              },
              body: JSON.stringify({
                  sender_id: window.currentUserId,
                  receiver_id: window.receiverId,
                  message: text,
                  service_id: window.serviceId
              })
          });

          if (!response.ok) {
              alert("Erro ao enviar mensagem.");
              return;
          }

          const now = new Date();
          const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

          const bubble = document.createElement("div");
          bubble.className = "message-bubble";
          bubble.innerHTML = `${text}<div class="message-time">${time}</div>`;

          const messageList = document.getElementById("messageList");
          if (messageList) {
              messageList.prepend(bubble);
              messageList.scrollTop = messageList.scrollHeight;
          }

          textInput.value = "";
      } catch (err) {
          console.error("Erro ao enviar mensagem:", err);
          alert("Erro ao enviar mensagem.");
      }
  };

  // Carregar mensagens no popup
  function carregarMensagens(receiverId = window.receiverId) {
      if (!receiverId) return;

      fetch(`/Views/Actions/get_messages.php?receiver_id=${receiverId}`)
          .then(response => response.json())
          .then(messages => {
              const container = document.getElementById("messageList");
              if (!container) return;

              container.innerHTML = "";
              
              if (Array.isArray(messages)) {
                  messages.forEach(msg => {
                      const div = document.createElement("div");
                      div.className = msg.sender_id === window.currentUserId ? "mensagem-enviada" : "mensagem-recebida";

                      const text = document.createElement("div");
                      text.className = "message-text";
                      text.textContent = msg.body_;

                      const time = document.createElement("div");
                      time.className = "message-time";
                      time.textContent = msg.time_?.substring(0, 5) || "";

                      div.appendChild(text);
                      div.appendChild(time);
                      container.appendChild(div);
                  });
              }

              container.scrollTop = container.scrollHeight;
          })
          .catch(error => {
              console.error("Erro ao carregar mensagens:", error);
          });
  }

  /*------------------
  Inicialização Condicional
  ------------------*/
  
  // Verificar se estamos na página de mensagens principal
  const isMessagesPage = document.querySelector('.messages-page-container');
  
  if (isMessagesPage) {
      // Inicializar funcionalidades da página principal
      initConversationHandlers();
      initMessageSending();
      initRequestModals();
      initRequestButtons();
      startPolling();
      
      console.log('Messages page JavaScript initialized');
  } else {
      // Inicializar funcionalidades do popup para outras páginas
      console.log('Popup messages JavaScript initialized');
  }

  /*------------------
  Event Listeners Globais
  ------------------*/
  
  // Fechar modais com ESC
  document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
          // Fechar modais de pedido
          document.querySelectorAll('.request-modal[data-active="true"]').forEach(modal => {
              modal.style.display = 'none';
              modal.removeAttribute('data-active');
          });
          
          // Fechar popup se estiver aberto
          const popup = document.getElementById("messagePopup");
          if (popup && !popup.classList.contains("hidden")) {
              closePopup();
          }
      }
  });

  // Fechar modais clicando fora
  document.addEventListener('click', (e) => {
      document.querySelectorAll('.request-modal[data-active="true"]').forEach(modal => {
          if (e.target === modal) {
              modal.style.display = 'none';
              modal.removeAttribute('data-active');
          }
      });
  });

  /*------------------
  Utilitários de Validação
  ------------------*/
  
  function validateMessageInput(input) {
      const message = input.trim();
      
      if (!message) {
          return { valid: false, error: 'Mensagem não pode estar vazia' };
      }
      
      if (message.length > 1000) {
          return { valid: false, error: 'Mensagem muito longa (máximo 1000 caracteres)' };
      }
      
      return { valid: true };
  }

  function validatePriceInput(price) {
      const numPrice = parseFloat(price);
      
      if (isNaN(numPrice) || numPrice <= 0) {
          return { valid: false, error: 'Preço deve ser um número positivo' };
      }
      
      if (numPrice > 10000) {
          return { valid: false, error: 'Preço muito alto (máximo €10.000)' };
      }
      
      return { valid: true };
  }

  /*------------------
  Gestão de Estado de UI
  ------------------*/
  
  function showLoadingState(element) {
      if (element) {
          element.disabled = true;
          element.textContent = 'Enviando...';
      }
  }

  function hideLoadingState(element, originalText = 'Enviar') {
      if (element) {
          element.disabled = false;
          element.textContent = originalText;
      }
  }

  /*------------------
  Formatação de Tempo
  ------------------*/
  
  function formatTime(date = new Date()) {
      return date.toLocaleTimeString([], { 
          hour: '2-digit', 
          minute: '2-digit',
          hour12: false 
      });
  }

  function formatDate(dateString) {
      const date = new Date(dateString);
      const now = new Date();
      const diffInHours = (now - date) / (1000 * 60 * 60);
      
      if (diffInHours < 24) {
          return formatTime(date);
      } else if (diffInHours < 48) {
          return 'Ontem';
      } else {
          return date.toLocaleDateString('pt-PT', { 
              day: '2-digit', 
              month: '2-digit' 
          });
      }
  }

  // Log para debug (remover em produção)
  console.log('Messages JavaScript module loaded');
});