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
