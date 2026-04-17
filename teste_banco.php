<?php
declare(strict_types=1);

require_once __DIR__ . "/conexao.php";

header("Content-Type: text/plain; charset=UTF-8");

if (!bancoDeDadosDisponivel($pdo)) {
    echo "ERRO: nao foi possivel conectar ao banco.\n";
    echo $databaseConnectionError !== "" ? $databaseConnectionError . "\n" : "";
    exit(1);
}

echo "OK: conexao com o banco realizada com sucesso.\n";

try {
    $stmt = $pdo->query("SELECT DATABASE() AS banco_atual");
    $resultado = $stmt->fetch();
    echo "Banco atual: " . ($resultado["banco_atual"] ?? "desconhecido") . "\n";
} catch (PDOException $exception) {
    echo "Conectou, mas nao foi possivel consultar o banco atual.\n";
}
