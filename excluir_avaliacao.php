<?php
require 'conn.php'; // ajuste o caminho se necessário

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido.']);
        exit;
    }

    // Exclui a avaliação
    $sql = "DELETE FROM avaliacoes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir do banco.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido.']);
}
?>
