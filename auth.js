const STORAGE_KEY_AUTH = "imperio_auth_session";
const STORAGE_KEY_USERS = "imperio_auth_users";
};

function obterRotasApp() {
    const usandoPhp = /\.php($|\?)/i.test(window.location.pathname);

    return {
        admin: usandoPhp ? "admin.php" : "admin.html",
        index: usandoPhp ? "index.php" : "index.html",
        login: usandoPhp ? "login.php" : "login.html",
        cadastro: usandoPhp ? "cadastro.php" : "cadastro.html",
        conta: usandoPhp ? "conta.php" : "conta.html",
function salvarSessaoAutenticada(usuario) {
    localStorage.setItem(STORAGE_KEY_AUTH, JSON.stringify({
        nome: usuario.nome,
        email: usuario.email,
        papel: usuario.papel || "cliente",
        return {
            sucesso: true,
            usuario: obterSessaoAutenticada(),
        };
    }

    return {
        sucesso: false,
        mensagem: "Email ou senha invalidos.",
    };
}

function cadastrarUsuario(nome, email, senha) {
    const nomeNormalizado = String(nome || "").trim();
    const emailNormalizado = normalizarEmail(email);
    const senhaNormalizada = String(senha || "").trim();

    if (!nomeNormalizado || !emailNormalizado || !senhaNormalizada) {
        return {
            sucesso: false,
            mensagem: "Preencha todos os campos para continuar.",
        };
    }

    if (buscarUsuarioPorEmail(emailNormalizado)) {
        return {
            sucesso: false,
            mensagem: "Ja existe uma conta com este email.",
        };
    }

    const usuarios = obterUsuariosLocais();
    const novoUsuario = {
        nome: nomeNormalizado,
        email: emailNormalizado,
        senha: senhaNormalizada,
        papel: "cliente",
    };

    usuarios.push(novoUsuario);
    salvarUsuariosLocais(usuarios);
    salvarSessaoAutenticada(novoUsuario);

    return {
        sucesso: true,
        usuario: obterSessaoAutenticada(),
    };
}

function redirecionarSeNaoAutenticado(destino = obterRotasApp().login) {
    if (!usuarioEstaAutenticado()) {
        window.location.href = destino;
        return false;
    }

    return true;
}

function aplicarLinkDaConta() {
    const linkConta = document.getElementById("link-conta");
    const textoConta = document.getElementById("link-conta-texto");
    const linkContaMobile = document.getElementById("link-conta-mobile");
    const textoContaMobile = document.getElementById("link-conta-mobile-texto");
    const rotas = obterRotasApp();

    if (!linkConta && !linkContaMobile) {
        return;
    }

    function aplicarEstadoLink(link, texto, destino, autenticado, rotuloTitleAutenticado) {
        if (!link) {
            return;
        }

        link.href = destino;
        link.setAttribute("aria-label", autenticado ? "Abrir minha conta" : "Entrar ou acessar conta");
        link.title = autenticado ? rotuloTitleAutenticado : "Minha conta";
        link.dataset.logado = autenticado ? "true" : "false";

        if (texto) {
            const ocultarTextoQuandoAutenticado = texto.dataset.hideWhenAuthenticated === "true";
            texto.hidden = ocultarTextoQuandoAutenticado && autenticado;
            texto.textContent = autenticado ? "Minha conta" : "Entrar";
        }
    }

    if (window.APP_AUTH) {
        const destinoConta = window.APP_AUTH.destinoConta || rotas.login;
        const autenticado = Boolean(window.APP_AUTH.autenticado);
        const rotuloTitleAutenticado = window.APP_AUTH.papel === "admin" ? "Painel administrativo" : "Minha conta";

        aplicarEstadoLink(linkConta, textoConta, destinoConta, autenticado, rotuloTitleAutenticado);
        aplicarEstadoLink(linkContaMobile, textoContaMobile, destinoConta, autenticado, rotuloTitleAutenticado);

        return;
    }

    const autenticado = usuarioEstaAutenticado();
    const sessao = obterSessaoAutenticada();
    const destinoConta = autenticado ? obterDestinoPosLogin(sessao?.papel) : rotas.login;
    const rotuloTitleAutenticado = sessao?.papel === "admin" ? "Painel administrativo" : "Minha conta";

    aplicarEstadoLink(linkConta, textoConta, destinoConta, autenticado, rotuloTitleAutenticado);
    aplicarEstadoLink(linkContaMobile, textoContaMobile, destinoConta, autenticado, rotuloTitleAutenticado);
}
