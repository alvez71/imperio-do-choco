const formCadastro = document.getElementById("form-cadastro");
const campoCadastroNome = document.getElementById("cadastro-nome");
const campoCadastroEmail = document.getElementById("cadastro-email");
const campoCadastroSenha = document.getElementById("cadastro-senha");
const campoCadastroConfirmacao = document.getElementById("cadastro-confirmar-senha");
const mensagemCadastro = document.getElementById("cadastro-mensagem");

if (usuarioEstaAutenticado()) {
    const sessaoAtual = obterSessaoAutenticada();
    window.location.href = obterDestinoPosLogin(sessaoAtual?.papel);
}

function exibirMensagemCadastro(texto, tipo = "erro") {
    if (!mensagemCadastro) {
        return;
    }

    mensagemCadastro.textContent = texto;
    mensagemCadastro.classList.toggle("sucesso", tipo === "sucesso");
}

if (formCadastro) {
    formCadastro.addEventListener("submit", (event) => {
        event.preventDefault();

        const nome = String(campoCadastroNome?.value || "").trim();
        const email = String(campoCadastroEmail?.value || "").trim();
        const senha = String(campoCadastroSenha?.value || "").trim();
        const confirmacao = String(campoCadastroConfirmacao?.value || "").trim();

        if (!nome || !email || !senha || !confirmacao) {
            exibirMensagemCadastro("Preencha todos os campos para continuar.");
            return;
        }

        if (senha.length < 6) {
            exibirMensagemCadastro("A senha precisa ter pelo menos 6 caracteres.");
            return;
        }

        if (senha !== confirmacao) {
            exibirMensagemCadastro("A confirmacao de senha nao confere.");
            return;
        }

        const resultado = cadastrarUsuario(nome, email, senha);

        if (!resultado.sucesso) {
            exibirMensagemCadastro(resultado.mensagem);
            return;
        }

        exibirMensagemCadastro("Conta criada com sucesso. Redirecionando...", "sucesso");

        window.setTimeout(() => {
            window.location.href = obterDestinoPosLogin(resultado.usuario?.papel);
        }, 600);
    });
}
