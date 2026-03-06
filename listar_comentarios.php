<?php
// listar_comentarios.php
header('Content-Type: application/json; charset=utf-8');
require 'conn.php';

$id = isset($_GET['id_ocorrencia']) ? intval($_GET['id_ocorrencia']) : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'id_ocorrencia inválido']);
    exit;
}

// Primeiro pega o título da ocorrência (sempre)
$stmt = mysqli_prepare($conn, "SELECT titulo FROM ocorrencias WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$titulo = null;
if ($row = mysqli_fetch_assoc($res)) {
    $titulo = $row['titulo'];
}
mysqli_stmt_close($stmt);

// Agora pega os comentários (se houver)
$sql = "
    SELECT a.id, a.id_ocorrencia, a.id_usuario, u.nome AS usuario,
           a.comentario, a.tipo, a.criado_em
    FROM avaliacoes a
    LEFT JOIN usuarios u ON a.id_usuario = u.id
    WHERE a.id_ocorrencia = ?
      AND a.comentario IS NOT NULL
    ORDER BY a.criado_em DESC
";

$stmt2 = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt2, "i", $id);
mysqli_stmt_execute($stmt2);
$res2 = mysqli_stmt_get_result($stmt2);

$comentarios = [];
while ($r = mysqli_fetch_assoc($res2)) {
    $comentarios[] = $r;
}
mysqli_stmt_close($stmt2);

// Retorna objeto com título e array de comentários
echo json_encode([
    'titulo' => $titulo ?: "Sem título",
    'comentarios' => $comentarios
]);
