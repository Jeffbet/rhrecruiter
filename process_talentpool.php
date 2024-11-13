<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


    // Dados de conexão ao banco de dados
    $servername = "localhost";
    $username = "rhrecr47_jeffadmin";
    $password = "91085467Jl";
    $dbname = "rhrecr47_talent_pool";

    // Conectar ao banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexão
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }
    
// Verifica se o método de requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Preparar dados do formulário
    $full_name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $whatsapp = trim($_POST['whatsapp']);
    $state = trim($_POST['state']);
    $job_title = trim($_POST['jobTitle']);
    $is_pcd = isset($_POST['isPCD']) ? 1 : 0;

    // Verificar se o e-mail já está registrado
    $check_email_sql = "SELECT * FROM banco_talentos WHERE email=?";
    $stmt = $conn->prepare($check_email_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $check_email_result = $stmt->get_result();

    if ($check_email_result->num_rows > 0) {
        // Mensagem de erro se o e-mail já foi cadastrado
        echo "<script>
                alert('Você já cadastrou seu currículo com este e-mail.');
                window.location.href = 'talentpool.html';
              </script>";
        $stmt->close();
        $conn->close();
        exit();
    }

    // Caminho para armazenar o currículo na pasta "talentpool_uploads"
    $resume_dir = "talentpool_uploads/";
    if (!is_dir($resume_dir)) {
        mkdir($resume_dir, 0777, true); // Criar a pasta se não existir
    }
    $resume_file = $resume_dir . time() . "_" . basename($_FILES["resume"]["name"]);

    // Fazer upload do currículo para a pasta correta
    if (move_uploaded_file($_FILES["resume"]["tmp_name"], $resume_file)) {
        // Inserir dados no banco de dados
        $sql = "INSERT INTO banco_talentos (full_name, email, whatsapp, state, job_title, is_pcd, resume_path) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssis", $full_name, $email, $whatsapp, $state, $job_title, $is_pcd, $resume_file);

        if ($stmt->execute()) {
            // Adicionar os dados ao arquivo CSV
            $csv_file = 'talentpool.csv';
            $csv_data = array($full_name, $email, $whatsapp, $state, $job_title, $is_pcd ? 'Sim' : 'Não', $resume_file);

            $file = fopen($csv_file, 'a');
            if ($file !== false) {
                fputcsv($file, $csv_data);
                fclose($file);
            }

            // Mensagem de sucesso no modal
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('confirmationModal').style.display = 'flex';
                    });
                  </script>";
        } else {
            echo "<script>
                    alert('Erro ao cadastrar no banco de talentos: " . addslashes($stmt->error) . "');
                    window.location.href = 'talentpool.html';
                  </script>";
        }
        $stmt->close();
    } else {
        // Mensagem de erro se o upload falhar
        echo "<script>
                alert('Erro ao fazer o upload do currículo.');
                window.location.href = 'talentpool.html';
              </script>";
    }

    // Fechar a conexão com o banco de dados
    $conn->close();
}
?>
