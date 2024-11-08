<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Dados para a conexão ao banco de dados
$servername = "localhost";
$username = "rhrecr47_jeffadmin";
$password = "91085467Jl";
$dbname = "rhrecr47_vagas";

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar se há erro de conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dados recebidos do formulário
    $vaga = $_POST['vaga'];
    $nome = $_POST['name'];
    $whatsapp = $_POST['whatsapp'];
    $email = $_POST['email'];
    $linkedin = $_POST['linkedin'];
    $curriculo = $_FILES['resume'];

    // Criar diretório para a vaga específica dentro de 'uploads'
    $vaga_slug = str_replace(' ', '_', strtolower($vaga));
    $upload_dir = 'uploads/' . $vaga_slug . '/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Nome do currículo e caminho completo
    $curriculo_nome = basename($curriculo['name']);
    $upload_path = $upload_dir . time() . "_" . $curriculo_nome;

    // Fazer upload do currículo
    if (move_uploaded_file($curriculo['tmp_name'], $upload_path)) {
        // Inserir dados no banco de dados
        $sql = "INSERT INTO candidatos (nome, whatsapp, email, linkedin, curriculo, data_envio) VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nome, $whatsapp, $email, $linkedin, $upload_path);

        if ($stmt->execute()) {
            // Gerar/atualizar arquivo CSV com os dados do candidato
            $csv_file = $upload_dir . 'candidatos.csv';

            // Verificar se o arquivo CSV já existe
            $csv_header = ["Nome", "WhatsApp", "Email", "LinkedIn", "Currículo", "Data de Envio"];
            $file_exists = file_exists($csv_file);

            $file = fopen($csv_file, 'a');

            // Se o CSV não existir, adicionar o cabeçalho
            if (!$file_exists) {
                fputcsv($file, $csv_header);
            }

            // Adicionar os dados do candidato ao CSV
            $data = [$nome, $whatsapp, $email, $linkedin, $upload_path, date("Y-m-d H:i:s")];
            fputcsv($file, $data);

            fclose($file);

            // Redirecionar para a página de vagas com mensagem de sucesso (em modal)
            echo "<script>
                    alert('Candidatura enviada com sucesso!');
                    window.location.href = 'candidate.html';
                  </script>";
            exit();
        } else {
            echo "Erro ao enviar candidatura: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Erro ao fazer upload do currículo.";
    }
}

// Fechar a conexão com o banco de dados
$conn->close();
?>
