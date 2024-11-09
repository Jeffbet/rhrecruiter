<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "rhrecr47_jeffadmin";
    $password = "91085467Jl";
    $dbname = "rhrecr47_talent_pool"; // Nome do banco de dados

     // Criar conexão
     $conn = new mysqli($servername, $username, $password, $dbname);

     // Verificar conexão
     if ($conn->connect_error) {
         die("Falha na conexão: " . $conn->connect_error);
     }
 
     // Preparar dados do formulário
     $full_name = $_POST['name'];
     $email = $_POST['email'];
     $whatsapp = $_POST['whatsapp'];
     $state = $_POST['state'];
     $job_title = $_POST['jobTitle'];
     $is_pcd = isset($_POST['isPCD']) ? 1 : 0;
 
     // Verificar se o e-mail já está registrado
     $check_email_sql = "SELECT * FROM candidates WHERE email='$email'";
     $check_email_result = $conn->query($check_email_sql);
 
     if ($check_email_result->num_rows > 0) {
         echo "Você já cadastrou seu currículo com este e-mail.";
     } else {
         // Caminho para armazenar o currículo na pasta "talentpool_uploads"
         $resume_dir = "talentpool_uploads/";
         if (!is_dir($resume_dir)) {
             mkdir($resume_dir, 0777, true); // Criar a pasta se não existir
         }
         $resume_file = $resume_dir . basename($_FILES["resume"]["name"]);
 
         // Mover o currículo para a pasta correta
         if (move_uploaded_file($_FILES["resume"]["tmp_name"], $resume_file)) {
             // Inserir dados no banco de dados
             $sql = "INSERT INTO candidates (full_name, email, whatsapp, state, job_title, is_pcd, resume_path) 
                     VALUES ('$full_name', '$email', '$whatsapp', '$state', '$job_title', $is_pcd, '$resume_file')";
 
             if ($conn->query($sql) === TRUE) {
                 echo "Cadastro realizado com sucesso!";
 
                 // Adicionar os dados ao arquivo CSV
                 $csv_file = 'talentpool.csv';
                 $csv_data = array($full_name, $email, $whatsapp, $state, $job_title, $is_pcd ? 'Sim' : 'Não', $resume_file);
 
                 $file = fopen($csv_file, 'a');
                 fputcsv($file, $csv_data);
                 fclose($file);
             } else {
                 echo "Erro ao cadastrar no banco de talentos: " . $conn->error;
             }
         } else {
             echo "Erro ao fazer o upload do currículo.";
         }
     }
 
     $conn->close();
 }
 ?>