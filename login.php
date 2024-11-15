<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

$servername = "localhost";
$username = "rhrecr47_jeffadmin";
$password = "91085467Jl";
$dbname = "rhrecr47_users";

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro ao conectar ao banco de dados: " . $conn->connect_error]);
    exit();
}

// Receber os dados do JSON enviado via POST
$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->username) || !isset($data->password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Dados inválidos."]);
    exit();
}

$username = $data->username;
$password = $data->password;

// Consultar usuário no banco de dados
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            echo json_encode(["success" => true, "permissions" => explode(",", $user['role'])]);
        } else {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Senha incorreta."]);
        }
    } else {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Usuário não encontrado."]);
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro ao preparar consulta: " . $conn->error]);
}

$conn->close();
?>
