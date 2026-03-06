<?php
session_start();
include 'conn.php';
header('Content-Type: application/json');

if(!isset($_SESSION['usuario_id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'ADMIN'){
    echo json_encode(['sucesso'=>false,'mensagem'=>'Acesso negado']);
    exit;
}

$conn->query("UPDATE requisicoes_verificacao SET status='REPROVADO' WHERE status='PENDENTE'");
echo json_encode(['sucesso'=>true]);
