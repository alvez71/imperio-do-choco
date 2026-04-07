<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$papelUsuario = (string) ($_SESSION["usuario_papel"] ?? "cliente");

if ($papelUsuario !== "admin") {
    header("Location: conta.php");
    exit;
}

$nomeUsuario = (string) ($_SESSION["usuario_nome"] ?? "Administrador");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <title>Admin | Velle Dulcis</title>
</head>
<body>
    <div class="admin-shell">
        <div class="admin-topbar">
            <a class="admin-topbar__voltar" href="index.php">Voltar para a vitrine</a>

            <div class="admin-topbar__acoes">
                <span class="admin-topbar__usuario">
                    <?php echo "Logado como " . htmlspecialchars($nomeUsuario, ENT_QUOTES, "UTF-8"); ?>
                </span>
                <a class="admin-topbar__sair" href="logout.php">Sair</a>
            </div>
        </div>

        <section class="form-container admin-card" aria-labelledby="admin-titulo">
            <div class="admin-card__header">
                <div>
                    <p class="admin-card__eyebrow">Painel administrativo</p>
                    <h1 id="admin-titulo">Cadastrar produto</h1>
                </div>

                <aside class="admin-summary" aria-live="polite">
                    <span class="admin-summary__label">Produtos visiveis no painel</span>
                    <strong id="admin-total-produtos" class="admin-summary__value">0</strong>
                </aside>
            </div>

            <p class="form-container__intro">
                Cadastre uma vez e o produto passa a funcionar na vitrine e na pagina de detalhes dinamica.
            </p>

            <p id="admin-status" class="admin-status" aria-live="polite" hidden></p>

            <form id="admin-form" class="admin-form">
                <div class="admin-form__grid">
                    <label class="admin-field admin-field--full" for="imgUrl">
                        <span class="admin-field__label">Imagem principal</span>
                        <span class="admin-field__hint">Use uma URL completa ou um caminho local valido.</span>
                        <input type="text" id="imgUrl" placeholder="URL da imagem principal" required>
                    </label>

                    <label class="admin-field" for="nome">
                        <span class="admin-field__label">Nome do chocolate</span>
                        <input type="text" id="nome" placeholder="Nome do chocolate" required>
                    </label>

                    <label class="admin-field" for="preco">
                        <span class="admin-field__label">Preco</span>
                        <input type="number" id="preco" placeholder="Ex: 25.90" step="0.01" min="0.01" inputmode="decimal" required>
                    </label>

                    <label class="admin-field" for="categoria">
                        <span class="admin-field__label">Categoria</span>
                        <input type="text" id="categoria" placeholder="Ex: Ovos de Pascoa">
                    </label>

                    <label class="admin-field" for="peso">
                        <span class="admin-field__label">Peso ou tamanho</span>
                        <input type="text" id="peso" placeholder="Ex: 210g">
                    </label>

                    <label class="admin-field" for="ref">
                        <span class="admin-field__label">Referencia</span>
                        <input type="text" id="ref" placeholder="Codigo interno ou SKU">
                    </label>

                    <label class="admin-field" for="destaque">
                        <span class="admin-field__label">Selo de destaque</span>
                        <input type="text" id="destaque" placeholder="Ex: 119 pontos Kop Club">
                    </label>

                    <label class="admin-field admin-field--full" for="descricao">
                        <span class="admin-field__label">Descricao</span>
                        <textarea id="descricao" placeholder="Descricao do produto"></textarea>
                    </label>

                    <label class="admin-field admin-field--full" for="galeria">
                        <span class="admin-field__label">Galeria complementar</span>
                        <span class="admin-field__hint">Adicione uma URL por linha para montar a galeria do produto.</span>
                        <textarea id="galeria" placeholder="Outras imagens (uma URL por linha)"></textarea>
                    </label>
                </div>

                <div class="admin-toolbar">
                    <button id="btn-publicar" type="submit">Publicar produto</button>
                    <button id="btn-limpar" class="admin-toolbar__secondary" type="button">Limpar campos</button>
                </div>
            </form>
        </section>

        <section class="admin-card" aria-labelledby="admin-lista-titulo">
            <div class="admin-card__header">
                <div>
                    <p class="admin-card__eyebrow">Gestao de catalogo</p>
                    <h2 id="admin-lista-titulo">Produtos cadastrados</h2>
                </div>
            </div>

            <p class="form-container__intro">
                Remova daqui os chocolates que aparecem na vitrine deste navegador.
            </p>

            <div id="admin-lista-produtos" class="admin-product-list" aria-live="polite"></div>
        </section>

        <section class="admin-card" aria-labelledby="admin-removidos-titulo">
            <div class="admin-card__header">
                <div>
                    <p class="admin-card__eyebrow">Backup local</p>
                    <h2 id="admin-removidos-titulo">Itens removidos</h2>
                </div>
                <button id="btn-toggle-removidos" class="admin-toolbar__secondary admin-toolbar__secondary--compact" type="button" aria-expanded="false">
                    Reexibir itens removidos
                </button>
            </div>

            <p class="form-container__intro">
                Abra a lista para ver chocolates apagados neste navegador e restaurar os que quiser.
            </p>

            <div id="admin-lista-removidos" class="admin-product-list" aria-live="polite" hidden></div>
        </section>
    </div>

    <script src="products-data.js"></script>
    <script>
        const elementos = {
            formulario: document.getElementById("admin-form"),
            status: document.getElementById("admin-status"),
            totalProdutos: document.getElementById("admin-total-produtos"),
            listaProdutos: document.getElementById("admin-lista-produtos"),
            listaRemovidos: document.getElementById("admin-lista-removidos"),
            btnToggleRemovidos: document.getElementById("btn-toggle-removidos"),
            btnLimpar: document.getElementById("btn-limpar"),
        };

        const campos = {
            imgUrl: document.getElementById("imgUrl"),
            nome: document.getElementById("nome"),
            preco: document.getElementById("preco"),
            categoria: document.getElementById("categoria"),
            peso: document.getElementById("peso"),
            ref: document.getElementById("ref"),
            destaque: document.getElementById("destaque"),
            descricao: document.getElementById("descricao"),
            galeria: document.getElementById("galeria"),
        };

        function exibirStatus(mensagem, tipo = "info") {
            elementos.status.hidden = false;
            elementos.status.dataset.status = tipo;
            elementos.status.textContent = mensagem;
        }

        async function atualizarResumo() {
            const produtos = await carregarTodosChocolates();
            elementos.totalProdutos.textContent = String(produtos.length);
        }

        function limparFormulario() {
            elementos.formulario.reset();
            campos.imgUrl.focus();
        }

        function lerGaleria() {
            return campos.galeria.value
                .split(/\r?\n/)
                .map((url) => url.trim())
                .filter(Boolean);
        }

        function criarSlugUnico(nome, listaAtual) {
            const slugBase = slugifyProductName(nome) || "produto";
            const slugsExistentes = new Set(listaAtual.map((item) => item.slug));

            if (!slugsExistentes.has(slugBase)) {
                return slugBase;
            }

            let contador = 2;
            while (slugsExistentes.has(`${slugBase}-${contador}`)) {
                contador += 1;
            }

            return `${slugBase}-${contador}`;
        }

        function lerDadosFormulario() {
            const precoNormalizado = campos.preco.value.replace(",", ".").trim();

            return {
                img: campos.imgUrl.value.trim(),
                nome: campos.nome.value.trim(),
                preco: Number.parseFloat(precoNormalizado),
                categoria: campos.categoria.value.trim(),
                peso: campos.peso.value.trim(),
                ref: campos.ref.value.trim(),
                destaque: campos.destaque.value.trim(),
                descricao: campos.descricao.value.trim(),
                galeria: lerGaleria(),
            };
        }

        function validarDados(dados) {
            if (!dados.img || !dados.nome || Number.isNaN(dados.preco)) {
                return "Preencha imagem principal, nome e preco antes de publicar.";
            }

            if (dados.preco <= 0) {
                return "Informe um preco maior que zero.";
            }

            return "";
        }

        async function renderizarListaProdutos() {
            const produtos = await carregarTodosChocolates();

            if (produtos.length === 0) {
                elementos.listaProdutos.innerHTML = '<p class="admin-product-list__empty">Nenhum chocolate visivel no painel neste navegador.</p>';
                return;
            }

            elementos.listaProdutos.innerHTML = produtos.map((produto) => `
                <article class="admin-product-item">
                    <div class="admin-product-item__media">
                        <img src="${produto.img || produto.imagem || ""}" alt="${produto.nome}">
                    </div>
                    <div class="admin-product-item__content">
                        <strong>${produto.nome}</strong>
                        <span>${produto.categoria || "Chocolate"}</span>
                        <span>R$ ${Number(produto.preco || 0).toFixed(2).replace(".", ",")}</span>
                    </div>
                    <button
                        type="button"
                        class="admin-product-item__remove"
                        data-produto-chave="${produto.id || produto.slug || slugifyProductName(produto.nome)}"
                        aria-label="Remover ${produto.nome}"
                    >
                        Remover
                    </button>
                </article>
            `).join("");
        }

        async function renderizarListaRemovidos() {
            const produtos = await carregarChocolatesRemovidos();

            if (produtos.length === 0) {
                elementos.listaRemovidos.innerHTML = '<p class="admin-product-list__empty">Nenhum chocolate removido neste navegador.</p>';
                return;
            }

            elementos.listaRemovidos.innerHTML = produtos.map((produto) => `
                <article class="admin-product-item admin-product-item--removed">
                    <div class="admin-product-item__media">
                        <img src="${produto.img || produto.imagem || ""}" alt="${produto.nome}">
                    </div>
                    <div class="admin-product-item__content">
                        <strong>${produto.nome}</strong>
                        <span>${produto.categoria || "Chocolate"}</span>
                        <span>R$ ${Number(produto.preco || 0).toFixed(2).replace(".", ",")}</span>
                    </div>
                    <button
                        type="button"
                        class="admin-product-item__restore"
                        data-restaurar-produto="${produto.id || produto.slug || slugifyProductName(produto.nome)}"
                        aria-label="Restaurar ${produto.nome}"
                    >
                        Restaurar
                    </button>
                </article>
            `).join("");
        }

        async function removerChocolate(chaveProduto) {
            const produtos = await carregarTodosChocolates();
            const produto = produtos.find((item) => obterChaveProduto(item) === chaveProduto);

            if (!produto) {
                exibirStatus("Nao foi possivel localizar o chocolate para remover.", "error");
                return;
            }

            removerChocolateDoCatalogoLocal(chaveProduto);
            await atualizarResumo();
            await renderizarListaProdutos();
            await renderizarListaRemovidos();
            exibirStatus(`Chocolate "${produto.nome}" removido com sucesso.`, "success");
        }

        async function restaurarChocolate(chaveProduto) {
            const produtosRemovidos = await carregarChocolatesRemovidos();
            const produto = produtosRemovidos.find((item) => obterChaveProduto(item) === chaveProduto);

            if (!produto) {
                exibirStatus("Nao foi possivel localizar o chocolate para restaurar.", "error");
                return;
            }

            restaurarChocolateDoBackup(chaveProduto);
            await atualizarResumo();
            await renderizarListaProdutos();
            await renderizarListaRemovidos();
            exibirStatus(`Chocolate "${produto.nome}" restaurado com sucesso.`, "success");
        }

        async function adicionarCard(evento) {
            evento.preventDefault();

            const dados = lerDadosFormulario();
            const erro = validarDados(dados);

            if (erro) {
                exibirStatus(erro, "error");
                return;
            }

            const listaAtual = await carregarTodosChocolates();
            const imagens = [...new Set([dados.img, ...dados.galeria])];
            const novoChocolate = {
                id: gerarIdProduto(),
                slug: criarSlugUnico(dados.nome, listaAtual),
                img: dados.img,
                nome: dados.nome,
                preco: Number(dados.preco.toFixed(2)),
                categoria: dados.categoria,
                peso: dados.peso,
                ref: dados.ref || gerarRefProduto(),
                destaque: dados.destaque,
                descricao: dados.descricao,
                imagens,
            };

            const chocolatesLocais = lerChocolatesLocais();
            chocolatesLocais.push(novoChocolate);
            salvarChocolatesLocais(chocolatesLocais);
            restaurarChocolateRemovido(obterChaveProduto(novoChocolate));
            await atualizarResumo();
            await renderizarListaProdutos();
            await renderizarListaRemovidos();
            limparFormulario();
            exibirStatus("Produto publicado com sucesso. A vitrine ja pode usar esse cadastro local.", "success");
        }

        elementos.formulario.addEventListener("submit", adicionarCard);
        elementos.listaProdutos.addEventListener("click", (evento) => {
            const botaoRemover = evento.target.closest("[data-produto-chave]");

            if (!botaoRemover) {
                return;
            }

            removerChocolate(botaoRemover.dataset.produtoChave);
        });
        elementos.listaRemovidos.addEventListener("click", (evento) => {
            const botaoRestaurar = evento.target.closest("[data-restaurar-produto]");

            if (!botaoRestaurar) {
                return;
            }

            restaurarChocolate(botaoRestaurar.dataset.restaurarProduto);
        });
        elementos.btnToggleRemovidos.addEventListener("click", async () => {
            const estaOculta = elementos.listaRemovidos.hasAttribute("hidden");

            if (estaOculta) {
                await renderizarListaRemovidos();
                elementos.listaRemovidos.removeAttribute("hidden");
                elementos.btnToggleRemovidos.textContent = "Ocultar itens removidos";
                elementos.btnToggleRemovidos.setAttribute("aria-expanded", "true");
                return;
            }

            elementos.listaRemovidos.setAttribute("hidden", "");
            elementos.btnToggleRemovidos.textContent = "Reexibir itens removidos";
            elementos.btnToggleRemovidos.setAttribute("aria-expanded", "false");
        });
        elementos.btnLimpar.addEventListener("click", () => {
            limparFormulario();
            exibirStatus("Campos limpos. Voce pode cadastrar um novo produto.", "info");
        });
        atualizarResumo();
        renderizarListaProdutos();
        renderizarListaRemovidos();
    </script>
</body>
</html>
