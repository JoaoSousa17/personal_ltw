document.addEventListener("DOMContentLoaded", function () {
    const artigosText = document.querySelector(".page-header h3");
    const productsContainer = document.querySelector(".products-containers");
    const subtotalEl = document.querySelector(".subtotal-value");
    const totalEl = document.querySelector(".Preco-final");
    const custoDeslocacao = 0.00;
    const comprarBtn = document.getElementById("comprar-btn");

    // Função para extrair o valor numérico do preço
    function parsePreco(precoText) {
        if (!precoText) return 0;
        
        // Remover símbolos de moeda e converter vírgulas para pontos
        const cleanText = precoText.toString()
            .replace(/[€$£R$]/g, '') // Remove símbolos de moeda
            .replace(/\s/g, '') // Remove espaços
            .replace(',', '.') // Converte vírgula para ponto
            .replace(/[^\d.]/g, ''); // Remove qualquer coisa que não seja dígito ou ponto
        
        const value = parseFloat(cleanText);
        return isNaN(value) ? 0 : value;
    }

    // Função para formatar o preço com o símbolo da moeda
    function formatPreco(valor, symbol = '€') {
        if (isNaN(valor) || valor === null || valor === undefined) {
            valor = 0;
        }
        
        const [inteira, decimal = "00"] = valor.toFixed(2).split(".");
        return `${symbol}${inteira},<span class="decimais">${decimal}</span>`;
    }

    // Função para calcular o subtotal e o total
    function calcularTotais() {
        let subtotal = 0;
        const produtos = document.querySelectorAll(".product-item");
        
        // Detectar símbolo da moeda do primeiro item
        let currencySymbol = '€';
        const firstPriceEl = document.querySelector(".item-price");
        if (firstPriceEl) {
            const priceText = firstPriceEl.textContent;
            if (priceText.includes('$')) currencySymbol = '$';
            else if (priceText.includes('£')) currencySymbol = '£';
            else if (priceText.includes('R$')) currencySymbol = 'R$';
        }

        produtos.forEach((produto) => {
            // Tentar obter o preço do atributo data-price primeiro
            let precoValue = 0;
            const dataPrice = produto.querySelector('[data-price]')?.getAttribute('data-price');
            
            if (dataPrice) {
                precoValue = parseFloat(dataPrice);
            } else {
                // Fallback para o texto do elemento h3
                const precoEl = produto.querySelector("h3");
                if (precoEl) {
                    precoValue = parsePreco(precoEl.textContent);
                }
            }
            
            if (!isNaN(precoValue) && precoValue > 0) {
                subtotal += precoValue;
            }
        });

        console.log('Subtotal calculado:', subtotal); // Debug

        // Atualizar displays
        if (subtotalEl) {
            subtotalEl.innerHTML = formatPreco(subtotal, currencySymbol);
        }
        
        const total = subtotal + custoDeslocacao;
        if (totalEl) {
            totalEl.innerHTML = formatPreco(total, currencySymbol);
        }

        // Atualizar o input hidden do formulário
        const totalInput = document.querySelector('input[name="total"]');
        if (totalInput) {
            totalInput.value = total.toFixed(2);
        }

        // Controlar o estado do botão "Comprar"
        if (comprarBtn) {
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
    }

    // Função para atualizar o número de artigos no carrinho
    function atualizarNumeroArtigos() {
        const totalProdutos = document.querySelectorAll(".product-item").length;
        
        if (artigosText) {
            artigosText.textContent = `${totalProdutos} artigo${totalProdutos !== 1 ? 's' : ''}`;
        }
    
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

    // Função para criar a mensagem de carrinho vazio
    function criarCarrinhoVazio() {
        const carrinhoVazioContainer = document.createElement("div");
        carrinhoVazioContainer.classList.add("carrinho-vazio");
        carrinhoVazioContainer.style.cssText = `
            border: 2px dashed #555;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin: 20px 0;
        `;

        const imagemCarrinho = document.createElement("img");
        imagemCarrinho.src = "../Images/site/staticPages/cart.png";
        imagemCarrinho.style.cssText = "width: 100px; height: auto; margin-bottom: 15px;";
        carrinhoVazioContainer.appendChild(imagemCarrinho);

        const tituloCarrinhoVazio = document.createElement("h2");
        tituloCarrinhoVazio.textContent = "Carrinho vazio";
        tituloCarrinhoVazio.style.color = "#666";
        carrinhoVazioContainer.appendChild(tituloCarrinhoVazio);

        const subtitulo = document.createElement("p");
        subtitulo.textContent = "Adicione itens ao seu carrinho para continuar";
        subtitulo.style.color = "#999";
        carrinhoVazioContainer.appendChild(subtitulo);

        productsContainer.innerHTML = '';
        productsContainer.appendChild(carrinhoVazioContainer);
    }

    // Função para remover o produto do carrinho
    function removerProduto(id, productItem) {
        // Mostrar loading
        const removeBtn = productItem.querySelector('.remove-btn');
        if (removeBtn) {
            removeBtn.disabled = true;
            removeBtn.style.opacity = '0.5';
        }

        fetch("/Controllers/remove_from_cart.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `id=${encodeURIComponent(id)}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Animar remoção
                productItem.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                productItem.style.opacity = '0';
                productItem.style.transform = 'translateX(-100%)';
                
                setTimeout(() => {
                    productItem.remove();
                    atualizarNumeroArtigos();
                    calcularTotais();

                    if (document.querySelectorAll(".product-item").length === 0) {
                        criarCarrinhoVazio();
                    }
                }, 300);
            } else {
                console.error("Erro ao remover item:", data.message);
                alert("Erro ao remover item: " + (data.message || "Erro desconhecido"));
                
                // Restaurar botão
                if (removeBtn) {
                    removeBtn.disabled = false;
                    removeBtn.style.opacity = '1';
                }
            }
        })
        .catch(error => {
            console.error("Erro na comunicação com o servidor:", error);
            alert("Erro ao conectar com o servidor. Tente novamente.");
            
            // Restaurar botão
            if (removeBtn) {
                removeBtn.disabled = false;
                removeBtn.style.opacity = '1';
            }
        });
    }

    // Evento para remover produtos
    if (productsContainer) {
        productsContainer.addEventListener("click", function (event) {
            const btn = event.target.closest(".remove-btn");
            if (!btn) return;

            event.preventDefault();
            event.stopPropagation();

            const productItem = btn.closest(".product-item");
            if (!productItem) return;

            const id = productItem.dataset.id;
            if (!id) {
                alert("Erro: ID do produto não encontrado");
                return;
            }

            // Confirmação antes de remover
            if (confirm("Tem certeza que deseja remover este item do carrinho?")) {
                removerProduto(id, productItem);
            }
        });
    }

    // Inicializações
    console.log('Inicializando carrinho...'); // Debug
    calcularTotais();
    atualizarNumeroArtigos();
    
    // Verificar se o carrinho está vazio na inicialização
    if (document.querySelectorAll(".product-item").length === 0) {
        criarCarrinhoVazio();
    }
});
