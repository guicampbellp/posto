<?php
$pacientes = json_decode(file_get_contents('mensagem.json'), true);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecionar Pacientes para Envio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .actions {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        button, .btn-voltar {
            padding: 10px 15px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        #selecionarTodos {
            background-color: #2196F3;
        }
        #selecionarTodos:hover {
            background-color: #0b7dda;
        }
        #confirmarEnvio {
            background-color: #4CAF50;
        }
        #confirmarEnvio:hover {
            background-color: #45a049;
        }
        #cancelarEnvio {
            background-color: #f44336;
        }
        #cancelarEnvio:hover {
            background-color: #da190b;
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
        .btn-voltar {
            background-color: #9E9E9E;
            margin-bottom: 20px;
        }
        .btn-voltar:hover {
            background-color: #757575;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.html" class="btn-voltar">← Voltar</a>
        <h1>Selecionar Pacientes para Envio</h1>
        <form id="formPacientes" action="enviar_mensagens.php" method="post">
            <input type="hidden" name="tipo_mensagem" id="tipo_mensagem" value="confirmacao">
            <table>
                <thead>
                    <tr>
                        <th>Selecionar</th>
                        <th>Nome</th>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Telefone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacientes as $index => $paciente): 
                        preg_match('/Data: (\d{2}\/\d{2}\/\d{4})/', $paciente['mensagem'], $dataMatch);
                        preg_match('/Horário: (\d{2}:\d{2})/', $paciente['mensagem'], $horaMatch);
                        preg_match('/Olá, (.*?)!/', $paciente['mensagem'], $nomeMatch);
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="pacientes[]" value="<?= $index ?>" class="checkbox-paciente" checked>
                        </td>
                        <td><?= htmlspecialchars($nomeMatch[1] ?? '') ?></td>
                        <td><?= htmlspecialchars($dataMatch[1] ?? '') ?></td>
                        <td><?= htmlspecialchars($horaMatch[1] ?? '') ?></td>
                        <td><?= htmlspecialchars($paciente['telefone'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="actions">
                <button type="button" id="selecionarTodos">Tirar Seleção</button>
                <div class="button-group">
                    <button type="submit" id="confirmarEnvio">Confirmar Consultas</button>
                    <button type="button" id="cancelarEnvio">Cancelar Consultas</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const btnSelecionarTodos = document.getElementById('selecionarTodos');
        const checkboxes = document.querySelectorAll('.checkbox-paciente');

        function atualizarBotao() {
            const todosSelecionados = [...checkboxes].every(checkbox => checkbox.checked);
            btnSelecionarTodos.textContent = todosSelecionados ? "Tirar Seleção" : "Selecionar Todos";
        }

        btnSelecionarTodos.addEventListener('click', function() {
            const todosSelecionados = [...checkboxes].every(checkbox => checkbox.checked);
            checkboxes.forEach(checkbox => checkbox.checked = !todosSelecionados);
            atualizarBotao();
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', atualizarBotao);
        });

        document.getElementById('cancelarEnvio').addEventListener('click', function() {
            if(confirm('Tem certeza que deseja enviar mensagens de cancelamento para os pacientes selecionados?')) {
                document.getElementById('tipo_mensagem').value = 'cancelamento';
                document.getElementById('formPacientes').submit();
            }
        });

        // Atualiza o botão ao carregar a página
        atualizarBotao();
    </script>
</body>
</html>