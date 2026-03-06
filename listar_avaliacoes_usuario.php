<?php
session_start();
include "conn.php";

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]);
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

$sql = "SELECT a.id, a.tipo, a.comentario, a.criado_em, o.titulo AS nome_ocorrencia
        FROM avaliacoes a
        JOIN ocorrencias o ON a.id_ocorrencia = o.id
        WHERE a.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$avaliacoes = [];
while ($row = $result->fetch_assoc()) {
    $avaliacoes[] = $row;
}

echo json_encode($avaliacoes);
