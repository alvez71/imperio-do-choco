const formLogin = document.getElementById("form-login");
const campoEmail = document.getElementById("email");
const campoSenha = document.getElementById("senha");
const mensagemLogin = document.getElementById("login-mensagem");

if (usuarioEstaAutenticado()) {
<<<<<<< HEAD
    const sessaoAtual = obterSessaoAutenticada();
    window.location.href = obterDestinoPosLogin(sessaoAtual?.papel);
=======
    window.location.href = "admin.html";
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
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

<<<<<<< HEAD
    exibirMensagemLogin("Login realizado. Redirecionando...", "sucesso");

    setTimeout(() => {
        window.location.href = obterDestinoPosLogin(resultado.usuario?.papel);
=======
    exibirMensagemLogin("Login realizado. Redirecionando para o painel...", "sucesso");

    setTimeout(() => {
        window.location.href = "admin.html";
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
    }, 600);
});
