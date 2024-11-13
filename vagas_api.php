<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "rhrecr47_jeffadmin";
$password = "91085467Jl";
$dbname = "rhrecr47_vagascandidaturas";

// Cria a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Falha na conexão: " . $conn->connect_error]);
    exit();
}

// Define qual método HTTP está sendo utilizado
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Recebe dados via POST e insere no banco de dados
        $data = json_decode(file_get_contents("php://input"), true);
        $titulo = $data['jobTitle'] ?? '';
        $estado = $data['state'] ?? '';
        $modalidade = $data['workMode'] ?? '';
        $senioridade = $data['seniority'] ?? '';
        $tipo_contrato = $data['contractType'] ?? '';
        $salario = $data['salary'] ?? '';
        $beneficios = $data['benefits'] ?? '';
        $categoria = $data['category'] ?? '';
        $descricao = $data['description'] ?? '';
        $pcd = $data['pcd'] ?? '';
        $requisitos = $data['requirements'] ?? '';
    
        if (empty($titulo) || empty($estado) || empty($modalidade) || empty($senioridade) || empty($tipo_contrato)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados insuficientes para cadastrar a vaga"]);
            exit();
        }
    
        $stmt = $conn->prepare("INSERT INTO vagas (titulo, estado, modalidade, senioridade, tipo_contrato, salario, beneficios, categoria, descricao, pcd, requisitos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $titulo, $estado, $modalidade, $senioridade, $tipo_contrato, $salario, $beneficios, $categoria, $descricao, $pcd, $requisitos);
    
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Vaga cadastrada com sucesso!"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao cadastrar vaga: " . $stmt->error]);
        }
        $stmt->close();
        break;
    

    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT * FROM vagas WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Vaga não encontrada"]);
            }
            $stmt->close();
        } else {
            // Retorna todas as vagas do banco de dados
            $sql = "SELECT * FROM vagas";
            $result = $conn->query($sql);

            $vagas = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $vagas[] = $row;
                }
            }
            echo json_encode($vagas);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id'] ?? 0);
        $titulo = $data['jobTitle'] ?? '';
        $estado = $data['state'] ?? '';
        $modalidade = $data['workMode'] ?? '';
        $senioridade = $data['seniority'] ?? '';
        $tipo_contrato = $data['contractType'] ?? '';
        $salario = $data['salary'] ?? '';
        $beneficios = $data['benefits'] ?? '';
        $categoria = $data['category'] ?? '';
        $descricao = $data['description'] ?? '';
        $requisitos = $data['requirements'] ?? '';
        $pcd = $data['pcd'] ?? '';

        if ($id === 0 || empty($titulo) || empty($estado) || empty($modalidade) || empty($senioridade) || empty($tipo_contrato)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados insuficientes para atualizar a vaga"]);
            exit();
        }

        $stmt = $conn->prepare("UPDATE vagas SET titulo=?, estado=?, modalidade=?, senioridade=?, tipo_contrato=?, salario=?, beneficios=?, categoria=?, descricao=?, requisitos=?, pcd=? WHERE id=?");
        $stmt->bind_param("sssssssssssi", $titulo, $estado, $modalidade, $senioridade, $tipo_contrato, $salario, $beneficios, $categoria, $descricao, $requisitos, $pcd, $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Vaga atualizada com sucesso!"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar vaga: " . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id'] ?? 0);

        if ($id === 0) {
            http_response_code(400);
            echo json_encode(["error" => "ID inválido para exclusão"]);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM vagas WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Vaga deletada com sucesso!"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao deletar vaga: " . $stmt->error]);
        }
        $stmt->close();
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método não permitido"]);
        break;
}

$conn->close();
?>
