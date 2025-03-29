<?php
$respostas = json_decode(file_get_contents('respostas.json'), true);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respostas dos Pacientes</title>
</head>
<body>
    <h1>Respostas Recebidas</h1>
    <table border="1">
        <tr>
            <th>Número</th>
            <th>Mensagem</th>
            <th>Data/Hora</th>
        </tr>
        <?php foreach ($respostas as $resposta): ?>
        <tr>
            <td><?= htmlspecialchars($resposta['numero']) ?></td>
            <td><?= htmlspecialchars($resposta['mensagem']) ?></td>
            <td><?= htmlspecialchars($resposta['timestamp']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
