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
    $vaga = trim($_POST['vaga']); // Garantir que não tenha espaços desnecessários
    $nome = $_POST['name'];
    $whatsapp = $_POST['whatsapp'];
    $email = $_POST['email'];
    $linkedin = $_POST['linkedin'];
    $curriculo = $_FILES['resume'];

    // Função para normalizar o nome da vaga e evitar problemas com caracteres especiais
    function normalize_string($string) {
        $table = [
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'AE', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ð'=>'D', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 
            'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 
            'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'ae', 'ç'=>'c', 'è'=>'e', 
            'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 
            'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 
            'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', '/' => '_', ' ' => '_'
        ];
        return strtolower(strtr($string, $table));
    }

    // Normalizar o nome da vaga para ser usado no diretório
    $vaga_slug = normalize_string($vaga);
    $upload_dir = 'uploads/' . $vaga_slug . '/';

    // Criar diretório para a vaga específica dentro de 'uploads', se não existir
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            die("Erro ao criar diretório para a vaga: " . $vaga_slug);
        }
    }

    // Nome do currículo e caminho completo
    $curriculo_nome = basename($curriculo['name']);
    $upload_path = $upload_dir . time() . "_" . $curriculo_nome;

    // Verificar se o candidato já enviou candidatura para esta vaga específica
    $check_duplicate_sql = "SELECT * FROM candidatos WHERE email = ? AND vaga = ?";
    $stmt = $conn->prepare($check_duplicate_sql);
    $stmt->bind_param("ss", $email, $vaga);
    $stmt->execute();
    $check_duplicate_result = $stmt->get_result();

    if ($check_duplicate_result->num_rows > 0) {
        // Se o candidato já enviou candidatura para esta vaga, mostrar mensagem e interromper o processo
        echo "<script>
                alert('Você já enviou uma candidatura para esta vaga.');
                window.location.href = 'candidate.html';
              </script>";
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->close();

    // Fazer upload do currículo
    if (move_uploaded_file($curriculo['tmp_name'], $upload_path)) {
        // Inserir dados no banco de dados
        $sql = "INSERT INTO candidatos (vaga, nome, whatsapp, email, linkedin, curriculo, data_envio) VALUES (?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $vaga, $nome, $whatsapp, $email, $linkedin, $upload_path);

        if ($stmt->execute()) {
            // Gerar/atualizar arquivo CSV com os dados do candidato
            $csv_file = $upload_dir . 'candidatos.csv';

            // Verificar se o arquivo CSV já existe
            $csv_header = ["Vaga", "Nome", "WhatsApp", "Email", "LinkedIn", "Currículo", "Data de Envio"];
            $file_exists = file_exists($csv_file);

            $file = fopen($csv_file, 'a');

            // Se o CSV não existir, adicionar o cabeçalho
            if (!$file_exists) {
                fputcsv($file, $csv_header);
            }

            // Adicionar os dados do candidato ao CSV
            $data = [$vaga, $nome, $whatsapp, $email, $linkedin, $upload_path, date("Y-m-d H:i:s")];
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
