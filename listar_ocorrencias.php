<?php
header('Content-Type: application/json; charset=utf-8');
require 'conn.php';
session_start();

$id_usuario = $_SESSION['usuario_id'] ?? null;
if (!$id_usuario) {
    http_response_code(401);
    echo json_encode(['mensagem' => 'Usuário não está logado.']);
    exit;
}

// Seleciona todas as ocorrências e suas contagens de avaliações
$sql = "SELECT id, id_usuario, titulo, descricao, gravidade, latitude, longitude, aval_positivo, aval_negativo, imagem
        FROM ocorrencias
        ORDER BY data DESC";

$result = mysqli_query($conn, $sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(['mensagem' => 'Erro ao listar ocorrências: ' . mysqli_error($conn)]);
    exit;
}

$ocorrencias = [];
while ($row = mysqli_fetch_assoc($result)) {
    $ocorrencias[] = $row;
}

echo json_encode($ocorrencias);
?>
