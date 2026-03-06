<?php
// registrar_avaliacao.php
// sem saída antes do JSON
header('Content-Type: application/json; charset=utf-8');
require 'conn.php';
session_start(); // necessário para recuperar $_SESSION['usuario_id']

// garante método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido']);
    exit;
}

// garante usuário logado
$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não está logado.']);
    exit;
}

// lê JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'JSON inválido']);
    exit;
}

$id_ocorrencia = intval($data['id_ocorrencia'] ?? 0);
$tipo = $data['tipo'] ?? null;
$comentario = trim($data['comentario'] ?? '');

if ($id_ocorrencia <= 0 || !in_array($tipo, ['real','falso'])) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
    exit;
}

// escape do comentário
$comentario_sql = mysqli_real_escape_string($conn, $comentario);

// usa coluna id_usuario (consistente com o DB)
$sql = "
    INSERT INTO avaliacoes (id_ocorrencia, id_usuario, tipo, comentario)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE tipo = VALUES(tipo), comentario = VALUES(comentario), criado_em = NOW()
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iiss", $id_ocorrencia, $usuario_id, $tipo, $comentario_sql);

if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);
    $err = mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao registrar: ' . $err]);
    exit;
}
mysqli_stmt_close($stmt);

// Agora calcula os contadores atualizados para essa ocorrência
$countSql = "
    SELECT
      SUM(tipo = 'real') AS aval_pos,
      SUM(tipo = 'falso') AS aval_neg
    FROM avaliacoes
    WHERE id_ocorrencia = ?
";
$stmt2 = mysqli_prepare($conn, $countSql);
mysqli_stmt_bind_param($stmt2, "i", $id_ocorrencia);
mysqli_stmt_execute($stmt2);
$resCounts = mysqli_stmt_get_result($stmt2);
$counts = mysqli_fetch_assoc($resCounts);
mysqli_stmt_close($stmt2);

$aval_pos = intval($counts['aval_pos'] ?? 0);
$aval_neg = intval($counts['aval_neg'] ?? 0);

echo json_encode([
    'sucesso' => true,
    'mensagem' => 'Avaliação registrada com sucesso.',
    'aval_positivo' => $aval_pos,
    'aval_negativo' => $aval_neg
]);

if ($tipo === 'real') {
    $sql_update = "UPDATE ocorrencias SET aval_positivo = aval_positivo + 1 WHERE id = ?";
} else {
    $sql_update = "UPDATE ocorrencias SET aval_negativo = aval_negativo + 1 WHERE id = ?";
}

$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("i", $id_ocorrencia);
$stmt_update->execute();
