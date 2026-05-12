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

<<<<<<< HEAD
    while (slugProdutoExiste($pdo, $slug, $ignorarId)) {
        $slug = "{$slugBase}-{$contador}";
        $contador += 1;
    }

    return $slug;
}

function slugProdutoExiste(PDO $pdo, string $slug, ?int $ignorarId = null): bool
{
    $sql = "SELECT id FROM produtos WHERE slug = :slug";

    if ($ignorarId !== null) {
        $sql .= " AND id <> :id";
    }

    $sql .= " LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $params = ["slug" => $slug];

    if ($ignorarId !== null) {
        $params["id"] = $ignorarId;
    }

    $stmt->execute($params);

    return (bool) $stmt->fetch();
}

function referenciaProdutoExiste(PDO $pdo, string $referencia, ?int $ignorarId = null): bool
{
    $sql = "SELECT id FROM produtos WHERE ref = :ref";

    if ($ignorarId !== null) {
        $sql .= " AND id <> :id";
    }

    $sql .= " LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $params = ["ref" => $referencia];

    if ($ignorarId !== null) {
        $params["id"] = $ignorarId;
    }

    $stmt->execute($params);

    return (bool) $stmt->fetch();
}

function gerarReferenciaUnicaProduto(PDO $pdo, ?string $referenciaBase = null): string
{
    $referencia = normalizarReferenciaProduto($referenciaBase) ?? gerarReferenciaProduto();

    while (referenciaProdutoExiste($pdo, $referencia)) {
        $referencia = gerarReferenciaProduto();
    }

    return $referencia;
}

function resolverReferenciaProduto(
    PDO $pdo,
    ?string $referenciaInformada,
    ?string $referenciaAtual = null,
    ?int $ignorarId = null
): string {
    $referenciaInformada = normalizarReferenciaProduto($referenciaInformada);
    $referenciaAtual = normalizarReferenciaProduto($referenciaAtual);

    if ($referenciaInformada !== null) {
        if (referenciaProdutoExiste($pdo, $referenciaInformada, $ignorarId)) {
            responderJsonAdmin(["erro" => "Ja existe um produto com essa referencia."], 409);
        }

        return $referenciaInformada;
    }

    if ($referenciaAtual !== null && !referenciaProdutoExiste($pdo, $referenciaAtual, $ignorarId)) {
        return $referenciaAtual;
    }

    return gerarReferenciaUnicaProduto($pdo);
=======
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
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
}

function formatarProdutoAdmin(array $produto): array
{
    $galeria = [];
    $imagensBrutas = $produto["imagens"] ?? "[]";

    if (is_string($imagensBrutas) && $imagensBrutas !== "") {
        $imagensDecodificadas = json_decode($imagensBrutas, true);
<<<<<<< HEAD

=======
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
        if (json_last_error() === JSON_ERROR_NONE && is_array($imagensDecodificadas)) {
            $galeria = normalizarListaImagens($imagensDecodificadas);
        }
    }

    if ($galeria === [] && !empty($produto["img"])) {
        $galeria = [trim((string) $produto["img"])];
    }

<<<<<<< HEAD
    $pesoGramas = array_key_exists("peso_gramas", $produto) && $produto["peso_gramas"] !== null
        ? (int) $produto["peso_gramas"]
        : null;

=======
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
    return [
        "id" => (int) ($produto["id"] ?? 0),
        "slug" => (string) ($produto["slug"] ?? ""),
        "img" => (string) ($produto["img"] ?? ""),
        "nome" => (string) ($produto["nome"] ?? ""),
        "preco" => (float) ($produto["preco"] ?? 0),
<<<<<<< HEAD
        "categoria" => (string) ($produto["categoria"] ?? $produto["tipo"] ?? "Chocolate"),
        "peso" => formatarPesoProduto((string) ($produto["peso"] ?? ""), $pesoGramas),
        "peso_gramas" => $pesoGramas,
=======
        "categoria" => (string) ($produto["categoria"] ?? $produto["tipo"] ?? ""),
        "peso" => (string) ($produto["peso"] ?? ""),
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
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
<<<<<<< HEAD
    $ref = normalizarReferenciaProduto((string) ($dados["ref"] ?? ""));
=======
    $ref = trim((string) ($dados["ref"] ?? ""));
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
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
<<<<<<< HEAD
        "categoria" => $categoria !== "" ? $categoria : "Chocolate",
        "peso" => $peso,
        "peso_gramas" => extrairPesoGramas($peso),
=======
        "categoria" => $categoria,
        "peso" => $peso,
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
        "ref" => $ref,
        "destaque" => $destaque,
        "preco" => $preco,
        "estoque_quantidade" => $estoqueQuantidade,
<<<<<<< HEAD
        "tipo" => $tipo !== "" ? $tipo : ($categoria !== "" ? $categoria : "Chocolate"),
=======
        "tipo" => $tipo,
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
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
<<<<<<< HEAD
    $stmt = $pdo->prepare(
        "SELECT id, slug, nome, descricao, preco, categoria, tipo, peso, peso_gramas, ref, destaque, img, imagens, estoque_quantidade, ativo, deleted_at
         FROM produtos
         WHERE id = :id
         LIMIT 1"
    );
=======
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id LIMIT 1");
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
    $stmt->execute(["id" => $id]);
    $produto = $stmt->fetch();

    return is_array($produto) ? $produto : null;
}

<<<<<<< HEAD
function registrarMovimentacaoEstoque(
    PDO $pdo,
    int $produtoId,
    string $tipo,
    int $quantidade,
    ?int $estoqueAntes,
    ?int $estoqueDepois,
    string $origem,
    ?string $observacao,
    ?int $usuarioId
): void {
    if (!tabelasBancoDisponiveis($pdo, ["estoque_movimentacoes"])) {
        return;
    }

    if ($quantidade === 0) {
        return;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO estoque_movimentacoes (
            produto_id, usuario_id, tipo, origem, quantidade, estoque_antes, estoque_depois, observacao
        ) VALUES (
            :produto_id, :usuario_id, :tipo, :origem, :quantidade, :estoque_antes, :estoque_depois, :observacao
        )"
    );
    $stmt->execute([
        "produto_id" => $produtoId,
        "usuario_id" => $usuarioId,
        "tipo" => $tipo,
        "origem" => $origem,
        "quantidade" => $quantidade,
        "estoque_antes" => $estoqueAntes,
        "estoque_depois" => $estoqueDepois,
        "observacao" => $observacao,
    ]);
}

function responderErroDePersistencia(PDOException $exception, string $mensagemPadrao): void
{
    if (excecaoEntradaDuplicada($exception)) {
        $chaveDuplicada = obterChaveDuplicada($exception);

        if ($chaveDuplicada === "produtos_ref_unique") {
            responderJsonAdmin(["erro" => "Ja existe um produto com essa referencia."], 409);
        }

        if ($chaveDuplicada === "produtos_slug_unique") {
            responderJsonAdmin(["erro" => "Ja existe um produto com esse identificador de URL."], 409);
        }
    }

    responderJsonAdmin(["erro" => $mensagemPadrao], 500);
}

=======
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
if (!usuarioEhAdmin()) {
    responderJsonAdmin(["erro" => "Acesso negado."], 403);
}

if (!bancoDeDadosDisponivel($pdo)) {
    responderJsonAdmin(["erro" => "Banco de dados indisponivel."], 503);
}

<<<<<<< HEAD
if (!schemaProdutosDisponivel($pdo)) {
    responderJsonAdmin(["erro" => "Schema de produtos nao aplicado. Execute database/migrate.php antes de usar o painel."], 503);
=======
try {
    garantirTabelaProdutos($pdo);
} catch (PDOException $exception) {
    responderJsonAdmin(["erro" => "Nao foi possivel preparar a tabela de produtos."], 500);
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    try {
<<<<<<< HEAD
        $stmt = $pdo->query(
            "SELECT id, slug, nome, descricao, preco, categoria, tipo, peso, peso_gramas, ref, destaque, img, imagens, estoque_quantidade, ativo, deleted_at
             FROM produtos
             WHERE ativo = 1 AND deleted_at IS NULL
             ORDER BY id DESC"
        );
=======
        $stmt = $pdo->query("SELECT * FROM produtos ORDER BY id DESC");
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
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
<<<<<<< HEAD
        $referencia = resolverReferenciaProduto($pdo, $produto["ref"]);

        $stmt = $pdo->prepare(
            "INSERT INTO produtos (
                slug, nome, descricao, preco, categoria, tipo, peso, peso_gramas, ref, destaque, img, imagens, estoque_quantidade, ativo, criado_por_usuario_id, atualizado_por_usuario_id
            ) VALUES (
                :slug, :nome, :descricao, :preco, :categoria, :tipo, :peso, :peso_gramas, :ref, :destaque, :img, :imagens, :estoque_quantidade, 1, :criado_por_usuario_id, :atualizado_por_usuario_id
=======
        $stmt = $pdo->prepare(
            "INSERT INTO produtos (
                slug, nome, descricao, preco, categoria, peso, ref, destaque, img, imagens, estoque_quantidade, tipo
            ) VALUES (
                :slug, :nome, :descricao, :preco, :categoria, :peso, :ref, :destaque, :img, :imagens, :estoque_quantidade, :tipo
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
            )"
        );
        $stmt->execute([
            "slug" => $slug,
            "nome" => $produto["nome"],
            "descricao" => $produto["descricao"] !== "" ? $produto["descricao"] : null,
            "preco" => $produto["preco"],
<<<<<<< HEAD
            "categoria" => $produto["categoria"],
            "tipo" => $produto["tipo"],
            "peso" => $produto["peso"] !== "" ? $produto["peso"] : null,
            "peso_gramas" => $produto["peso_gramas"],
            "ref" => $referencia,
=======
            "categoria" => $produto["categoria"] !== "" ? $produto["categoria"] : null,
            "peso" => $produto["peso"] !== "" ? $produto["peso"] : null,
            "ref" => $produto["ref"] !== "" ? $produto["ref"] : null,
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
            "destaque" => $produto["destaque"] !== "" ? $produto["destaque"] : null,
            "img" => $produto["img"],
            "imagens" => json_encode($produto["imagens"], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            "estoque_quantidade" => $produto["estoque_quantidade"],
<<<<<<< HEAD
            "criado_por_usuario_id" => (int) ($_SESSION["usuario_id"] ?? 0) ?: null,
            "atualizado_por_usuario_id" => (int) ($_SESSION["usuario_id"] ?? 0) ?: null,
        ]);

        $produtoId = (int) $pdo->lastInsertId();
        if ($produto["estoque_quantidade"] > 0) {
            registrarMovimentacaoEstoque(
                $pdo,
                $produtoId,
                "entrada",
                (int) $produto["estoque_quantidade"],
                0,
                (int) $produto["estoque_quantidade"],
                "painel_admin",
                "Estoque inicial do cadastro do produto.",
                (int) ($_SESSION["usuario_id"] ?? 0) ?: null
            );
        }
=======
            "tipo" => $produto["tipo"] !== "" ? $produto["tipo"] : null,
        ]);

        $produtoId = (int) $pdo->lastInsertId();
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
        $produtoSalvo = buscarProdutoAdminPorId($pdo, $produtoId);

        responderJsonAdmin([
            "sucesso" => true,
            "produto" => formatarProdutoAdmin($produtoSalvo ?: []),
        ], 201);
    } catch (PDOException $exception) {
<<<<<<< HEAD
        responderErroDePersistencia($exception, "Nao foi possivel salvar o produto.");
=======
        responderJsonAdmin(["erro" => "Nao foi possivel salvar o produto."], 500);
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
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

<<<<<<< HEAD
        $estoqueAnterior = (int) ($produtoAtual["estoque_quantidade"] ?? 0);

=======
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
        $slugAtual = trim((string) ($produtoAtual["slug"] ?? ""));
        $slugDesejado = gerarSlugProduto($produto["nome"]);
        $slug = $slugAtual !== "" && $slugDesejado === gerarSlugProduto((string) ($produtoAtual["nome"] ?? ""))
            ? $slugAtual
            : gerarSlugUnicoProduto($pdo, $produto["nome"], $produto["id"]);

<<<<<<< HEAD
        $referencia = resolverReferenciaProduto(
            $pdo,
            $produto["ref"],
            (string) ($produtoAtual["ref"] ?? ""),
            $produto["id"]
        );

=======
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
        $stmt = $pdo->prepare(
            "UPDATE produtos SET
                slug = :slug,
                nome = :nome,
                descricao = :descricao,
                preco = :preco,
                categoria = :categoria,
<<<<<<< HEAD
                tipo = :tipo,
                peso = :peso,
                peso_gramas = :peso_gramas,
=======
                peso = :peso,
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
                ref = :ref,
                destaque = :destaque,
                img = :img,
                imagens = :imagens,
                estoque_quantidade = :estoque_quantidade,
<<<<<<< HEAD
                atualizado_por_usuario_id = :atualizado_por_usuario_id
=======
                tipo = :tipo
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
             WHERE id = :id
             LIMIT 1"
        );
        $stmt->execute([
            "id" => $produto["id"],
            "slug" => $slug,
            "nome" => $produto["nome"],
            "descricao" => $produto["descricao"] !== "" ? $produto["descricao"] : null,
            "preco" => $produto["preco"],
<<<<<<< HEAD
            "categoria" => $produto["categoria"],
            "tipo" => $produto["tipo"],
            "peso" => $produto["peso"] !== "" ? $produto["peso"] : null,
            "peso_gramas" => $produto["peso_gramas"],
            "ref" => $referencia,
=======
            "categoria" => $produto["categoria"] !== "" ? $produto["categoria"] : null,
            "peso" => $produto["peso"] !== "" ? $produto["peso"] : null,
            "ref" => $produto["ref"] !== "" ? $produto["ref"] : null,
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
            "destaque" => $produto["destaque"] !== "" ? $produto["destaque"] : null,
            "img" => $produto["img"],
            "imagens" => json_encode($produto["imagens"], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            "estoque_quantidade" => $produto["estoque_quantidade"],
<<<<<<< HEAD
            "atualizado_por_usuario_id" => (int) ($_SESSION["usuario_id"] ?? 0) ?: null,
        ]);

        $diferencaEstoque = (int) $produto["estoque_quantidade"] - $estoqueAnterior;
        if ($diferencaEstoque !== 0) {
            registrarMovimentacaoEstoque(
                $pdo,
                (int) $produto["id"],
                "ajuste",
                $diferencaEstoque,
                $estoqueAnterior,
                (int) $produto["estoque_quantidade"],
                "painel_admin",
                "Ajuste manual de estoque pelo painel.",
                (int) ($_SESSION["usuario_id"] ?? 0) ?: null
            );
        }

=======
            "tipo" => $produto["tipo"] !== "" ? $produto["tipo"] : null,
        ]);

>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
        $produtoAtualizado = buscarProdutoAdminPorId($pdo, $produto["id"]);

        responderJsonAdmin([
            "sucesso" => true,
            "produto" => formatarProdutoAdmin($produtoAtualizado ?: []),
        ]);
    } catch (PDOException $exception) {
<<<<<<< HEAD
        responderErroDePersistencia($exception, "Nao foi possivel atualizar o produto.");
=======
        responderJsonAdmin(["erro" => "Nao foi possivel atualizar o produto."], 500);
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
    }
}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $dados = lerCorpoJsonAdmin();
    $id = (int) ($dados["id"] ?? 0);

    if ($id <= 0) {
        responderJsonAdmin(["erro" => "Produto invalido."], 422);
    }

    try {
<<<<<<< HEAD
        $produtoAtual = buscarProdutoAdminPorId($pdo, $id);

        if ($produtoAtual === null || (int) ($produtoAtual["ativo"] ?? 1) !== 1) {
            responderJsonAdmin(["erro" => "Produto nao encontrado."], 404);
        }

        $stmt = $pdo->prepare(
            "UPDATE produtos
             SET ativo = 0,
                 deleted_at = CURRENT_TIMESTAMP,
                 atualizado_por_usuario_id = :atualizado_por_usuario_id
             WHERE id = :id
               AND ativo = 1
             LIMIT 1"
        );
        $stmt->execute([
            "id" => $id,
            "atualizado_por_usuario_id" => (int) ($_SESSION["usuario_id"] ?? 0) ?: null,
        ]);
=======
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = :id LIMIT 1");
        $stmt->execute(["id" => $id]);
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd

        if ($stmt->rowCount() === 0) {
            responderJsonAdmin(["erro" => "Produto nao encontrado."], 404);
        }

        responderJsonAdmin(["sucesso" => true]);
    } catch (PDOException $exception) {
        responderJsonAdmin(["erro" => "Nao foi possivel remover o produto."], 500);
    }
}

responderJsonAdmin(["erro" => "Metodo nao permitido."], 405);
