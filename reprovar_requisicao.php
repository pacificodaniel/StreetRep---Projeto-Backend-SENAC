<?php
session_start();
include 'conn.php';
header('Content-Type: application/json');

if(!isset($_SESSION['usuario_id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'ADMIN'){
    echo json_encode(['sucesso'=>false,'mensagem'=>'Acesso negado']);
    exit;
}

$id = $_POST['id'] ?? 0;
$id = intval($id);

if(!$id){
    echo json_encode(['sucesso'=>false,'mensagem'=>'ID inválido']);
    exit;
}

$stmt = $conn->prepare("UPDATE requisicoes_verificacao SET status='REPROVADO' WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

echo json_encode(['sucesso'=>true]);
