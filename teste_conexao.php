<?php
// Mostrar todos os erros para ajudar no diagnóstico
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Dados para a conexão ao banco de dados
$servername = "162.241.61.229"; // IP do servidor de hospedagem ao invés de "localhost"
$username = "rhrecr47_jeffadmin";
$password = "91085467Jl";
$dbname = "rhrecr47_vagas";

// Tentar conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar se há erro de conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
} else {
    echo "Conexão ao banco de dados estabelecida com sucesso!";
}

// Fechar a conexão
$conn->close();
?>
