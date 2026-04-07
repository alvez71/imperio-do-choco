<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$nomeUsuario = (string) ($_SESSION["usuario_nome"] ?? "Cliente");
$emailUsuario = (string) ($_SESSION["usuario_email"] ?? "");
$papelUsuario = (string) ($_SESSION["usuario_papel"] ?? "cliente");

if ($papelUsuario === "admin") {
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <title>Minha Conta | Velle Dulcis</title>
</head>
<body>
    <div class="admin-shell">
        <div class="admin-topbar">
            <a class="admin-topbar__voltar" href="index.php">Voltar para a vitrine</a>

            <div class="admin-topbar__acoes">
                <span class="admin-topbar__usuario">
                    <?php echo htmlspecialchars($nomeUsuario, ENT_QUOTES, "UTF-8"); ?>
                </span>
                <a class="admin-topbar__sair" href="logout.php">Sair</a>
            </div>
        </div>

        <div class="form-container">
            <h2>Minha conta</h2>
            <p class="form-container__intro">Sua conta foi criada com sucesso. Aqui podemos conectar historico de pedidos, favoritos e dados do cliente nos proximos passos.</p>

            <input type="text" value="<?php echo htmlspecialchars($nomeUsuario, ENT_QUOTES, "UTF-8"); ?>" readonly>
            <input type="email" value="<?php echo htmlspecialchars($emailUsuario, ENT_QUOTES, "UTF-8"); ?>" readonly>
            <input type="text" value="Perfil: cliente" readonly>
        </div>
    </div>
</body>
</html>
