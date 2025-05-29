// Cart&Checkout.js

// ==============================
// GESTÃO DE ENDEREÇOS DIFERENTES
// ==============================

document.getElementById('differentAddresses').addEventListener('change', function () {
    const commonAddress = document.getElementById('commonAddress');
    const serviceDetails = document.querySelectorAll('.service-details');
    const specificAddresses = document.querySelectorAll('.specific-address');

    if (this.checked) {
        commonAddress.style.display = 'none';
        specificAddresses.forEach(address => {
            address.style.display = 'block';
        });
    } else {
        commonAddress.style.display = 'block';
        specificAddresses.forEach(address => {
            address.style.display = 'none';
        });
    }

    serviceDetails.forEach(service => {
        service.style.display = 'block';
    });
});

// Inicializar visibilidade dos endereços ao carregar a página
if (document.getElementById('differentAddresses').checked) {
    document.getElementById('commonAddress').style.display = 'none';
    document.querySelectorAll('.specific-address').forEach(address => {
        address.style.display = 'block';
    });
} else {
    document.getElementById('commonAddress').style.display = 'block';
    document.querySelectorAll('.specific-address').forEach(address => {
        address.style.display = 'none';
    });
}

// ==============================
// GESTÃO DO CARRINHO E CHECKOUT
// ==============================

document.addEventListener("DOMContentLoaded", function () {
    const artigosText = document.querySelector(".page-header h3");
    const productsContainer = document.querySelector(".products-containers");
    const subtotalEl = document.querySelector(".subtotal h4:nth-child(2)");
    const totalEl = document.querySelector(".Preco-final");
    const custoDeslocacao = 0.00;
    const comprarBtn = document.getElementById("comprar-btn");

    // Função para converter o texto de preço para número
    function parsePreco(precoText) {
        return parseFloat(precoText.replace("€", "").replace(",", "."));
    }

    // Função para formatar o preço
    function formatPreco(valor) {
        const [inteira, decimal = "00"] = valor.toFixed(2).split(".");
        return `€${inteira},<span class="decimais">${decimal}</span>`;
    }

    // Função para calcular o subtotal e o total
    function calcularTotais() {
        let subtotal = 0;
        const produtos = document.querySelectorAll(".product-item");

        produtos.forEach((produto) => {
            const precoText = produto.querySelector("h3").textContent;
            subtotal += parsePreco(precoText);
        });

        subtotalEl.innerHTML = formatPreco(subtotal);
        const total = subtotal + custoDeslocacao;
        totalEl.innerHTML = formatPreco(total);

        // Desabilitar o botão "Comprar" se o total for 0 ou menor
        if (total <= 0) {
            comprarBtn.disabled = true;
            comprarBtn.style.opacity = '0.6';
            comprarBtn.style.cursor = 'not-allowed';
        } else {
            comprarBtn.disabled = false;
            comprarBtn.style.opacity = '1';
            comprarBtn.style.cursor = 'pointer';
        }
    }

    // Atualizar o número de artigos no carrinho
    function atualizarNumeroArtigos() {
        const totalProdutos = document.querySelectorAll(".product-item").length;
        artigosText.textContent = `${totalProdutos} artigo${totalProdutos !== 1 ? 's' : ''}`;

        const cartCountEl = document.getElementById("cart-count");
        if (cartCountEl) {
            cartCountEl.textContent = totalProdutos;
        }

        const cartBadge = document.getElementById("cart-badge");
        if (cartBadge) {
            if (totalProdutos === 0) {
                cartBadge.style.display = "none";
            } else {
                cartBadge.textContent = totalProdutos;
                cartBadge.style.display = "flex";

                cartBadge.classList.remove("animate");
                void cartBadge.offsetWidth;
                cartBadge.classList.add("animate");
            }
        }
    }

    // Criar mensagem de carrinho vazio
    function criarCarrinhoVazio() {
        const carrinhoVazioContainer = document.createElement("div");
        carrinhoVazioContainer.classList.add("carrinho-vazio");
        carrinhoVazioContainer.style.border = "2px dashed #555";
        carrinhoVazioContainer.style.padding = "20px";
        carrinhoVazioContainer.style.textAlign = "center";

        const imagemCarrinho = document.createElement("img");
        imagemCarrinho.src = "../Images/site/staticPages/cart.png";
        imagemCarrinho.style.width = "100px";
        imagemCarrinho.style.height = "auto";
        carrinhoVazioContainer.appendChild(imagemCarrinho);

        const tituloCarrinhoVazio = document.createElement("h2");
        tituloCarrinhoVazio.textContent = "Carrinho vazio";
        carrinhoVazioContainer.appendChild(tituloCarrinhoVazio);

        productsContainer.appendChild(carrinhoVazioContainer);
    }

    // Remover produto do carrinho
    function removerProduto(id, productItem) {
        fetch("/Controllers/remove_from_cart.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `id=${encodeURIComponent(id)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                productItem.remove();
                atualizarNumeroArtigos();
                calcularTotais();

                if (document.querySelectorAll(".product-item").length === 0) {
                    productsContainer.innerHTML = '';
                    criarCarrinhoVazio();
                }
            } else {
                console.error("Erro ao remover item:", data.message);
            }
        })
        .catch(error => {
            console.error("Erro na comunicação com o servidor:", error);
        });
    }

    // Evento de clique para remover produtos
    productsContainer.addEventListener("click", function (event) {
        const btn = event.target.closest(".remove-btn");
        if (!btn) return;

        const productItem = btn.closest(".product-item");
        const id = productItem.dataset.id;

        removerProduto(id, productItem);
    });

    // Inicializações ao carregar
    calcularTotais();
    atualizarNumeroArtigos();
});
