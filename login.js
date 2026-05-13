const formLogin = document.getElementById("form-login");
const campoEmail = document.getElementById("email");
const campoSenha = document.getElementById("senha");
const mensagemLogin = document.getElementById("login-mensagem");

if (usuarioEstaAutenticado()) {
    const sessaoAtual = obterSessaoAutenticada();
    window.location.href = obterDestinoPosLogin(sessaoAtual?.papel);
}

function exibirMensagemLogin(texto, tipo = "erro") {
    mensagemLogin.textContent = texto;
    mensagemLogin.classList.toggle("sucesso", tipo === "sucesso");
}

formLogin.addEventListener("submit", (event) => {
    event.preventDefault();

    const resultado = autenticarUsuario(campoEmail.value, campoSenha.value);

    if (!resultado.sucesso) {
        exibirMensagemLogin(resultado.mensagem);
        return;
    }

    exibirMensagemLogin("Login realizado. Redirecionando...", "sucesso");

    setTimeout(() => {
        window.location.href = obterDestinoPosLogin(resultado.usuario?.papel);
    }, 600);
});
