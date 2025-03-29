<?php
set_time_limit(300);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["pdf"])) {
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $pdfFile = $uploadDir . basename($_FILES["pdf"]["name"]);
    if (move_uploaded_file($_FILES["pdf"]["tmp_name"], $pdfFile)) {
        // Executa o script para extrair os dados
        $command = "node extrair.js " . escapeshellarg($pdfFile) . " 2>&1";
        shell_exec($command);
        
        if (file_exists("mensagem.json")) {
            // Redireciona para a página de seleção
            header("Location: selecionar_pacientes.php");
            exit();
        } else {
            echo "Erro: Arquivo mensagem.json não foi criado.";
        }
    } else {
        echo "Erro ao enviar o arquivo.";
    }
} else {
    echo "Nenhum arquivo enviado.";
}
?>