<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require 'conn.php';
include 'conn.php';

$id = isset($_GET['id_ocorrencia']) ? intval($_GET['id_ocorrencia']) : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'id_ocorrencia inválido']);
    exit;
}

$sql = "
    SELECT a.id, a.id_ocorrencia, u.nome AS usuario,
           a.comentario, a.tipo, a.criado_em,
           o.titulo
    FROM avaliacoes a
    LEFT JOIN usuarios u ON a.id_usuario = u.id
    LEFT JOIN ocorrencias o ON a.id_ocorrencia = o.id
    WHERE a.id_ocorrencia = $id
      AND a.comentario IS NOT NULL
    ORDER BY a.criado_em DESC
";


$result = mysqli_query($conn, $sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar comentários: ' . mysqli_error($conn)]);
    exit;
}

$comentarios = [];
while ($row = mysqli_fetch_assoc($result)) {
    $comentarios[] = $row;
}

echo json_encode($comentarios);
?>
