<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug_editar.log');

include "conn.php";

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

$id = $_POST['id_ocorrencia'] ?? '';
$descricao = $_POST['descricao'] ?? '';

if (!$id || !$descricao) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Prepara e executa o UPDATE
$stmt = $conn->prepare("UPDATE ocorrencias SET descricao = ? WHERE id = ? AND id_usuario = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro no prepare: ' . $conn->error]);
    exit;
}

$stmt->bind_param("sii", $descricao, $id, $usuario_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
