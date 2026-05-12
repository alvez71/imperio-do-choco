<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . "/conexao.php";

if (isset($_SESSION["usuario_id"])) {
    $destino = (string) ($_SESSION["usuario_papel"] ?? "cliente") === "admin" ? "admin.php" : "conta.php";
    header("Location: " . $destino);
    exit;
}

$erro = "";
$sucesso = "";
$nomePreenchido = "";
$emailPreenchido = "";
<<<<<<< HEAD
$bancoDisponivel = bancoDeDadosDisponivel($pdo) && ($pdo instanceof PDO ? schemaUsuariosDisponivel($pdo) : false);
=======
$bancoDisponivel = bancoDeDadosDisponivel($pdo);
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
$mensagemBancoIndisponivel = "O banco de dados esta indisponivel no momento. Tente novamente mais tarde.";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nomePreenchido = trim((string) ($_POST["nome"] ?? ""));
    $emailPreenchido = trim((string) ($_POST["email"] ?? ""));
    $senha = trim((string) ($_POST["senha"] ?? ""));
    $confirmacaoSenha = trim((string) ($_POST["confirmar_senha"] ?? ""));

    if ($nomePreenchido === "" || $emailPreenchido === "" || $senha === "" || $confirmacaoSenha === "") {
        $erro = "Preencha todos os campos para criar sua conta.";
    } elseif (!filter_var($emailPreenchido, FILTER_VALIDATE_EMAIL)) {
        $erro = "Digite um email valido.";
    } elseif (mb_strlen($senha) < 6) {
        $erro = "A senha precisa ter pelo menos 6 caracteres.";
    } elseif ($senha !== $confirmacaoSenha) {
        $erro = "A confirmacao de senha nao confere.";
    } elseif (!$bancoDisponivel) {
        $erro = $mensagemBancoIndisponivel;
    } else {
        try {
<<<<<<< HEAD
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $insert = $pdo->prepare(
                "INSERT INTO usuarios (nome, email, senha_hash, papel) VALUES (:nome, :email, :senha_hash, :papel)"
            );
            $insert->execute([
                "nome" => $nomePreenchido,
                "email" => $emailPreenchido,
                "senha_hash" => $senhaHash,
                "papel" => "cliente",
            ]);

            $sucesso = "Conta criada com sucesso. Agora voce ja pode entrar.";
            $nomePreenchido = "";
            $emailPreenchido = "";
        } catch (PDOException $exception) {
            $chaveDuplicada = obterChaveDuplicada($exception);

            if (excecaoEntradaDuplicada($exception) && ($chaveDuplicada === "usuarios_email_unique" || str_contains($chaveDuplicada, "email"))) {
                $erro = "Ja existe uma conta com este email.";
            } else {
                $erro = "Nao foi possivel criar sua conta agora. Tente novamente em instantes.";
            }
=======
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
            $stmt->execute(["email" => $emailPreenchido]);

            if ($stmt->fetch()) {
                $erro = "Ja existe uma conta com este email.";
            } else {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $insert = $pdo->prepare(
                    "INSERT INTO usuarios (nome, email, senha_hash, papel) VALUES (:nome, :email, :senha_hash, :papel)"
                );
                $insert->execute([
                    "nome" => $nomePreenchido,
                    "email" => $emailPreenchido,
                    "senha_hash" => $senhaHash,
                    "papel" => "cliente",
                ]);

                $sucesso = "Conta criada com sucesso. Agora voce ja pode entrar.";
                $nomePreenchido = "";
                $emailPreenchido = "";
            }
        } catch (PDOException $exception) {
            $erro = "Nao foi possivel criar sua conta agora. Tente novamente em instantes.";
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta | Velle Dulcis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<<<<<<< HEAD
<body class="login-body login-body--customer">
    <script src="theme-init.js"></script>
    <main class="login-modal-shell">
        <section class="login-modal" aria-labelledby="cadastro-title">
            <a class="login-modal__close" href="index.php" aria-label="Fechar e voltar para a vitrine">
                <span aria-hidden="true">&times;</span>
            </a>

            <div class="login-modal__intro">
                <a class="login-modal__brand" href="index.php" aria-label="Voltar para a vitrine">
                    <img src="logo-velle-dulcis.png" alt="Velle Dulcis">
                </a>
                <h1 id="cadastro-title">Criar conta</h1>
                <p>
                    Cadastre seus dados para acompanhar pedidos e acessar sua conta. Novos cadastros entram como clientes, enquanto acessos administrativos continuam protegidos.
                </p>
            </div>

            <form method="post" class="login-form login-form--customer" novalidate>
                <label class="login-form__field login-form__field--customer">
                    <span>Nome*</span>
                    <input
                        type="text"
                        name="nome"
                        autocomplete="name"
                        placeholder="Seu nome"
                        value="<?php echo htmlspecialchars($nomePreenchido, ENT_QUOTES, "UTF-8"); ?>"
                        required
                    >
                </label>

                <label class="login-form__field login-form__field--customer">
                    <span>Email*</span>
                    <input
                        type="email"
                        name="email"
                        autocomplete="email"
                        placeholder="Email"
                        value="<?php echo htmlspecialchars($emailPreenchido, ENT_QUOTES, "UTF-8"); ?>"
                        required
                    >
                </label>

                <label class="login-form__field login-form__field--customer">
                    <span>Senha*</span>
                    <input
                        type="password"
                        name="senha"
                        autocomplete="new-password"
                        placeholder="Minimo de 6 caracteres"
                        required
                    >
                </label>

                <label class="login-form__field login-form__field--customer">
                    <span>Confirmar senha*</span>
                    <input
                        type="password"
                        name="confirmar_senha"
                        autocomplete="new-password"
                        placeholder="Repita sua senha"
                        required
                    >
                </label>

                <button type="submit">Criar conta</button>
=======
<body>
    <main class="login-page">
        <section class="login-hero">
            <a class="login-hero__brand" href="index.php" aria-label="Voltar para a vitrine">
                <img src="logo-velle-dulcis.png" alt="Velle Dulcis">
            </a>

            <p class="login-hero__eyebrow">Nova conta</p>
            <h1>Crie sua conta para acompanhar pedidos e acessar sua area.</h1>
            <p class="login-hero__text">
                Novos cadastros entram como clientes. O painel administrativo continua reservado somente para administradores.
            </p>

            <div class="login-hero__actions">
                <a class="login-hero__cta" href="login.php">Ja tenho conta</a>
            </div>
        </section>

        <section class="login-card">
            <div class="login-card__header">
                <p class="login-card__eyebrow">Cadastro</p>
                <h2>Criar conta</h2>
                <p>Preencha seus dados para liberar o acesso a sua area de cliente.</p>
            </div>

            <form method="post" class="login-form" novalidate>
                <label class="login-form__field">
                    <span>Nome</span>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($nomePreenchido, ENT_QUOTES, "UTF-8"); ?>" required>
                </label>

                <label class="login-form__field">
                    <span>Email</span>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($emailPreenchido, ENT_QUOTES, "UTF-8"); ?>" required>
                </label>

                <label class="login-form__field">
                    <span>Senha</span>
                    <input type="password" name="senha" placeholder="Minimo de 6 caracteres" required>
                </label>

                <label class="login-form__field">
                    <span>Confirmar senha</span>
                    <input type="password" name="confirmar_senha" required>
                </label>

                <button type="submit">Criar minha conta</button>

                <a class="login-form__secondary" href="login.php">Ja tenho conta e quero entrar</a>
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd

                <?php if ($erro !== "" || (!$bancoDisponivel && $sucesso === "")): ?>
                    <p class="login-form__message" aria-live="polite">
                        <?php echo htmlspecialchars($erro !== "" ? $erro : $mensagemBancoIndisponivel, ENT_QUOTES, "UTF-8"); ?>
                    </p>
                <?php elseif ($sucesso !== ""): ?>
                    <p class="login-form__message sucesso" aria-live="polite">
                        <?php echo htmlspecialchars($sucesso, ENT_QUOTES, "UTF-8"); ?>
                    </p>
<<<<<<< HEAD
                <?php endif; ?>

                <a class="login-form__create" href="login.php">
                    Ja tenho conta
                    <span aria-hidden="true">&rarr;</span>
                </a>

                <?php if ($sucesso !== ""): ?>
                    <a class="login-form__success-link" href="login.php">Ir para o login</a>
                <?php endif; ?>
            </form>

            <p class="login-modal__note">
                Depois do cadastro, clientes entram pela propria area e administradores seguem para o painel ao usar credenciais autorizadas.
            </p>
=======
                    <a class="login-form__success-link" href="login.php">Ir para o login</a>
                <?php endif; ?>

                <p class="login-form__hint">
                    Ja tem conta?
                    <a href="login.php">Entrar agora</a>
                </p>
            </form>
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
        </section>
    </main>
</body>
</html>
