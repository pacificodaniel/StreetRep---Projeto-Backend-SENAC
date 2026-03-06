<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
echo $_SESSION['usuario_id'] ?? 'vazio';
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://localhost/streetrep'); // ajuste conforme necessário
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require 'conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();

// garante que o usuário está logado
$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não está logado.']);
    exit;
}

// pega dados do POST
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$id_ocorrencia = intval($data['id_ocorrencia'] ?? 0);
$tipo = $data['tipo'] ?? null;
$comentario = trim($data['comentario'] ?? '');

if ($id_ocorrencia <= 0 || !in_array($tipo, ['real','falso'])) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
    exit;
}

// escapa o comentário
$comentario_sql = mysqli_real_escape_string($conn, $comentario);

// prepara e executa o INSERT
$sql = "
    INSERT INTO avaliacoes (id_ocorrencia, usuario_id, tipo, comentario)
    VALUES ($id_ocorrencia, $usuario_id, '$tipo', '$comentario_sql')
    ON DUPLICATE KEY UPDATE tipo='$tipo', comentario='$comentario_sql', criado_em=NOW()
";


if (mysqli_query($conn, $sql)) {
    
    echo json_encode(['sucesso' => true, 'mensagem' => 'Avaliação registrada com sucesso.']);
} else {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao registrar: ' . mysqli_error($conn)]);
}

