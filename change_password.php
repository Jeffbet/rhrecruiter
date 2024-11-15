<?php
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

if (!$data || !isset($data->username) || !isset($data->newPassword)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Dados inválidos."]);
    exit();
}

$usernameToChange = $data->username;
$newPassword = $data->newPassword;

// Criar um hash da nova senha
$newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

// Atualizar a senha do usuário no banco de dados
$sql = "UPDATE users SET password_hash = ? WHERE username = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ss", $newPasswordHash, $usernameToChange);
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erro ao atualizar senha: " . $stmt->error]);
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro ao preparar consulta: " . $conn->error]);
}

$conn->close();
?>
