const produtoContainer = document.getElementById("produto-container");
const HOME_ROUTE_STORAGE_KEY = "imperio_home_route";
const THEME_STORAGE_KEY = "imperio_theme";

function formatarPreco(valor) {
    return `R$ ${Number(valor).toFixed(2).replace(".", ",")}`;
}

function obterRotaHome() {
    return sessionStorage.getItem(HOME_ROUTE_STORAGE_KEY) || "index.html";
}

function atualizarLinksDeRetorno() {
    const rotaHome = obterRotaHome();

    document.querySelectorAll("[data-home-link]").forEach((link) => {
        link.setAttribute("href", rotaHome);
    });
}

function aplicarTemaDaHome() {
    const temaSalvo = localStorage.getItem(THEME_STORAGE_KEY);

    if (temaSalvo === "dark" || temaSalvo === "light") {
        document.body.setAttribute("data-theme", temaSalvo);
        return;
    }

    const temaPreferido = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
    document.body.setAttribute("data-theme", temaPreferido);
}

function criarEstadoVazio() {
    const rotaHome = obterRotaHome();

    produtoContainer.innerHTML = `
        <section class="produto-estado">
            <p>Produto nao encontrado.</p>
            <a href="${rotaHome}">Voltar para a vitrine</a>
        </section>
    `;
}

function renderizarProduto(produto, relacionados) {
    const rotaHome = obterRotaHome();
    const galeria = produto.imagens.length > 0 ? produto.imagens : [produto.imagem].filter(Boolean);
    const miniaturas = galeria.map((imagem, index) => `
        <button type="button" class="produto-miniatura${index === 0 ? " ativo" : ""}" data-imagem="${imagem}">
            <img src="${imagem}" alt="${produto.nome}">
        </button>
    `).join("");

    const relacionadosHtml = relacionados.map((item) => `
        <a class="produto-relacionado" href="produto.html?id=${encodeURIComponent(item.slug)}">
            <img src="${item.imagem}" alt="${item.nome}">
            <strong>${item.nome}</strong>
            <span>${formatarPreco(item.preco)}</span>
        </a>
    `).join("");

    produtoContainer.innerHTML = `
        <section class="produto-detalhe">
            <div class="produto-galeria">
                <div class="produto-miniaturas">
                    ${miniaturas}
                </div>
                <div class="produto-imagem-principal">
                    <span class="produto-selo">${produto.destaque}</span>
                    <img id="produto-imagem-atual" src="${galeria[0] || ""}" alt="${produto.nome}">
                </div>
            </div>

            <div class="produto-info">
                <div class="produto-breadcrumb">
                    <a href="${rotaHome}">Inicio</a>
                    <span>></span>
                    <span>${produto.categoria}</span>
                    <span>></span>
                    <span>${produto.nome}</span>
                </div>
                <h1>${produto.nome}</h1>
                <p class="produto-ref">Ref: ${produto.ref}</p>
                <p class="produto-preco">${formatarPreco(produto.preco)}</p>
                <p class="produto-descricao">${produto.descricao}</p>

                ${produto.peso ? `<div class="produto-meta"><span>Peso</span><strong>${produto.peso}</strong></div>` : ""}
                ${produto.categoria ? `<div class="produto-meta"><span>Categoria</span><strong>${produto.categoria}</strong></div>` : ""}

                <a class="produto-cta" href="${rotaHome}">Voltar para a vitrine</a>

                ${relacionados.length > 0 ? `
                    <div class="produto-relacionados-box">
                        <h2>Experimente outro sabor:</h2>
                        <div class="produto-relacionados-lista">${relacionadosHtml}</div>
                    </div>
                ` : ""}
            </div>
        </section>
    `;

    const imagemAtual = document.getElementById("produto-imagem-atual");
    const botoesMiniatura = produtoContainer.querySelectorAll(".produto-miniatura");

    botoesMiniatura.forEach((botao) => {
        botao.addEventListener("click", () => {
            imagemAtual.src = botao.dataset.imagem;
            botoesMiniatura.forEach((item) => item.classList.remove("ativo"));
            botao.classList.add("ativo");
        });
    });
}

async function iniciarPaginaProduto() {
    const params = new URLSearchParams(window.location.search);
    const identificador = params.get("id");

    if (!identificador) {
        criarEstadoVazio();
        return;
    }

    const produto = await buscarChocolatePorIdentificador(identificador);

    if (!produto) {
        criarEstadoVazio();
        return;
    }

    document.title = `${produto.nome} | Velle Dulcis`;

    const chocolates = await carregarTodosChocolates();
    const relacionados = chocolates
        .filter((item) => item.id !== produto.id)
        .slice(0, 4);

    renderizarProduto(produto, relacionados);
}

atualizarLinksDeRetorno();
aplicarTemaDaHome();
iniciarPaginaProduto();
