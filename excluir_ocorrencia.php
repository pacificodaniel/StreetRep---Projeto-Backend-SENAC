<?php
session_start();
require 'conn.php';

header('Content-Type: application/json; charset=utf-8');



$usuario_id = $_SESSION['usuario_id'] ?? null;
$id_ocorrencia = intval($_POST['id_ocorrencia'] ?? 0);

if (!$usuario_id || $id_ocorrencia <= 0) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
    exit;
}

// 🔍 Verifica se a ocorrência realmente pertence ao usuário
$sqlVerifica = "SELECT id_usuario FROM ocorrencias WHERE id = ?";
$stmt = $conn->prepare($sqlVerifica);
$stmt->bind_param("i", $id_ocorrencia);
$stmt->execute();
$stmt->bind_result($dono_id);
$stmt->fetch();
$stmt->close();

if ($dono_id != $usuario_id) {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'mensagem' => "Usuário não é dono da ocorrência (esperado $usuario_id, encontrado $dono_id)."]);
    exit;
}

// 🔥 Só se passar no teste acima, excluir
$sql = "DELETE FROM ocorrencias WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_ocorrencia);

// 🔍 AQUI está a modificação:
if ($stmt->execute()) {
    echo json_encode(["sucesso" => true, "mensagem" => "Ocorrência excluída com sucesso."]);
} else {
    echo json_encode([
        "sucesso" => false,
       
        "erro_sql" => $stmt->error // Mostra o erro real
    ]);
}

$stmt->close();
?>
