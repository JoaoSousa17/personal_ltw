function openPopup() {
    document.getElementById("messagePopup").classList.remove("hidden");
    carregarMensagens(receiverId);
}

function closePopup() {
    document.getElementById("messagePopup").classList.add("hidden");
}

async function sendMessage() {
  const text = document.getElementById("messageText").value.trim();
  if (!text) return;

  const response = await fetch("/Views/Actions/send_message.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      sender_id: currentUserId,
      receiver_id: receiverId,
      message: text,
      service_id: serviceId
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
  messageList.prepend(bubble);

  document.getElementById("messageText").value = "";
  messageList.scrollTop = messageList.scrollHeight;
}


function minimizePopup(event) {
  event.stopPropagation();

  const popup = document.getElementById("messagePopup");
  popup.classList.add("minimized");
}

function togglePopup() {
  const popup = document.getElementById("messagePopup");
  if (popup.classList.contains("minimized")) {
    popup.classList.remove("minimized");
  }
}

function carregarMensagens(receiverId) {
    fetch(`/Views/Actions/get_messages.php?receiver_id=${receiverId}`)
        .then(response => response.json())
        .then(messages => {
            const container = document.getElementById("messageList");
            container.innerHTML = "";
            messages.forEach(msg => {
              const div = document.createElement("div");
              div.className = msg.sender_id === currentUserId ? "mensagem-enviada" : "mensagem-recebida";

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

            container.scrollTop = container.scrollHeight;
        });
}
