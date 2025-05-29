document.getElementById("orderBtn").addEventListener("click", (event) => {
    const formData = new FormData();
  
    const productId = event.target.getAttribute("data-id");
    const title = document.querySelector(".product-info h2")?.textContent.trim();

    const price = event.target.getAttribute("data-price") || "0";

    const image = document.querySelector(".product-image-img")?.src;
    const seller = document.querySelector(".product-advertiser")?.textContent.trim().replace("Utilizador", "").trim();
  
    formData.append("id", productId);
    formData.append("title", title);
    formData.append("price", price);
    formData.append("image", image);
    formData.append("seller", seller);
  
    fetch("/Controllers/add_to_cart.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        const cartBadge = document.getElementById("cart-badge");
        if (cartBadge) {
          cartBadge.textContent = data.total;
          cartBadge.style.display = "flex";
          cartBadge.classList.remove("animate");
          void cartBadge.offsetWidth;
          cartBadge.classList.add("animate");
        }
      } else {
        alert("Erro ao adicionar ao carrinho.");
      }
    })
    .catch(() => alert("Erro de rede."));
  });
