<?php
declare(strict_types=1);

<<<<<<< HEAD
carregarVariaveisAmbiente(__DIR__ . "/.env");

$databaseConfig = obterConfiguracaoBanco();
$host = $databaseConfig["host"];
$port = $databaseConfig["port"];
$dbname = $databaseConfig["dbname"];
$user = $databaseConfig["user"];
$pass = $databaseConfig["pass"];
$appEnv = strtolower(lerVariavelAmbiente("APP_ENV", "development") ?? "development");
$pdo = null;
$databaseConnectionError = "";

if (in_array($appEnv, ["prod", "production"], true) && ($user === "root" || $pass === "")) {
    $databaseConnectionError = "Configuracao de banco insegura para producao.";
} else {
    try {
        $pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $exception) {
        $databaseConnectionError = "Erro na conexao com o banco de dados.";
    }
}

function carregarVariaveisAmbiente(string $arquivo): void
{
    static $arquivosCarregados = [];

    if (isset($arquivosCarregados[$arquivo]) || !is_file($arquivo)) {
        return;
    }

    $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($linhas === false) {
        return;
    }

    foreach ($linhas as $linha) {
        $linha = trim($linha);

        if ($linha === "" || str_starts_with($linha, "#") || !str_contains($linha, "=")) {
            continue;
        }

        [$chave, $valor] = explode("=", $linha, 2);
        $chave = trim($chave);
        $valor = trim($valor);

        if ($chave === "") {
            continue;
        }

        if (
            (str_starts_with($valor, "\"") && str_ends_with($valor, "\"")) ||
            (str_starts_with($valor, "'") && str_ends_with($valor, "'"))
        ) {
            $valor = substr($valor, 1, -1);
        }

        if (getenv($chave) === false) {
            putenv("{$chave}={$valor}");
        }

        $_ENV[$chave] = $valor;
        $_SERVER[$chave] = $valor;
    }

    $arquivosCarregados[$arquivo] = true;
}

function lerVariavelAmbiente(string $nome, ?string $padrao = null): ?string
{
    $valor = $_ENV[$nome] ?? $_SERVER[$nome] ?? getenv($nome);

    if ($valor === false || $valor === null || $valor === "") {
        return $padrao;
    }

    return (string) $valor;
}

function obterConfiguracaoBanco(): array
{
    return [
        "host" => lerVariavelAmbiente("DB_HOST", "127.0.0.1"),
        "port" => lerVariavelAmbiente("DB_PORT", "3306"),
        "dbname" => lerVariavelAmbiente("DB_NAME", "imperio_do_choco"),
        "user" => lerVariavelAmbiente("DB_USER", "root"),
        "pass" => lerVariavelAmbiente("DB_PASS", ""),
    ];
=======
$host = "127.0.0.1";
$dbname = "imperio_do_choco";
$user = "root";
$pass = "";
$pdo = null;
$databaseConnectionError = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $exception) {
    $databaseConnectionError = "Erro na conexao com o banco de dados.";
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
}

function bancoDeDadosDisponivel($pdo): bool
{
    return $pdo instanceof PDO;
}

<<<<<<< HEAD
function listarTabelasExistentes(PDO $pdo): array
{
    static $cache = [];

    $cacheKey = spl_object_hash($pdo);

    if (isset($cache[$cacheKey])) {
        return $cache[$cacheKey];
    }

    $stmt = $pdo->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()");
    $tabelas = array_map(
        static fn (array $linha): string => (string) ($linha["TABLE_NAME"] ?? ""),
        $stmt->fetchAll()
    );

    $cache[$cacheKey] = array_values(array_filter($tabelas));

    return $cache[$cacheKey];
}

function listarTabelasAusentes(PDO $pdo, array $tabelas): array
{
    $existentes = array_flip(listarTabelasExistentes($pdo));

    return array_values(array_filter(
        array_map(static fn ($tabela): string => trim((string) $tabela), $tabelas),
        static fn (string $tabela): bool => $tabela !== "" && !isset($existentes[$tabela])
    ));
}

function tabelasBancoDisponiveis(PDO $pdo, array $tabelas): bool
{
    return listarTabelasAusentes($pdo, $tabelas) === [];
}

function schemaUsuariosDisponivel(PDO $pdo): bool
{
    return tabelasBancoDisponiveis($pdo, ["usuarios"]);
}

function schemaProdutosDisponivel(PDO $pdo): bool
{
    return tabelasBancoDisponiveis($pdo, ["produtos"]);
}

function schemaCarrinhoDisponivel(PDO $pdo): bool
{
    return tabelasBancoDisponiveis($pdo, ["usuarios", "produtos", "carrinho_itens"]);
}

function schemaComercialDisponivel(PDO $pdo): bool
{
    return tabelasBancoDisponiveis(
        $pdo,
        ["usuarios", "produtos", "enderecos", "pedidos", "pedido_itens", "estoque_movimentacoes"]
    );
}

=======
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
function gerarSlugProduto(string $texto): string
{
    $texto = trim(mb_strtolower($texto, "UTF-8"));
    $texto = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $texto) ?: $texto;
    $texto = preg_replace("/[^a-z0-9]+/", "-", $texto) ?? "";
<<<<<<< HEAD
    $texto = trim($texto, "-");

    return substr($texto, 0, 190);
}

function normalizarReferenciaProduto(?string $referencia): ?string
{
    $referencia = trim((string) $referencia);

    if ($referencia === "") {
        return null;
    }

    $referencia = mb_strtoupper($referencia, "UTF-8");
    $referencia = preg_replace("/\s+/", "-", $referencia) ?? $referencia;

    return mb_substr($referencia, 0, 40, "UTF-8");
}

function gerarReferenciaProduto(): string
{
    return "PRD-" . date("YmdHis") . "-" . strtoupper(bin2hex(random_bytes(2)));
}

function extrairPesoGramas(?string $peso): ?int
{
    $peso = trim((string) $peso);

    if ($peso === "") {
        return null;
    }

    if (!preg_match("/(\d+(?:[.,]\d+)?)\s*(kg|kgs|quilo|quilos|g|gr|grama|gramas)\b/iu", $peso, $matches)) {
        return null;
    }

    $valor = (float) str_replace(",", ".", (string) ($matches[1] ?? "0"));
    $unidade = mb_strtolower((string) ($matches[2] ?? ""), "UTF-8");

    if ($valor <= 0) {
        return null;
    }

    if (in_array($unidade, ["kg", "kgs", "quilo", "quilos"], true)) {
        return (int) round($valor * 1000);
    }

    return (int) round($valor);
}

function formatarPesoProduto(?string $peso, ?int $pesoGramas): string
{
    $peso = trim((string) $peso);

    if ($peso !== "") {
        return $peso;
    }

    if ($pesoGramas === null || $pesoGramas <= 0) {
        return "";
    }

    if ($pesoGramas >= 1000 && $pesoGramas % 1000 === 0) {
        return (string) ($pesoGramas / 1000) . "kg";
    }

    return (string) $pesoGramas . "g";
}

function excecaoEntradaDuplicada(PDOException $exception): bool
{
    return (int) ($exception->errorInfo[1] ?? 0) === 1062;
}

function obterChaveDuplicada(PDOException $exception): string
{
    $mensagem = $exception->getMessage();

    if (!preg_match("/for key '([^']+)'/i", $mensagem, $matches)) {
        return "";
    }

    return (string) ($matches[1] ?? "");
=======
    return trim($texto, "-");
}

function garantirTabelaProdutos(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS produtos (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            slug VARCHAR(190) DEFAULT NULL,
            nome VARCHAR(120) NOT NULL,
            descricao TEXT DEFAULT NULL,
            preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            categoria VARCHAR(80) DEFAULT NULL,
            peso VARCHAR(40) DEFAULT NULL,
            ref VARCHAR(40) DEFAULT NULL,
            destaque VARCHAR(80) DEFAULT NULL,
            img TEXT DEFAULT NULL,
            imagens JSON DEFAULT NULL,
            estoque_quantidade INT NOT NULL DEFAULT 0,
            tipo VARCHAR(50) DEFAULT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $colunasObrigatorias = [
        "slug" => "ALTER TABLE produtos ADD COLUMN slug VARCHAR(190) DEFAULT NULL AFTER id",
        "categoria" => "ALTER TABLE produtos ADD COLUMN categoria VARCHAR(80) DEFAULT NULL AFTER preco",
        "peso" => "ALTER TABLE produtos ADD COLUMN peso VARCHAR(40) DEFAULT NULL AFTER categoria",
        "ref" => "ALTER TABLE produtos ADD COLUMN ref VARCHAR(40) DEFAULT NULL AFTER peso",
        "destaque" => "ALTER TABLE produtos ADD COLUMN destaque VARCHAR(80) DEFAULT NULL AFTER ref",
        "img" => "ALTER TABLE produtos ADD COLUMN img TEXT DEFAULT NULL AFTER destaque",
        "imagens" => "ALTER TABLE produtos ADD COLUMN imagens JSON DEFAULT NULL AFTER img",
        "estoque_quantidade" => "ALTER TABLE produtos ADD COLUMN estoque_quantidade INT NOT NULL DEFAULT 0 AFTER preco",
        "tipo" => "ALTER TABLE produtos ADD COLUMN tipo VARCHAR(50) DEFAULT NULL AFTER estoque_quantidade",
    ];

    $stmt = $pdo->query("SHOW COLUMNS FROM produtos");
    $colunasExistentes = array_map(
        static fn (array $coluna): string => (string) ($coluna["Field"] ?? ""),
        $stmt->fetchAll()
    );

    foreach ($colunasObrigatorias as $coluna => $sql) {
        if (!in_array($coluna, $colunasExistentes, true)) {
            $pdo->exec($sql);
        }
    }

    $produtos = $pdo->query("SELECT id, nome, slug FROM produtos ORDER BY id ASC")->fetchAll();
    $slugsUsados = [];
    $updateSlug = $pdo->prepare("UPDATE produtos SET slug = :slug WHERE id = :id");

    foreach ($produtos as $produto) {
        $slugAtual = trim((string) ($produto["slug"] ?? ""));

        if ($slugAtual !== "" && !isset($slugsUsados[$slugAtual])) {
            $slugsUsados[$slugAtual] = true;
            continue;
        }

        $slugBase = gerarSlugProduto((string) ($produto["nome"] ?? "")) ?: "produto";
        $slug = $slugBase;
        $contador = 2;

        while (isset($slugsUsados[$slug])) {
            $slug = "{$slugBase}-{$contador}";
            $contador += 1;
        }

        $updateSlug->execute([
            "slug" => $slug,
            "id" => (int) $produto["id"],
        ]);

        $slugsUsados[$slug] = true;
    }

    $indices = $pdo->query("SHOW INDEX FROM produtos")->fetchAll();
    $indiceSlugExiste = false;

    foreach ($indices as $indice) {
        if (($indice["Key_name"] ?? "") === "produtos_slug_unique") {
            $indiceSlugExiste = true;
            break;
        }
    }

    if (!$indiceSlugExiste) {
        $pdo->exec("CREATE UNIQUE INDEX produtos_slug_unique ON produtos (slug)");
    }
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
}
