const STORAGE_KEY_AUTH = "imperio_auth_session";

const AUTH_DEFAULT_USER = {
    nome: "Administrador",
    email: "admin@imperiodochocolate.com",
    senha: "admin123",
};

function obterRotasApp() {
    const usandoPhp = /\.php($|\?)/i.test(window.location.pathname);

    return {
        admin: usandoPhp ? "admin.php" : "admin.html",
        index: usandoPhp ? "index.php" : "index.html",
        login: usandoPhp ? "login.php" : "login.html",
    };
}

function obterSessaoAutenticada() {
    if (window.APP_AUTH && window.APP_AUTH.autenticado) {
        return {
            nome: window.APP_AUTH.nome,
            email: window.APP_AUTH.email || "",
            papel: window.APP_AUTH.papel,
        };
    }

    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY_AUTH)) || null;
    } catch (erro) {
        console.warn("Nao foi possivel ler a sessao atual.", erro);
        return null;
    }
}

function usuarioEstaAutenticado() {
    if (window.APP_AUTH) {
        return Boolean(window.APP_AUTH.autenticado);
    }

    const sessao = obterSessaoAutenticada();
    return Boolean(sessao && sessao.email);
}

function salvarSessaoAutenticada(usuario) {
    localStorage.setItem(STORAGE_KEY_AUTH, JSON.stringify({
        nome: usuario.nome,
        email: usuario.email,
        loginEm: new Date().toISOString(),
    }));
}

function encerrarSessaoAutenticada() {
    localStorage.removeItem(STORAGE_KEY_AUTH);
}

function autenticarUsuario(email, senha) {
    const emailNormalizado = String(email || "").trim().toLowerCase();
    const senhaNormalizada = String(senha || "").trim();

    if (
        emailNormalizado === AUTH_DEFAULT_USER.email &&
        senhaNormalizada === AUTH_DEFAULT_USER.senha
    ) {
        salvarSessaoAutenticada(AUTH_DEFAULT_USER);
        return {
            sucesso: true,
            usuario: obterSessaoAutenticada(),
        };
    }

    return {
        sucesso: false,
        mensagem: "Email ou senha invalidos. Use o acesso de demonstracao para testar.",
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
    const rotas = obterRotasApp();

    if (!linkConta) {
        return;
    }

    if (window.APP_AUTH) {
        linkConta.href = window.APP_AUTH.destinoConta || rotas.login;
        linkConta.setAttribute("aria-label", window.APP_AUTH.autenticado ? "Abrir minha conta" : "Entrar ou acessar conta");
        linkConta.title = window.APP_AUTH.autenticado ? "Minha conta" : "Entrar";
        linkConta.dataset.logado = window.APP_AUTH.autenticado ? "true" : "false";

        if (textoConta) {
            textoConta.hidden = window.APP_AUTH.autenticado;
            textoConta.textContent = "Entre ou cadastre-se";
        }

        return;
    }

    const autenticado = usuarioEstaAutenticado();

    linkConta.href = autenticado ? rotas.admin : rotas.login;
    linkConta.setAttribute("aria-label", autenticado ? "Abrir painel administrativo" : "Entrar ou acessar conta");
    linkConta.title = autenticado ? "Painel administrativo" : "Minha conta";
    linkConta.dataset.logado = autenticado ? "true" : "false";

    if (textoConta) {
        textoConta.hidden = autenticado;
        textoConta.textContent = "Entre ou cadastre-se";
    }
}
