<?php
declare(strict_types=1);

session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/conexao.php";

function responderJsonAdmin(array $dados, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function usuarioEhAdmin(): bool
{
    return isset($_SESSION["usuario_id"]) && (string) ($_SESSION["usuario_papel"] ?? "") === "admin";
}

function normalizarListaImagens(array $imagens): array
{
    return array_values(array_unique(array_filter(array_map(
        static fn ($imagem): string => trim((string) $imagem),
        $imagens
    ))));
}

function gerarSlugUnicoProduto(PDO $pdo, string $nome, ?int $ignorarId = null): string
{
    $slugBase = gerarSlugProduto($nome) ?: "produto";
    $slug = $slugBase;
    $contador = 2;

    $sql = "SELECT id FROM produtos WHERE slug = :slug";
    if ($ignorarId !== null) {
        $sql .= " AND id <> :id";
    }
    $sql .= " LIMIT 1";

    $stmt = $pdo->prepare($sql);

    while (true) {
        $params = ["slug" => $slug];
        if ($ignorarId !== null) {
            $params["id"] = $ignorarId;
        }

        $stmt->execute($params);

        if (!$stmt->fetch()) {
            return $slug;
        }

        $slug = "{$slugBase}-{$contador}";
        $contador += 1;
    }
}

function formatarProdutoAdmin(array $produto): array
{
    $galeria = [];
    $imagensBrutas = $produto["imagens"] ?? "[]";

    if (is_string($imagensBrutas) && $imagensBrutas !== "") {
        $imagensDecodificadas = json_decode($imagensBrutas, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($imagensDecodificadas)) {
            $galeria = normalizarListaImagens($imagensDecodificadas);
        }
    }

    if ($galeria === [] && !empty($produto["img"])) {
        $galeria = [trim((string) $produto["img"])];
    }

    return [
        "id" => (int) ($produto["id"] ?? 0),
        "slug" => (string) ($produto["slug"] ?? ""),
        "img" => (string) ($produto["img"] ?? ""),
        "nome" => (string) ($produto["nome"] ?? ""),
        "preco" => (float) ($produto["preco"] ?? 0),
        "categoria" => (string) ($produto["categoria"] ?? $produto["tipo"] ?? ""),
        "peso" => (string) ($produto["peso"] ?? ""),
        "ref" => (string) ($produto["ref"] ?? ""),
        "destaque" => (string) ($produto["destaque"] ?? ""),
        "descricao" => (string) ($produto["descricao"] ?? ""),
        "imagens" => $galeria,
        "estoque_quantidade" => (int) ($produto["estoque_quantidade"] ?? 0),
        "tipo" => (string) ($produto["tipo"] ?? ""),
    ];
}

function lerCorpoJsonAdmin(): array
{
    $dados = json_decode(file_get_contents("php://input") ?: "", true);
    return is_array($dados) ? $dados : [];
}

function normalizarPayloadProdutoAdmin(array $dados): array
{
    $nome = trim((string) ($dados["nome"] ?? ""));
    $img = trim((string) ($dados["img"] ?? ""));
    $descricao = trim((string) ($dados["descricao"] ?? ""));
    $categoria = trim((string) ($dados["categoria"] ?? ""));
    $peso = trim((string) ($dados["peso"] ?? ""));
    $ref = trim((string) ($dados["ref"] ?? ""));
    $destaque = trim((string) ($dados["destaque"] ?? ""));
    $preco = round((float) ($dados["preco"] ?? 0), 2);
    $estoqueQuantidade = max(0, (int) ($dados["estoque_quantidade"] ?? 0));
    $tipo = trim((string) ($dados["tipo"] ?? $categoria));
    $imagens = normalizarListaImagens(is_array($dados["imagens"] ?? null) ? $dados["imagens"] : [$img]);

    return [
        "id" => (int) ($dados["id"] ?? 0),
        "nome" => $nome,
        "img" => $img,
        "descricao" => $descricao,
        "categoria" => $categoria,
        "peso" => $peso,
        "ref" => $ref,
        "destaque" => $destaque,
        "preco" => $preco,
        "estoque_quantidade" => $estoqueQuantidade,
        "tipo" => $tipo,
        "imagens" => $imagens,
    ];
}

function validarPayloadProdutoAdmin(array $produto): ?string
{
    if ($produto["nome"] === "" || $produto["img"] === "" || $produto["preco"] <= 0) {
        return "Preencha imagem, nome e preco valido.";
    }

    return null;
}

function buscarProdutoAdminPorId(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id LIMIT 1");
    $stmt->execute(["id" => $id]);
    $produto = $stmt->fetch();

    return is_array($produto) ? $produto : null;
}

if (!usuarioEhAdmin()) {
    responderJsonAdmin(["erro" => "Acesso negado."], 403);
}

if (!bancoDeDadosDisponivel($pdo)) {
    responderJsonAdmin(["erro" => "Banco de dados indisponivel."], 503);
}

try {
    garantirTabelaProdutos($pdo);
} catch (PDOException $exception) {
    responderJsonAdmin(["erro" => "Nao foi possivel preparar a tabela de produtos."], 500);
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    try {
        $stmt = $pdo->query("SELECT * FROM produtos ORDER BY id DESC");
        $produtos = array_map("formatarProdutoAdmin", $stmt->fetchAll());
        responderJsonAdmin(["produtos" => $produtos]);
    } catch (PDOException $exception) {
        responderJsonAdmin(["erro" => "Nao foi possivel listar os produtos."], 500);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dados = lerCorpoJsonAdmin();

    if ($dados === []) {
        responderJsonAdmin(["erro" => "Dados invalidos."], 422);
    }

    $produto = normalizarPayloadProdutoAdmin($dados);
    $erroValidacao = validarPayloadProdutoAdmin($produto);

    if ($erroValidacao !== null) {
        responderJsonAdmin(["erro" => $erroValidacao], 422);
    }

    try {
        $slug = gerarSlugUnicoProduto($pdo, $produto["nome"]);
        $stmt = $pdo->prepare(
            "INSERT INTO produtos (
                slug, nome, descricao, preco, categoria, peso, ref, destaque, img, imagens, estoque_quantidade, tipo
            ) VALUES (
                :slug, :nome, :descricao, :preco, :categoria, :peso, :ref, :destaque, :img, :imagens, :estoque_quantidade, :tipo
            )"
        );
        $stmt->execute([
            "slug" => $slug,
            "nome" => $produto["nome"],
            "descricao" => $produto["descricao"] !== "" ? $produto["descricao"] : null,
            "preco" => $produto["preco"],
            "categoria" => $produto["categoria"] !== "" ? $produto["categoria"] : null,
            "peso" => $produto["peso"] !== "" ? $produto["peso"] : null,
            "ref" => $produto["ref"] !== "" ? $produto["ref"] : null,
            "destaque" => $produto["destaque"] !== "" ? $produto["destaque"] : null,
            "img" => $produto["img"],
            "imagens" => json_encode($produto["imagens"], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            "estoque_quantidade" => $produto["estoque_quantidade"],
            "tipo" => $produto["tipo"] !== "" ? $produto["tipo"] : null,
        ]);

        $produtoId = (int) $pdo->lastInsertId();
        $produtoSalvo = buscarProdutoAdminPorId($pdo, $produtoId);

        responderJsonAdmin([
            "sucesso" => true,
            "produto" => formatarProdutoAdmin($produtoSalvo ?: []),
        ], 201);
    } catch (PDOException $exception) {
        responderJsonAdmin(["erro" => "Nao foi possivel salvar o produto."], 500);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $dados = lerCorpoJsonAdmin();

    if ($dados === []) {
        responderJsonAdmin(["erro" => "Dados invalidos."], 422);
    }

    $produto = normalizarPayloadProdutoAdmin($dados);
    $erroValidacao = validarPayloadProdutoAdmin($produto);

    if ($erroValidacao !== null) {
        responderJsonAdmin(["erro" => $erroValidacao], 422);
    }

    if ($produto["id"] <= 0) {
        responderJsonAdmin(["erro" => "Produto invalido."], 422);
    }

    try {
        $produtoAtual = buscarProdutoAdminPorId($pdo, $produto["id"]);

        if ($produtoAtual === null) {
            responderJsonAdmin(["erro" => "Produto nao encontrado."], 404);
        }

        $slugAtual = trim((string) ($produtoAtual["slug"] ?? ""));
        $slugDesejado = gerarSlugProduto($produto["nome"]);
        $slug = $slugAtual !== "" && $slugDesejado === gerarSlugProduto((string) ($produtoAtual["nome"] ?? ""))
            ? $slugAtual
            : gerarSlugUnicoProduto($pdo, $produto["nome"], $produto["id"]);

        $stmt = $pdo->prepare(
            "UPDATE produtos SET
                slug = :slug,
                nome = :nome,
                descricao = :descricao,
                preco = :preco,
                categoria = :categoria,
                peso = :peso,
                ref = :ref,
                destaque = :destaque,
                img = :img,
                imagens = :imagens,
                estoque_quantidade = :estoque_quantidade,
                tipo = :tipo
             WHERE id = :id
             LIMIT 1"
        );
        $stmt->execute([
            "id" => $produto["id"],
            "slug" => $slug,
            "nome" => $produto["nome"],
            "descricao" => $produto["descricao"] !== "" ? $produto["descricao"] : null,
            "preco" => $produto["preco"],
            "categoria" => $produto["categoria"] !== "" ? $produto["categoria"] : null,
            "peso" => $produto["peso"] !== "" ? $produto["peso"] : null,
            "ref" => $produto["ref"] !== "" ? $produto["ref"] : null,
            "destaque" => $produto["destaque"] !== "" ? $produto["destaque"] : null,
            "img" => $produto["img"],
            "imagens" => json_encode($produto["imagens"], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            "estoque_quantidade" => $produto["estoque_quantidade"],
            "tipo" => $produto["tipo"] !== "" ? $produto["tipo"] : null,
        ]);

        $produtoAtualizado = buscarProdutoAdminPorId($pdo, $produto["id"]);

        responderJsonAdmin([
            "sucesso" => true,
            "produto" => formatarProdutoAdmin($produtoAtualizado ?: []),
        ]);
    } catch (PDOException $exception) {
        responderJsonAdmin(["erro" => "Nao foi possivel atualizar o produto."], 500);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $dados = lerCorpoJsonAdmin();
    $id = (int) ($dados["id"] ?? 0);

    if ($id <= 0) {
        responderJsonAdmin(["erro" => "Produto invalido."], 422);
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = :id LIMIT 1");
        $stmt->execute(["id" => $id]);

        if ($stmt->rowCount() === 0) {
            responderJsonAdmin(["erro" => "Produto nao encontrado."], 404);
        }

        responderJsonAdmin(["sucesso" => true]);
    } catch (PDOException $exception) {
        responderJsonAdmin(["erro" => "Nao foi possivel remover o produto."], 500);
    }
}

responderJsonAdmin(["erro" => "Metodo nao permitido."], 405);
