<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/conexao.php";

if (!bancoDeDadosDisponivel($pdo)) {
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
                "categoria" => $registro["categoria"] ?? "Chocolate",
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
