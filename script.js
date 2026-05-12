const listaCarrinho = document.getElementById("lista-carrinho");
const qtdItens = document.getElementById("qtd-itens");
const btnCarrinho = document.getElementById("btn-carrinho");
const carrinhoLateral = document.getElementById("carrinho-lateral");
const overlayCarrinho = document.getElementById("overlay-carrinho");
const fecharCarrinho = document.getElementById("fechar-carrinho");
const tituloCarrinho = document.querySelector(".carrinho-topo h3");
const resumoCarrinho = document.querySelector(".carrinho-resumo");
const subtotalPreco = document.getElementById("subtotal-preco");
const totalPrecoElement = document.getElementById("total-preco");
const abrirPesquisa = document.getElementById("abrir-pesquisa");
const overlayPesquisa = document.getElementById("overlay-pesquisa");
const fecharPesquisa = document.getElementById("fechar-pesquisa");
const barraPesquisa = document.getElementById("barra-pesquisa");
const btnLimparPesquisa = document.getElementById("btn-limpar-pesquisa");
const btnVerTodos = document.getElementById("btn-ver-todos");
const container = document.getElementById("container-cards");
const vitrineCount = document.getElementById("vitrine-count");
const vitrineSortButtons = document.querySelectorAll("[data-vitrine-sort]");
const vitrineScrollButtons = document.querySelectorAll("[data-vitrine-scroll]");
const instagramLinks = document.querySelectorAll('a[aria-label="Instagram"]');
const painelPesquisa = document.getElementById("painel-pesquisa");
const listaSugestoes = document.getElementById("lista-sugestoes");
const listaResultadosPesquisa = document.getElementById("lista-resultados-pesquisa");
const listaAtalhosPesquisa = document.getElementById("lista-atalhos-pesquisa");
const popupCarrinho = document.getElementById("popup-carrinho");
const popupCarrinhoProduto = document.getElementById("popup-carrinho-produto");
const popupCarrinhoBarra = document.getElementById("popup-carrinho-barra");
const popupRemocao = document.getElementById("popup-remocao");
const popupRemocaoTexto = document.getElementById("popup-remocao-texto");
const fecharPopupRemocao = document.getElementById("fechar-popup-remocao");
const popupBoasVindas = document.getElementById("popup-boas-vindas");
const fecharPopupBoasVindas = document.getElementById("fechar-popup-boas-vindas");
const btnTema = document.getElementById("btn-tema");
const btnFinalizarPedido = document.getElementById("finalizar");
const headerSite = document.querySelector(".topo-site");
const menuMobileToggle = document.getElementById("menu-mobile-toggle");
const menuMobile = document.getElementById("menu-mobile");
const menuMobileCloseButtons = document.querySelectorAll("[data-menu-mobile-close]");
const menuMobileActionButtons = document.querySelectorAll("[data-mobile-action]");
const menuMobileLinks = document.querySelectorAll(".menu-mobile__link, .menu-mobile__acao-link");
const HOME_ROUTE_STORAGE_KEY = "imperio_home_route";
const THEME_STORAGE_KEY = "imperio_theme";
const VITRINE_SORT_STORAGE_KEY = "imperio_vitrine_sort";
const VITRINE_SORT_OPTIONS = new Set(["recommended", "price-asc"]);
const CART_SYNC_URL = "carrinho.php";
const CHECKOUT_URL = "finalizar_pedido.php";
const rotaHomeAtual = /\.php($|\?)/i.test(window.location.pathname) ? "index.php" : "index.html";
const HEADER_COMPACT_ENTER_SCROLL = 80;
const HEADER_COMPACT_EXIT_SCROLL = 28;
const THEME_TRANSITION_CLASS = "tema-transicao-ativa";
const THEME_TRANSITION_DURATION = 820;
const REDUCE_MOTION_QUERY = "(prefers-reduced-motion: reduce)";
const MOBILE_MENU_CLOSE_DURATION = 320;

aplicarLinkDaConta();
sessionStorage.setItem(HOME_ROUTE_STORAGE_KEY, rotaHomeAtual);

let timeoutAnimacaoTema;
let timeoutFechamentoMenuMobile;

function aplicarTema(theme) {
    document.body.setAttribute("data-theme", theme);

    if (!btnTema) {
        return;
    }

    const temaEscuroAtivo = theme === "dark";
    btnTema.setAttribute("aria-label", temaEscuroAtivo ? "Ativar modo claro" : "Ativar modo escuro");
    btnTema.setAttribute("title", temaEscuroAtivo ? "Ativar modo claro" : "Ativar modo escuro");
}

function obterTemaInicial() {
    const temaSalvo = localStorage.getItem(THEME_STORAGE_KEY);

    if (temaSalvo === "dark" || temaSalvo === "light") {
        return temaSalvo;
    }

    return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
}

function prefereMovimentoReduzido() {
    return window.matchMedia(REDUCE_MOTION_QUERY).matches;
}

function obterOrigemAnimacaoTema(origem) {
    if (origem && typeof origem.getBoundingClientRect === "function") {
        const dimensoes = origem.getBoundingClientRect();

        return {
            x: dimensoes.left + dimensoes.width / 2,
            y: dimensoes.top + dimensoes.height / 2,
        };
    }

    return {
        x: window.innerWidth * 0.5,
        y: Math.min(96, window.innerHeight * 0.16),
    };
}

function animarTransicaoDeTema(origem) {
    if (prefereMovimentoReduzido()) {
        document.body.classList.remove(THEME_TRANSITION_CLASS);
        return;
    }

    const { x, y } = obterOrigemAnimacaoTema(origem);

    document.body.style.setProperty("--tema-origem-x", `${x}px`);
    document.body.style.setProperty("--tema-origem-y", `${y}px`);

    document.body.classList.remove(THEME_TRANSITION_CLASS);
    void document.body.offsetWidth;
    document.body.classList.add(THEME_TRANSITION_CLASS);

    clearTimeout(timeoutAnimacaoTema);
    timeoutAnimacaoTema = window.setTimeout(() => {
        document.body.classList.remove(THEME_TRANSITION_CLASS);
    }, THEME_TRANSITION_DURATION);
}

function alternarTema(origem) {
    const temaAtual = document.body.getAttribute("data-theme") === "dark" ? "dark" : "light";
    const proximoTema = temaAtual === "dark" ? "light" : "dark";
    localStorage.setItem(THEME_STORAGE_KEY, proximoTema);
    aplicarTema(proximoTema);
    animarTransicaoDeTema(origem || btnTema);
}

function menuMobileAberto() {
    return Boolean(menuMobile && menuMobile.classList.contains("ativo"));
}

function abrirMenuMobile() {
    if (!menuMobile || !menuMobileToggle) {
        return;
    }

    clearTimeout(timeoutFechamentoMenuMobile);
    menuMobile.classList.remove("menu-mobile--fechando");
    menuMobile.classList.add("ativo");
    menuMobile.setAttribute("aria-hidden", "false");
    menuMobileToggle.setAttribute("aria-expanded", "true");
    document.body.classList.add("sem-rolagem");
}

function fecharMenuMobile() {
    if (!menuMobile || !menuMobileToggle) {
        return;
    }

    if (!menuMobile.classList.contains("ativo") && !menuMobile.classList.contains("menu-mobile--fechando")) {
        return;
    }

    clearTimeout(timeoutFechamentoMenuMobile);
    menuMobile.classList.remove("ativo");
    menuMobile.classList.add("menu-mobile--fechando");
    menuMobileToggle.setAttribute("aria-expanded", "false");

    timeoutFechamentoMenuMobile = window.setTimeout(() => {
        menuMobile.classList.remove("menu-mobile--fechando");
        menuMobile.setAttribute("aria-hidden", "true");

        if (overlayPesquisa.classList.contains("oculto") && !carrinhoLateral.classList.contains("ativo")) {
            document.body.classList.remove("sem-rolagem");
        }
    }, MOBILE_MENU_CLOSE_DURATION);
}

function atualizarEstadoHeader() {
    if (!headerSite) {
        return;
    }

    const headerCompacto = headerSite.classList.contains("topo-site--compact");
    const scrollAtual = window.scrollY;

    if (!headerCompacto && scrollAtual >= HEADER_COMPACT_ENTER_SCROLL) {
        headerSite.classList.add("topo-site--compact");
        return;
    }

    if (headerCompacto && scrollAtual <= HEADER_COMPACT_EXIT_SCROLL) {
        headerSite.classList.remove("topo-site--compact");
    }
}

aplicarTema(obterTemaInicial());
atualizarEstadoHeader();

if (btnTema) {
    btnTema.addEventListener("click", (event) => {
        alternarTema(event.currentTarget);
    });
}

if (menuMobileToggle) {
    menuMobileToggle.addEventListener("click", () => {
        if (menuMobileAberto()) {
            fecharMenuMobile();
            return;
        }

        abrirMenuMobile();
    });
}

menuMobileCloseButtons.forEach((button) => {
    button.addEventListener("click", fecharMenuMobile);
});

menuMobileLinks.forEach((link) => {
    link.addEventListener("click", fecharMenuMobile);
});

menuMobileActionButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
        event.preventDefault();
        event.stopPropagation();

        const action = button.dataset.mobileAction;
        fecharMenuMobile();

        window.setTimeout(() => {
            if (action === "theme") {
                alternarTema(button);
            }

            if (action === "search") {
                abrirOverlayPesquisa();
            }

            if (action === "cart") {
                void abrirCarrinho();
            }
        }, MOBILE_MENU_CLOSE_DURATION + 20);
    });
});

window.addEventListener("scroll", () => {
    atualizarEstadoHeader();
    reposicionarPopupCarrinhoAtivo();
}, { passive: true });
window.addEventListener("resize", () => {
    if (window.innerWidth > 768 && menuMobileAberto()) {
        fecharMenuMobile();
    }

    reposicionarPopupCarrinhoAtivo();
});
window.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && menuMobileAberto()) {
        fecharMenuMobile();
    }
});

let carrinho = [];
let listaChocolates = [];
let popupCarrinhoTimeout;
let popupCarrinhoInicio = 0;
let popupRemocaoTimeout;
let popupBoasVindasTimeout;
let sincronizandoCarrinho = false;
let timeoutFechamentoPesquisa;
let carregamentoCarrinhoPromise = Promise.resolve();
let ordenacaoVitrine = obterOrdenacaoInicial();
let vitrineArrastando = false;
let vitrineArrasteInicioX = 0;
let vitrineScrollInicio = 0;

const DURACAO_POPUP_CARRINHO = 4000;

function esconderPopupCarrinho() {
    popupCarrinho.classList.remove("ativo");
    popupCarrinho.setAttribute("aria-hidden", "true");
}

function encerrarPopupCarrinho() {
    clearTimeout(popupCarrinhoTimeout);
    popupCarrinhoInicio = 0;
    esconderPopupCarrinho();
}

function animarBarraPopup(duracao, proporcaoInicial = 1) {
    popupCarrinhoBarra.style.transition = "none";
    popupCarrinhoBarra.style.transform = `scaleX(${proporcaoInicial})`;
    popupCarrinhoBarra.offsetHeight;
    popupCarrinhoBarra.style.transition = `transform ${duracao}ms linear`;
    popupCarrinhoBarra.style.transform = "scaleX(0)";
}

function posicionarPopupCarrinho() {
    if (!popupCarrinho || !btnCarrinho) {
        return;
    }

    const margemTela = window.innerWidth <= 768 ? 14 : 12;
    const larguraPopup = Math.min(window.innerWidth <= 768 ? 300 : 340, window.innerWidth - margemTela * 2);
    const carrinhoRect = btnCarrinho.getBoundingClientRect();
    const centroCarrinho = carrinhoRect.left + carrinhoRect.width / 2;
    const esquerdaIdeal = carrinhoRect.right - larguraPopup + 26;
    const esquerdaPopup = Math.min(
        Math.max(esquerdaIdeal, margemTela),
        window.innerWidth - larguraPopup - margemTela
    );
    const setaTamanho = window.innerWidth <= 768 ? 16 : 20;
    const setaLeft = Math.min(
        Math.max(centroCarrinho - esquerdaPopup - setaTamanho / 2, 18),
        larguraPopup - 28
    );

    popupCarrinho.style.setProperty("--popup-carrinho-top", `${carrinhoRect.bottom + 14}px`);
    popupCarrinho.style.setProperty("--popup-carrinho-left", `${esquerdaPopup}px`);
    popupCarrinho.style.setProperty("--popup-carrinho-seta-left", `${setaLeft}px`);
}

function reposicionarPopupCarrinhoAtivo() {
    if (popupCarrinho && popupCarrinho.classList.contains("ativo")) {
        posicionarPopupCarrinho();
    }
}

function mostrarPopupCarrinho(nomeProduto, duracao = DURACAO_POPUP_CARRINHO, proporcaoInicial = 1) {
    if (carrinhoLateral.classList.contains("ativo")) {
        return;
    }

    clearTimeout(popupCarrinhoTimeout);
    popupCarrinhoInicio = Date.now();

    popupCarrinhoProduto.textContent = nomeProduto;
    posicionarPopupCarrinho();
    popupCarrinho.classList.add("ativo");
    popupCarrinho.setAttribute("aria-hidden", "false");
    animarBarraPopup(duracao, proporcaoInicial);

    popupCarrinhoTimeout = setTimeout(encerrarPopupCarrinho, duracao);
}

function pausarPopupCarrinho() {
    if (!popupCarrinho.classList.contains("ativo") || !popupCarrinhoInicio) {
        return;
    }

    encerrarPopupCarrinho();
}

function mostrarPopupRemocao(nomeProduto) {
    mostrarPopupAcao(`${nomeProduto} removido do carrinho.`);
}

function mostrarPopupAcao(mensagem) {
    clearTimeout(popupRemocaoTimeout);
    popupRemocaoTexto.textContent = mensagem;
    popupRemocao.classList.add("ativo");
    popupRemocao.setAttribute("aria-hidden", "false");

    popupRemocaoTimeout = setTimeout(() => {
        popupRemocao.classList.remove("ativo");
        popupRemocao.setAttribute("aria-hidden", "true");
    }, 3500);
}

function fecharPopupBoasVindasComEstado() {
    if (!popupBoasVindas) {
        return;
    }

    clearTimeout(popupBoasVindasTimeout);
    popupBoasVindas.classList.remove("ativo");
    popupBoasVindas.setAttribute("aria-hidden", "true");
}

function exibirPopupBoasVindas() {
    if (!popupBoasVindas || !window.APP_AUTH || !window.APP_AUTH.mensagemBoasVindas) {
        return;
    }

    popupBoasVindas.classList.add("ativo");
    popupBoasVindas.setAttribute("aria-hidden", "false");
    popupBoasVindasTimeout = setTimeout(fecharPopupBoasVindasComEstado, 4200);
}

function obterQuantidadeNoCarrinho(nomeProduto) {
    const item = carrinho.find((produto) => produto.nome === nomeProduto);
    return item ? item.qtd : 0;
}

function usuarioTemCarrinhoPersistente() {
    return Boolean(window.APP_AUTH && window.APP_AUTH.autenticado);
}

function obterDadosProduto(nomeProduto, imagemProduto = "") {
    return listaChocolates.find((produto) =>
        produto.nome === nomeProduto && (!imagemProduto || produto.imagem === imagemProduto)
    ) || listaChocolates.find((produto) => produto.nome === nomeProduto) || null;
}

function normalizarItemCarrinho(item) {
    const nome = String(item?.nome || "").trim();
    const imagem = String(item?.imagem || "").trim();
    const produtoRelacionado = obterDadosProduto(nome, imagem);
    const produtoIdBruto = item?.produto_id ?? item?.produtoId ?? produtoRelacionado?.id ?? null;
    const produtoId = Number.parseInt(String(produtoIdBruto ?? ""), 10);
    const slug = String(item?.slug || produtoRelacionado?.slug || "").trim();
    const chave = String(
        item?.chave || slug || (typeof slugifyProductName === "function" ? slugifyProductName(nome) : nome)
    ).trim();

    return {
        produto_id: Number.isInteger(produtoId) && produtoId > 0 ? produtoId : null,
        chave,
        slug,
        nome,
        preco: Number(item?.preco || 0),
        imagem: imagem || produtoRelacionado?.imagem || "",
        qtd: Math.max(1, Number.parseInt(item?.qtd, 10) || 1),
    };
}

function obterPayloadCarrinho() {
    return carrinho
        .map((item) => normalizarItemCarrinho(item))
        .filter((item) => item.nome && item.chave && item.qtd > 0);
}

async function persistirCarrinhoNoServidor() {
    if (!usuarioTemCarrinhoPersistente() || sincronizandoCarrinho) {
        return;
    }

    try {
        const resposta = await fetch(CART_SYNC_URL, {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
            },
            body: JSON.stringify({
                itens: obterPayloadCarrinho(),
            }),
        });

        if (!resposta.ok) {
            console.warn("Nao foi possivel salvar o carrinho da conta.", resposta.status);
        }
    } catch (erro) {
        console.warn("Nao foi possivel salvar o carrinho da conta.", erro);
    }
}

async function finalizarPedido() {
    if (!usuarioTemCarrinhoPersistente()) {
        window.location.href = obterRotasApp().login;
        return;
    }

    const itensParaFinalizar = obterPayloadCarrinho();

    if (itensParaFinalizar.length === 0) {
        mostrarPopupAcao("Seu carrinho esta vazio.");
        return;
    }

    if (btnFinalizarPedido) {
        btnFinalizarPedido.disabled = true;
        btnFinalizarPedido.textContent = "Finalizando...";
    }

    try {
        await persistirCarrinhoNoServidor();

        const resposta = await fetch(CHECKOUT_URL, {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Accept": "application/json",
            },
        });
        const dados = await resposta.json();

        if (!resposta.ok) {
            if (dados?.codigo === "endereco_principal_ausente") {
                window.location.href = obterRotasApp().conta;
                return;
            }

            mostrarPopupAcao(String(dados?.erro || "Nao foi possivel finalizar o pedido."));
            return;
        }

        carrinho = [];
        atualizarCarrinho(false);
        localStorage.removeItem("carrinho");
        mostrarPopupAcao(`Pedido ${dados.numero_pedido} finalizado com sucesso.`);
        window.location.href = `${obterRotasApp().conta}?pedido=${encodeURIComponent(dados.numero_pedido || "")}`;
    } catch (erro) {
        console.warn("Nao foi possivel finalizar o pedido.", erro);
        mostrarPopupAcao("Nao foi possivel finalizar o pedido.");
    } finally {
        if (btnFinalizarPedido) {
            btnFinalizarPedido.disabled = false;
            btnFinalizarPedido.textContent = "Finalizar pedido";
        }
    }
}

function persistirCarrinhoDeSaida() {
    if (!usuarioTemCarrinhoPersistente() || sincronizandoCarrinho || !navigator.sendBeacon) {
        return;
    }

    const payload = JSON.stringify({
        itens: obterPayloadCarrinho(),
    });

    navigator.sendBeacon(CART_SYNC_URL, new Blob([payload], { type: "application/json" }));
}

async function carregarCarrinhoDaConta() {
    if (!usuarioTemCarrinhoPersistente()) {
        return;
    }

    sincronizandoCarrinho = true;

    try {
        const resposta = await fetch(CART_SYNC_URL, {
            credentials: "same-origin",
            headers: {
                "Accept": "application/json",
            },
        });

        if (!resposta.ok) {
            return;
        }

        const dados = await resposta.json();
        carrinho = Array.isArray(dados.itens)
            ? dados.itens.map((item) => normalizarItemCarrinho(item)).filter((item) => item.nome && item.chave)
            : [];
        atualizarCarrinho(false);
    } catch (erro) {
        console.warn("Nao foi possivel carregar o carrinho salvo da conta.", erro);
    } finally {
        sincronizandoCarrinho = false;
    }
}

function obterOrdenacaoInicial() {
    try {
        const ordenacaoSalva = localStorage.getItem(VITRINE_SORT_STORAGE_KEY);

        if (VITRINE_SORT_OPTIONS.has(ordenacaoSalva)) {
            return ordenacaoSalva;
        }
    } catch (erro) {
        console.warn("Nao foi possivel ler a ordenacao da vitrine.", erro);
    }

    return "recommended";
}

function salvarOrdenacaoVitrine() {
    try {
        localStorage.setItem(VITRINE_SORT_STORAGE_KEY, ordenacaoVitrine);
    } catch (erro) {
        console.warn("Nao foi possivel salvar a ordenacao da vitrine.", erro);
    }
}

function atualizarBotoesOrdenacao() {
    vitrineSortButtons.forEach((button) => {
        const ativo = button.dataset.vitrineSort === ordenacaoVitrine;
        button.classList.toggle("ativo", ativo);
        button.setAttribute("aria-pressed", ativo ? "true" : "false");
    });
}

function ordenarChocolates(chocolates) {
    const listaOrdenada = [...chocolates];

    if (ordenacaoVitrine === "price-asc") {
        listaOrdenada.sort((produtoA, produtoB) => {
            const precoA = Number(produtoA.preco) || 0;
            const precoB = Number(produtoB.preco) || 0;

            if (precoA !== precoB) {
                return precoA - precoB;
            }

            return String(produtoA.nome || "").localeCompare(String(produtoB.nome || ""), "pt-BR");
        });
    }

    return listaOrdenada;
}

function atualizarContadorVitrine(quantidadeExibida) {
    if (!vitrineCount) {
        return;
    }

    vitrineCount.textContent = `Exibindo: ${quantidadeExibida} / ${listaChocolates.length}`;
}

function escapeHtml(value) {
    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

function criarResumoCard(produto) {
    const descricao = String(produto.descricao || "").trim();

    if (descricao) {
        return descricao;
    }

    return "Chocolate selecionado para uma experiencia delicada, presenteavel e marcante.";
}

function obterTotalAvaliacoes(produto, index) {
    const chave = String(produto.slug || produto.nome || index);
    const somaCaracteres = [...chave].reduce((total, caractere) => total + caractere.charCodeAt(0), 0);

    return 24 + (somaCaracteres % 38);
}

function configurarArrasteVitrine() {
    if (!container) {
        return;
    }

    container.addEventListener("pointerdown", (event) => {
        if (event.button !== 0 || event.target.closest("button")) {
            return;
        }

        vitrineArrastando = true;
        vitrineArrasteInicioX = event.clientX;
        vitrineScrollInicio = container.scrollLeft;
        container.setPointerCapture(event.pointerId);
    });

    container.addEventListener("pointermove", (event) => {
        if (!vitrineArrastando) {
            return;
        }

        const deslocamento = event.clientX - vitrineArrasteInicioX;
        container.scrollLeft = vitrineScrollInicio - deslocamento;
    });

    ["pointerup", "pointercancel", "pointerleave"].forEach((evento) => {
        container.addEventListener(evento, () => {
            vitrineArrastando = false;
        });
    });
}

function rolarVitrine(direcao) {
    if (!container) {
        return;
    }

    const primeiroCard = container.querySelector(".card");
    const deslocamento = primeiroCard
        ? primeiroCard.getBoundingClientRect().width + 22
        : container.clientWidth * 0.85;

    container.scrollBy({
        left: direcao === "prev" ? -deslocamento : deslocamento,
        behavior: "smooth",
    });
}

function atualizarSetasVitrine() {
    if (!container || vitrineScrollButtons.length === 0) {
        return;
    }

    const tolerancia = 2;
    const podeVoltar = container.scrollLeft > tolerancia;
    const podeAvancar = container.scrollLeft + container.clientWidth < container.scrollWidth - tolerancia;

    vitrineScrollButtons.forEach((button) => {
        const podeUsar = button.dataset.vitrineScroll === "prev" ? podeVoltar : podeAvancar;
        button.classList.toggle("vitrine-seta--oculta", !podeUsar);
        button.disabled = !podeUsar;
        button.setAttribute("aria-hidden", podeUsar ? "false" : "true");
        button.setAttribute("tabindex", podeUsar ? "0" : "-1");
    });
}

function posicionarSetasVitrine() {
    if (!container) {
        return;
    }

    const vitrine = document.getElementById("vitrine");
    const primeiraImagem = container.querySelector(".card__imagem-box");

    if (!vitrine || !primeiraImagem) {
        return;
    }

    const vitrineRect = vitrine.getBoundingClientRect();
    const imagemRect = primeiraImagem.getBoundingClientRect();
    const centroImagem = imagemRect.top + imagemRect.height / 2 - vitrineRect.top;

    vitrine.style.setProperty("--vitrine-setas-top", `${centroImagem}px`);
}

function normalizarTextoBusca(valor) {
    return String(valor || "")
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toLowerCase()
        .trim();
}

function produtoCorrespondeBusca(produto, termoBusca) {
    const campos = [produto.nome, produto.categoria, produto.destaque, produto.peso];
    return campos.some((campo) => normalizarTextoBusca(campo).includes(termoBusca));
}

function aplicarPesquisa() {
    const texto = normalizarTextoBusca(barraPesquisa.value);
    const filtrados = texto
        ? listaChocolates.filter((choc) => produtoCorrespondeBusca(choc, texto))
        : [...listaChocolates];

    renderizarChocolates(filtrados);
    atualizarPainelPesquisa(texto, filtrados);
    btnLimparPesquisa.classList.toggle("oculto", !texto);
}

function obterChocolatesVisiveisPelaBusca() {
    const texto = normalizarTextoBusca(barraPesquisa.value);

    return texto
        ? listaChocolates.filter((choc) => produtoCorrespondeBusca(choc, texto))
        : [...listaChocolates];
}

function criarSugestoes(texto) {
    const nomes = listaChocolates.map((choc) => choc.nome);

    if (!texto) {
        return [...new Set(nomes)].slice(0, 5);
    }

    return [...new Set(
        nomes.filter((nome) => normalizarTextoBusca(nome).includes(texto))
    )].slice(0, 5);
}

function renderizarAtalhosPesquisa() {
    if (!listaAtalhosPesquisa) {
        return;
    }

    listaAtalhosPesquisa.innerHTML = "";

    const atalhos = [...new Set(
        listaChocolates
            .map((item) => String(item.categoria || "").trim())
            .filter(Boolean)
    )].slice(0, 5);

    atalhos.forEach((atalho) => {
        const botao = document.createElement("button");
        botao.type = "button";
        botao.className = "pesquisa-atalho";
        botao.textContent = atalho;
        botao.addEventListener("click", () => {
            barraPesquisa.value = atalho;
            aplicarPesquisa();
            barraPesquisa.focus();
        });
        listaAtalhosPesquisa.appendChild(botao);
    });
}

function renderizarSugestoes(sugestoes) {
    listaSugestoes.innerHTML = "";

    if (sugestoes.length === 0) {
        listaSugestoes.innerHTML = "<p class=\"painel-vazio\">Nenhuma sugestao encontrada.</p>";
        return;
    }

    sugestoes.forEach((sugestao) => {
        const botao = document.createElement("button");
        botao.type = "button";
        botao.className = "sugestao-item";
        botao.innerHTML = `<strong>${sugestao.split(" ")[0]}</strong> ${sugestao.split(" ").slice(1).join(" ")}`.trim();
        botao.addEventListener("click", () => {
            barraPesquisa.value = sugestao;
            aplicarPesquisa();
        });
        listaSugestoes.appendChild(botao);
    });
}

function renderizarResultadosPesquisa(chocolates) {
    listaResultadosPesquisa.innerHTML = "";

    if (chocolates.length === 0) {
        listaResultadosPesquisa.innerHTML = "<p class=\"painel-vazio\">Nenhum produto encontrado.</p>";
        return;
    }

    chocolates.slice(0, 5).forEach((choc) => {
        const nomeSerializado = JSON.stringify(choc.nome);
        const imagemSerializada = JSON.stringify(choc.imagem);
        const item = document.createElement("div");
        item.className = "resultado-pesquisa";
        item.innerHTML = `
            <a class="resultado-pesquisa__link" href="produto.html?id=${encodeURIComponent(choc.slug)}">
                <img class="resultado-pesquisa__imagem" src="${choc.imagem}" alt="${choc.nome}">
                <div class="resultado-pesquisa__info">
                    <h4>${choc.nome}</h4>
                    <p>R$ ${choc.preco.toFixed(2).replace(".", ",")}</p>
                </div>
            </a>
            <button type="button" class="resultado-pesquisa__botao" onclick='adicionarAoCarrinho(${nomeSerializado}, ${choc.preco}, ${imagemSerializada})'>+</button>
        `;
        listaResultadosPesquisa.appendChild(item);
    });
}

function atualizarPainelPesquisa(texto, filtrados) {
    btnVerTodos.textContent = `Veja todos os ${texto ? filtrados.length : listaChocolates.length} produtos`;
    renderizarSugestoes(criarSugestoes(texto));
    renderizarResultadosPesquisa(texto ? filtrados : listaChocolates);
}

function abrirOverlayPesquisa() {
    if (carrinhoLateral.classList.contains("ativo")) {
        fecharPainelCarrinho();
    }

    if (menuMobileAberto()) {
        fecharMenuMobile();
    }

    clearTimeout(timeoutFechamentoPesquisa);
    fecharPesquisa.classList.remove("overlay-pesquisa__fechar--fechando");
    overlayPesquisa.classList.remove("oculto");
    overlayPesquisa.classList.remove("overlay-pesquisa--visivel");
    document.body.classList.add("sem-rolagem");
    atualizarPainelPesquisa(normalizarTextoBusca(barraPesquisa.value), listaChocolates);
    requestAnimationFrame(() => {
        overlayPesquisa.classList.add("overlay-pesquisa--visivel");
    });
    barraPesquisa.focus();
}

function fecharOverlayPesquisa() {
    overlayPesquisa.classList.remove("overlay-pesquisa--visivel");

    clearTimeout(timeoutFechamentoPesquisa);
    timeoutFechamentoPesquisa = window.setTimeout(() => {
        overlayPesquisa.classList.add("oculto");
    }, 280);

    if (!carrinhoLateral.classList.contains("ativo")) {
        document.body.classList.remove("sem-rolagem");
    }
}

function limparPesquisa() {
    barraPesquisa.value = "";
    btnLimparPesquisa.classList.add("oculto");
    renderizarChocolates(listaChocolates);
    atualizarPainelPesquisa("", listaChocolates);
    barraPesquisa.focus();
}

function renderizarChocolates(chocolates) {
    container.innerHTML = "";
    container.scrollLeft = 0;
    const chocolatesOrdenados = ordenarChocolates(chocolates);
    atualizarContadorVitrine(chocolatesOrdenados.length);

    if (chocolatesOrdenados.length === 0) {
        container.innerHTML = "<p>Nenhum chocolate encontrado.</p>";
        atualizarSetasVitrine();
        return;
    }

    chocolatesOrdenados.forEach((choc, index) => {
        const quantidade = obterQuantidadeNoCarrinho(choc.nome);
        const card = document.createElement("article");
        const descricao = criarResumoCard(choc);
        const totalAvaliacoes = obterTotalAvaliacoes(choc, index);
        card.className = "card";
        card.style.setProperty("--card-stagger-index", String(index % 12));
        card.innerHTML = `
            <a class="card__imagem-link" href="produto.html?id=${encodeURIComponent(choc.slug)}">
                <div class="card__imagem-box">
                    <img src="${escapeHtml(choc.imagem)}" alt="${escapeHtml(choc.nome)}">
                </div>
            </a>
            <div class="card__conteudo">
                <a class="card__link" href="produto.html?id=${encodeURIComponent(choc.slug)}">
                    <p class="card__descricao">${escapeHtml(descricao)}</p>
                    <div class="card__linha">
                        <h3>${escapeHtml(choc.nome)}</h3>
                        <p class="card__preco">R$ ${Number(choc.preco || 0).toFixed(2).replace(".", ",")}</p>
                    </div>
                    <div class="card__rating" aria-label="Avaliacao maxima com ${totalAvaliacoes} avaliacoes">
                        <span aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        <span>${totalAvaliacoes}</span>
                    </div>
                    <span class="card__selo">${escapeHtml(choc.destaque)}</span>
                    <span class="card__quantidade-info">${quantidade > 0 ? `${quantidade} na sacola` : "Disponivel agora"}</span>
                </a>
            </div>
        `;
        const botaoAdicionar = document.createElement("button");
        botaoAdicionar.type = "button";
        botaoAdicionar.className = "card__cta";
        botaoAdicionar.textContent = "+";
        botaoAdicionar.setAttribute("aria-label", `Adicionar ${choc.nome} ao carrinho`);
        botaoAdicionar.addEventListener("click", (event) => {
            event.preventDefault();
            event.stopPropagation();
            adicionarAoCarrinho(choc.nome, choc.preco, choc.imagem);
        });
        card.querySelector(".card__conteudo").appendChild(botaoAdicionar);
        container.appendChild(card);
    });

    requestAnimationFrame(() => {
        posicionarSetasVitrine();
        atualizarSetasVitrine();
    });
}

function formatarPreco(valor) {
    return `R$ ${Number(valor || 0).toFixed(2).replace(".", ",")}`;
}

function obterSugestoesCarrinhoVazio() {
    if (listaChocolates.length <= 2) {
        return [...listaChocolates];
    }

    const embaralhados = [...listaChocolates].sort(() => Math.random() - 0.5);
    const sugestoes = [];
    const categoriasUsadas = new Set();

    for (const item of embaralhados) {
        const categoria = String(item.categoria || "").trim().toLowerCase();

        if (categoria && categoriasUsadas.has(categoria)) {
            continue;
        }

        sugestoes.push(item);

        if (categoria) {
            categoriasUsadas.add(categoria);
        }

        if (sugestoes.length === 2) {
            return sugestoes;
        }
    }

    for (const item of embaralhados) {
        if (!sugestoes.includes(item)) {
            sugestoes.push(item);
        }

        if (sugestoes.length === 2) {
            break;
        }
    }

    return sugestoes;
}

window.abrirVitrinePrincipal = function () {
    fecharPainelCarrinho();
    const vitrine = document.getElementById("vitrine");

    if (vitrine) {
        vitrine.scrollIntoView({ behavior: "smooth", block: "start" });
    }
};

window.adicionarSugestaoCarrinho = function (slugProduto, botao) {
    const produto = listaChocolates.find((item) => item.slug === slugProduto);

    if (!produto) {
        return;
    }

    if (botao) {
        botao.classList.remove("carrinho-vazio__adicionar--ativo");
        botao.offsetHeight;
        botao.classList.add("carrinho-vazio__adicionar--ativo");
    }

    window.setTimeout(() => {
        window.adicionarAoCarrinho(produto.nome, produto.preco, produto.imagem);
    }, 170);
};

async function carregarChocolates() {
    listaChocolates = await carregarTodosChocolates();
    renderizarChocolates(listaChocolates);
    renderizarAtalhosPesquisa();
    atualizarCarrinho(false);
}

window.adicionarAoCarrinho = function (nome, preco, imagem) {
    const itemExistente = carrinho.find((item) => item.nome === nome);
    const produtoEntrouNaSacola = !itemExistente;

    if (itemExistente) {
        itemExistente.qtd++;
    } else {
        const produtoRelacionado = obterDadosProduto(nome, imagem);
        carrinho.push(normalizarItemCarrinho({
            nome,
            preco,
            imagem,
            qtd: 1,
            slug: produtoRelacionado?.slug || "",
        }));
    }

    atualizarCarrinho();

    if (produtoEntrouNaSacola) {
        mostrarPopupCarrinho(nome);
    } else {
        mostrarPopupAcao("Produto foi adicionado ao carrinho !!!");
    }
};

function atualizarCarrinho(devePersistir = true) {
    listaCarrinho.innerHTML = "";

    let totalItens = 0;
    let totalPreco = 0;

    if (tituloCarrinho) {
        tituloCarrinho.textContent = `Minha sacola (${carrinho.reduce((soma, item) => soma + item.qtd, 0)})`;
    }

    if (carrinho.length === 0) {
        const sugestoes = obterSugestoesCarrinhoVazio();

        listaCarrinho.innerHTML = `
            <li class="carrinho-vazio">
                <div class="carrinho-vazio__resumo">
                    <p class="carrinho-vazio__frete">Continue explorando para montar sua selecao especial.</p>
                    <h4>Sua sacola esta vazia!</h4>
                    <p>Adicione seus chocolates favoritos para comecar seu pedido.</p>
                    <button type="button" class="carrinho-vazio__cta" onclick="abrirVitrinePrincipal()">Explorar vitrine</button>
                </div>

                <div class="carrinho-vazio__sugestoes">
                    <p class="carrinho-vazio__titulo">Voce tambem pode gostar</p>
                    ${sugestoes.map((item) => `
                        <article class="carrinho-vazio__item">
                            <img src="${item.imagem}" alt="${item.nome}">
                            <div class="carrinho-vazio__item-info">
                                <strong>${item.nome}</strong>
                                <span>${formatarPreco(item.preco)}</span>
                            </div>
                            <button type="button" class="carrinho-vazio__adicionar" onclick='event.stopPropagation(); adicionarSugestaoCarrinho(${JSON.stringify(item.slug)}, this)'>ADD +</button>
                        </article>
                    `).join("")}
                </div>
            </li>
        `;

        qtdItens.textContent = "0";
        subtotalPreco.textContent = "R$ 0,00";
        totalPrecoElement.textContent = "R$ 0,00";

        if (resumoCarrinho) {
            resumoCarrinho.hidden = true;
        }

        if (container) {
            renderizarChocolates(listaChocolates);
        }

        if (devePersistir) {
            persistirCarrinhoNoServidor();
        }

        return;
    }

    if (resumoCarrinho) {
        resumoCarrinho.hidden = false;
    }

    carrinho.forEach((item, index) => {
        totalItens += item.qtd;
        totalPreco += item.preco * item.qtd;

        const li = document.createElement("li");
        li.innerHTML = `
            <div class="item-carrinho">
                <img class="item-carrinho__imagem" src="${item.imagem}" alt="${item.nome}">

                <div class="item-carrinho__conteudo">
                    <div class="item-carrinho__topo">
                        <div class="item-carrinho__info">
                            <strong>${item.nome}</strong>
                            <span class="item-carrinho__preco">R$ ${(item.preco * item.qtd).toFixed(2).replace(".", ",")}</span>
                        </div>

                        <button type="button" class="item-carrinho__remover-total" onclick="event.stopPropagation(); removerItemCompleto(${index}, this)" aria-label="Remover ${item.nome} do carrinho">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 7h16"></path>
                                <path d="M9 7V5h6v2"></path>
                                <path d="M8 7l1 12h6l1-12"></path>
                                <path d="M10 11v5"></path>
                                <path d="M14 11v5"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="item-carrinho__rodape">
                        <div class="item-carrinho__acoes">
                            <button type="button" onclick="event.stopPropagation(); removerItem(${index}, this)">-</button>
                            <span>${item.qtd}</span>
                            <button type="button" onclick="event.stopPropagation(); adicionarItem(${index})">+</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        listaCarrinho.appendChild(li);
    });

    qtdItens.textContent = totalItens;

    const totalFormatado = "R$ " + totalPreco.toFixed(2).replace(".", ",");
    subtotalPreco.textContent = totalFormatado;
    totalPrecoElement.textContent = totalFormatado;

    if (container) {
        renderizarChocolates(listaChocolates);
    }

    if (devePersistir) {
        persistirCarrinhoNoServidor();
    }
}

window.adicionarItem = function (index) {
    carrinho[index].qtd++;
    atualizarCarrinho();
};

function animarReducaoItem(botao) {
    const item = botao ? botao.closest("li") : null;

    if (!item) {
        return;
    }

    item.classList.remove("item-carrinho--reduzindo");
    item.offsetHeight;
    item.classList.add("item-carrinho--reduzindo");

    window.setTimeout(() => {
        item.classList.remove("item-carrinho--reduzindo");
    }, 320);
}

function animarRemocaoCompleta(botao, aoFinalizar) {
    const item = botao ? botao.closest("li") : null;

    if (!item) {
        aoFinalizar();
        return;
    }

    const botaoExcluir = item.querySelector(".item-carrinho__remover-total");

    if (botaoExcluir) {
        botaoExcluir.classList.add("item-carrinho__remover-total--ativo");
    }

    item.classList.add("item-carrinho--saindo");

    window.setTimeout(() => {
        aoFinalizar();
    }, 280);
}

window.removerItem = function (index, botao) {
    const item = carrinho[index];

    if (!item) {
        return;
    }

    if (item.qtd > 1) {
        item.qtd--;
        atualizarCarrinho();
        animarReducaoItem(botao);
        return;
    }

    animarRemocaoCompleta(botao, () => {
        carrinho.splice(index, 1);
        atualizarCarrinho();
    });
};

window.removerItemCompleto = function (index, botao) {
    if (!carrinho[index]) {
        return;
    }

    animarRemocaoCompleta(botao, () => {
        carrinho.splice(index, 1);
        atualizarCarrinho();
    });
};

window.removerItemHome = function (nomeProduto) {
    const index = carrinho.findIndex((item) => item.nome === nomeProduto);

    if (index === -1) {
        return;
    }

    const itemRemovido = carrinho[index];

    if (carrinho[index].qtd > 1) {
        carrinho[index].qtd--;
    } else {
        carrinho.splice(index, 1);
    }

    atualizarCarrinho();
    mostrarPopupRemocao(itemRemovido.nome);
};

async function abrirCarrinho() {
    if (menuMobileAberto()) {
        fecharMenuMobile();
    }

    if (sincronizandoCarrinho) {
        await carregamentoCarrinhoPromise;
    }

    atualizarCarrinho(false);
    pausarPopupCarrinho();
    carrinhoLateral.classList.add("ativo");
    overlayCarrinho.classList.add("ativo");
    document.body.classList.add("sem-rolagem");
}

function fecharPainelCarrinho() {
    if (!carrinhoLateral.classList.contains("ativo")) {
        return;
    }

    carrinhoLateral.classList.remove("ativo");
    overlayCarrinho.classList.remove("ativo");

    if (overlayPesquisa.classList.contains("oculto") && !menuMobileAberto()) {
        document.body.classList.remove("sem-rolagem");
    }
}

btnCarrinho.addEventListener("click", (e) => {
    if (carrinhoLateral.classList.contains("ativo")) {
        fecharPainelCarrinho();
    } else {
        void abrirCarrinho();
    }

    e.stopPropagation();
});

fecharCarrinho.addEventListener("click", fecharPainelCarrinho);
overlayCarrinho.addEventListener("click", fecharPainelCarrinho);

document.addEventListener("click", (e) => {
    if (carrinhoLateral.classList.contains("ativo") && !carrinhoLateral.contains(e.target) && !btnCarrinho.contains(e.target)) {
        fecharPainelCarrinho();
    }

    if (!e.target.closest(".pesquisa-box")) {
        return;
    }
});

atualizarBotoesOrdenacao();
configurarArrasteVitrine();
posicionarSetasVitrine();
atualizarSetasVitrine();

if (container) {
    container.addEventListener("scroll", atualizarSetasVitrine, { passive: true });
}

window.addEventListener("resize", () => {
    posicionarSetasVitrine();
    atualizarSetasVitrine();
});

vitrineSortButtons.forEach((button) => {
    button.addEventListener("click", () => {
        const proximaOrdenacao = button.dataset.vitrineSort;

        if (!VITRINE_SORT_OPTIONS.has(proximaOrdenacao) || ordenacaoVitrine === proximaOrdenacao) {
            return;
        }

        ordenacaoVitrine = proximaOrdenacao;
        salvarOrdenacaoVitrine();
        atualizarBotoesOrdenacao();
        renderizarChocolates(obterChocolatesVisiveisPelaBusca());
    });
});

vitrineScrollButtons.forEach((button) => {
    button.addEventListener("click", () => {
        rolarVitrine(button.dataset.vitrineScroll);
    });
});

instagramLinks.forEach((link) => {
    link.addEventListener("click", (event) => {
        const novaJanela = window.open(link.href, "_blank", "noopener,noreferrer");

        if (novaJanela) {
            event.preventDefault();
            return;
        }

        link.removeAttribute("target");
    });
});

barraPesquisa.addEventListener("input", aplicarPesquisa);
barraPesquisa.addEventListener("keydown", (event) => {
    if (event.key === "Enter") {
        aplicarPesquisa();
    }
});
abrirPesquisa.addEventListener("click", abrirOverlayPesquisa);
fecharPesquisa.addEventListener("click", () => {
    fecharPesquisa.classList.add("overlay-pesquisa__fechar--fechando");
    window.setTimeout(() => {
        fecharOverlayPesquisa();
    }, 120);
});
barraPesquisa.addEventListener("focus", aplicarPesquisa);
btnLimparPesquisa.addEventListener("click", limparPesquisa);
btnVerTodos.addEventListener("click", () => {
    renderizarChocolates(listaChocolates);
    fecharOverlayPesquisa();
});
if (fecharPopupRemocao && popupRemocao) {
    fecharPopupRemocao.addEventListener("click", () => {
        clearTimeout(popupRemocaoTimeout);
        popupRemocao.classList.remove("ativo");
        popupRemocao.setAttribute("aria-hidden", "true");
    });
}

if (fecharPopupBoasVindas) {
    fecharPopupBoasVindas.addEventListener("click", fecharPopupBoasVindasComEstado);
}

if (btnFinalizarPedido) {
    btnFinalizarPedido.addEventListener("click", () => {
        void finalizarPedido();
    });
}

window.addEventListener("storage", carregarChocolates);
window.addEventListener("pagehide", persistirCarrinhoDeSaida);
window.addEventListener("beforeunload", persistirCarrinhoDeSaida);

carregamentoCarrinhoPromise = carregarCarrinhoDaConta();

Promise.all([
    carregarChocolates(),
    carregamentoCarrinhoPromise,
]).finally(() => {
    document.body.classList.remove("pagina-carregando");
});
exibirPopupBoasVindas();
