<?php
declare(strict_types=1);

session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/conexao.php";

function responderJson(array $dados, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function garantirTabelaCarrinho(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS carrinho_itens (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            usuario_id INT(10) UNSIGNED NOT NULL,
            chave_produto VARCHAR(190) NOT NULL,
            produto_slug VARCHAR(190) DEFAULT NULL,
            produto_nome VARCHAR(180) NOT NULL,
            preco_unitario DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            imagem TEXT DEFAULT NULL,
            quantidade INT(10) UNSIGNED NOT NULL DEFAULT 1,
            criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY usuario_item_unico (usuario_id, chave_produto),
            KEY usuario_id_idx (usuario_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
}

if (!isset($_SESSION["usuario_id"])) {
    responderJson(["erro" => "Usuario nao autenticado."], 401);
}

if (!bancoDeDadosDisponivel($pdo)) {
    responderJson(["erro" => "Banco de dados indisponivel."], 503);
}

$usuarioId = (int) $_SESSION["usuario_id"];

try {
    garantirTabelaCarrinho($pdo);
} catch (PDOException $exception) {
    responderJson(["erro" => "Nao foi possivel preparar o carrinho."], 500);
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    try {
        $stmt = $pdo->prepare(
            "SELECT chave_produto, produto_slug, produto_nome, preco_unitario, imagem, quantidade
             FROM carrinho_itens
             WHERE usuario_id = :usuario_id
             ORDER BY id ASC"
        );
        $stmt->execute(["usuario_id" => $usuarioId]);

        $itens = array_map(static function (array $item): array {
            return [
                "chave" => (string) ($item["chave_produto"] ?? ""),
                "slug" => (string) ($item["produto_slug"] ?? ""),
                "nome" => (string) ($item["produto_nome"] ?? ""),
                "preco" => (float) ($item["preco_unitario"] ?? 0),
                "imagem" => (string) ($item["imagem"] ?? ""),
                "qtd" => (int) ($item["quantidade"] ?? 0),
            ];
        }, $stmt->fetchAll());

        responderJson(["itens" => $itens]);
    } catch (PDOException $exception) {
        responderJson(["erro" => "Nao foi possivel carregar o carrinho."], 500);
    }
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responderJson(["erro" => "Metodo nao permitido."], 405);
}

$conteudoBruto = file_get_contents("php://input");
$dados = json_decode($conteudoBruto ?: "", true);
$itens = $dados["itens"] ?? null;

if (!is_array($itens)) {
    responderJson(["erro" => "Formato do carrinho invalido."], 422);
}

$itensNormalizados = [];

foreach ($itens as $item) {
    if (!is_array($item)) {
        continue;
    }

    $nome = trim((string) ($item["nome"] ?? ""));
    $chave = trim((string) ($item["chave"] ?? ""));
    $slug = trim((string) ($item["slug"] ?? ""));
    $imagem = trim((string) ($item["imagem"] ?? ""));
    $preco = round((float) ($item["preco"] ?? 0), 2);
    $quantidade = (int) ($item["qtd"] ?? 0);

    if ($nome === "" || $chave === "" || $quantidade <= 0) {
        continue;
    }

    $itensNormalizados[] = [
        "chave" => mb_substr($chave, 0, 190),
        "slug" => $slug !== "" ? mb_substr($slug, 0, 190) : null,
        "nome" => mb_substr($nome, 0, 180),
        "preco" => $preco > 0 ? $preco : 0,
        "imagem" => $imagem !== "" ? $imagem : null,
        "qtd" => $quantidade,
    ];
}

try {
    $pdo->beginTransaction();

    $delete = $pdo->prepare("DELETE FROM carrinho_itens WHERE usuario_id = :usuario_id");
    $delete->execute(["usuario_id" => $usuarioId]);

    if ($itensNormalizados !== []) {
        $insert = $pdo->prepare(
            "INSERT INTO carrinho_itens (
                usuario_id, chave_produto, produto_slug, produto_nome, preco_unitario, imagem, quantidade
            ) VALUES (
                :usuario_id, :chave_produto, :produto_slug, :produto_nome, :preco_unitario, :imagem, :quantidade
            )"
        );

        foreach ($itensNormalizados as $item) {
            $insert->execute([
                "usuario_id" => $usuarioId,
                "chave_produto" => $item["chave"],
                "produto_slug" => $item["slug"],
                "produto_nome" => $item["nome"],
                "preco_unitario" => $item["preco"],
                "imagem" => $item["imagem"],
                "quantidade" => $item["qtd"],
            ]);
        }
    }

    $pdo->commit();
    responderJson(["sucesso" => true, "itens" => $itensNormalizados]);
} catch (PDOException $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    responderJson(["erro" => "Nao foi possivel salvar o carrinho."], 500);
}
