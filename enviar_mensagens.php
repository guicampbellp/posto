<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["pacientes"])) {
    $pacientes = json_decode(file_get_contents('mensagem.json'), true);
    $pacientesSelecionados = [];
    $tipoMensagem = $_POST["tipo_mensagem"] ?? 'confirmacao';
    
    foreach ($_POST["pacientes"] as $indice) {
        if (isset($pacientes[$indice])) {
            $paciente = $pacientes[$indice];
            
            // Extrai informações da mensagem original
            preg_match('/Olá, (.*?)!/', $paciente['mensagem'], $nomeMatch);
            preg_match('/Data: (\d{2}\/\d{2}\/\d{4})/', $paciente['mensagem'], $dataMatch);
            preg_match('/Horário: (\d{2}:\d{2})/', $paciente['mensagem'], $horaMatch);
            
            $nome = $nomeMatch[1] ?? '';
            $data = $dataMatch[1] ?? '';
            $hora = $horaMatch[1] ?? '';
            
            if ($tipoMensagem === 'cancelamento') {
                $paciente['mensagem'] = "Mensagem Automática - Cancelamento de Consulta\n\n" .
                                        "Olá, $nome!\n\n" .
                                        "Lamentamos informar que sua consulta no Posto de saúde do Guapiranga " .
                                        "marcada para:\n\n" .
                                        "📅 Data: $data\n" .
                                        "⏰ Horário: $hora\n\n" .
                                        "precisa ser cancelada devido a imprevistos. Entraremos em contato em breve " .
                                        "para reagendamento.\n\n" .
                                        "Pedimos desculpas pelo inconveniente e agradecemos sua compreensão.";
            }
            
            $pacientesSelecionados[] = $paciente;
        }
    }
    
    // Salva apenas os selecionados em um novo arquivo
    file_put_contents('mensagem_selecionados.json', json_encode($pacientesSelecionados));
    
    // Executa o envio via WhatsApp
    $output = shell_exec("node whatsapp.js mensagem_selecionados.json 2>&1");
    
    echo "<h1>Resultado do Envio</h1>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    echo "<p>Mensagens de " . ($tipoMensagem === 'cancelamento' ? 'cancelamento' : 'confirmação') . " enviadas com sucesso!</p>";
    echo "<a href='selecionar_pacientes.php'>Voltar</a>";
} else {
    header("Location: selecionar_pacientes.php");
    exit();
}
?>