<?php
session_start();
include "conn.php";

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["erro" => "Usuário não autenticado"]);
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

$sql = "SELECT id, titulo, descricao, gravidade, aval_positivo, aval_negativo, latitude, longitude, data, imagem
        FROM ocorrencias 
        WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$ocorrencias = [];
while ($row = $result->fetch_assoc()) {
    $ocorrencias[] = $row;
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($ocorrencias);

