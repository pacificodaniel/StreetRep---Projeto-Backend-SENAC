<?php
include "conn.php";
session_start();

$dados = json_decode(file_get_contents('php://input'), true);

$titulo = trim($dados['titulo'] ?? '');
$descricao = trim($dados['descricao'] ?? '');
$gravidade = trim($dados['gravidade'] ?? '');
$lat = $dados['latitude'] ?? null;
$lng = $dados['longitude'] ?? null;
$id_usuario = $_SESSION['usuario_id'] ?? null;

if (!$id_usuario) {
    echo json_encode(['mensagem' => 'Você precisa estar logado.']);
    exit;
}

if ($titulo && $descricao && $gravidade && $lat && $lng) {
    $stmt = $conn->prepare("INSERT INTO ocorrencias (id_usuario, titulo, descricao, gravidade, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdd", $id_usuario, $titulo, $descricao, $gravidade, $lat, $lng);
    $ok = $stmt->execute();

    if ($ok) {
        echo json_encode(['mensagem' => 'Ocorrência registrada com sucesso!']);
    } else {
        echo json_encode(['mensagem' => 'Erro ao registrar ocorrência.']);
    }
    $stmt->close();
} else {
    echo json_encode(['mensagem' => 'Preencha todos os campos.']);
}
?>
