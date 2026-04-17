<?php
declare(strict_types=1);

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
}

function bancoDeDadosDisponivel($pdo): bool
{
    return $pdo instanceof PDO;
}

function gerarSlugProduto(string $texto): string
{
    $texto = trim(mb_strtolower($texto, "UTF-8"));
    $texto = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $texto) ?: $texto;
    $texto = preg_replace("/[^a-z0-9]+/", "-", $texto) ?? "";
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
}
