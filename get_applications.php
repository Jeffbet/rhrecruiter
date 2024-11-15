<?php
// get_applications.php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Acesso negado. Faça login primeiro.");
}

$servername = "localhost";
$username = "rhrecr47_jeffadmin";
$password = "91085467Jl";
$dbname = "rhrecr47_vagas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Verificar permissões
$conn_perm = new mysqli($servername, $username, $password, "rhrecr47_users");
$stmt_perm = $conn_perm->prepare("SELECT permission FROM permissions WHERE user_id = ?");
$stmt_perm->bind_param("i", $user_id);
$stmt_perm->execute();
$result = $stmt_perm->get_result();

$permissions = [];
while ($row = $result->fetch_assoc()) {
    $permissions[] = $row['permission'];
}

if (in_array('view_applications', $permissions)) {
    // Se o usuário tiver permissão para ver as candidaturas
    $result = $conn->query("SELECT * FROM vagas_candidaturas");

    if ($result->num_rows > 0) {
        $applications = [];
        while ($row = $result->fetch_assoc()) {
            $applications[] = $row;
        }
        echo json_encode($applications);
    } else {
        echo "Nenhuma candidatura encontrada.";
    }
} else {
    echo "Você não tem permissão para visualizar as candidaturas.";
}

$conn->close();
$conn_perm->close();
?>
