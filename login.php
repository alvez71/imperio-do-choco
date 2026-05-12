<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . "/conexao.php";

function redirecionarUsuarioPorPapel(string $papel, ?string $nomeUsuario = null): void
{
    if ($papel === "admin") {
        header("Location: admin.php");
        exit;
    }

    if ($nomeUsuario !== null && $nomeUsuario !== "") {
        $_SESSION["flash_boas_vindas"] = "Bem-vindo de volta, {$nomeUsuario}.";
    }

    header("Location: index.php");
    exit;
}

if (isset($_SESSION["usuario_id"])) {
    redirecionarUsuarioPorPapel((string) ($_SESSION["usuario_papel"] ?? "cliente"));
}

$erro = "";
$emailPreenchido = "";
<<<<<<< HEAD
$bancoDisponivel = bancoDeDadosDisponivel($pdo) && ($pdo instanceof PDO ? schemaUsuariosDisponivel($pdo) : false);
=======
$bancoDisponivel = bancoDeDadosDisponivel($pdo);
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
$mensagemBancoIndisponivel = "O banco de dados esta indisponivel no momento. Tente novamente mais tarde.";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emailPreenchido = trim((string) ($_POST["email"] ?? ""));
    $senha = trim((string) ($_POST["senha"] ?? ""));

    if ($emailPreenchido === "" || $senha === "") {
        $erro = "Preencha email e senha para continuar.";
    } elseif (!$bancoDisponivel) {
        $erro = $mensagemBancoIndisponivel;
    } else {
        try {
            $sql = "SELECT id, nome, email, senha_hash, papel FROM usuarios WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["email" => $emailPreenchido]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($senha, (string) $usuario["senha_hash"])) {
                $_SESSION["usuario_id"] = (int) $usuario["id"];
                $_SESSION["usuario_nome"] = (string) $usuario["nome"];
                $_SESSION["usuario_email"] = (string) $usuario["email"];
                $_SESSION["usuario_papel"] = (string) ($usuario["papel"] ?? "cliente");

                redirecionarUsuarioPorPapel($_SESSION["usuario_papel"], $_SESSION["usuario_nome"]);
            }

            $erro = "Email ou senha invalidos.";
        } catch (PDOException $exception) {
            $erro = "Nao foi possivel validar o login agora. Tente novamente em instantes.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Velle Dulcis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<<<<<<< HEAD
<body class="login-body login-body--customer">
    <script src="theme-init.js"></script>
    <main class="login-modal-shell">
        <section class="login-modal" aria-labelledby="login-title">
            <a class="login-modal__close" href="index.php" aria-label="Fechar e voltar para a vitrine">
                <span aria-hidden="true">&times;</span>
            </a>

            <div class="login-modal__intro">
                <a class="login-modal__brand" href="index.php" aria-label="Voltar para a vitrine">
                    <img src="logo-velle-dulcis.png" alt="Velle Dulcis">
                </a>
                <h1 id="login-title">Login</h1>
                <p>
                    Entre para acompanhar pedidos e acessar sua conta. Se o perfil for administrativo, o painel abre automaticamente apos a autenticacao.
                </p>
            </div>

            <form method="post" class="login-form login-form--customer" novalidate>
                <label class="login-form__field login-form__field--customer">
                    <span>Email*</span>
                    <input
                        type="email"
                        name="email"
                        autocomplete="email"
                        placeholder="Email"
=======
<body>
    <main class="login-page">
        <section class="login-hero">
            <a class="login-hero__brand" href="index.php" aria-label="Voltar para a vitrine">
                <img src="logo-velle-dulcis.png" alt="Velle Dulcis">
            </a>

            <p class="login-hero__eyebrow">Minha conta</p>
            <h1>Entre na sua conta para acompanhar seus pedidos e acessar sua area.</h1>
            <p class="login-hero__text">
                Clientes entram normalmente por aqui e, se ainda nao tiverem acesso, podem criar uma conta em segundos. Contas administrativas continuam protegidas e so acessam o painel quando tiverem permissao.
            </p>

            <div class="login-hero__actions">
                <a class="login-hero__cta" href="cadastro.php">Criar conta de cliente</a>
            </div>
        </section>

        <section class="login-card">
            <div class="login-card__header">
                <p class="login-card__eyebrow">Login</p>
                <h2>Fazer login</h2>
                <p>Entre com sua conta para acessar o painel administrativo ou a sua area de cliente.</p>
            </div>

            <form method="post" class="login-form" novalidate>
                <label class="login-form__field">
                    <span>Email</span>
                    <input
                        type="email"
                        name="email"
                        placeholder="admin@imperiodochocolate.com"
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
                        value="<?php echo htmlspecialchars($emailPreenchido, ENT_QUOTES, "UTF-8"); ?>"
                        required
                    >
                </label>

<<<<<<< HEAD
                <label class="login-form__field login-form__field--customer">
                    <span>Senha*</span>
                    <input
                        type="password"
                        name="senha"
                        autocomplete="current-password"
                        placeholder="Senha"
                        required
                    >
                </label>

                <div class="login-form__meta">
                    <a class="login-form__assist" href="mailto:contato@velledulcis.com?subject=Recuperar%20senha">Esqueceu sua senha?</a>
                </div>

                <button type="submit">Entrar</button>
=======
                <label class="login-form__field">
                    <span>Senha</span>
                    <input type="password" name="senha" placeholder="Digite sua senha" required>
                </label>

                <button type="submit">Entrar na conta</button>

                <a class="login-form__secondary" href="cadastro.php">Nao tenho conta ainda</a>
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd

                <?php if ($erro !== "" || !$bancoDisponivel): ?>
                    <p id="login-mensagem" class="login-form__message" aria-live="polite">
                        <?php echo htmlspecialchars($erro !== "" ? $erro : $mensagemBancoIndisponivel, ENT_QUOTES, "UTF-8"); ?>
                    </p>
                <?php endif; ?>

<<<<<<< HEAD
                <a class="login-form__create" href="cadastro.php">
                    Criar conta
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </form>

            <p class="login-modal__note">
                Clientes seguem para a propria area e administradores entram no painel ao usar as credenciais corretas.
            </p>
=======
                <p class="login-form__hint">
                    Ainda nao tem conta?
                    <a href="cadastro.php">Criar cadastro</a>
                </p>
            </form>
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
        </section>
    </main>
</body>
</html>
