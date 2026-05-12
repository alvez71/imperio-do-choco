<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/conexao.php";

<<<<<<< HEAD
if (!bancoDeDadosDisponivel($pdo) || !schemaProdutosDisponivel($pdo)) {
=======
if (!bancoDeDadosDisponivel($pdo)) {
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
    echo json_encode([], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
<<<<<<< HEAD
        $stmt = $pdo->query(
        "SELECT id, slug, nome, descricao, preco, categoria, tipo, peso, peso_gramas, ref, destaque, img, imagens, estoque_quantidade
         FROM produtos
         WHERE ativo = 1 AND deleted_at IS NULL
         ORDER BY id DESC"
    );
    $registros = $stmt->fetchAll();

    $produtos = array_map(static function (array $registro): array {
        $galeria = [];
        $galeriaBruta = $registro["imagens"] ?? "[]";

        if (is_string($galeriaBruta) && $galeriaBruta !== "") {
            $galeriaDecodificada = json_decode($galeriaBruta, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($galeriaDecodificada)) {
                $galeria = array_values(array_filter($galeriaDecodificada, "is_string"));
            }
        }

        $pesoGramas = array_key_exists("peso_gramas", $registro) && $registro["peso_gramas"] !== null
            ? (int) $registro["peso_gramas"]
            : null;

        return [
            "id" => (int) ($registro["id"] ?? 0),
            "slug" => (string) ($registro["slug"] ?? ""),
            "img" => (string) ($registro["img"] ?? ""),
            "nome" => (string) ($registro["nome"] ?? "Chocolate sem nome"),
            "preco" => (float) ($registro["preco"] ?? 0),
            "categoria" => (string) ($registro["categoria"] ?? $registro["tipo"] ?? "Chocolate"),
            "tipo" => (string) ($registro["tipo"] ?? $registro["categoria"] ?? "Chocolate"),
            "peso" => formatarPesoProduto((string) ($registro["peso"] ?? ""), $pesoGramas),
            "peso_gramas" => $pesoGramas,
            "ref" => (string) ($registro["ref"] ?? ""),
            "destaque" => (string) ($registro["destaque"] ?? "Selecao da casa"),
            "descricao" => (string) ($registro["descricao"] ?? ""),
            "imagens" => $galeria,
            "estoque_quantidade" => (int) ($registro["estoque_quantidade"] ?? 0),
        ];
    }, $registros);

    echo json_encode($produtos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (PDOException $exception) {
    echo json_encode([], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
=======
    garantirTabelaProdutos($pdo);
} catch (PDOException $exception) {
    echo json_encode([], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$tabelasPossiveis = ["produtos", "chocolates"];

foreach ($tabelasPossiveis as $tabela) {
    try {
        $stmt = $pdo->query("SELECT * FROM {$tabela} ORDER BY id DESC");
        $registros = $stmt->fetchAll();

        if (!$registros) {
            continue;
        }

        $produtos = array_map(static function (array $registro): array {
            $galeria = [];
            $galeriaBruta = $registro["imagens"] ?? $registro["galeria"] ?? "[]";

            if (is_string($galeriaBruta)) {
                $galeriaDecodificada = json_decode($galeriaBruta, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($galeriaDecodificada)) {
                    $galeria = array_values(array_filter($galeriaDecodificada, "is_string"));
                }
            } elseif (is_array($galeriaBruta)) {
                $galeria = array_values(array_filter($galeriaBruta, "is_string"));
            }

            return [
                "id" => $registro["id"] ?? null,
                "slug" => $registro["slug"] ?? null,
                "img" => $registro["img"] ?? $registro["imagem"] ?? $registro["imagem_url"] ?? "",
                "nome" => $registro["nome"] ?? "Chocolate sem nome",
                "preco" => (float) ($registro["preco"] ?? 0),
                "categoria" => $registro["categoria"] ?? $registro["tipo"] ?? "Chocolate",
                "peso" => $registro["peso"] ?? "",
                "ref" => $registro["ref"] ?? $registro["referencia"] ?? "",
                "destaque" => $registro["destaque"] ?? "Selecao da casa",
                "descricao" => $registro["descricao"] ?? "",
                "imagens" => $galeria,
            ];
        }, $registros);

        echo json_encode($produtos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    } catch (PDOException $exception) {
        continue;
    }
}

echo json_encode([], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
>>>>>>> 42de13b18067624c8c82cf4681fed6951fc785dd
