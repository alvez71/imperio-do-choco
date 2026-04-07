<?php
declare(strict_types=1);

session_start();

$usuarioAutenticado = isset($_SESSION["usuario_id"]);
$usuarioNome = (string) ($_SESSION["usuario_nome"] ?? "");
$usuarioPapel = (string) ($_SESSION["usuario_papel"] ?? "cliente");
$mensagemBoasVindas = "";

if (isset($_SESSION["flash_boas_vindas"])) {
    $mensagemBoasVindas = (string) $_SESSION["flash_boas_vindas"];
    unset($_SESSION["flash_boas_vindas"]);
}

$destinoConta = $usuarioAutenticado
    ? ($usuarioPapel === "admin" ? "admin.php" : "conta.php")
    : "login.php";
$rotuloConta = $usuarioAutenticado ? "" : "Entre ou cadastre-se";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imperio do Chocolate</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<<<<<<< HEAD
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
=======
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
>>>>>>> f2cedfee0b6c235c4b3af2c04d6952b954af5714
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header class="topo-site">
        <div class="marca-site">
            <img class="marca-site__logo" src="logo-velle-dulcis.png" alt="Velle Dulcis">
        </div>

        <nav class="menu-topo" aria-label="Principal">
            <div class="menu-topo__item menu-topo__item--mega">
                <a href="#vitrine" class="menu-topo__link menu-topo__link--dropdown">
                    <span>Chocolate</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7 10l5 5 5-5"></path>
                    </svg>
                </a>

                <div class="mega-menu" role="group" aria-label="Explorar chocolates">
                    <div class="mega-menu__colunas">
                        <section class="mega-menu__coluna">
                            <p class="mega-menu__titulo">Colecoes</p>
                            <a href="#vitrine" class="mega-menu__item">
                                <strong>Selecao da Casa</strong>
                                <span>Os sabores que mais chamam atencao na vitrine.</span>
                            </a>
                            <a href="#vitrine" class="mega-menu__item">
                                <strong>Presentes Especiais</strong>
                                <span>Opcoes pensadas para surpreender com visual e sabor.</span>
                            </a>
                            <a href="#vitrine" class="mega-menu__item">
                                <strong>Importados</strong>
                                <span>Achados diferentes para quem quer provar algo novo.</span>
                            </a>
                            <a href="#vitrine" class="mega-menu__item">
                                <strong>Edicoes Premium</strong>
                                <span>Produtos com proposta mais sofisticada e marcante.</span>
                            </a>
                        </section>

                        <section class="mega-menu__coluna">
                            <p class="mega-menu__titulo">Tipos</p>
                            <a href="#vitrine" class="mega-menu__lista-link">Chocolate recheado</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Chocolate artesanal</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Caixas premium</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Tabletes</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Importados</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Mais Vendidos</a>
                        </section>

                        <section class="mega-menu__coluna">
                            <p class="mega-menu__titulo">Momentos</p>
                            <a href="#vitrine" class="mega-menu__lista-link">Para presentear</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Para dividir</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Para kits</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Novidades</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Vitrine completa</a>
                        </section>
                    </div>

                    <div class="mega-menu__destaques">
                        <a class="mega-menu__card" href="produto.html?id=jojo-moranguinho">
                            <img src="https://www.rickdoces.com.br/estatico/rickdoces/images/temp/620_alpinellawithstrawberry100gr.jpeg?v=1761647260" alt="Jojo Moranguinho">
                            <div class="mega-menu__card-conteudo">
                                <span class="mega-menu__card-tag">Selecao da casa</span>
                                <strong>Jojo Moranguinho</strong>
                            </div>
                        </a>

                        <a class="mega-menu__card" href="produto.html?id=beicinho-de-chocolate">
                            <img src="https://www.rickdoces.com.br/estatico/rickdoces/images/temp/620_560cde9e09eea7d7770f1c1d5db26d91.jpeg?v=1768478586" alt="Beicinho de Chocolate">
                            <div class="mega-menu__card-conteudo">
                                <span class="mega-menu__card-tag">Favorito da vitrine</span>
                                <strong>Beicinho de Chocolate</strong>
                            </div>
                        </a>

                        <a class="mega-menu__card mega-menu__card--alto" href="produto.html?id=six-server">
                            <img src="https://www.rickdoces.com.br/estatico/rickdoces/images/produto/f93490b5f7cab0f200bc76fb4cddde1f.png?v=1774458174" alt="Six Server">
                            <div class="mega-menu__card-conteudo">
                                <span class="mega-menu__card-tag">Edicao premium</span>
                                <strong>Six Server</strong>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="menu-topo__item menu-topo__item--mega">
                <a href="#vitrine" class="menu-topo__link menu-topo__link--dropdown">
                    <span>Coleções</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7 10l5 5 5-5"></path>
                    </svg>
                </a>

                <div class="mega-menu" role="group" aria-label="Explorar colecoes">
                    <div class="mega-menu__colunas">
                        <section class="mega-menu__coluna">
                            <p class="mega-menu__titulo">Curadoria</p>
                            <a href="#vitrine" class="mega-menu__item">
                                <strong>SeleÃ§Ã£o da Casa</strong>
                                <span>Os produtos que melhor representam a identidade da vitrine.</span>
                            </a>
                            <a href="#vitrine" class="mega-menu__item">
                                <strong>Favoritos da Vitrine</strong>
                                <span>Destaques que chamam atenÃ§Ã£o pelo visual e pelo sabor.</span>
                            </a>
                            <a href="#vitrine" class="mega-menu__item">
                                <strong>Pequenos Luxos</strong>
                                <span>Escolhas para presentear ou transformar um momento simples.</span>
                            </a>
                            <a href="#vitrine" class="mega-menu__item">
                                <strong>Importados em Destaque</strong>
                                <span>Sabores diferentes para quem quer sair do comum.</span>
                            </a>
                        </section>

                        <section class="mega-menu__coluna">
                            <p class="mega-menu__titulo">Coleções</p>
                            <a href="#vitrine" class="mega-menu__lista-link">Mais Vendidos</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Presentes especiais</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Importados</a>
                            <a href="#vitrine" class="mega-menu__lista-link">ClÃ¡ssicos cremosos</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Premium</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Novidades</a>
                        </section>

                        <section class="mega-menu__coluna">
                            <p class="mega-menu__titulo">Compre por Estilo</p>
                            <a href="#vitrine" class="mega-menu__lista-link">Para presentear</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Para compartilhar</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Para kits</a>
                            <a href="#vitrine" class="mega-menu__lista-link">Para impressionar</a>
                            <a href="#vitrine" class="mega-menu__lista-link">LanÃ§amentos</a>
                        </section>
                    </div>

                    <div class="mega-menu__destaques">
                        <a class="mega-menu__card" href="produto.html?id=paizao">
                            <img src="https://www.rickdoces.com.br/estatico/rickdoces/images/temp/620_arnottstimtamoriginal163gr.jpeg?v=1761131171" alt="Paizao">
                            <div class="mega-menu__card-conteudo">
                                <span class="mega-menu__card-tag">Presente especial</span>
                                <strong>Paizao</strong>
                            </div>
                        </a>

                        <a class="mega-menu__card" href="produto.html?id=jelly-belly-boba-milk-tea-28gr">
                            <img src="https://www.rickdoces.com.br/estatico/rickdoces/images/temp/620_jellybellybobamilktea28gr.jpeg?v=1772802157" alt="Jelly Belly Boba Milk Tea 28gr">
                            <div class="mega-menu__card-conteudo">
                                <span class="mega-menu__card-tag">Importado</span>
                                <strong>Jelly Belly Boba Milk Tea</strong>
                            </div>
                        </a>

                        <a class="mega-menu__card mega-menu__card--alto" href="produto.html?id=milka-caramel-creme-100gr">
                            <img src="https://www.rickdoces.com.br/estatico/rickdoces/images/temp/620_milkacaramelcreme100gr.jpeg?v=1666638362" alt="Milka Caramel-Creme 100gr">
                            <div class="mega-menu__card-conteudo">
                                <span class="mega-menu__card-tag">ClÃ¡ssico cremoso</span>
                                <strong>Milka Caramel-Creme 100gr</strong>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <a href="#" class="menu-topo__link">Aprenda</a>
        </nav>

        <div class="acoes-topo">
            <button id="btn-tema" class="acao-topo acao-topo--tema" type="button" aria-label="Alternar modo escuro" title="Alternar tema">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M21 12.8A8.5 8.5 0 1111.2 3a6.5 6.5 0 109.8 9.8z"></path>
                </svg>
            </button>
            <button id="abrir-pesquisa" class="acao-topo" type="button" aria-label="Buscar">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="7.5"></circle>
                    <path d="M16.5 16.5L21 21"></path>
                </svg>
            </button>
            <a
                id="link-conta"
                class="acao-topo acao-topo--link acao-topo--conta"
                href="<?php echo htmlspecialchars($destinoConta, ENT_QUOTES, "UTF-8"); ?>"
                aria-label="Entrar ou acessar conta"
                data-logado="<?php echo $usuarioAutenticado ? "true" : "false"; ?>"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="8" r="4"></circle>
                    <path d="M5 20c1.8-3.3 4.2-5 7-5s5.2 1.7 7 5"></path>
                </svg>
                <span id="link-conta-texto" <?php echo $usuarioAutenticado ? 'hidden' : ''; ?>>
                    <?php echo htmlspecialchars($rotuloConta, ENT_QUOTES, "UTF-8"); ?>
                </span>
            </a>
            <?php if (!$usuarioAutenticado): ?>
                <a class="acao-topo acao-topo--cadastro" href="cadastro.php">
                    Criar conta
                </a>
            <?php endif; ?>
            <button id="btn-carrinho" class="acao-topo acao-topo--carrinho" type="button" aria-label="Carrinho">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8 8V7a4 4 0 118 0v1"></path>
                    <path d="M6 8h12l-1 11H7L6 8z"></path>
                </svg>
                <span id="qtd-itens">0</span>
            </button>

            <div id="popup-carrinho" class="popup-carrinho" aria-live="polite" aria-hidden="true">
                <div class="popup-carrinho__seta" aria-hidden="true"></div>
                <div class="popup-carrinho__conteudo">
                    <div class="popup-carrinho__icone" aria-hidden="true">&#10003;</div>
                    <div class="popup-carrinho__texto">
                        <strong>Adicionado com sucesso a sacola!</strong>
                        <span id="popup-carrinho-produto">Produto</span>
                    </div>
                </div>
                <div class="popup-carrinho__progresso">
                    <span id="popup-carrinho-barra"></span>
                </div>
            </div>
        </div>
    </header>

    <div id="overlay-pesquisa" class="overlay-pesquisa oculto">
        <div class="overlay-pesquisa__cabecalho">
            <button id="fechar-pesquisa" class="overlay-pesquisa__fechar" type="button" aria-label="Fechar pesquisa">&times;</button>
        </div>

        <div class="overlay-pesquisa__conteudo">
            <h2>Pesquisar Chocolates</h2>

            <div class="pesquisa-wrapper">
                <div class="pesquisa-box">
                    <div class="pesquisa-campo">
                        <span class="pesquisa-icone" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" focusable="false">
                                <circle cx="11" cy="11" r="7.5"></circle>
                                <path d="M16.5 16.5L21 21"></path>
                            </svg>
                        </span>
                        <input type="text" id="barra-pesquisa" placeholder="Buscar chocolate...">
                        <button id="btn-limpar-pesquisa" class="btn-icone oculto" type="button" aria-label="Limpar pesquisa">&times;</button>
                    </div>

                    <div id="painel-pesquisa" class="painel-pesquisa">
                        <div class="painel-pesquisa__bloco">
                            <h3>Sugestoes</h3>
                            <div id="lista-sugestoes" class="lista-sugestoes"></div>
                        </div>

                        <div class="painel-pesquisa__bloco">
                            <div class="painel-pesquisa__topo">
                                <button id="btn-ver-todos" class="btn-ver-todos" type="button">Veja todos os produtos</button>
                            </div>
                            <div id="lista-resultados-pesquisa" class="lista-resultados-pesquisa"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main>
        <h2>Nossos Chocolates</h2>

        <section id="vitrine" class="grid-chocolates">
            <div id="container-cards" class="container"></div>
        </section>
    </main>

    <footer class="rodape-site">
        <div class="rodape-site__container">
            <div class="rodape-site__grid">
                <section class="rodape-site__coluna">
                    <h3>Shop</h3>
                    <a href="#vitrine">Coleções</a>
                    <a href="#vitrine">Presentes &amp; Kits</a>
                    <a href="#vitrine">Mais Vendidos</a>
                    <a href="#vitrine">Importados</a>
                    <a href="#vitrine">Edições Premium</a>
                </section>

                <section class="rodape-site__coluna">
                    <h3>Aprenda</h3>
                    <a href="#">Nossa História</a>
                    <a href="#">Guia de Sabores</a>
                    <a href="#">Como Montar Presentes</a>
                    <a href="#">Novidades</a>
                </section>

                <section class="rodape-site__coluna">
                    <h3>Suporte</h3>
                    <a href="#">Dúvidas Frequentes</a>
                    <a href="#">Fale Conosco</a>
                    <a href="#">Entrega e Retirada</a>
                    <a href="#">Trocas e Devoluções</a>
                    <a href="#">Privacidade</a>
                </section>

                <section class="rodape-site__coluna rodape-site__coluna--contato">
                    <h3>Contato</h3>
                    <p>Segunda a sexta, das 7h às 12h</p>
                    <p>(11) 4002-8922</p>
                    <p>contato@velledulcis.com</p>
                    <p>São Paulo, Brasil</p>

                    <div class="rodape-site__social">
                        <a href="#" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3.5" y="3.5" width="17" height="17" rx="5"></rect>
                                <circle cx="12" cy="12" r="4"></circle>
                                <circle cx="17.2" cy="6.8" r="1"></circle>
                            </svg>
                        </a>
                        <a href="#" aria-label="TikTok">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M14.5 4.5c.5 1.5 1.5 2.6 3 3.2v2.3c-1.1 0-2.2-.3-3-.9V14a4.5 4.5 0 11-4.5-4.5c.3 0 .6 0 .9.1V12a2.2 2.2 0 101.4 2V4.5z"></path>
                            </svg>
                        </a>
                    </div>
                </section>
            </div>

            <div class="rodape-site__base">
                <p>© Velle Dulcis 2026</p>
                <div class="rodape-site__pagamentos" aria-label="Formas de pagamento">
                    <span class="rodape-site__pix" aria-label="Pix" title="Pix">
                        <img src="logo-pix-icone-1024.png" alt="Pix">
                    </span>
                </div>
                <span class="rodape-site__moeda">BRL</span>
            </div>
        </div>
    </footer>

    <div id="overlay-carrinho" class="overlay-carrinho" aria-hidden="true"></div>

    <aside id="carrinho-lateral">
        <div class="carrinho-topo">
            <h3>Minha sacola</h3>
            <button id="fechar-carrinho" class="carrinho-fechar" type="button" aria-label="Fechar carrinho">&times;</button>
        </div>

        <div class="carrinho-corpo">
            <ul id="lista-carrinho"></ul>
        </div>

        <div class="carrinho-resumo">
            <div class="carrinho-resumo__linha">
                <span>Subtotal</span>
                <strong id="subtotal-preco">R$ 0,00</strong>
            </div>
            <div class="carrinho-resumo__linha carrinho-resumo__linha--total">
                <span>Total</span>
                <strong id="total-preco">R$ 0,00</strong>
            </div>
            <button id="finalizar">Finalizar pedido</button>
        </div>
    </aside>

    <div id="popup-remocao" class="popup-remocao" aria-live="polite" aria-hidden="true">
        <span id="popup-remocao-texto">Produto removido do carrinho.</span>
        <button id="fechar-popup-remocao" class="popup-remocao__fechar" type="button" aria-label="Fechar aviso">&times;</button>
    </div>

    <div
        id="popup-boas-vindas"
        class="popup-boas-vindas<?php echo $mensagemBoasVindas !== "" ? " ativo" : ""; ?>"
        aria-live="polite"
        aria-hidden="<?php echo $mensagemBoasVindas !== "" ? "false" : "true"; ?>"
    >
        <div class="popup-boas-vindas__conteudo">
            <strong>Sessao iniciada</strong>
            <span id="popup-boas-vindas-texto"><?php echo htmlspecialchars($mensagemBoasVindas, ENT_QUOTES, "UTF-8"); ?></span>
        </div>
        <button id="fechar-popup-boas-vindas" class="popup-boas-vindas__fechar" type="button" aria-label="Fechar mensagem">&times;</button>
    </div>

    <script>
        window.APP_AUTH = {
            autenticado: <?php echo $usuarioAutenticado ? "true" : "false"; ?>,
            nome: <?php echo json_encode($usuarioNome, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
            papel: <?php echo json_encode($usuarioPapel, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
            destinoConta: <?php echo json_encode($destinoConta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
            mensagemBoasVindas: <?php echo json_encode($mensagemBoasVindas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
        };
    </script>
    <script src="products-data.js"></script>
    <script src="auth.js"></script>
    <script src="script.js"></script>

</body>

</html>


<<<<<<< HEAD
=======

>>>>>>> f2cedfee0b6c235c4b3af2c04d6952b954af5714
