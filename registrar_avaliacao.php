<?php
header('Content-Type: application/json; charset=utf-8');
require 'conn.php';
session_start();

// Permitir CORS (caso o JS rode em outra origem)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

// Só aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido']);
    exit;
}

// Verifica se o usuário está logado
$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não está logado.']);
    exit;
}

// Lê o JSON enviado
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'JSON inválido']);
    exit;
}

$id_ocorrencia = intval($data['id_ocorrencia'] ?? 0);
$tipo = $data['tipo'] ?? null; // 'positivo' ou 'negativo'
$comentario = trim($data['comentario'] ?? '');

if ($id_ocorrencia <= 0 || !in_array($tipo, ['positivo', 'negativo'])) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
    exit;
}

// === Inserir ou atualizar a avaliação ===
$sql = "INSERT INTO avaliacoes (id_ocorrencia, id_usuario, tipo, comentario)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE tipo = VALUES(tipo), comentario = VALUES(comentario), criado_em = NOW()";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "iiss", $id_ocorrencia, $usuario_id, $tipo, $comentario);
    $ok = mysqli_stmt_execute($stmt);
    if ($ok === false) {
        $err = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        http_response_code(500);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao gravar avaliação: ' . $err]);
        exit;
    }
    mysqli_stmt_close($stmt);
} else {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao preparar INSERT.']);
    exit;
}

// === Recalcula as contagens de avaliações ===
$countSql = "SELECT 
                SUM(CASE WHEN tipo = 'positivo' THEN 1 ELSE 0 END) AS aval_pos,
                SUM(CASE WHEN tipo = 'negativo' THEN 1 ELSE 0 END) AS aval_neg
             FROM avaliacoes
             WHERE id_ocorrencia = ?";

$aval_pos = 0;
$aval_neg = 0;

if ($stmt2 = mysqli_prepare($conn, $countSql)) {
    mysqli_stmt_bind_param($stmt2, "i", $id_ocorrencia);
    mysqli_stmt_execute($stmt2);
    $res = mysqli_stmt_get_result($stmt2);
    if ($row = mysqli_fetch_assoc($res)) {
        $aval_pos = intval($row['aval_pos'] ?? 0);
        $aval_neg = intval($row['aval_neg'] ?? 0);
    }
    mysqli_stmt_close($stmt2);
}

// === Atualiza os campos na tabela de ocorrências ===
$updateSql = "UPDATE ocorrencias
              SET aval_positivo = ?, aval_negativo = ?
              WHERE id = ?";
if ($stmt3 = mysqli_prepare($conn, $updateSql)) {
    mysqli_stmt_bind_param($stmt3, "iii", $aval_pos, $aval_neg, $id_ocorrencia);
    mysqli_stmt_execute($stmt3);
    mysqli_stmt_close($stmt3);
}

// === Retorna JSON com os dados atualizados ===
echo json_encode([
    'sucesso' => true,
    'mensagem' => 'Avaliação registrada com sucesso.',
    'aval_positivo' => $aval_pos,
    'aval_negativo' => $aval_neg
]);
?>
