document.getElementById("orderBtn").addEventListener("click", (event) => {
  const formData = new FormData();

  const productId = event.target.getAttribute("data-id");
  const title = document.querySelector(".product-info h2")?.textContent.trim();
  
  // CORRIGIDO: Obter o preço do atributo data-price (já é o preço total em EUR base)
  let price = event.target.getAttribute("data-price") || "0";
  const currency = event.target.getAttribute("data-currency") || "eur";
  
  // Debug information
  const debugDuration = event.target.getAttribute("data-debug-duration");
  const debugHours = event.target.getAttribute("data-debug-hours");
  const debugPriceHour = event.target.getAttribute("data-debug-price-hour");
  
  console.log("=== DEBUG INFORMAÇÕES DO PRODUTO ===");
  console.log("Duração (minutos):", debugDuration);
  console.log("Duração (horas):", debugHours);
  console.log("Preço por hora:", debugPriceHour);
  console.log("Preço total calculado:", price);
  console.log("Moeda base:", currency);
  console.log("=====================================");
  
  // O preço já vem limpo do PHP, mas vamos garantir
  price = price.replace(',', '.');
  
  const image = document.querySelector(".product-image-img")?.src;
  const seller = document.querySelector(".product-advertiser")?.textContent.trim().replace("Utilizador", "").trim();

  // Validação básica
  if (!productId || !title || !price) {
      alert("Erro: Informações do produto incompletas.");
      console.log("Dados:", {productId, title, price});
      return;
  }

  // Converter preço para número para validação
  const priceNum = parseFloat(price);
  if (isNaN(priceNum) || priceNum <= 0) {
      alert("Erro: Preço inválido: " + price);
      console.log("Preço original do data-price:", event.target.getAttribute("data-price"));
      console.log("Preço limpo:", price);
      console.log("Preço convertido:", priceNum);
      return;
  }

  console.log("Enviando preço base EUR:", priceNum); // Debug

  // Adicionar dados ao FormData
  formData.append("id", productId);
  formData.append("title", title);
  formData.append("price", priceNum.toString()); // Preço total em EUR (base)
  formData.append("currency", currency); // Moeda base (EUR)
  formData.append("image", image || "/Images/site/staticPages/placeholder.jpg");
  formData.append("seller", seller || "Vendedor desconhecido");
  formData.append("type", "service");

  // Desabilitar botão temporariamente
  const originalText = event.target.textContent;
  event.target.disabled = true;
  event.target.textContent = "Adicionando...";

  fetch("/Controllers/add_to_cart.php", {
      method: "POST",
      body: formData
  })
  .then(res => {
      if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.json();
  })
  .then(data => {
      console.log("Resposta do servidor:", data); // Debug
      
      if (data.status === "success") {
          // Mostrar informações de debug se disponíveis
          if (data.debug) {
              console.log("=== DEBUG CONVERSÃO ===");
              console.log("Preço original (EUR):", data.debug.price_eur);
              console.log("Preço convertido:", data.debug.converted_price);
              console.log("Moeda do utilizador:", data.debug.currency);
              console.log("=======================");
          }
          
          // Atualizar badge do carrinho
          const cartBadge = document.getElementById("cart-badge");
          if (cartBadge) {
              cartBadge.textContent = data.total;
              cartBadge.style.display = "flex";
              cartBadge.classList.remove("animate");
              void cartBadge.offsetWidth; // Force reflow
              cartBadge.classList.add("animate");
          }

          // Mostrar feedback visual
          event.target.textContent = "Adicionado!";
          event.target.style.backgroundColor = "#28a745";
          
          // Mostrar mensagem de sucesso se disponível
          if (data.message) {
              console.log(data.message);
          }

          // Restaurar botão após um tempo
          setTimeout(() => {
              event.target.disabled = false;
              event.target.textContent = originalText;
              event.target.style.backgroundColor = "";
          }, 2000);

      } else {
          throw new Error(data.message || "Erro desconhecido ao adicionar ao carrinho.");
      }
  })
  .catch(error => {
      console.error("Erro ao adicionar ao carrinho:", error);
      alert("Erro ao adicionar ao carrinho: " + error.message);
      
      // Restaurar botão
      event.target.disabled = false;
      event.target.textContent = originalText;
      event.target.style.backgroundColor = "";
  });
});
