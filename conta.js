const contaNome = document.getElementById("conta-nome");
const contaEmail = document.getElementById("conta-email");
const contaPerfil = document.getElementById("conta-perfil");
const contaUsuarioNome = document.getElementById("conta-usuario-nome");
const contaMensagem = document.getElementById("conta-mensagem");

const sessaoConta = obterSessaoAutenticada();

if (!sessaoConta || !sessaoConta.email) {
    window.location.href = obterRotasApp().login;
} else if (sessaoConta.papel === "admin") {
    window.location.href = obterRotasApp().admin;
} else {

    const nome = sessaoConta.nome || "Cliente";
    const email = sessaoConta.email || "";
    const papel = "cliente";

    if (contaNome) {
        contaNome.value = nome;
    }

    if (contaEmail) {
        contaEmail.value = email;
    }

    if (contaPerfil) {
        contaPerfil.value = `Perfil: ${papel}`;
    }

    if (contaUsuarioNome) {
        contaUsuarioNome.textContent = nome;
    }

    if (contaMensagem) {
        const numeroPedido = new URLSearchParams(window.location.search).get("pedido");
        contaMensagem.textContent = numeroPedido
            ? `Pedido ${numeroPedido} registrado na navegacao atual.`
            : "Sessao carregada com sucesso.";
        contaMensagem.classList.add("sucesso");
    }
}
